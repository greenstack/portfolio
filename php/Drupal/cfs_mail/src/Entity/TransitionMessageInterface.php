<?php

namespace Drupal\cfs_mail\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a TransitionHandler entity.
 */
interface TransitionMessageInterface extends ConfigEntityInterface {

  /**
   * Determines if the TransitionMessage matches the workflow transition.
   *
   * @param string $field
   *   The field the workflow lives in.
   * @param string $workflow_from
   *   The state that the workflow is coming from.
   * @param string $workflow_to
   *   The state that the workflow is transitioning to.
   *
   * @return bool
   *   TRUE if all three items match, otherwise, boolean FALSE.
   */
  public function matchesTransition(string $field, $workflow_from, $workflow_to);

  /**
   * Sends the emails to all the recipients as defined in the transition.
   *
   * @param array $params
   *   All of the parameters that will be used by this message.
   * @param array $mailer_plugins
   *   The mailers that are responsible for sending the messages.
   */
  public function sendMessages(array &$params, array $mailer_plugins);

}
