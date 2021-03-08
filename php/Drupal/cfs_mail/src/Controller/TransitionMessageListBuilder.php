<?php

namespace Drupal\cfs_mail\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Controller to display the available transition messages.
 */
class TransitionMessageListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'label' => $this->t("Transition Message"),
      'id' => $this->t("Machine name"),
      'node_type' => $this->t("Node bundle"),
      'field' => $this->t("Workflow"),
      'transition' => $this->t("Transition"),
      'subject' => $this->t("Subject"),
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    $content_type = $entity->get('node_type');

    $row = [
      'label' => $entity->label(),
      'id' => $entity->id(),
      'node_type' => $content_type,
      'field' => $entity->get('field'),
      'transition' => $this->t("From %from to %to", [
        '%from' => ucwords(str_replace([$content_type . "_", '_'], ['', ' '], $entity->get('workflow_from'))),
        '%to' => ucwords(str_replace([$content_type . "_", '_'], ['', ' '], $entity->get('workflow_to'))),
      ]
      ),
      'subject' => $entity->get('subject'),
    ];
    return $row + parent::buildRow($entity);
  }

}
