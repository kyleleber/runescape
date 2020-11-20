<?php

namespace Drupal\runescape\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ItemAddForm.
 *
 * Provides the add form for our Item entity.
 *
 * @ingroup runescape
 */
class ItemAddForm extends ItemFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Create Item');
    return $actions;
  }

}
