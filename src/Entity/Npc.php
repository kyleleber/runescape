<?php

namespace Drupal\runescape\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\media\Entity\Media;

/**
 * Defines the npc entity.
 *
 * @ingroup runescape
 *
 * @ConfigEntityType(
 *   id = "npc",
 *   label = @Translation("Npc"),
 *   admin_permission = "administer npcs",
 *   handlers = {
 *     "access" = "Drupal\runescape\NpcAccessController",
 *     "list_builder" = "Drupal\runescape\Controller\NpcListBuilder",
 *     "form" = {
 *       "add" = "Drupal\runescape\Form\NpcAddForm",
 *       "edit" = "Drupal\runescape\Form\NpcEditForm",
 *       "delete" = "Drupal\runescape\Form\NpcDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/runescape/npcs/manage/{npc}",
 *     "delete-form" = "/runescape/npcs/manage/{npc}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "npc_id",
 *     "npc_description",
 *     "uuid",
 *     "label",
 *     "items"
 *   }
 * )
 */
class Npc extends ConfigEntityBase {

  /**
   * The npc ID.
   *
   * @var string
   */
  public $id;

  /**
   * The id of the Npc.
   *
   * @var string
   */
  public $npc_id;

  /**
   * The description of the Npc.
   *
   * @var string
   */
  public $npc_description;

  /**
   * The npc UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The npc label.
   *
   * @var string
   */
  public $label;

  /**
   * @var Media
   *   The image of the npc.
   */
  public $image;

  /*
   * get npc image.
   *
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Get the Npc Id.
   *
   * @return string
   *   The id of the npc.
   */
  public function getNpcId() {
    return $this->npc_id;
  }

  /**
   * Get the Npc description.
   *
   * @return string
   */
  public function getNpcDescription() {
    return $this->npc_description;
  }

}
