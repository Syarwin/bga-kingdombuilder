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
