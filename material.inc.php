<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * KingdomBuilder implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * KingdomBuilder game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

 $this->terrainNames = [
   clienttranslate('Grass'),
   clienttranslate('Canyon'),
   clienttranslate('Desert'),
   clienttranslate('Flower Field'),
   clienttranslate('Forest'),
];

$this->locations = [
  HEX_ORACLE => [
    'name' => clienttranslate('Oracle'),
    'desc' => [
      clienttranslate('Build one settlement on a hex of the same terrain type as your played terrain card.'),
      clienttranslate('Build adjacent if possible.'),
    ],
  ],
  HEX_FARM => [
    'name' => clienttranslate('Farm'),
    'desc' => [
      clienttranslate('Build one settlement on a grass hex.'),
      clienttranslate('Build adjacent if possible.'),
    ],
  ],
  HEX_TAVERN => [
    'name' => clienttranslate('Tavern'),
    'desc' => [
      clienttranslate('Build one settlement at one end of a line of at least 3 of your own settlements.'),
      clienttranslate('The orientation of the line does not matter (horizontally or diagonally).'),
      clienttranslate('The chosen hex must be suitable for building.'),
    ],
  ],
  HEX_TOWER => [
    'name' => clienttranslate('Tower'),
    'desc' => [
      clienttranslate('Build one settlement at the edge of the game board.'),
      clienttranslate('Choose any of the 5 suitable terrain type hexes.'),
      clienttranslate('Build adjacent if possible.'),
    ],
  ],
  HEX_HARBOR => [
    'name' => clienttranslate('Harbor'),
    'desc' => [
      clienttranslate('Move any one of your existing settlements to a water hex.'),
      clienttranslate('Build adjacent if possible.'),
      clienttranslate('This is the only way to build settlements on water hexes.'),
    ],
  ],
  HEX_PADDOCK => [
    'name' => clienttranslate('Paddock'),
    'desc' => [
      clienttranslate('Move any one of your existing settlements two hexes in a straight line in any direction (horizontally or diagonally) to an eligible hex.'),
      clienttranslate('You may jump across any terrain type hex, even water, mountain, castle and location, and/or your own and other players’ settlements.'),
      clienttranslate('The target hex must not necessarily be adjacent to one of your own settlement.'),
    ],
  ],
  HEX_OASIS => [
    'name' => clienttranslate('Oasis'),
    'desc' => [
      clienttranslate('Build one settlement on a desert hex.'),
      clienttranslate('Build adjacent if possible.'),
    ],
  ],
  HEX_BARN => [
    'name' => clienttranslate('Barn'),
    'desc' => [
      clienttranslate('Move any one of your existing settlements to a hex of the same terrain type as your played terrain card.'),
      clienttranslate('Build adjacent if possible.'),
    ],
  ],
];


$this->kbcards = [
  [
    'name' => 'Fishermen',
    'short' => clienttranslate("Build settlements on the waterfront"),
    'points' => clienttranslate("1 gold for each of your own settlements built adjacents to one or more water hexes")
  ],
  [
    'name' => 'Merchants',
    'short' => clienttranslate("Connect location and castle hexes"),
    'points' => clienttranslate("4 gold for each location and/or castle hex linked contiguously by your own settlements to other locations and/or castle hexes")
  ],
  [
    'name' => 'Discoverers',
    'short' => clienttranslate("Build settlements on many horizontal lines"),
    'points' => clienttranslate("1 gold for each horizontal line on which you have built at least one of your own settlement")
  ],
  [
    'name' => 'Hermits',
    'short' => clienttranslate("Create many settlements area"),
    'points' => clienttranslate("1 gold for each of your own separate settlement and for each separate settlement area")
  ],
  [
    'name' => 'Citizens',
    'short' => clienttranslate("Create a large settlement area"),
    'points' => clienttranslate("1 gold for every 2 of your own settlements in your largest own settlement area")
  ],
  [
    'name' => 'Miners',
    'short' => clienttranslate("Build settlements next to a mountain"),
    'points' => clienttranslate("1 gold for each of your own settlements built adjacents to one or more mountain hexes")
  ],
  [
    'name' => 'Workers',
    'short' => clienttranslate("Build settlements next to location or castle hex"),
    'points' => clienttranslate("1 gold for each of your own settlements built adjacents to a location or castle hex")
  ],
  [
    'name' => 'Knights',
    'short' => clienttranslate("Build many settlements on one horizontal line"),
    'points' => clienttranslate("2 gold for each of your own settlements built on that horizontal line with the most of your own settlements")
  ],
  [
    'name' => 'Lords',
    'short' => clienttranslate("Build the most settlements in each sector"),
    'points' => clienttranslate("Each sector : 12 gold for maximum number of settlements there, 6 golds for the next highest number of settlements")
  ],
  [
    'name' => 'Farmers',
    'short' => clienttranslate("Build settlements in all sectors"),
    'points' => clienttranslate("3 gold for each of your own settlements in that sector with the fewest of your own settlements")
  ],
];
