<?php

namespace Drupal\runescape_account_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use PDO;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * HomepageController class that allows for the homepage /home route to work.
 */
class Account extends ControllerBase {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * The route match service.
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
   * Method that is used when the route is accessed.
   *
   * @return array
   *   Build array for page theme.
   */
  public function build() {


    return [
      $this->buildNpcKills(),
      '#cache' => [
        'max-age' => 0,
      ],
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

    return $instance;
  }

  /**
   * Returns the npc kills for the current logged in account + their in-game account.
   *
   * @return array|null
   *   The build array for npc kills.
   */
  private function buildNpcKills() {
    $current_user = $this->currentUser()->id();
    $player_id = $this->routeMatch->getParameter('account_id');
    $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('npckills');
    $query->join('players',NULL,'npckills.playerID = players.id');
    $query->condition('players.forum_account', $current_user);
    $query->condition('players.id', $player_id);
    $query->fields('npckills', ['npcID','killCount']);
    $npc_kills = $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    $kills = [];

    foreach ($npc_kills as $id => $kill) {
      if ($npc_configuration = $this->npcManager->loadByProperties(['npc_id' => $kill['npcID']])) {
        $npc_name = array_shift($npc_configuration)->label();
      }
      $drops = $this->getDropItems($player_id,$kill['npcID']);
      foreach ($drops as $id => $drop) {
        if ($item = $this->itemManager->loadByProperties(['item_id' => $drop['itemID']])) {
          $drops[$id]['name'] = array_shift($item)->label();
        }
        else {
          $drops[$id]['name'] = $this->t('Undefined: A site administrator has been notified');
        }
      }

      $kills[] = [
        '#theme' => 'npc_kill',
        '#name' => $npc_name,
        '#id' => $kill['npcID'],
        '#kill_count' => $kill['killCount'],
        '#items' => $drops,
      ];
    }

    return [
      '#type' => 'details',
      '#title' => $this->t('NPC Statistics'),
      '#open' => TRUE,
      'npc_kills' => [
        '#theme' => 'npc_kills',
        '#kills' => $kills,
        '#cache' => [
          'max-age' => 0,
        ],
      ]
    ];
  }

  /**
   *  Returns the title of page based on current user's in-game account name.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The title of the page used by controller.
   */
  public function accountTitle() {
    $current_user = $this->currentUser()->id();
    $player_id = $this->routeMatch->getParameter('account_id'); // get from route.

    $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players');
    $query->condition('players.forum_account', $current_user);
    $query->condition('players.id', $player_id);
    $query->fields('players', ['username']);
    $user = $query->execute()->fetchField();
    return !empty($user) ? $user : $this->t('Access Denied');
  }

  /**
   * Returns an array of drop data for the user for each NPC.
   *
   * @param int $player_id
   *   The player account ID from the game database.
   * @param int $npc_id
   *   The id of the npc.
   * @return array
   *   Array of drop data.
   */
  private function getDropItems($player_id, $npc_id) {
    $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('droplogs');
    $query->condition('playerID', $player_id);
    $query->condition('npcId', $npc_id);
    $query->addExpression('count(*)', 'dropQuantity');
    $query->groupBy('itemId');
    $query->groupBy('dropAmount');
    $query->fields('droplogs', ['itemID','dropAmount']);

    return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
  }

}
