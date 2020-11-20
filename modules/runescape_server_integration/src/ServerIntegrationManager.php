<?php

namespace Drupal\runescape_server_integration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Exception;

/**
 * Class ServerIntegrationManager.
 */
class ServerIntegrationManager implements ServerIntegrationManagerInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a new Runescape Account Manager.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The runescape manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function setExternalDatabaseConnection(): void {
    $server_configuration = \Drupal::config('server_integration.settings');
    $database = [
      'database' => $server_configuration->get('external_database_name'),
      'username' => $server_configuration->get('external_database_username'),
      'password' => $server_configuration->get('external_database_password'),
      'host' => $server_configuration->get('external_database_host'),
      'port' => $server_configuration->get('external_database_port'),
      'prefix' => $server_configuration->get('external_database_prefix'),
      'namespace' => 'Drupal\Core\Database\Driver\mysql',
      'driver' => $server_configuration->get('external_database_driver'),
    ];
    Database::addConnectionInfo('external', 'default', $database);

  }

  /**
   * {@inheritdoc}
   */
  public function getExternalDatabaseConnection(): Connection {
    $this->setExternalDatabaseConnection();
    return Database::getConnection('default', 'external');;
  }

}
