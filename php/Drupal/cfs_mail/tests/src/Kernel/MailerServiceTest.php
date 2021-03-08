<?php

namespace Drupal\Tests\cfs_mail\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the MailerService.
 *
 * @group cfs_mail
 * @group capstone_suite
 */
class MailerServiceTest extends KernelTestBase {
  /**
   * The service under test.
   *
   * @var \Drupal\cfs_mail\MailerInterface
   */
  protected $mailers;

  /**
   * The modules to load to run the test.
   *
   * @var array
   */
  public static $modules = [
    'cfs_mail',
    'cfs_mail_mailer_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // $this->installEntitySchema(['cfs_mail']);
    $mailerService = \Drupal::service('plugin.manager.mailer');
    $this->mailers = $mailerService->getDefinitions();
  }

  /**
   * Ensures that mailers are found properly.
   */
  public function testMailerFinding() {
    // Make sure we found the mailer from the test module (always)
    $this->assertArrayHasKey('test_always_send', $this->mailers);
    // Make sure we found the mailer from the main module (author)
    $this->assertArrayHasKey('author', $this->mailers);
  }

}
