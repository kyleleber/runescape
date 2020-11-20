<?php

namespace Drupal\runescape_account_management;

use DOMDocument;
use Drupal\runescape\RunescapeManagerInterface;

/**
 * Class AccountMigrationManager.
 */
class AccountMigrationManager implements AccountMigrationManagerInterface {

  /**
   * The runescape manager service.
   *
   * @var \Drupal\runescape\RunescapeManagerInterface
   */
  protected $runescapeManager;

  /**
   * Constructs a new Account Migration Manager.
   *
   * @param \Drupal\runescape\RunescapeManagerInterface $runescape_manager_interface
   *   The runescape manager service.
   */
  public function __construct(RunescapeManagerInterface $runescape_manager_interface) {
    $this->runescapeManager = $runescape_manager_interface;
  }

  /**
   * {inheritdoc}
   */
  public function getExternalUserData($username, $data_source): array {
    $account_data = [];
    switch ($data_source) {
      case 'rscvanilla':
        $account_data = $this->rscvDataSource($username);
        break;
      case 'rscemulation':
      case 'rsclegacy':
        $account_data = [];
        break;

    }
    return $account_data;
  }

  /**
   * Parses the RSCVanilla highscores for the given user.
   *
   * @see https://www.runescapeclassic.org/hiscore/ranking?user=<username>
   *
   * @param $username
   *   The username of the player to query.
   * @return mixed
   *   An array of data for the user.
   */
  private function rscvDataSource($username) {

    $htmlContent = file_get_contents("https://www.runescapeclassic.org/hiscore/ranking?user=$username");

    $DOM = new DOMDocument();
    $DOM->loadHTML($htmlContent);

    $Caption = $DOM->getElementsByTagName('caption');
    // This means it found a personal user's score. However, find a different way.
    if ($Caption->length) {
      $Header = $DOM->getElementsByTagName('th');
      $Detail = $DOM->getElementsByTagName('td');
      //Get header name of the table
      foreach ($Header as $NodeHeader) {
        $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
      }

      //Get row data/detail table without header name as key
      $i = 0;
      $j = 0;
      foreach ($Detail as $sNodeDetail) {
        $aDataTableDetailHTML[$j][] = str_replace(",", "", trim($sNodeDetail->textContent));
        $i = $i + 1;
        $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
      }
      $migration_skills = ['overall' => 'Overall'] + $this->runescapeManager->getAvailableSkills();
      foreach ($aDataTableDetailHTML as $id => $record) {
        $aDataTableDetailHTML[$id][3] = array_keys($migration_skills)[$id];
      }

      return $aDataTableDetailHTML;
    }
    return [];
  }

}
