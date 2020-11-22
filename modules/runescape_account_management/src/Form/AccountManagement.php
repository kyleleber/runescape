<?php

namespace Drupal\runescape_account_management\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use PDO;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountManagement extends FormBase {

  /**
   * The runescape account manager service.
   *
   * @var \Drupal\runescape_account_management\AccountManagerInterface
   */
  protected $accountManager;

  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->accountManager = $container->get('runescape_account_management.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'runescape_account_management_account_management';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $existing_accounts = $this->accountManager->getExistingAccounts($this->currentUser()->id());
    $form['account_creation']['existing_accounts'] = [
      '#type' => 'details',
      '#title' => $this->t('Current Accounts (%c)', ['%c' => count($existing_accounts)]),
      'accounts' => [
        '#theme' => 'account_cards',
        '#items' => $existing_accounts,
      ],
      '#open' => TRUE,
    ];
    $form['account_creation']['new_account'] = [
      '#type' => 'details',
      '#title' => 'Create new account',
    ];
    $form['account_creation']['new_account']['username'] = [
      '#type' => 'textfield',
      '#title' => 'Username',
      '#attributes' => [
        'maxlength' => 12,
      ],
    ];
    $form['account_creation']['new_account']['email'] = [
      '#type' => 'email',
      '#title' => 'Email',
    ];
    $form['account_creation']['new_account']['password'] = [
      '#type' => 'password',
      '#title' => 'Password',
      '#maxlength' => 64,
    ];

    $form['account_creation']['new_account']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Create Account',

    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getErrors())) {
      $data['username'] = $form_state->getValue('username');
      $data['password'] = $form_state->getValue('password');
      $data['email'] = $form_state->getValue('email');
      $data['forum_account'] = $this->currentUser()->id();
      if ($this->accountManager->createInGameAccount($data)) {
        $this->messenger()->addMessage("Your account has been successfully created! You can now log in.");
      }
    }
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $username = $form_state->getValue('username');
    $password = $form_state->getValue('password');
    $account_count = count($this->accountManager->getExistingAccounts($this->currentUser()->id()));

    if ($account_count < 10) {
      // validate username
      if (empty($username) || strlen($username) >= 2 && strlen($username) <= 12) {
        if (preg_match('/^[a-zA-Z0-9 ]+$/', $username)) {
          // check if username already taken.
          if ($this->accountManager->isUsernameTaken($username)) {
            $form_state->setErrorByName('username', 'Username is already taken. Choose a different one.');
          }
        }
        else {
          $form_state->setErrorByName('username', 'Username can only contain letters, numbers, and spaces.');

        }
      }
      else {
        $form_state->setErrorByName('username', 'Username must be between 2 and 12 characters.');
      }

      //validate password
      if (strlen($password) >= 4 && strlen($password) <= 64) {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
          $form_state->setErrorByName('password', 'Password may contain only numbers and letters. No special characters.');
        }

      }
      else {
        $form_state->setErrorByName('password', 'Password must be between 4 and 64 characters.');
      }
    }
    else {
      $form_state->setErrorByName("new_account", "You are only allowed 5 characters. You have $account_count accounts. ");
    }
  }
}

