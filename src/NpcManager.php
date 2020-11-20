<?php

namespace Drupal\runescape;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Static service container wrapper for npcs.
 */
class NpcManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Create an NpcManager.
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
    return $this->entityTypeManager->getStorage('npc')->loadMultiple();
  }

  /**
   * {@inheritdoc}
   */
  public function loadByProperties(array $properties) {
    return $this->entityTypeManager->getStorage('npc')->loadByProperties($properties);
  }

}
