<?php

namespace Drupal\runescape_server_administration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\runescape_server_integration\ServerIntegrationManagerInterface;
use PDO;

/**
 * Class GameAssetManager.
 */
class GameAssetManager implements GameAssetManagerInterface {

  use StringTranslationTrait;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The server integration manager.
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
   * Constructs a new Runescape server integration Manager.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The runescape manager service.
   * @param \Drupal\runescape_server_integration\ServerIntegrationManagerInterface $server_integration_manager
   *   The server integration manager service.
   * @param \Drupal\Core\Messenger\MessengerInterface
   *   The messenger service
   */
  public function __construct(ConfigFactoryInterface $config_factory, ServerIntegrationManagerInterface $server_integration_manager, MessengerInterface $messenger) {
    $this->config = $config_factory;
    $this->serverIntegrationManager = $server_integration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllItems(): array {
    $items = [];
    try {
      $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('itemstatuses')
        ->fields('itemstatuses', ['catalogId']);
        $query->addExpression('SUM(amount)','total');
      $items = $query->groupBy('catalogId')
        ->execute()->fetchAll(PDO::FETCH_ASSOC);

    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to lookup game items. Try again later.'));
    }

    return $items;
  }


  /**
   * {@inheritdoc}
   */
  public function getGroupRankings(): array {
    return [
      0 => $this->t('Owner'),
      1 => $this->t('Administrator'),
      2 => $this->t('Super Moderator'),
      3 => $this->t('Moderator'),
      5 => $this->t('Developer'),
      7 => $this->t('Event'),
      8 => $this->t('Player Moderator'),
      9 => $this->t('Tester'),
      10 => $this->t('User'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getAllAccounts(): array {
    $accounts = [];

    try {
      $accounts = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
        ->fields('players')->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to lookup game accounts. Try again later.'));
    }

    return $accounts;
  }

  /**
   * {@inheritdoc}
   */
  public function getTradeLogs(): array {
    $logs = [];

    try {
      $logs = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('trade_logs')
        ->fields('trade_logs')->orderBy('id')->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to lookup game trade logs. Try again later.'));
    }

    return $logs;
  }

  /**
   * {@inheritdoc}
   */
  public function getChatLogs(): array {
    $logs = [];

    try {
      $logs = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('chat_logs')
        ->fields('chat_logs')->orderBy('time')->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to lookup chat logs. Try again later.'));
    }

    return $logs;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrivateChatLogs(): array {
    $logs = [];

    try {
      $logs = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('private_message_logs')
        ->fields('private_message_logs')->orderBy('time')->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to lookup private chat logs. Try again later.'));
    }

    return $logs;
  }
}
