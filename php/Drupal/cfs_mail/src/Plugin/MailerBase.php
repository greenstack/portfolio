<?php

namespace Drupal\cfs_mail\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Messenger\MessengerTrait;

use Drupal\cfs_mail\Entity\TransitionMessage;

/**
 * Provides variables and methods for all Mailer classes.
 */
abstract class MailerBase extends PluginBase implements MailerInterface {

  use MessengerTrait;

  /**
   * The parameters used to control this transition.
   *
   * @var array
   */
  protected $params;

  /**
   * The workflow transition triggering this mailer.
   *
   * @var string
   */
  protected $transition;

  /**
   * The transition message entity using this mailer.
   *
   * @var \Drupal\cfs_mail\Entity\TransitionMessage
   */
  protected $transition_message;

  /**
   * Builds a MailerBase object.
   *
   * @param string $id
   *   The id of the mailer being created.
   * @param mixed $definition
   *   The annotation object defining this mailer.
   * @param mixed[] $params
   *   The arguments and settings for the sent mail.
   * @param \Drupal\cfs_mail\Entity\TransitionMessage $transition_message
   *   The transition message entity that this Mailer will operate with.
   * @param string $transition
   *   The transition that this is acting on.
   */
  public function __construct($id, $definition, array $params, TransitionMessage &$transition_message, $transition) {
    parent::__construct([], $id, $definition);
    $this->params = $params;
    $this->params['transition_message'] = $transition_message;
    $this->transition = $transition;
    $this->transition_message = $transition_message;
  }

  /**
   * Determines if the mail should be sent.
   *
   * Checks to see if this mailer's id is listed among the recipients.
   *
   * @param string $recipient_type
   *   The type of recipient. Valid values are 'recipients', 'cc', and 'bcc'.
   *
   * @return bool
   *   TRUE if the mail should be sent.
   */
  protected function shouldSend(string $recipient_type) {
    $recipients = $this->transition_message->getRecipients($recipient_type);
    if (!is_array($recipients)) {
      // The message doesn't have CC/BCC set up, so that means there are no cc/
      // bcc recipients set.
      return FALSE;
    }
    if (!array_key_exists($this->getPluginId(), $recipients)) {
      return FALSE;
    }
    return $recipients[$this->getPluginId()];
  }

  /**
   * Prepares the mailer to send the emails.
   */
  abstract protected function prepareMail();

  /**
   * Gets the address that this mailer will send to.
   *
   * @return mixed
   *   A single address or an array of addresses, or null if none.
   */
  abstract protected function getAddress();

  /**
   * Retrieves the $cc_type address for this mailer.
   *
   * @param string $cc_type
   *   Must be either 'cc' or 'bcc'.
   */
  // @codingStandardsIgnoreStart
  public function getCCAddress($cc_type) {
  // @codingStandardsIgnoreEnd
    if (!$this->shouldSend($cc_type)) {
      return "";
    }
    $this->prepareMail();
    return $this->getAddress();
  }

  /**
   * Contains the bulwark of the code used to send mail.
   *
   * Its purpose is to abstract away many of the requirements for sending mail
   * to the recipients, by calling shouldSend(), prepareMail(), and
   * deliverMail().
   */
  public function sendMail() {
    if (!$this->shouldSend('recipients')) {
      return FALSE;
    }
    $this->prepareMail();

    \Drupal::moduleHandler()->alter($this->getPluginId() . "_mailer_params", $this->params, $this->pluginId);
    $address = $this->getAddress();

    $mailLogger = $this->params['logger'];
    $transition_message_id = $this->transition_message->id();
    $logger_prefix = "Sending an email to";
    $logger_suffix = "as a part of the $transition_message_id transition (mailer ID: $this->pluginId)";
    if (empty($address)) {
      $mailLogger->error("No addresses were found for $this->pluginId recipient(s). Check the recipients that you expected this to go to. Aborting.");
      $this->messenger()->addError("An email could not be sent. Please contact the site administrator.");
      return;
    }
    elseif (is_array($address)) {
      foreach ($address as $string) {
        $mailLogger->notice("$logger_prefix $string $logger_suffix");
        $this->mail($string);
      }
    }
    else {
      $mailLogger->notice("$logger_prefix $address $logger_suffix");
      $this->mail($address);
    }
  }

  /**
   * Uses Drupal's mail API to send a mail message.
   *
   * @param string $address
   *   The email address to send the mail to.
   */
  protected function mail($address) {
    \Drupal::service('plugin.manager.mail')->mail('cfs_mail', $this->transition, $address, 'en-us', $this->params);
  }

}
