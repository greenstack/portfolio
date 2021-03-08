<?php

namespace Drupal\cfs_mail\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This form defines the site-wide settings for CFS Mail.
 */
class CFSSettingsForm extends ConfigFormBase {
  /**
   * Configuration settings.
   *
   * @var string
   */
  const SETTINGS = 'cfs_mail.settings';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['from_address'] = [
      '#type' => 'email',
      '#title' => $this->t('"From" email address'),
      '#description' => $this->t("The address that the emails will be sent from."),
      '#default_value' => $config->get('from_address'),
      '#required' => TRUE,
    ];

    $form['disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Disable emailing"),
      '#description' => $this->t("Prevents mail from being sent. Useful for development sites or when you want to stop emails without uninstalling."),
      '#default_value' => $config->get('disabled'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('from_address', $form_state->getValue('from_address'))
      ->set('disabled', $form_state->getValue('disabled'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfs_mail_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

}
