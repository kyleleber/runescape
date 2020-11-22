<?php

namespace Drupal\runescape_account_management\Form;

use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserMigrationManagement extends FormBase {

  /**
   * The runescape account manager service.
   *
   * @var \Drupal\runescape_account_management\AccountManagerInterface
   */
  protected $accountManager;

  /**
   * The runescape account migration manager service.
   *
   * @var \Drupal\runescape_account_management\AccountMigrationManagerInterface
   */
  protected $migrationManager;

  /**
   * The runescape manager service.
   *
   * @var \Drupal\runescape\RunescapeManagerInterface
   */
  protected $runescapeManager;

  /**
   * The item manager service.
   *
   * @var \Drupal\runescape\ItemManager
   */
  protected $itemManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->accountManager = $container->get('runescape_account_management.manager');
    $instance->migrationManager = $container->get('runescape_account_management.migration_manager');
    $instance->itemManager = $container->get('runescape.item_manager');
    $instance->runescapeManager = $container->get('runescape.manager');
    $instance->entityTypeManager = $container->get('entity_type.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'runescape_account_management_migrate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['user_data_source'] = [
      '#type' => 'select',
      '#options' => [
        'rscvanilla' => $this->t('RSCVanilla'),
        'rscemulation' => $this->t('RSCEmulation'),
        'rsclegacy' => $this->t('RSCLegacy'),
      ],
      '#title' => $this->t('Select the source of the user'),
      '#description' => $this->t('This is the source from which the user will be ported from'),
    ];

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => $this->t('This is the username to lookup'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::user_migration_management_lookup_account',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Looking up user from external source...'),
        ],
        'wrapper' => 'user-account-data'
      ]
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('This is the email to use when creating the account.'),
      '#required' => TRUE,
    ];

    $users = $this->entityTypeManager->getStorage('user')->loadByProperties(['status' => 1]);
    $forum_accounts = [];
    foreach ($users as $user) {
      $forum_accounts[$user->id()] = $user->label();
    }
    $form['forum_account'] = [
      '#type' => 'select',
      '#title' => $this->t('Forum Account'),
      '#options' => $forum_accounts,
      '#description' => $this->t('This is the forum account to relate the in-game account to.'),
      '#required' => TRUE,
    ];

    $form['account_data']['stats'] = [
      '#type' => 'details',
      '#title' => $this->t('Account Stats'),
      '#attributes' => [
        'id' => 'user-account-data',
      ],
    ];

    foreach ($this->runescapeManager->getAvailableSkills() as $id => $skill) {
      $form['account_data']['stats'][$id] = [
        '#type' => 'textfield',
        '#title' => $skill,
      ];
    }

    $form['account_data']['bank'] = [
      '#type' => 'details',
      '#title' => $this->t('Bank Items'),
      '#attributes' => [
        'id' => 'account-data-bank',
      ],
    ];
    $form['account_data']['bank']['items'] = [
      '#type' => 'table',
      '#title' => $this->t('Bank Items'),
      '#header' => array('Item Name', 'Quantity'),
    ];

    $num_names = $form_state
      ->get('num_names');

    // We have to ensure that there is at least one name field.
    if ($num_names === NULL) {
      $name_field = $form_state
        ->set('num_names', 1);
      $num_names = 1;
    }
    $form['#tree'] = TRUE;
    for ($i = 0; $i < $num_names; $i++) {
      $form['account_data']['bank']['items'][$i] = [
        'id' => [
          '#type' => 'entity_autocomplete',
          '#title' => t('Name'),
          '#title_display' => 'invisible',
          '#target_type' => 'item',
          '#attributes' => [
            'placeholder' => $this->t('Start typing your item\'s name'),
          ],
        ],
        'quantity' => [
          '#type' => 'textfield',
          '#title' => t('Name'),
          '#title_display' => 'invisible',
          '#attributes' => [
            'placeholder' => $this->t('Enter the number of items.'),
          ],
        ],
      ];
    }
    $form['account_data']['bank']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add another item'),
      '#submit' => [
        '::addOne',
      ],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'account-data-bank',
      ],
      '#limit_validation_errors' => TRUE,
    ];

    if ($num_names > 1) {
      $form['account_data']['bank']['remove_name'] = [
        '#type' => 'submit',
        '#value' => $this
          ->t('Remove item'),
        '#submit' => [
          '::removeCallback',
        ],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'account-data-bank',
        ],
        '#limit_validation_errors' => TRUE
      ];
    }


    $form['migrate_account'] = [
      '#type' => 'submit',
      '#value' => $this->t('Migrate Account'),
    ];

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $form['account_data']['bank']['#open'] = TRUE;
    return $form['account_data']['bank'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state
      ->get('num_names');
    $add_button = $name_field + 1;
    $form_state
      ->set('num_names', $add_button);

    // Since our buildForm() method relies on the value of 'num_names' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state
      ->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state
      ->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state
        ->set('num_names', $remove_button);
    }
    $form_state
      ->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if (empty($form_state->getErrors())) {
      $data['username'] = $form_state->getValue('username');
      $data['password'] = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
      $data['email'] = $form_state->getValue('email');
      $data['forum_account'] = $form_state->getValue('forum_account');
      $data['experience']['attack'] = $form_state->getValues()['account_data']['stats']['attack'];
      $data['experience']['defense'] = $form_state->getValues()['account_data']['stats']['defense'];
      $data['experience']['strength'] = $form_state->getValues()['account_data']['stats']['strength'];
      $data['experience']['hits'] = $form_state->getValues()['account_data']['stats']['hits'];
      $data['experience']['ranged'] = $form_state->getValues()['account_data']['stats']['ranged'];
      $data['experience']['prayer'] = $form_state->getValues()['account_data']['stats']['prayer'];
      $data['experience']['magic'] = $form_state->getValues()['account_data']['stats']['magic'];
      $data['experience']['cooking'] = $form_state->getValues()['account_data']['stats']['cooking'];
      $data['experience']['woodcut'] = $form_state->getValues()['account_data']['stats']['woodcut'];
      $data['experience']['fletching'] = $form_state->getValues()['account_data']['stats']['fletching'];
      $data['experience']['fishing'] = $form_state->getValues()['account_data']['stats']['fishing'];
      $data['experience']['firemaking'] = $form_state->getValues()['account_data']['stats']['firemaking'];
      $data['experience']['crafting'] = $form_state->getValues()['account_data']['stats']['crafting'];
      $data['experience']['smithing'] = $form_state->getValues()['account_data']['stats']['smithing'];
      $data['experience']['mining'] = $form_state->getValues()['account_data']['stats']['mining'];
      $data['experience']['herblaw'] = $form_state->getValues()['account_data']['stats']['herblaw'];
      $data['experience']['agility'] = $form_state->getValues()['account_data']['stats']['agility'];
      $data['experience']['thieving'] = $form_state->getValues()['account_data']['stats']['thieving'];

      foreach ($form_state->getValues()['account_data']['bank']['items'] as $id => $item) {
        $item_entity = array_shift(\Drupal::service('runescape.item_manager')->loadByProperties(['id' => $item['id']]));
        if (!empty($item_entity) && !empty($item['quantity'])) {
          $data['bank_items'][$id]['id'] = $item_entity->item_id;
          $data['bank_items'][$id]['quantity'] = $item['quantity'];
        }
      }

      if ($this->accountManager->createInGameAccount($data)) {
        $this->messenger()->addMessage($this->t("The account has been created. Copy this password: <strong>%s</strong> and send to the user who you migrated.", ['%s' => $data['password']]));
      }
    }
  }

  public function user_migration_management_lookup_account(&$form, FormStateInterface $form_state) {
    $form['account_data']['stats']['#open'] = TRUE;
    $data_source = $form_state->getValue('user_data_source');
    $account_data = $this->migrationManager->getExternalUserData($form_state->getValue('username'), $data_source);

    if ($account_data) {
      $this->messenger()->addMessage("The account has been found.");
      foreach ($account_data as $record) {
        $form['account_data']['stats'][$record[3]]['#value'] = $record[1];
      }
    }
    else {
      $this->messenger()->addWarning("The account could not be parsed. Please check and try again later, or manually enter the values.");
    }

    return $form['account_data']['stats'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $username = $form_state->getValue('username');

    if (!$username) {
      $form_state->setErrorByName('username', 'You must provide a username to lookup');
    }
  }

}

