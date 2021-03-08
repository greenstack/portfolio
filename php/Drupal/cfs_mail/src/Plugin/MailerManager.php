<?php

namespace Drupal\cfs_mail\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a Mailer plugin manager.
 *
 * @see \Drupal\cfs_mail\Annotation\Mailer
 * @see \Drupal\cfs_mail\MailerInterface
 * @see plugin_api
 */
class MailerManager extends DefaultPluginManager {
  /**
   * The class for the mailer.
   *
   * @var array
   */
  private $mailerClass = [];

  /**
   * Constructs a MailerManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/Mailer',
      $namespaces,
      $module_handler,
      'Drupal\cfs_mail\Plugin\MailerInterface',
      'Drupal\cfs_mail\Annotation\Mailer'
    );
    $this->alterInfo('cfs_mail_mailer_info');
    $this->setCacheBackend($cache_backend, 'cfs_mail_mailer_info_plugins');
  }

}
