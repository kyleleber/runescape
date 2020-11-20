<?php

namespace Drupal\runescape_map_tracking\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a callback for the tracking map. This integrates
 * with the player's table to show current player locations and
 * attributes.
 */
class TrackingMap extends ControllerBase {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->serverIntegrationManager = $container->get('runescape_server_integration.manager');

    return $instance;
  }

  /**
   * Method that is used when the route is accessed.
   *
   * @return array
   *   Build array for page theme.
   */
  public function build() {
    $build = [
      '#theme' => 'tracking_map',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    $players = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
      ->fields('players',['id','username','combat','x','y'])
      ->condition('online',1)->execute()->fetchAll();

    foreach ($players as $player) {
      $build['#attached']['drupalSettings']['player_coordinates'][] = [
        'id' => $player->id,
        'username' => $player->username,
        'combat' => $player->combat,
        'x' => $player->x,
        'y' => $player->y,
      ];
    }

    return $build;
  }

}
