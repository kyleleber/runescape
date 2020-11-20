<?php

namespace Drupal\runescape;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * A service that provides static data definitions of
 * server related attributes.
 */
class RunescapeManager implements RunescapeManagerInterface {

  use StringTranslationTrait;

/**
 * {@inheritdoc}
 */
 public function getAvailableSkills(): array {
   return [
     'attack' => $this->t('Attack'),
     'defense' => $this->t('Defense'),
     'strength' => $this->t('Strength'),
     'hits' => $this->t('Hits'),
     'ranged' => $this->t('Ranged'),
     'prayer' => $this->t('Prayer'),
     'magic' => $this->t('Magic'),
     'cooking' => $this->t('Cooking'),
     'woodcut' => $this->t('Woodcut'),
     'fletching' => $this->t('Fletching'),
     'fishing' => $this->t('Fishing'),
     'firemaking' => $this->t('Firemaking'),
     'crafting' => $this->t('Crafting'),
     'smithing' => $this->t('Smithing'),
     'mining' => $this->t('Mining'),
     'herblaw' => $this->t('Herblaw'),
     'agility' => $this->t('Agility'),
     'thieving' => $this->t('Thieving'),
   ];
 }

/**
 * {@inheritdoc}
 */
 public function calculateLevel($xp): int {
   switch ($xp) {
     case $xp < 83: $level = 1; break;
     case $xp < 174: $level = 2; break;
     case $xp < 276: $level = 3; break;
     case $xp < 388: $level = 4; break;
     case $xp < 512: $level = 5; break;
     case $xp < 650: $level = 6; break;
     case $xp < 801: $level = 7; break;
     case $xp < 969: $level = 8; break;
     case $xp < 1154: $level = 9; break;
     case $xp < 1358: $level = 10; break;
     case $xp < 1584: $level = 11; break;
     case $xp < 1833: $level = 12; break;
     case $xp < 2107: $level = 13; break;
     case $xp < 2411: $level = 14; break;
     case $xp < 2746: $level = 15; break;
     case $xp < 3115: $level = 16; break;
     case $xp < 3523: $level = 17; break;
     case $xp < 3973: $level = 18; break;
     case $xp < 4470: $level = 19; break;
     case $xp < 5018: $level = 20; break;
     case $xp < 5624: $level = 21; break;
     case $xp < 6291: $level = 22; break;
     case $xp < 7028: $level = 23; break;
     case $xp < 7842: $level = 24; break;
     case $xp < 8740: $level = 25; break;
     case $xp < 9730: $level = 26; break;
     case $xp < 10824: $level = 27; break;
     case $xp < 12031: $level = 28; break;
     case $xp < 13363: $level = 29; break;
     case $xp < 14833: $level = 30; break;
     case $xp < 16456: $level = 31; break;
     case $xp < 18247: $level = 32; break;
     case $xp < 20224: $level = 33; break;
     case $xp < 22406: $level = 34; break;
     case $xp < 24815: $level = 35; break;
     case $xp < 27473: $level = 36; break;
     case $xp < 30408: $level = 37; break;
     case $xp < 33648: $level = 38; break;
     case $xp < 37224: $level = 39; break;
     case $xp < 41171: $level = 40; break;
     case $xp < 45529: $level = 41; break;
     case $xp < 50339: $level = 42; break;
     case $xp < 55649: $level = 43; break;
     case $xp < 61512: $level = 44; break;
     case $xp < 67983: $level = 45; break;
     case $xp < 75127: $level = 46; break;
     case $xp < 83014: $level = 47; break;
     case $xp < 91721: $level = 48; break;
     case $xp < 101333: $level = 49; break;
     case $xp < 111945: $level = 50; break;
     case $xp < 123660: $level = 51; break;
     case $xp < 136594: $level = 52; break;
     case $xp < 150872: $level = 53; break;
     case $xp < 166636: $level = 54; break;
     case $xp < 184040: $level = 55; break;
     case $xp < 203254: $level = 56; break;
     case $xp < 224466: $level = 57; break;
     case $xp < 247886: $level = 58; break;
     case $xp < 273742: $level = 59; break;
     case $xp < 302288: $level = 60; break;
     case $xp < 333804: $level = 61; break;
     case $xp < 368599: $level = 62; break;
     case $xp < 407015: $level = 63; break;
     case $xp < 449428: $level = 64; break;
     case $xp < 496254: $level = 65; break;
     case $xp < 547953: $level = 66; break;
     case $xp < 605032: $level = 67; break;
     case $xp < 668051: $level = 68; break;
     case $xp < 737627: $level = 69; break;
     case $xp < 814445: $level = 70; break;
     case $xp < 899257: $level = 71; break;
     case $xp < 992895: $level = 72; break;
     case $xp < 1096278: $level = 73; break;
     case $xp < 1210421: $level = 74; break;
     case $xp < 1336443: $level = 75; break;
     case $xp < 1475581: $level = 76; break;
     case $xp < 1629200: $level = 77; break;
     case $xp < 1798808: $level = 78; break;
     case $xp < 1986068: $level = 79; break;
     case $xp < 2192818: $level = 80; break;
     case $xp < 2421087: $level = 81; break;
     case $xp < 2673114: $level = 82; break;
     case $xp < 2951373: $level = 83; break;
     case $xp < 3258594: $level = 84; break;
     case $xp < 3597792: $level = 85; break;
     case $xp < 3972294: $level = 86; break;
     case $xp < 4385776: $level = 87; break;
     case $xp < 4842295: $level = 88; break;
     case $xp < 5346332: $level = 89; break;
     case $xp < 5902831: $level = 90; break;
     case $xp < 6517253: $level = 91; break;
     case $xp < 7195629: $level = 92; break;
     case $xp < 7944614: $level = 93; break;
     case $xp < 8771558: $level = 94; break;
     case $xp < 9684577: $level = 95; break;
     case $xp < 10692629: $level = 96; break;
     case $xp < 11805606: $level = 97; break;
     case $xp < 13034431: $level = 98; break;
     case $xp < PHP_INT_MAX: $level = 99; break;
     default: $level = 1; break;
   }

   return $level;
 }
}
