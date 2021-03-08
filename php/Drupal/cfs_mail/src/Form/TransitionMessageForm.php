<?php

namespace Drupal\cfs_mail\Form;

use Drupal\cfs_mail\Parser\MailParser;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cfs_mail\WorkflowDiscovery;

/**
 * The form used to create and edit Transition Message entities.
 */
class TransitionMessageForm extends EntityForm {

  /**
   * The available workflows.
   *
   * @var string[]
   */
  private $workflows = [];
  /**
   * The available workflow states.
   *
   * @var string[]
   */
  private $states = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $transition_message = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Label"),
      '#maxlength' => '255',
      '#default_value' => $transition_message->label(),
      '#description' => $this->t("Label for the transition message."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $transition_message->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
      '#disabled' => !$transition_message->isNew(),
    ];

    $form['transition'] = [
      '#type' => 'fieldset',
      '#title' => 'Transition Information',
    ];
    $nodeTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $nodeOptions = WorkflowDiscovery::getContentTypes();

    $form['transition']['node_type'] = [
      '#type' => 'select',
      '#title' => 'Select Content Type',
      '#description' => $this->t("This can't be changed once saved."),
      '#options' => $nodeOptions,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'getWorkflowFields'],
        'wrapper' => 'workflow-wrapper',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading workflow fields...',
        ],
      ],
      '#default_value' => $transition_message->get('node_type'),
    ];
    $disabled_attribute = ['disabled' => 'disabled'];
    // Do you really want to know why I'm doing this? It's because the ajax
    // is broken and I don't want to solve it. It's not worth it, and it's
    // probably better this way anyways. You can't break preexisting messages
    // accidentally if we make is so you can't change the content and workflow
    // type. If you absolutely must, be my guest in discovering how to fix it.
    if (!$transition_message->isNew()) {
      $form['transition']['node_type']['#attributes'] = $disabled_attribute;
    }

    $form['transition']['workflow'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'workflow-wrapper'],
    ];
    $node_type = $transition_message->get('node_type') ?? $form_state->getValue('node_type');
    $this->workflows = WorkflowDiscovery::getWorkflows($node_type);

    // We need this to figure out the transition field as well as some
    // other things down the line.
    $workflow_id = $transition_message->deduceWorkflow();

    if ($transition_message->isNew()) {
      $selectedField = NULL;
    }
    else {
      $transition_field = $transition_message->get('field');
      $transition_field = "$workflow_id%$transition_field";
      // $this->workflows[$transition_field];
      $selectedField = $transition_field;
    }

    // In the format workflow%workflow_field.
    $form['transition']['workflow']['field'] = [
      '#type' => 'select',
      '#title' => 'Select the Workflow.',
      '#description' => $this->t("This can't be changed once saved."),
      '#options' => $this->workflows,
      '#required' => TRUE,
      '#default_value' => $selectedField,
      '#ajax' => [
        'callback' => [$this, 'getWorkflowStates'],
        'wrapper' => 'states-wrapper',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Loading workflow states...',
        ],
      ],
    ];
    if (!$transition_message->isNew()) {
      $form['transition']['workflow']['field']['#attributes'] = $disabled_attribute;
    }

    $workflow = $workflow_id ?? explode('%', $selectedField)[0];

    $this->states = WorkflowDiscovery::getStates($workflow);
    $form['transition']['workflow']['states'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'states-wrapper'],
    ];
    $form['transition']['workflow']['states']['workflow_from'] = [
      '#type' => 'select',
      '#title' => 'From State',
      '#options' => $this->states,
      '#required' => TRUE,
      '#default_value' => $transition_message->get("workflow_from"),
    ];
    $form['transition']['workflow']['states']['workflow_to'] = [
      '#type' => 'select',
      '#title' => 'To State',
      '#options' => $this->states,
      '#required' => TRUE,
      '#default_value' => $transition_message->get("workflow_to"),
    ];

    $form['message'] = [
      '#type' => 'fieldset',
      '#title' => 'Message Details',
    ];
    $form['message']['subject'] = [
      '#type' => 'textfield',
      '#title' => 'Subject',
      '#default_value' => $transition_message->get("subject"),
    ];
    $form['message']['body'] = [
      '#type' => 'textarea',
      '#title' => 'Message Body',
      '#default_value' => $transition_message->get('body'),
    ];
    $form['message']['replacements'] = [
      '#type' => 'details',
      '#title' => 'Replacements',
    ];
    $form['message']['replacements']['tokens'] = [
      '#type' => 'table',
      '#header' => ['token' => 'Token', 'description' => 'Description'],
    ];

    $tokens = _get_tokens();
    ksort($tokens);
    foreach ($tokens as $token => $description) {
      $form['message']['replacements']['tokens'][$token]['token'] = [
        '#plain_text' => $token,
      ];
      $form['message']['replacements']['tokens'][$token]['description'] = [
        '#plain_text' => $description,
      ];
    }

    $form['configuration'] = [
      '#type' => 'fieldset',
      '#title' => 'Message Configuration',
    ];

    $mailer_plugins = \Drupal::service('plugin.manager.mailer');
    $mailer_plugin_definitions = $mailer_plugins->getDefinitions();

    $recipient_options = [];
    foreach ($mailer_plugin_definitions as $plugin_id => $definition) {
      $recipient_options[$plugin_id] = $definition['label']->render();
    }
    asort($recipient_options);
    $form['configuration']['recipients'] = [
      '#type' => 'checkboxes',
      '#title' => 'Send To',
      '#description' => 'Select the users/groups to send the email to when this transition is launched. Author is the node author, dependant on role, and Coach is the team Coach.',
      '#options' => $recipient_options,
      '#default_value' => $transition_message->get('recipients') ?? [],
    ];

    $form['configuration']['cc'] = [
      '#type' => 'checkboxes',
      '#title' => 'CC',
      '#description' => 'Select the users/groups to send the email as CC. No custom data is supported for this.',
      '#options' => $recipient_options,
      '#default_value' => $transition_message->get('cc') ?? [],
    ];

    $form['configuration']['bcc'] = [
      '#type' => 'checkboxes',
      '#title' => 'BCC',
      '#description' => 'Select the users/groups to send the email as BCC. No custom data is supported for this.',
      '#options' => $recipient_options,
      '#default_value' => $transition_message->get('bcc') ?? [],
    ];

    $roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
    $roleOptions = [];
    foreach ($roles as $machineName => $role) {
      $roleOptions[$machineName] = $role->label();
    }
    $form['configuration']['node_author_role'] = [
      '#type' => 'checkboxes',
      '#title' => 'Author Role',
      '#description' => 'Select which roles will receive an email on this transition if the "author" checkbox is selected above.',
      '#options' => $roleOptions,
      '#default_value' => $transition_message->get('node_author_role') ?? [],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('workflow_from') == $form_state->getValue('workflow_to')) {
      $form_state->setErrorByName('workflow_from', "Please select different states.");
      $form_state->setErrorByName('workflow_to');
    }
    $empty_arr = [];
    if (MailParser::parse('body', $form_state->getValue('body')) === FALSE) {
      $form_state->setErrorByName('body', "Please confirm that your MailParse syntax is correct in the message body.");
    }
    if (MailParser::parse('subject', $form_state->getValue('subject')) === FALSE) {
      $form_state->setErrorByName('subject', "Please confirm that the MailParse syntax is correct in the subject line.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // For some reason, it's stored as content%field_name, so we need to extract
    // this information.
    $field = explode('%', $form_state->getValue('field'))[1];

    $transition_message = $this->entity;
    // Save the field appropriately.
    $transition_message->field = $field;

    // Keep only the machine names of the roles. This makes interaction with
    // the AuthorMailer easy and the configuration smaller.
    $transition_message->node_author_roles = array_keys(
      array_diff(
        $form_state->getValue('node_author_role'), [0]
      )
    );

    $status = $transition_message->save();
    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label transition message.', ['%label' => $transition_message->label()]));
    }
    else {
      $this->messenger()->addMessage($this->t('Could not save the %label transition message.', ['%label' => $transition_message->label()]), "error");
    }
    $form_state->setRedirect('entity.transition_message.collection');
  }

  /**
   * Checks if the entity already exists.
   *
   * @param string $id
   *   The id of the entity.
   */
  public function exists($id) {
    $entity = $this->entityTypeManager->getStorage('transition_message')->getQuery()
      ->condition('id', $id)
      ->execute();

    return (bool) $entity;
  }

  /**
   * Gets all workflow fields in the content type.
   *
   * @return string[]
   *   The section of the form that needs to be replaced.
   */
  public function getWorkflowFields(array &$form, FormStateInterface $form_state) {
    $this->workflows = WorkflowDiscovery::getWorkflows($form_state->getValue('content'));
    $form['transition']['workflow']['workflow']['#options'] = $this->workflows;
    $form_state->setValue('workflow', NULL);
    $this->getWorkflowStates($form, $form_state);
    return $form['transition']['workflow'];
  }

  /**
   * Retrieves the workflow states and inserts them into the form.
   *
   * @param array &$form
   *   The form being manipulated.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the form being changed.
   *
   * @return array
   *   The changed element of the render array.
   */
  public function getWorkflowStates(array &$form, FormStateInterface $form_state) {
    $pre = $form_state->getValue('field');
    // Extract the workflow type.
    $workflow = explode('%', $pre)[0];
    $form_state->setValue('workflow_to', 'creation');
    $form_state->setValue('workflow_from', 'creation');

    $this->workflow = $workflow;
    $this->states = WorkflowDiscovery::getStates($this->workflow);

    $form['transition']['workflow']['states']['workflow_from']['#options'] = $this->states;
    $form['transition']['workflow']['states']['workflow_to']['#options'] = $this->states;

    return $form['transition']['workflow']['states'];
  }

}
