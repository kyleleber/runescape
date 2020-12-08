<?php

namespace Drupal\runescape_auctionhouse\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\DatabaseNotFoundException;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an AuctionHouse block.
 *
 * @Block(
 *  id = "auction_house",
 *  admin_label = @Translation("Auction House"),
 * )
 */
class AuctionHouse extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The server integration service.
   *
   * @var \Drupal\runescape_server_integration\ServerIntegrationManagerInterface
   */
  protected $serverIntegrationManager;

  /**
   * The auction house manager service.
   *
   * @var \Drupal\runescape_auctionhouse\AuctionHouseManagerInterface
   */
  protected $auctionHouseManager;

  /**
   * The item manager service.
   *
   * @var \Drupal\runescape\ItemManager
   */
  protected $itemManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->serverIntegrationManager = $container->get('runescape_server_integration.manager');
    $instance->auctionHouseManager = $container->get('runescape_auctionhouse.manager');
    $instance->itemManager = $container->get('runescape.item_manager');

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
      '#theme' => 'auction_house',
      '#active_auctions' => $this->parseAuctionItems($this->auctionHouseManager->getActiveAuctions()),
      '#soldout_auctions' => $this->parseAuctionItems($this->auctionHouseManager->getSoldoutAuctions()),
    ];

    $build['#cache']['max-age'] = 0;

    return $build;
  }

  /**
   * Helper function to parse auction items into a friendly format.
   *
   * @return array
   *   An associative array of parsed auctions.
   */
  private function parseAuctionItems($items) {
    $auction_items = [];
    foreach ($items as $item) {
      $item_name = $this->itemManager->loadByProperties(['item_id' => $item->itemID]);
      $auction_items[] = [
        'item_id' => $item->itemID,
        'item_name' => $item_name ? array_shift($item_name)->label() : 'Undefined',
        'amount' => $item->amount,
        'amount_left' => $item->amount_left,
        'price' => $item->price,
        'seller_username' => $item->seller_username,
        'time' => $item->time,
      ];
    }
    return $auction_items;
  }

}
