<?php

namespace Drupal\cfs_mail\Commands;

use Drupal\cfs_mail\Form\CFSSettingsForm;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class CfsMailCommands extends DrushCommands {

  /**
   * Stops CFS Mail from sending emails without uninstalling.
   *
   * @validate-module-enabled cfs_mail
   *
   * @command cfs_mail:disable
   * @aliases cfsmd,cfs_mail_disable
   */
  public function cfsMailDisable() {
    $this->setDisable(TRUE);
    $this->output()->writeln("Disabled sending mail through CFS Mail.");
  }

  /**
   * Causes CFS Mail to resume sending emails.
   *
   * @validate-module-enabled cfs_mail
   *
   * @command cfs_mail:enable
   * @aliases cfsme,cfs_mail_enable
   */
  public function cfsMailEnable() {
    $this->setDisable(FALSE);
    $this->logger()->info("Enabled sending mail.");
    $this->output()->writeln("Enabled sending mail through CFS Mail.");
  }

  /**
   * Sets the module's configuration for disabling the mailers.
   *
   * @param bool $disabled
   *   Whether or not CFS Mail should send out emails or not.
   */
  private function setDisable(bool $disabled) {
    $cName = CFSSettingsForm::SETTINGS;
    \Drupal::configFactory()->getEditable($cName)
      ->set('disabled', $disabled)
      ->save();
  }

}
