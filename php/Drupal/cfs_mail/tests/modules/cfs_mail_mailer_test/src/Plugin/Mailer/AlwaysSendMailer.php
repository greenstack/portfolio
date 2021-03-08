<?php

namespace Drupal\cfs_mail_mailer_test\Plugin\Mailer;

use Drupal\cfs_mail\Plugin\MailerBase;

/**
 * A mailer that always sends mail properly.
 *
 * @Mailer(
 *   id = "test_always_send",
 *   label = @Translation("Test Always Send"),
 *   description = @Translation("Always sends an email")
 * )
 */
class AlwaysSendMailer extends MailerBase {

  /**
   * {@inheritdoc}
   */
  protected function shouldSend(string $recipient_type) {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareMail() {}

  /**
   * {@inheritdoc}
   */
  protected function getAddress() {
    return "no-reply@example.com";
  }

}
