<?php

namespace Drupal\cfs_mail\Entity;

use Drupal\workflow\Entity\Workflow;
use Drupal\workflow\Entity\WorkflowState;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the TransitionMessage entity.
 *
 * @ConfigEntityType(
 *   id = "transition_message",
 *   label = @Translation("Transition Mailer"),
 *   handlers = {
 *     "list_builder" = "Drupal\cfs_mail\Controller\TransitionMessageListBuilder",
 *     "form" = {
 *       "add" = "Drupal\cfs_mail\Form\TransitionMessageForm",
 *       "edit" = "Drupal\cfs_mail\Form\TransitionMessageForm",
 *       "delete" = "Drupal\cfs_mail\Form\TransitionMessageDeleteForm",
 *     },
 *   },
 *   config_prefix = "message",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "node_type",
 *     "field",
 *     "workflow_from",
 *     "workflow_to",
 *     "node_author_role",
 *     "recipients",
 *     "cc",
 *     "bcc",
 *     "subject",
 *     "body",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/cfs/mail/{transition_message}",
 *     "delete-form" = "/admin/config/cfs/mail/{transition_message}/delete",
 *   }
 * )
 */
class TransitionMessage extends ConfigEntityBase implements TransitionMessageInterface {

  /**
   * The message ID.
   *
   * It's the machine name of the item.
   *
   * @var string
   */
  public $id;

  /**
   * The type of the node to check on.
   *
   * Example: order, article, vehicle_request.
   *
   * @var string
   */
  public $node_type;

  /**
   * The field to be checking against.
   *
   * @var string
   */
  public $field;

  /**
   * The state that the workflow is transitioning from.
   *
   * @var string
   */
  public $workflow_from;

  /**
   * The state that the workflow is transitioning to.
   *
   * @var string
   */
  public $workflow_to;

  /**
   * The role(s) of the author to run this transition on.
   *
   * This also identifies the roles an author should have to receive an email
   * should the "author" option be selected in the send_to array.
   *
   * Keys and values are the machine names of the various roles.
   *
   * @var array
   */
  public $node_author_role;

  /**
   * The people the message should be sent to. Keys are author and coach.
   *
   * If Author is selected, an email will be sent to the node's author, if
   * the at least one of author's roles is identified in the node_author_role
   * array.
   *
   * If Coach is selected, then the coach pertaining to the team associated
   * with the node will be sent an email.
   *
   * Keys and values are author and coach, respectively.
   *
   * @var array
   */
  public $recipients;

  /**
   * The people the message should be sent to as cc.
   *
   * @var array
   *
   * @see $recipients
   */
  public $cc;

  /**
   * The people the message should be sent to as bcc.
   *
   * @var array
   *
   * @see $recipients
   */
  public $bcc;

  /**
   * The text to be placed in the subject line.
   *
   * @var string
   */
  public $subject;

  /**
   * The text to be placed in the body via HTML.
   *
   * @var string
   */
  public $body;

  /**
   * Retrieves the recipients.
   *
   * @param string $recipient_type
   *   Unused. TODO: Why is this needed?
   */
  public function getRecipients(string $recipient_type) {
    return $this->$recipient_type;
  }

  /**
   * {@inheritdoc}
   */
  public function matchesTransition(string $field, $workflow_from, $workflow_to) {
    $field_matches = $this->field == $field;
    $from_matches = $workflow_from != NULL && $this->workflow_from == $workflow_from;
    $to_matches = $workflow_to != NULL && $this->workflow_to == $workflow_to;
    return $field_matches && $from_matches && $to_matches;
  }

  /**
   * Discovers the name of the workflow used to track this transition.
   *
   * @return string|null
   *   The id of the workflow, otherwise null.
   */
  public function deduceWorkflow() {
    if ($this->workflow_from === NULL) {
      return NULL;
    }
    $wf_state = WorkflowState::load($this->workflow_from);
    if (!$wf_state) {
      return NULL;
    }
    $workflow = $wf_state->getWorkflowId();
    $entity = Workflow::load($workflow);

    return $entity ? $entity->id() : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function sendMessages(array &$params, array $mailer_plugins) {
    $id = $this->id;
    $params['logger']->notice("Sending mail(s) for $id transition.");
    \Drupal::moduleHandler()->alter('mail_transition_params', $params, $id);
    \Drupal::moduleHandler()->alter("mail_transition_$id\_params", $params, $id);
    $transition = $this->workflow_from . "-" . $this->workflow_to;

    $this->setCCParam('cc', $transition, $params, $mailer_plugins);
    $this->setCCParam('bcc', $transition, $params, $mailer_plugins);

    foreach ($mailer_plugins as $plugin_id => $definition) {
      $mailer = new $definition['class']($plugin_id, $definition, $params, $this, $transition);
      $mailer->sendMail();
    }
  }

  /**
   * Sets $this->params[$cc_type] to contain all the emails.
   *
   * @param string $cc_type
   *   The CC type (CC or BCC). Should be all lower-case.
   * @param string $transition
   *   The transition that's being used to create the mail.
   * @param array $params
   *   The parameters controlling this transition.
   * @param array $mailer_plugins
   *   All mailer plugins.
   *
   * @see $params
   * @see $cc
   * @see $bcc
   */
  // @codingStandardsIgnoreStart
  private function setCCParam(string $cc_type, string $transition, array &$params, array &$mailer_plugins) {
  // @codingStandardsIgnoreEnd
    $ccRep = [];
    foreach ($mailer_plugins as $mailer_id => $cc_mailer) {
      if (!$this->$cc_type[$mailer_id]) {
        continue;
      }
      if (!$cc_mailer) {
        continue;
      }
      $mailer = new $cc_mailer['class']($mailer_id, $cc_mailer, $params, $this, $transition);
      $address = $mailer->getCCAddress($cc_type);
      if (is_array($address)) {
        foreach ($address as $addr) {
          $ccRep[] = $addr;
        }
      }
      else {
        $ccRep[] = $address;
      }
    }
    $params[$cc_type] = implode(',', $ccRep);
  }

}
