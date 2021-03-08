<?php

namespace Drupal\cfs_mail\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for MailerPlugin objects.
 */
interface MailerInterface extends PluginInspectionInterface {

  /**
   * Sends the mail according to the provided parameters.
   */
  public function sendMail();

}
