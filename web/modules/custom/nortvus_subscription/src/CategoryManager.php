<?php

namespace Drupal\nortvus_subscription;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class CategoryManager.
 */
class CategoryManager implements TaxonomyManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new \Drupal\nortvus_subscription\CategoryManager object.
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
  public function getAllTerms($vid): array {
    /** @var \Drupal\taxonomy\TermStorageInterface $taxonomy_term_storage */
    $taxonomy_term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $terms = $taxonomy_term_storage->loadByProperties(['vid' => $vid]);

    return $terms;
  }

}
