<?php

namespace Drupal\runescape_account_management;

/**
 * Provides an interface for the AccountMigrationManager.
 */
interface AccountMigrationManagerInterface {

  /**
   * Parses the external data source's highscores for user data.
   *
   * @param string $username
   *   The username of the player to lookup.
   * @param $data_source
   *   The source of data to look the player up in.
   * @return array|mixed
   *   An array of parsed user data.
   */
  public function getExternalUserData(string $username, string $data_source): array;

}
