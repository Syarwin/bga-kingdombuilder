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

require_once("modules/Utils.class.php");
require_once("modules/KingdomBuilderPlayer.class.php");
require_once("modules/KingdomBuilderLog.class.php");
require_once("modules/KingdomBuilderBoard.class.php");
require_once("modules/KingdomBuilderCards.class.php");
require_once("modules/KingdomBuilderObjective.class.php");
foreach (KingdomBuilderCards::$objectiveClasses as $className) {
  require_once("modules/objectives/$className.class.php");
}
require_once("modules/KingdomBuilderPlayerManager.class.php");
require_once("modules/KingdomBuilderLocationManager.class.php");
require_once("modules/KingdomBuilderLocation.class.php");
foreach (KingdomBuilderLocationManager::$classes as $className) {
  require_once("modules/locations/$className.class.php");
}

$this->terrainNames = [
   HEX_GRASS => clienttranslate('a Grass'),
   HEX_CANYON => clienttranslate('a Canyon'),
   HEX_DESERT => clienttranslate('a Desert'),
   HEX_FLOWER => clienttranslate('a Flower Field'),
   HEX_FOREST => clienttranslate('a Forest'),
];
