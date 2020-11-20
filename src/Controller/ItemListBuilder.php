<?php

namespace Drupal\runescape\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of item entities.
 *
 * @ingroup runescape
 */
class ItemListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'runescape';
  }

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['label'] = $this->t('Item');
    $header['item_id'] = $this->t('Item Id');
    $header['description'] = $this->t('Item Description');
    $header['machine_name'] = $this->t('Machine Name');

    return $header + parent::buildHeader();
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to build the row.
   *
   * @return array
   *   A render array of the table row for displaying the entity.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['item_id'] = $entity->getItemId();
    $row['description'] = $entity->getItemDescription();
    $row['machine_name'] = $entity->id();

    return $row + parent::buildRow($entity);
  }

}
