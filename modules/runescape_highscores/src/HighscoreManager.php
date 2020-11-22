<?php

namespace Drupal\runescape_highscores;

use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\runescape_server_integration\ServerIntegrationManagerInterface;
use PDOException;

/**
 * Class HighscoreManager.
 */
class HighscoreManager implements HighscoreManagerInterface {

  use StringTranslationTrait;

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
   * Constructs a new Highscore Manager.
   *
   * @param \Drupal\runescape_server_integration\ServerIntegrationManagerInterface $server_integration_manager
   *   The server integration service.
   */
  public function __construct(ServerIntegrationManagerInterface $server_integration_manager, MessengerInterface $messenger) {
    $this->serverIntegrationManager = $server_integration_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllHighscores(): array {
    try {
      $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('experience');
      $query->join('players', NULL, 'experience.playerID=players.id');
      $query->fields('players', ['username']);
      $query->addExpression('experience.attack', 'attack_xp');
      $query->addExpression('experience.defense', 'defense_xp');
      $query->addExpression('experience.strength', 'strength_xp');
      $query->addExpression('experience.hits', 'hits_xp');
      $query->addExpression('experience.ranged', 'ranged_xp');
      $query->addExpression('experience.prayer', 'prayer_xp');
      $query->addExpression('experience.magic', 'magic_xp');
      $query->addExpression('experience.cooking', 'cooking_xp');
      $query->addExpression('experience.woodcut', 'woodcut_xp');
      $query->addExpression('experience.fletching', 'fletching_xp');
      $query->addExpression('experience.fishing', 'fishing_xp');
      $query->addExpression('experience.firemaking', 'firemaking_xp');
      $query->addExpression('experience.crafting', 'crafting_xp');
      $query->addExpression('experience.smithing', 'smithing_xp');
      $query->addExpression('experience.mining', 'mining_xp');
      $query->addExpression('experience.herblaw', 'herblaw_xp');
      $query->addExpression('experience.agility', 'agility_xp');
      $query->addExpression('experience.thieving', 'thieving_xp');
    }
    catch (DatabaseNotFoundException | PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to load highscore data at this time. Please try again later.'));

      return [];
    }

    return $query->execute()->fetchAll();
  }

  /**
   * {@inheritdoc}
   */
  public function getOverallHighscores(): array {
    try {
      $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('experience');
      $query->join('players', NULL, 'players.id=experience.playerID');
      $query->fields('players', ['username']);
      $query->addExpression(
        '(
        experience.attack +
        experience.defense +
        experience.strength +
        experience.hits +
        experience.ranged +
        experience.prayer +
        experience.magic +
        experience.cooking +
        experience.fletching +
        experience.fishing +
        experience.firemaking +
        experience.crafting +
        experience.smithing +
        experience.mining +
        experience.herblaw +
        experience.agility +
        experience.thieving
      )', 'skill_xp'
      );
      $query->addExpression('skill_total', 'skill_lvl');
    }
    catch (DatabaseNotFoundException | PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to load highscore data at this time. Please try again later.'));

      return [];
    }
    return $query->execute()->fetchAll();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserHighscores($user): array {
    try {

      $query = $this->serverIntegrationManager->getExternalDatabaseConnection()->select('experience');
      $query->join('players', NULL, 'players.id=experience.playerID');
      $query->condition('players.username', $user);
      $query->fields('experience', [
        'attack', 'defense', 'strength', 'hits', 'ranged', 'prayer', 'magic', 'cooking', 'fletching', 'fishing', 'firemaking', 'crafting', 'smithing', 'mining', 'herblaw', 'agility', 'thieving'
      ]);
      $query->addExpression(
        '(
        experience.attack +
        experience.defense +
        experience.strength +
        experience.hits +
        experience.ranged +
        experience.prayer +
        experience.magic +
        experience.cooking +
        experience.fletching +
        experience.fishing +
        experience.firemaking +
        experience.crafting +
        experience.smithing +
        experience.mining +
        experience.herblaw +
        experience.agility +
        experience.thieving
      )', 'overall');
    }
    catch (DatabaseNotFoundException | PDOException $e) {
      $this->messenger->addWarning($this->t('Unable to load highscore data at this time. Please try again later.'));
      return [];
    }
    return $query->execute()->fetchAssoc();
  }
}
