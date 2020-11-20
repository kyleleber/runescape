<?php

namespace Drupal\runescape_server_integration;

use Drupal\Core\Database\Connection;

/**
 * Provides an interface for the ServerIntegrationManager service.
 */
interface ServerIntegrationManagerInterface {

  /**
   * Returns the configured external database instance.
   *
   * @return \Drupal\Core\Database\Connection
   *   An array of parsed user data.
   */
  public function getExternalDatabaseConnection(): Connection;

  /**
   * Sets the external database configuration to be used.
   */
  public function setExternalDatabaseConnection(): void;

}
