<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * santorini implementation : © Emmanuel Colin <ecolin@boardgamearena.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * KingdomBuilder game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!


$machinestates = [
  /*
   * BGA framework initial state. Do not modify.
   */
  ST_GAME_SETUP => [
    'name' => 'gameSetup',
    'description' => '',
    'type' => 'manager',
    'action' => 'stGameSetup',
    'transitions' => [
      '' => ST_NEXT_PLAYER,
    ],
  ],


  ST_NEXT_PLAYER => [
    'name' => 'nextPlayer',
    'description' => '',
    'type' => 'game',
    'action' => 'stNextPlayer',
    'transitions' => [
      'next' => ST_NEXT_PLAYER,
      'start' => ST_START_OF_TURN,
      'endgame' => ST_GAME_END,
    ],
    'updateGameProgression' => true,
  ],

  ST_START_OF_TURN => [
    'name' => 'startOfTurn',
    'description' => '',
    'type' => 'game',
    'action' => 'stStartOfTurn',
    'transitions' => [
      'build'  => ST_BUILD,
      'endgame' => ST_GAME_END,
    ],
  ],

  ST_USE_TILE => [
    'name' => 'playerUseTile',
    'description' => clienttranslate('${actplayer} may use a location tile'),
    'descriptionmyturn' => clienttranslate('${you} may use a location tile'),
    'type' => 'activeplayer',
    'args' => 'argUseTile',
    'possibleactions' => [ 'use', 'skip' ],
    'transitions' => [
      'next' => ST_NEXT_PLAYER,
      'build' => ST_BUILD,
      'endgame' => ST_GAME_END,
    ],
  ],


  ST_MOVE => [
    'name' => 'playerMove',
    'description' => clienttranslate('${actplayer} must move an existing settlement'),
    'descriptionmyturn' => clienttranslate('${you} must move an existing settlement'),
    'type' => 'activeplayer',
    'args' => 'argMove',
    'possibleactions' => [ 'move', 'cancel' ],
    'transitions' => [
      'next' => ST_NEXT_PLAYER,
      'build' => ST_BUILD,
      'cancel' => ST_START_OF_TURN,
    ],
  ],

  ST_BUILD => [
    'name' => 'playerBuild',
    'description' => clienttranslate('${actplayer} must build on a ${terrainName}'),
    'descriptionmyturn' => clienttranslate('${you} must build on a ${terrainName}'),
    'descriptiontile' => clienttranslate('${tileName} : ${actplayer} must build on a ${terrainName}'),
    'descriptiontilemyturn' => clienttranslate('${tileName} : ${you} must build on a ${terrainName}'),
    'type' => 'activeplayer',
    'args' => 'argPlayerBuild',
    'action' => 'stBeforeBuild',
    'possibleactions' => ['build', 'cancel', 'skip', 'endgame'],
    'transitions' => [
      'zombiePass' => ST_END_OF_TURN,
      'endturn'    => ST_PRE_END_OF_TURN,
      'endgame'    => ST_GAME_END,
      'build'      => ST_BUILD,
      'move'       => ST_MOVE,
      'done'       => ST_PRE_END_OF_TURN,
      'useTile'    => ST_USE_TILE,
      'cancel'     => ST_START_OF_TURN,
    ],
  ],

  ST_PRE_END_OF_TURN => [
    'name' => 'confirmTurn',
    'description' => clienttranslate('${actplayer} must confirm or restart their turn'),
    'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
    'type' => 'activeplayer',
    'possibleactions' => ['confirm','cancel'],
    'transitions' => [
      'zombiePass' => ST_END_OF_TURN,
      'endturn'    => ST_END_OF_TURN,
      'confirm'    => ST_END_OF_TURN,
      'cancel'     => ST_START_OF_TURN,
    ],
  ],


  ST_END_OF_TURN => [
    'name' => 'endOfTurn',
    'description' => '',
    'type' => 'game',
    'action' => 'stEndOfTurn',
    'transitions' => [
      'next' => ST_NEXT_PLAYER,
      'endgame' => ST_GAME_END,
    ],
  ],

  /*
   * BGA framework final state. Do not modify.
   */
  ST_GAME_END => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd'
  ]

];
