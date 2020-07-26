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
 * stats.inc.php
 *
 * KingdomBuilder game statistics description
 *
 */

require_once('modules/constants.inc.php');
require_once("modules/KingdomBuilderCards.class.php");
require_once("modules/KingdomBuilderObjective.class.php");

$stats_type = [
     // Statistics global to table
     'table' => [
         'move' => [
             'id' => STAT_MOVE,
             'name' => totranslate('Moves'),
             'type' => 'int'
         ],
         'build' => [
             'id' => STAT_BUILD,
             'name' => totranslate('Settlements built'),
             'type' => 'int'
         ],

         'grass' => [
             'id' => STAT_GRASS,
             'name' => totranslate('Grass cards drawn'),
             'type' => 'int'
         ],
         'canyon' => [
             'id' => STAT_CANYON,
             'name' => totranslate('Canyon cards drawn'),
             'type' => 'int'
         ],
         'desert' => [
             'id' => STAT_DESERT,
             'name' => totranslate('Desert cards drawn'),
             'type' => 'int'
         ],
         'flower' => [
             'id' => STAT_FLOWER,
             'name' => totranslate('Flower cards drawn'),
             'type' => 'int'
         ],
         'forest' => [
             'id' => STAT_FOREST,
             'name' => totranslate('Forest cards drawn'),
             'type' => 'int'
         ],
     ],

     // Statistics existing for each player
     'player' => [
         'obtainTile' => [
             'id' => STAT_OBTAIN_TILE,
             'name' => totranslate('Tile acquired'),
             'type' => 'int'
         ],
         'useTile' => [
             'id' => STAT_USE_TILE,
             'name' => totranslate('Tile uses'),
             'type' => 'int'
         ],
         'move' => [
             'id' => STAT_MOVE,
             'name' => totranslate('Settlements moved'),
             'type' => 'int'
         ],
         'build' => [
             'id' => STAT_BUILD,
             'name' => totranslate('Settlements built'),
             'type' => 'int'
         ],
         'largestBuild' => [
             'id' => STAT_LARGEST_BUILD,
             'name' => totranslate('Largest settlement area'),
             'type' => 'int'
         ],

         'grass' => [
             'id' => STAT_GRASS,
             'name' => totranslate('Grass cards drawn'),
             'type' => 'int'
         ],
         'canyon' => [
             'id' => STAT_CANYON,
             'name' => totranslate('Canyon cards drawn'),
             'type' => 'int'
         ],
         'desert' => [
             'id' => STAT_DESERT,
             'name' => totranslate('Desert cards drawn'),
             'type' => 'int'
         ],
         'flower' => [
             'id' => STAT_FLOWER,
             'name' => totranslate('Flower cards drawn'),
             'type' => 'int'
         ],
         'forest' => [
             'id' => STAT_FOREST,
             'name' => totranslate('Forest cards drawn'),
             'type' => 'int'
         ],
     ],
];

foreach (KingdomBuilderCards::$objectiveClasses as $file) {
  $className = "Objective".$file;
  require_once("modules/objectives/$file.class.php");
  $class = new $className(null);
  $stats_type['player'][$class->getName()] = [
    'id' => $class->getStatId(),
    'name' => $class->getStatName(),
    'type' => 'int',
  ];
}
