<?php

namespace Drupal\cfs_mail\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a cfs mailer annotation object.
 *
 * @see \Drupal\cfs_mail\Plugin\MailerManager
 * @see plugin_api
 *
 * @Annotation
 */
class Mailer extends Plugin {
  /**
   * The plugin id.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the mailer type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * A short description of the mailer type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The name of the mailer class.
   *
   * This is not provided manually, and is added by the discovery mechanism.
   *
   * @var string
   */
  public $class;

}
