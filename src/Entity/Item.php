<?php

namespace Drupal\runescape\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\media\Entity\Media;

/**
 * Defines the item entity.
 *
 * @ingroup runescape
 *
 * @ConfigEntityType(
 *   id = "item",
 *   label = @Translation("Item"),
 *   admin_permission = "administer items",
 *   handlers = {
 *     "access" = "Drupal\runescape\ItemAccessController",
 *     "list_builder" = "Drupal\runescape\Controller\ItemListBuilder",
 *     "form" = {
 *       "add" = "Drupal\runescape\Form\ItemAddForm",
 *       "edit" = "Drupal\runescape\Form\ItemEditForm",
 *       "delete" = "Drupal\runescape\Form\ItemDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/runescape/items/manage/{items}",
 *     "delete-form" = "/runescape/items/manage/{items}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "item_id",
 *     "item_description",
 *     "uuid",
 *     "label",
 *   }
 * )
 */
class Item extends ConfigEntityBase {

  /**
   * The item ID.
   *
   * @var string
   */
  public $id;

  /**
   * The game item id.
   *
   * @var string
   */
  public $item_id;

  /**
   * The game item description.
   *
   * @var string
   */
  public $item_description;

  /**
   * The item UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The item label.
   *
   * @var string
   */
  public $label;

  /**
   * @var Media
   *   The media of the item.
   */
  public $image;

  /**
   * Get the item image.
   *
   * @return Media
   *   The image/media entity of the item.
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Get the item id.
   *
   * @return int
   *   The id of the item.
   */
  public function getItemId() {
    return $this->item_id;
  }

  /**
   * Get the item description.
   *
   * @return string
   *   The description of the item.
   */
  public function getItemDescription() {
    return $this->item_description;
  }

}
