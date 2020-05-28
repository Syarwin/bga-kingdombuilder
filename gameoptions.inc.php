<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * santorini implementation : © quietmint
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * santorini game options description
 *
 * In this file, you can define your game options (= game variants).
 *
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in santorini.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once("modules/constants.inc.php");

$game_options = [
  OPTION_SETUP => [
    'name' => totranslate('Assignment of Powers'),
    'values' => [
      BASIC => [
        'name' => totranslate('First game'),
      ],
      RANDOM => [
        'name' => totranslate('Random'),
        'tmdisplay' => totranslate('Random'),
        'description' => totranslate('Board and Builder cards are random'),
        'nobeginner' => true,
      ],
    ],
  ],
];
