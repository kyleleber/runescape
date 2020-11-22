<?php

namespace Drupal\runescape_account_management;

/**
 * Provides an interface for the Account Manager service.
 */
interface AccountManagerInterface {

  /**
   * Creates a new runescape in-game account.
   *
   * @param array $data
   *   An array of associative data used to create user account.
   * @return bool
   *   Returns true the account has been created, false if not.
   */
  public function createInGameAccount(array $data): bool;

  /**
   * Checks whether a username is already taken.
   *
   * @param string $username
   *   The username to be checked if already taken.
   * @return bool
   *   Returns true if the username is taken, false if not.
   */
  public function isUsernameTaken(string $username): bool;

  /**
   * Gets all existing in-game accounts for given forum id.
   *
   * @param int $current_user
   *   The id of the forum account.
   * @return array
   *   Returns an array of existing in-game accounts for a given forum id.
   */
  public function getExistingAccounts(int $current_user): array;

  /**
   * @param int $current_user
   *   The id of the forum account.
   * @param string $player_id
   *   The player id of the in-game account.
   * @return array
   *   Returns an associative array of an account's data.
   */
  public function getAccountData(int $current_user, string $player_id): array;


}
