<?php

namespace Drupal\runescape_highscores;

/**
 * Provides an interface for the HighscoreManager.
 */
interface HighscoreManagerInterface {

  /**
   * Returns highscore data from the external database connection.
   *
   * @return array|mixed
   *   An array of parsed user data.
   */
  public function getAllHighscores(): array;

  /**
   * Returns overall highscore data from external database connection.
   *
   * @return array
   *   An array of overall highscore data for all accounts.
   */
  public function getOverallHighscores(): array;

  /**
   * Returns highscore data from external database connection for a specific user.
   *
   * @param string $user
   *   The name of the user to lookup.
   * @return array
   *   An array of overall highscore data for all accounts.
   */
  public function getUserHighscores($user): array;

}
