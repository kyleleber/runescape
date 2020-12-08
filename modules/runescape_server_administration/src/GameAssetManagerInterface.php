<?php

namespace Drupal\runescape_server_administration;

/**
 * Provides an interface for the GameAssetManager service.
 */
interface GameAssetManagerInterface {

  /**
   * Gets all items in the server.
   *
   * @return array
   *   Returns an array of all items in server.
   */
  public function getAllItems(): array;

  /**
   * Get all group rankings.
   *
   * @return array
   *   Returns an array of group rankings.
   */
  public function getGroupRankings(): array;

  /**
   * Get every in-game account.
   *
   * @return array
   *   Returns an array of every in-game account.
   */
  public function getAllAccounts(): array;

  /**
   * Get all trade logs.
   *
   * @return array
   *   Returns an array of every trade log.
   */
  public function getTradeLogs(): array;

  /**
   * Get all chat logs (global and local).
   *
   * @return array
   *   Returns an array of every chat log.
   */
  public function getChatLogs(): array;

  /**
   * Get all private chat logs.
   *
   * @return array
   *   Returns an array of every private chat log.
   */
  public function getPrivateChatLogs(): array;

}
