<?php

namespace Drupal\runescape_server_integration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServerIntegrationSettings.
 */
class ServerIntegrationSettings extends ConfigFormBase {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegration;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'server_integration.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'server_integration_settings';
  }

  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->serverIntegration = $container->get('runescape_server_integration.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('server_integration.settings');
    $form['external_database_connections'] = [
      '#type' => 'details',
      '#title' => $this->t('External Database Connections'),
      '#open' => TRUE,
    ];
    $form['external_database_connections']['external_database_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('External database name'),
      '#description' => $this->t('The external database name'),
      '#default_value' => $config->get('external_database_name') ?: '',
      '#required' => TRUE,
    ];
    $form['external_database_connections']['external_database_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database username'),
      '#description' => $this->t('The external database username'),
      '#default_value' => $config->get('external_database_username') ?: '',
      '#required' => TRUE,
    ];
    $form['external_database_connections']['external_database_password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database password'),
      '#description' => $this->t('The external database password'),
      '#default_value' => $config->get('external_database_password') ?: '',
      '#required' => TRUE,
    ];
    $form['external_database_connections']['external_database_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database host'),
      '#description' => $this->t('The external database host'),
      '#default_value' => $config->get('external_database_host') ?: '',
      '#required' => TRUE,
    ];
    $form['external_database_connections']['external_database_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database prefix'),
      '#description' => $this->t('The external database prefix'),
      '#default_value' => $config->get('external_database_prefix') ?: '',
    ];
    $form['external_database_connections']['external_database_port'] = [
      '#type' => 'number',
      '#title' => $this->t('Database port'),
      '#description' => $this->t('The external database port'),
      '#default_value' => $config->get('external_database_port') ?: '',
      '#required' => TRUE,
    ];
    $form['external_database_connections']['external_database_driver'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Database driver'),
      '#description' => $this->t('The external database driver'),
      '#default_value' => $config->get('external_database_driver') ?: '',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('server_integration.settings')
      ->set('external_database_name', $form_state->getValue('external_database_name'))
      ->set('external_database_username', $form_state->getValue('external_database_username'))
      ->set('external_database_password', $form_state->getValue('external_database_password'))
      ->set('external_database_host', $form_state->getValue('external_database_host'))
      ->set('external_database_prefix', $form_state->getValue('external_database_prefix'))
      ->set('external_database_port', $form_state->getValue('external_database_port'))
      ->set('external_database_driver', $form_state->getValue('external_database_driver'))
      ->save();
  }

}
