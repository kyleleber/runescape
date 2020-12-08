<?php

namespace Drupal\runescape_auctionhouse;

/**
 * Provides an interface for the AccountMigrationManager.
 */
interface AuctionHouseManagerInterface {

  /**
   * Gets the currently active auctions.
   *
   * @return array|mixed
   *   An array of auction data.
   */
  public function getActiveAuctions(): array;

  /**
   * Gets the soldout auctions.
   *
   * @return array|mixed
   *   An array of auction data.
   */
  public function getSoldoutAuctions(): array;

  /**
   * Gets the expired auctions.
   *
   * @return array|mixed
   *   An array of auction data.
   */
  public function getExpiredAuctions(): array;

}
