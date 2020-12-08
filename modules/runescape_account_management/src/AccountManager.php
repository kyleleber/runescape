<?php

namespace Drupal\runescape_account_management;

use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\runescape\RunescapeManagerInterface;
use Drupal\runescape_server_integration\ServerIntegrationManagerInterface;
use PDO;

/**
 * Class AccountManager.
 */
class AccountManager implements AccountManagerInterface {

  use StringTranslationTrait;

  /**
   * The runescape account manager service.
   *
   * @var \Drupal\runescape\RunescapeManagerInterface
   */
  protected $runescapeManager;

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
   * The account of current user.
   *
   * @var array
   */
  protected $account;

  /**
   * Constructs a new Runescape Account Manager.
   *
   * @param \Drupal\runescape\RunescapeManagerInterface $runescape_manager_interface
   *   The runescape manager service.
   * @param \Drupal\runescape_server_integration\ServerIntegrationManagerInterface $server_integration_manager
   *   The server integration service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(RunescapeManagerInterface $runescape_manager_interface, ServerIntegrationManagerInterface  $server_integration_manager, MessengerInterface $messenger) {
    $this->serverIntegrationManager = $server_integration_manager;
    $this->runescapeManager = $runescape_manager_interface;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function createInGameAccount($data): bool {
    try {
      $player_id = $this->insertPlayer($data);
      $this->insertCurStats($player_id, $data);
      $this->insertExperience($player_id, $data);
      $this->insertBankItems($player_id, $data);
      return true;
    }
    catch (DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addError($this->t('There was an issue creating the account. Please try again later'));
    }
    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function isUsernameTaken($username): bool {
    try {
      $account = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
        ->condition('username', $username)
        ->fields('players', ['username'])
        ->execute()->fetchField();
    }
    catch (DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addError($this->t('There was an issue creating the account. Please try again later'));
    }
    return $account ? true : false;
  }

  /**
   * {@inheritdoc}
   */
  public function getExistingAccounts($current_user): array {
    $accounts = [];
    try {
      $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
        ->fields('players', ['id', 'username', 'combat', 'login_date'])
        ->condition('forum_account', $current_user);
      $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(login_date), '%m/%d/%Y %H:%i')", 'login_date');

      $accounts = $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t("Your account's data could not be loaded at this time. Please try again later."));
    }

    return $accounts;
  }

  /**
   * {inheritdoc}
   */
  public function getAccountData($current_user, $player_id): array {
    $this->account = [];
    try {
      $database = $this->serverIntegrationManager->getExternalDatabaseConnection();
      $query = $database->select('npckills');
      $query->join('players', NULL, 'npckills.playerID = players.id');
      $query->condition('players.forum_account', $current_user);
      $query->condition('players.id', $player_id);
      $query->fields('npckills', ['npcID', 'killCount']);
      $query->groupBy('npckills.npcId');
      $query->groupBy('npckills.killCount');
      $query->groupBy('players.id');
      $npc_kills = $query->execute()->fetchAllAssoc('npcID');
      foreach ($npc_kills as $kill) {
        $query = $database->select('droplogs');
        $query->condition('playerID', $player_id);
        $query->condition('npcId', $kill->npcID);
        $query->addExpression('count(*)', 'dropQuantity');
        $query->groupBy('npcId');
        $query->groupBy('itemId');
        $query->groupBy('dropAmount');
        $query->fields('droplogs', ['npcId','itemID','dropAmount']);
        $query->orderBy('itemId');
        $drops = $query->execute()->fetchAll(PDO::FETCH_ASSOC);
        $kill->drops = $drops;
      }
      $this->account['npc_kills'] = $npc_kills;

      $query = $database->select('players');
      $query->condition('players.forum_account', $current_user);
      $query->condition('players.id', $player_id);
      $query->fields('players', ['username']);

      $this->account['forum_username'] = $query->execute()->fetchField();

    }
    catch (DatabaseNotFoundException | \PDOException $e) {
      $this->messenger->addWarning($this->t("Your account's data could not be loaded at this time. Please try again later."));
    }

    return $this->account;
  }

  /**
   * Helper method to insert a player into the game database..
   *
   * @param array $data
   *   An associative array of data used in data insertion.
   * @return int
   *   Returns the player id of the user just inserted.
   *   This is used elsewhere in subsequent insertions.
   * @throws \Exception
   *   An exception if there are issues inserting bank items.
   */
  private function insertPlayer($data) {
    return $this->serverIntegrationManager->getExternalDatabaseConnection()->insert("players")->fields([
      'username' => $data['username'],
      'email' => $data['email'],
      'pass' => password_hash($data['password'],PASSWORD_BCRYPT),
      'forum_account' => $data['forum_account'],
    ])->execute();
  }

  /**
   * Helper method to insert calculated stats for a player.
   *
   * @param int $player_id
   *   The in-game player ID.
   * @param array $data
   *   An associative array of data used in data insertion.
   * @throws \Exception
   *   An exception if there are issues inserting bank items.
   */
  private function insertCurStats($player_id, $data) {
    $this->serverIntegrationManager->getExternalDatabaseConnection()->insert("curstats")->fields([
      'playerId' => $player_id,
      'attack' => $this->runescapeManager->calculateLevel($data['experience']['attack']),
      'defense' => $this->runescapeManager->calculateLevel($data['experience']['defense']),
      'strength' => $this->runescapeManager->calculateLevel($data['experience']['strength']),
      'hits' => $this->runescapeManager->calculateLevel($data['experience']['hits']),
      'ranged' => $this->runescapeManager->calculateLevel($data['experience']['ranged']),
      'prayer' => $this->runescapeManager->calculateLevel($data['experience']['prayer']),
      'magic' => $this->runescapeManager->calculateLevel($data['experience']['magic']),
      'cooking' => $this->runescapeManager->calculateLevel($data['experience']['cooking']),
      'woodcut' => $this->runescapeManager->calculateLevel($data['experience']['woodcut']),
      'fletching' => $this->runescapeManager->calculateLevel($data['experience']['fletching']),
      'fishing' => $this->runescapeManager->calculateLevel($data['experience']['fishing']),
      'firemaking' => $this->runescapeManager->calculateLevel($data['experience']['firemaking']),
      'crafting' => $this->runescapeManager->calculateLevel($data['experience']['crafting']),
      'smithing' => $this->runescapeManager->calculateLevel($data['experience']['smithing']),
      'mining' => $this->runescapeManager->calculateLevel($data['experience']['mining']),
      'herblaw' => $this->runescapeManager->calculateLevel($data['experience']['herblaw']),
      'agility' => $this->runescapeManager->calculateLevel($data['experience']['agility']),
      'thieving' => $this->runescapeManager->calculateLevel($data['experience']['thieving']),
    ])->execute();
  }

  /**
   * Helper method to insert calculated experience for a player.
   *
   * @param int $player_id
   *   The in-game player ID.
   * @param array $data
   *   An associative array of data used in data insertion.
   * @throws \Exception
   *   An exception if there are issues inserting bank items.
   */
  private function insertExperience($player_id, $data) {
    $this->serverIntegrationManager->getExternalDatabaseConnection()->insert('experience')->fields([
      'playerId' => $player_id,
      'attack' => $data['experience']['attack']*4 ?: 0,
      'defense' => $data['experience']['defense']*4 ?: 0,
      'strength' => $data['experience']['strength']*4 ?: 0,
      'hits' => $data['experience']['hits']*4 ?: 0,
      'ranged' => $data['experience']['ranged']*4 ?: 0,
      'prayer' => $data['experience']['prayer']*4 ?: 0,
      'magic' => $data['experience']['magic']*4 ?: 0,
      'cooking' => $data['experience']['cooking']*4 ?: 0,
      'woodcut' => $data['experience']['woodcut']*4 ?: 0,
      'fletching' => $data['experience']['fletching']*4 ?: 0,
      'fishing' => $data['experience']['fishing']*4 ?: 0,
      'firemaking' => $data['experience']['firemaking']*4 ?: 0,
      'crafting' => $data['experience']['crafting']*4 ?: 0,
      'smithing' => $data['experience']['smithing']*4 ?: 0,
      'mining' => $data['experience']['mining']*4 ?: 0,
      'herblaw' => $data['experience']['herblaw']*4 ?: 0,
      'agility' => $data['experience']['agility']*4 ?: 0,
      'thieving' => $data['experience']['thieving']*4 ?: 0,
    ])->execute();
  }

  /**
   * Helper method to insert items into a player's bank.
   *
   * @param int $player_id
   *   The in-game player ID.
   * @param array $data
   *   An associative array of data used in data insertion.
   * @throws \Exception
   *   An exception if there are issues inserting bank items.
   */
  private function insertBankItems($player_id, $data) {
    // Get the item statuses max id first
    $max_id_query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('itemstatuses');
    $max_id_query->addExpression('MAX(itemID)', 'maxItemID');
    $max_id = $max_id_query->execute()->fetchField();

    foreach ($data['bank_items'] as $slot => $item) {
      $max_id++;
      $this->serverIntegrationManager->getExternalDatabaseConnection()->insert("itemstatuses")
        ->fields([
          'itemID' => $max_id,
          'catalogID' => $item['id'],
          'amount' => $item['quantity'],
          'noted' => 0,
          'wielded' => 0,
          'durability' => 100,
        ])->execute();

      $this->serverIntegrationManager->getExternalDatabaseConnection()->insert('bank')
        ->fields([
          'playerId' => $player_id,
          'itemID' => $max_id,
          'slot' => $slot,
        ])->execute();

    }
  }


}
