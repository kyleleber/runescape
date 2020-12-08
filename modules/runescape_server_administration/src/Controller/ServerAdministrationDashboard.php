<?php

namespace Drupal\runescape_server_administration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a ServerAdministrationDashboard controller to allow privileged users
 * to be able to view and manage components of the Runescape classic game-server.
 */
class ServerAdministrationDashboard extends ControllerBase {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The npc manager service.
   *
   * @var \Drupal\runescape\NpcManager
   */
  protected $npcManager;

  /**
   * The npc manager service.
   *
   * @var \Drupal\runescape\ItemManager
   */
  protected $itemManager;

  /**
   * The game asset manager service.
   *
   * @var \Drupal\runescape_server_administration\GameAssetManagerInterface
   */
  protected $gameAssetManager;

  /**
   * Method that is used when the route is accessed.
   *
   * @return array
   *   Build array for page theme.
   */
  public function build() {
    return [
      '#theme' => 'dashboard',
      '#accounts' => $this->parseUserAccounts(),
      '#items' => $this->parseItems(),
      '#trade_logs' => $this->parseTradeLogs(),
      '#chat_logs' => $this->parseChatLogs(),
      '#private_chat_logs' => $this->parsePrivateChatLogs(),
    ];
  }

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->serverIntegrationManager = $container->get('runescape_server_integration.manager');
    $instance->routeMatch = $container->get('current_route_match');
    $instance->npcManager = $container->get('runescape.npc_manager');
    $instance->itemManager = $container->get('runescape.item_manager');
    $instance->gameAssetManager = $container->get('runescape_server_administration.asset_manager');

    return $instance;
  }

  /**
   * Helper method to parse the user accounts before passing
   * to the template.
   *
   * @return array
   *   Associative array of parsed user accounts.
   */
  private function parseUserAccounts() {
    $accounts = [];
    foreach ($this->gameAssetManager->getAllAccounts() as $account) {
      $accounts[$account['username']] = [
        'forum_account' => User::load($account['forum_account'])->label(),
        'group' => $this->gameAssetManager->getGroupRankings()[$account['group_id']],
      ];
    }

    return $accounts;
  }

  /**
   * Helper method to parse the game items before passing
   * to the template.
   *
   * @return array
   *  Associative array of parsed items.
   */
  private function parseItems() {
    $items = [];
    foreach ($this->gameAssetManager->getAllItems() as $item) {
      $item_entity = $this->itemManager->loadByProperties(['item_id' => $item['catalogId']]);
      $items[] = [
        'id' => $item['catalogId'],
        'name' => $item_entity ? array_shift($item_entity)->label() : 'undefined',
        'amount' => $item['total'],
      ];
    }

    return $items;
  }

  /**
   * Helper method to parse the game trade logs before passing
   * to the template.
   *
   * @return array
   *  Associative array of parsed trade logs.
   */
  private function parseTradeLogs() {
    $logs = [];
    foreach ($this->gameAssetManager->getTradeLogs() as $log) {
      $player_1_traded_items = explode(",", $log['player1_items']);
      $player_1_items = $player_2_items = [];

      foreach ($player_1_traded_items as $item) {

        $item_data = array_filter(explode(":", $item));
        if (!empty($item_data)) {
          $item_entity = $this->itemManager->loadByProperties(['item_id' => $item_data[0]]);
          $player_1_items[$item_data[0]] = [
            'name' => array_shift($item_entity)->label(),
            'amount' => $item_data[1] + $player_1_items[$item_data[0]]['amount'],
          ];
        }

      }

      $player_2_traded_items = explode(",", $log['player2_items']);

      foreach ($player_2_traded_items as $item) {
        $item_data = array_filter(explode(":", $item));
        if (!empty($item_data)) {
          $item_entity = $this->itemManager->loadByProperties(['item_id' => $item_data[0]]);
          $player_2_items[$item_data[0]] = [
            'name' => array_shift($item_entity)->label(),
            'amount' => $item_data[1] + $player_2_items[$item_data[0]]['amount'],
          ];
        }
      }

      $logs[] = [
        'time' => $log['time'],
        'id' => $log['id'],
        'player1' => $log['player1'],
        'player2' => $log['player2'],
        'player1_items' => $player_1_items,
        'player2_items' => $player_2_items,
      ];
    }

    return $logs;
  }

  /**
   * Helper method to parse the chat (global/local) logs before passing
   * to the template.
   *
   * @return array
   *  Associative array of parsed chat log messages.
   */
  private function parseChatLogs() {
    $logs = [];
    foreach ($this->gameAssetManager->getChatLogs() as $log) {
      $logs[] = [
        'sender' => $log['sender'],
        'message' => $log['message'],
        'time' => $log['time'],
      ];
    }

    return $logs;
  }

  /**
   * Helper method to parse the private chat logs before passing
   * to the template.
   *
   * @return array
   *  Associative array of parsed chat log messages.
   */
  private function parsePrivateChatLogs() {
    $logs = [];
    foreach ($this->gameAssetManager->getPrivateChatLogs() as $log) {
      $logs[] = [
        'sender' => $log['sender'],
        'reciever' => $log['reciever'],
        'message' => $log['message'],
        'time' => $log['time'],
      ];
    }

    return $logs;
  }
}
