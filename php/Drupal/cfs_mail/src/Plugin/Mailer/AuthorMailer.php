<?php

namespace Drupal\cfs_mail\Plugin\Mailer;

use Drupal\cfs_mail\Plugin\MailerBase;

/**
 * A mailer that sends mail to the node's author, as long as conditions are met.
 *
 * @Mailer(
 *   id = "author",
 *   label = @Translation("Author"),
 *   description = @Translation("Sends the email to the author."),
 * )
 */
class AuthorMailer extends MailerBase {

  /**
   * {@inheritdoc}
   */
  protected function shouldSend(string $recipient_type) {
    $transition_message = $this->transition_message;
    $sendToAuthor = !empty(array_intersect($transition_message->get('node_author_role'), $this->params['owner']->getRoles()));
    return parent::shouldSend($recipient_type) && $sendToAuthor;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareMail() {}

  /**
   * {@inheritdoc}
   */
  protected function getAddress() {
    return $this->params['owner']->getEmail();
  }

}
