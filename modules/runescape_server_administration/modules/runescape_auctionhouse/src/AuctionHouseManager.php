<?php

namespace Drupal\runescape_auctionhouse;

use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\runescape_server_integration\ServerIntegrationManagerInterface;

/**
 * Class AccountManager.
 */
class AuctionHouseManager implements AuctionHouseManagerInterface {

  use StringTranslationTrait;

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new Runescape Account Manager.
   *
   * @param \Drupal\runescape_server_integration\ServerIntegrationManagerInterface $server_integration_manager
   *   The server integration service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(ServerIntegrationManagerInterface  $server_integration_manager, MessengerInterface $messenger) {
    $this->serverIntegrationManager = $server_integration_manager;
    $this->messenger = $messenger;
  }

  /**
   * {inheritdoc}
   */
  public function getActiveAuctions(): array {
    $auctions = [];
    try {
      $auctions = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('auctions')
      ->fields('auctions')
      ->execute()->fetchAll();

      $auctions = array_filter($auctions, function($auction) {
        return $auction->{'sold-out'} == 0;
      });

    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger()->addWarning($this->t('Unable to load auction data at this time. Please try again later.'));
    }
    return $auctions;
  }

  /**
   * {inheritdoc}
   */
  public function getExpiredAuctions(): array {
    return [];
  }

  /**
   * {inheritdoc}
   */
  public function getSoldoutAuctions(): array {
    $auctions = [];
    try {
      $auctions = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('auctions')
        ->fields('auctions')
        ->execute()->fetchAll();

      $auctions = array_filter($auctions, function($auction) {
        return $auction->{'sold-out'} == 1;
      });

    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger()->addWarning($this->t('Unable to load auction data at this time. Please try again later.'));
    }
    return $auctions;
  }

}
