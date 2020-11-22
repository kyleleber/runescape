<?php

namespace Drupal\runescape_map_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a MapTracking block.
 *
 * @Block(
 *  id = "map_tracking",
 *  admin_label = @Translation("Map Tracking"),
 * )
 */
class MapTracking extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
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
    try {
      $players = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
        ->fields('players', ['id', 'username', 'combat', 'x', 'y'])
        ->condition('online', 1)->execute()->fetchAll();

      foreach ($players as $player) {
        $build['#attached']['drupalSettings']['player_coordinates'][] = [
          'id' => $player->id,
          'username' => $player->username,
          'combat' => $player->combat,
          'x' => $player->x,
          'y' => $player->y,
        ];
      }
    }
    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger()->addWarning($this->t('Unable to load map data at this time. Please try again later.'));
    }

    return $build;
  }

}
