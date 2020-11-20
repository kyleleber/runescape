<?php

namespace Drupal\runescape\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class NpcEditForm.
 *
 * Provides the edit form for the Npc entity.
 *
 * @ingroup runescape
 */
class NpcEditForm extends NpcFormBase {

  /**
   * {inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Update Npc');
    return $actions;
  }

}
