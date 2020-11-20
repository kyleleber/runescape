<?php

namespace Drupal\runescape;

use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Static service container wrapper for Items.
 */
class ItemManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Create an ItemManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple() {
    return $this->entityTypeManager->getStorage('item')->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public function loadByProperties(array $properties) {
    return $this->entityTypeManager->getStorage('item')->loadByProperties($properties);
  }

}
