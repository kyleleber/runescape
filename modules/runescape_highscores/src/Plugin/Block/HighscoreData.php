<?php

namespace Drupal\runescape_highscores\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a runescape highscore data block.
 *
 * @Block(
 *  id = "runescape_highscore_data",
 *  admin_label = @Translation("Runescape Highscore Data block"),
 * )
 */
class HighscoreData extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * The runescape manager interface.
   *
   * @var \Drupal\runescape\RunescapeManagerInterface
   */
  protected $runescapeManager;

  /**
   * @var \Drupal\runescape_highscores\HighscoreManagerInterface
   */
  protected $highscoreManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->request = $container->get('request_stack');
    $instance->runescapeManager = $container->get('runescape.manager');
    $instance->highscoreManager = $container->get('runescape_highscore.manager');

    return $instance;
  }

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {
    $build = [];
    $skill = $this->request->getCurrentRequest()->query->get('skill');
    $user = $this->request->getCurrentRequest()->query->get('user');
    if ($user) {
      $build = [
        '#theme' => 'highscore_individual_data',
        '#results' => $this->buildHighScoreData('user',$user),
        '#user' => $user,
      ];
    }
    else if ($this->isValidSkill($skill) || $skill === "overall" || empty($skill)) {
      $build = [
        '#theme' => 'highscore_data',
        '#results' => $this->buildHighScoreData('skill',$skill),
        '#skill' => $skill ?? 'Overall',
      ];
    }
    $build['#cache']['max-age'] = 0;

    return $build;
  }

  /**
   * Helper method to build high score data based on passed in
   * parameters.
   *
   * @param string $type
   *   The type of high score (user, skill)
   * @param string $value
   *   The skill or user name.
   * @return array
   *   Returns an array of highscore data to pass to template.
   */
  private function buildHighScoreData($type,$value) {
    $highscores = [];
    if ($type === "user" && !empty($value)) {
      $results = $this->highscoreManager->getUserHighscores($value);
      $skills = [];
      foreach ($results as $skill => $result) {
        $skill_xp = (int)($result) !== 0 ? (int)$result/4 : 0;
        $skills[$skill]['xp'] = $skill_xp;
        $skills[$skill]['lvl'] = $this->runescapeManager->calculateLevel($skill_xp);
      }

      $highscores = [
        'username' => $value,
        'skills' => $skills,
      ];
    }
    else if ($value === "overall" || empty($value)) {
      $results = $this->highscoreManager->getOverallHighscores();
      $highscores = [];
      foreach ($results as $result) {
        $skill_xp = (int)($result->skill_xp) !== 0 ? (int)$result->skill_xp/4 : 0 ;
        $highscores[] = [
          'username' => $result->username,
          'skill_xp' => ceil($skill_xp),
          'skill_lvl' => $result->skill_lvl,
        ];
      }
    }
    else {
      $results = $this->highscoreManager->getAllHighscores();
      foreach ($results as $result) {
        $skill_xp = (int)($result->{$value."_xp"}) !== 0 ? (int)$result->{$value."_xp"}/4 : 0 ;
        $highscores[] = [
          'username' => $result->username,
          'skill_xp' => ceil($skill_xp),
          'skill_lvl' => $this->runescapeManager->calculateLevel($skill_xp),
        ];
      }
    }

    return $highscores;
  }

  /**
   * @param string $skill
   *   The string being checked.
   * @return bool
   *   Whether the skill exists.
   */
  private function isValidSkill($skill) {
    if (array_key_exists($skill, $this->runescapeManager->getAvailableSkills())) {
      return true;
    }
    return false;
  }

}
