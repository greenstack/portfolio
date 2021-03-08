<?php

namespace Drupal\cfs_mail;

use Psr\Log\LogLevel;

/**
 * Helps keep track of what's happening through the mailing process in CFS Mail.
 */
class MailLogBase implements MailLogInterface {
  /**
   * The queue of messages.
   *
   * @var mixed[]
   */
  private $messages;

  /**
   * If end has been called on this logger and it hasn't been reset.
   *
   * @var bool
   *
   * @see MailLogInterface::ended().
   */
  private $hasEnded;

  /**
   * Creates an instance of a MailLogBase.
   */
  public function __construct() {
    $this->messages = [];
    $this->hasEnded = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function alert($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::ALERT, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function critical($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::CRITICAL, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function debug($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::DEBUG, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function emergency($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::EMERGENCY, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function error($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::ERROR, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function info($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::INFO, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = []) {
    $this->pushTypedMessage($level, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function notice($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::NOTICE, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function warning($message, array $context = []) {
    $this->pushTypedMessage(LogLevel::WARNING, $message, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function end() {
    $logger = \Drupal::logger('cfs_mail');
    foreach ($this->messages as $key => $messageData) {
      $type = $messageData['type'];
      $message = $messageData['message'];
      // Thank goodness for PHP's reflection system - no switch statements
      // needed here!
      $logger->$type($message, $messageData['context']);
    }

    $this->hasEnded = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function flush() {
    $this->messages = [];
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->flush();
    $this->hasEnded = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    return count($this->messages);
  }

  /**
   * {@inheritdoc}
   */
  public function ended() {
    return $this->hasEnded;
  }

  /**
   * Pushes a message into the logging queue.
   *
   * @param string $type
   *   The type of message to be logged.
   * @param string $message
   *   The message to be logged.
   * @param array $context
   *   The context of the message.
   */
  private function pushTypedMessage(string $type, $message, array $context) {
    if ($this->ended()) {
      return;
    }
    $this->messages[] = [
      'type' => $type,
      'message' => $message,
      'context' => $context,
    ];
  }

}
