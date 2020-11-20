<?php

namespace Drupal\runescape\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ItemEditForm.
 *
 * Provides the edit form for our Item entity.
 *
 * @ingroup runescape
 */
class ItemEditForm extends ItemFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Update Item');
    return $actions;
  }

}
