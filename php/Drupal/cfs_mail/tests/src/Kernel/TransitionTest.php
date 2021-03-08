<?php

namespace Drupal\Tests\cfs_mail\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests Transition entities.
 *
 * @group cfs_mail
 * @group capstone_suite
 */
class TransitionTest extends KernelTestBase {
  /**
   * The modules required for this test.
   *
   * @var array
   */
  public static $modules = [
    'cfs_mail',
    'cfs_mail_mailer_test',
    'node',
    'workflow',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('transition_message');
    $this->installEntitySchema('node');
    $this->installEntitySchema('workflow');
    $this->installEntitySchema('workflow_state');
  }

}
