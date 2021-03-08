<?php

/**
 * @file
 * Contains the code necessary to generate CFS Mail-related drush commands.
 */

use Drupal\cfs_mail\Form\CFSSettingsForm;

/**
 * Implements hook_drush_command().
 */
function cfs_mail_drush_command() {
  $items = [];
  $items['cfs_mail_disable'] = [
    'description' => 'Stops CFS Mail from sending emails without uninstalling.',
    'drupal dependencies' => ['cfs_mail'],
    'aliases' => ['cfsmd'],
  ];
  $items['cfs_mail_enable'] = [
    'description' => 'Causes CFS Mail to resume sending emails.',
    'drupal dependencies' => ['cfs_mail'],
    'aliases' => ['cfsme'],
  ];
  return $items;
}

/**
 * Causes CFS Mail to stop sending emails without destroying config.
 */
function drush_cfs_mail_disable() {
  set_disable(TRUE);
  drush_print("Disabled CFS Mail messages. To re-enable, run drush cfsme.");
}

/**
 * Causes CFS Mail to start sending emails.
 */
function drush_cfs_mail_enable() {
  set_disable(FALSE);
  drush_print("Enabled CFS Mail messages. To disable, run drush cfsmd.");
}

/**
 * Sets the module's configuration for disabling the mailers.
 *
 * @param bool $disabled
 *   Whether or not CFS Mail should send out emails or not.
 */
function set_disable($disabled) {
  $cName = CFSSettingsForm::SETTINGS;
  \Drupal::configFactory()->getEditable($cName)
    ->set('disabled', $disabled)
    ->save();
}
