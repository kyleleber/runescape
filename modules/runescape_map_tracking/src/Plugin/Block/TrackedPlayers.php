<?php

namespace Drupal\runescape_map_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a TrackedPlayers block.
 *
 * @Block(
 *  id = "tracked_players",
 *  admin_label = @Translation("Tracked Players"),
 * )
 */
class TrackedPlayers extends BlockBase implements ContainerFactoryPluginInterface {

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

    $build = [];

    try {
      $players = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('players')
        ->fields('players', ['id', 'username', 'combat'])
        ->condition('online', 1)->execute()->fetchAll();

      $tracked_players = [];
      foreach ($players as $player) {
        $tracked_players[] = [
          'id' => $player->id,
          'username' => $player->username,
          'combat' => $player->combat,
        ];
      }
      $build = [
        '#theme' => 'tracked_players',
        '#results' => $tracked_players,
      ];
    }

    catch(DatabaseNotFoundException | \PDOException $e) {
      $this->messenger()->addWarning($this->t('Unable to load map data at this time. Please try again later.'));
    }

    return $build;
  }

}
