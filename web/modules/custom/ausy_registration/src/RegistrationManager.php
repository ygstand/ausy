<?php

namespace Drupal\ausy_registration;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class RegistrationManager.
 */
class RegistrationManager implements NodeManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new \Drupal\ausy_registration\RegistrationManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodesCount($type): int {
    /** @var \Drupal\node\NodeStorageInterface $node_storage */
    $node_storage = $this->entityTypeManager->getStorage('node');
    $nodes = $node_storage->loadByProperties(['type' => $type]);
    $result = count($nodes);

    return $result;
  }

}
