<?php

namespace Drupal\runescape_account_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Account class controller that allows a user to create accounts via
 * a drupal form. Users can also view their existing accounts and perform
 * actions such as changing password and deleting account through web application.
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
   * The account manager service.
   *
   * @var \Drupal\runescape_account_management\AccountManagerInterface
   */
  protected $accountManager;

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
    $instance->accountManager = $container->get('runescape_account_management.manager');

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
    $npc_kills = $this->accountManager->getAccountData($current_user, $player_id)['npc_kills'];
    $kills = [];


    foreach ($npc_kills as $id => $kill) {

      if ($npc_configuration = $this->npcManager->loadByProperties(['npc_id' => $kill->npcID])) {
        $npc_name = array_shift($npc_configuration)->label();
      }
      $drops = $kill->drops;

      foreach ($drops as $drop_id => $drop) {

        if ($item = $this->itemManager->loadByProperties(['item_id' => $drop['itemID']])) {

          $drops[$drop_id]['name'] = array_shift($item)->label();
        }
        else {
          $drops[$drop_id]['name'] = $this->t('Undefined: A site administrator has been notified.');
        }
      }
      $kills[] = [
        'name' => $npc_name ?? $this->t('Undefined: A site administrator has been notified.'),
        'id' => $kill->npcID,
        'kill_count' => $kill->killCount,
        'items' => $drops,
      ];
    }

    return [
      '#type' => 'details',
      '#title' => $this->t('NPC Tracking'),
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
    $user = $this->accountManager->getAccountData($current_user, $player_id)['forum_username'];

    return !empty($user) ? $user : $this->t('Access Denied');
  }

}
