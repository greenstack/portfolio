<?php

namespace Drupal\cfs_mail\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\cfs_mail\Parser\MailParser;

/**
 * A simple form to test mailparse messages.
 */
class ParseTester extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['test_area'] = [
      '#type' => 'textarea',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Parse',
      '#button_type' => 'primary',
    ];
    $session = \Drupal::request()->getSession();
    $results = $session->get('results');
    $form['results'] = [
      '#type' => 'textarea',
      '#default_value' => empty($results) ? 'pie' : $results,
      '#attributes' => ['readonly' => 'readonly'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $result = MailParser::parse($form_state->getValue('test_area'));
    $this->messenger()->addMessage(print_r($result, 1));

    $session = \Drupal::request()->getSession();
    $session->set('results', var_export($result, 1));
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'test_parsing_form';
  }

}
