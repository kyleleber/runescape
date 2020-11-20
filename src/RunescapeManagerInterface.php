<?php

namespace Drupal\runescape;

/**
 * Provides an interface for the RunescapeManager class.
 */
interface RunescapeManagerInterface {

  /**
   * Get a list of all available skills.
   *
   * @return array
   *   An array of available skills on server.
   */
  public function getAvailableSkills(): array;

  /**
   * Calculate the lvl by a given xp amount.
   *
   * @param int $xp
   *   The given xp to calculate level.
   * @return int
   *   The lvl calculated.
   *
   */
  public function calculateLevel($xp): int;

}
