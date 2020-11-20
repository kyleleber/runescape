<?php

namespace Drupal\runescape_highscores\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a runescape highscores block.
 *
 * @Block(
 *  id = "runescape_highscore_categories",
 *  admin_label = @Translation("Runescape Highscore Categories"),
 * )
 */
class HighscoreCategories extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The runescape manager service.
   *
   * @var \Drupal\runescape\RunescapeManagerInterface
   */
  private $runescapeManager;

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {
    return [
      '#theme' => 'highscore_categories',
      '#categories' => ['overall' => 'Overall'] + $this->runescapeManager->getAvailableSkills(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->runescapeManager = $container->get('runescape.manager');
    return $instance;
  }

}
