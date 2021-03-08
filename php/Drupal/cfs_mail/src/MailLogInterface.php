<?php

namespace Drupal\cfs_mail;

use Psr\Log\LoggerInterface;

/**
 * Defines an interface for creating a Mail Log.
 */
interface MailLogInterface extends LoggerInterface {

  /**
   * Terminates the logging sequence, sending the messages to Drupal's log.
   */
  public function end();

  /**
   * Removes all messages from the logger.
   */
  public function flush();

  /**
   * Flushes the log and enables it for logging if end has been called.
   */
  public function reset();

  /**
   * Gets the number of log messages in the queue.
   *
   * @return int
   *   The number of messages in the queue.
   */
  public function count();

  /**
   * Gets whether or not end() has been called on this logger and not reset.
   *
   * @return bool
   *   Whether or not the logger will accept any more messages.
   */
  public function ended();

}
