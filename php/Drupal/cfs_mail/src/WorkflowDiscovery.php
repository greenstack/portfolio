<?php

namespace Drupal\cfs_mail;

use Drupal\workflow\Entity\Workflow;

/**
 * Provides a bunch of helper methods to discover workflow entities.
 */
class WorkflowDiscovery {

  /**
   * Gets the id of the workflow from the state id.
   *
   * @param string $state_id
   *   The id (name) of the workflow state.
   *
   * @return string
   *   The name of the workflow state.
   */
  public static function findWorkflowFromState($state_id) {
    // Workflow states always name themselves [workflow]_[state].
    return explode('_', $state)[0];
  }

  /**
   * Retrieves all content types with workflow fields.
   *
   * @return string[]
   *   An array with machine names of all content types with workflow fields.
   */
  public static function getContentTypes() {
    $nodeTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $bundles = [];
    foreach ($nodeTypes as $machineName => $nodeType) {
      $workflows = self::getWorkflows($machineName);
      if (!empty($workflows)) {
        $bundles[$machineName] = $nodeType->label();
      }
    }
    return $bundles;
  }

  /**
   * Retrieves all the workflows available for the content type.
   *
   * @param string $bundle
   *   The name of the content type to load the workflows for.
   *
   * @return string[]
   *   An array of workflows, keyed by machine name.
   */
  public static function getWorkflows($bundle) {
    if ($bundle === NULL) {
      return [];
    }
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $bundle);
    $workflows = [];
    foreach ($field_definitions as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        if ($field_definition->getType() == 'workflow') {
          $key = $field_definition->getSettings()['workflow_type'] . "%" . $field_name;
          $workflows[$key] = $field_definition->getLabel();
        }
      }
    }
    return $workflows;
  }

  /**
   * Gets the states of the given workflow.
   *
   * @param string $workflowName
   *   The machine name of the workflow to load.
   *
   * @return string[]
   *   An array of names of the workflow states, keyed by machine name.
   */
  public static function getStates($workflowName) {
    if (empty($workflowName)) {
      return [];
    }

    $workflow = Workflow::load($workflowName);
    $states = $workflow->getStates('CREATION');
    $available = [];
    foreach ($states as $machineName => $state) {
      $available[$machineName] = $state->label();
    }

    return $available;
  }

}
