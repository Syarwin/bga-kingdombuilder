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
  * KingdomBuilder.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );
require_once('modules/constants.inc.php');
require_once("modules/KingdomBuilderBoard.class.php");
require_once("modules/KingdomBuilderCards.class.php");
require_once("modules/KingdomBuilderObjective.class.php");
require_once("modules/KingdomBuilderLog.class.php");
require_once("modules/KingdomBuilderPlayerManager.class.php");


class kingdombuilder extends Table
{
  public function __construct()
  {
    parent::__construct();
    self::initGameStateLabels([
      'optionSetup'  => OPTION_SETUP,
      'currentRound' => CURRENT_ROUND,
      'firstPlayer'  => FIRST_PLAYER,
    ]);

    // Initialize terrains cards
//    $this->cards = self::getNew('module.common.deck');
//    $this->cards->init('card');

    // Initialize logger, board and cards
    $this->log   = new KingdomBuilderLog($this);
    $this->board = new KingdomBuilderBoard($this);
    $this->cards = new KingdomBuilderCards($this);
    $this->playerManager = new KingdomBuilderPlayerManager($this);
    $this->locationManager = new KingdomBuilderLocationManager($this);
  }

  protected function getGameName()
  {
		return "kingdombuilder";
  }


  /*
   * setupNewGame:
   *  This method is called only once, when a new game is launched.
   * params:
   *  - array $players
   *  - mixed $options
   */
  protected function setupNewGame($players, $options = [])
  {
		// Initialize board and cards
    $optionSetup = intval(self::getGameStateValue('optionSetup'));
		$this->board->setupNewGame($optionSetup);
    $this->cards->setupNewGame($players, $optionSetup);

    // Initialize players (and settlements)
    $this->playerManager->setupNewGame($players);

    // Active first player to play
    $pId = $this->activeNextPlayer();
    self::setGameStateInitialValue('firstPlayer', $pId);
    self::setGameStateInitialValue('currentRound', 0);
  }

  /*
   * getAllDatas:
   *  Gather all informations about current game situation (visible by the current player).
   *  The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
   */
  protected function getAllDatas()
  {
    $currentPlayerId = self::getCurrentPlayerId();
    return [
      'quadrants' => $this->board->getQuadrants(),
      'objectives' => $this->cards->getUiData(),
      'board' => $this->board->getUiData(),
      'fplayers' => $this->playerManager->getUiData($currentPlayerId),
      'firstPlayer' => self::getGamestateValue("firstPlayer"),
      'cancelMoveIds' => $this->log->getCancelMoveIds(),
      'locations' => $this->locationManager->getUiData(),
    ];
  }

  /*
   * getGameProgression:
   *  Compute and return the current game progression approximation
   *  This method is called each time we are in a game state with the "updateGameProgression" property set to true
   */
  public function getGameProgression()
  {
    $total = 40 * $this->playerManager->getPlayerCount();
    $placed = count($this->board->getPlacedSettlements());
    return (int) $placed * 100 / $total;
  }



  ////////////////////////////////////////////////
  ////////////   Next player / Win   ////////////
  ////////////////////////////////////////////////

  /*
   * stNextPlayer: go to next player
   */
  public function stNextPlayer()
  {
    $pId = $this->activeNextPlayer();
    self::giveExtraTime($pId);
    if (self::getGamestateValue("firstPlayer") == $pId) {
      $n = (int) self::getGamestateValue('currentRound') + 1;
      self::setGamestateValue("currentRound", $n);

      if($this->log->isLastTurn()){
        $this->gamestate->nextState('endgame');
        return;
      }
    }

    $this->gamestate->nextState('start');
  }

  /*
   * stStartOfTurn: called at the beggining of each player turn
   */
  public function stStartOfTurn()
  {
    $this->log->startTurn();
    $this->playerManager->getPlayer()->startOfTurn();
    $this->gamestate->nextState("build");
  }


  /*
   * stEndOfTurn: called at the end of each player turn
   */
  public function stEndOfTurn()
  {
    $this->playerManager->getPlayer()->endOfTurn();
    $this->stCheckEndOfGame();
    $this->gamestate->nextState('next');
  }

  /*
   * stCheckEndOfGame: check if the game is finished
   */
  public function stCheckEndOfGame()
  {
		return false;
  }

  public function stScoringEnd()
  {
    $this->cards->getObjective(CASTLE)->scoringEnd();
    foreach($this->cards->getObjectives() as $objective){
      $objective->scoringEnd();
    }

    // TODO : tie breaker
    $this->gamestate->nextState("endgame");
  }


  /*
   * cancelPreviousWorks: called when a player decide to go back at the beggining of the turn
   */
  public function cancelPreviousWorks()
  {
    self::checkAction('cancel');

    if ($this->log->getLastActions() == null) {
      throw new BgaUserException(_("You have nothing to cancel"));
    }

    // Undo the turn
    $moveIds = $this->log->cancelTurn();
    self::notifyAllPlayers('cancel', clienttranslate('${player_name} restarts their turn'), [
      'player_name' => self::getActivePlayerName(),
      'moveIds' => $moveIds,
      'board' => $this->board->getUiData(),
      'fplayers' => $this->playerManager->getUiData(self::getCurrentPlayerId()),
    ]);

    // Apply power
    $this->gamestate->nextState('cancel');
  }

  /*
   * confirmTurn: called whenever a player confirm their turn
   */
  public function confirmTurn()
  {
    $this->gamestate->nextState('confirm');
  }


  ////////////////////////////////////////
  ////////////////////////////////////////
  ///////////    UseTile    //////////////
  ////////////////////////////////////////
  ////////////////////////////////////////
  public function argUseTile()
  {
    return [
      'tiles' => $this->playerManager->getPlayer()->getTilesInHand(),
      'skippable' => true,
    ];
  }

  public function skip()
  {
    $this->gamestate->nextState('skip');
  }

  /*
   * useTile: called when a player decide to a tile location
   */
  public function useTile($tileId)
  {
    $this->gamestate->nextState($this->locationManager->useTile($tileId));
  }


  /*
   * skip: called when a player decide to skip the use of power
   */
  public function skipPower()
  {
    self::checkAction('skip');

    $args = $this->gamestate->state()['args'];
    if (!$args['skippable']) {
      throw new BgaUserException(_("You can't skip this action"));
    }
    $this->log->addAction("skippedPower");

    // Apply power
    $state = $this->powerManager->stateAfterSkipPower() ?? 'move';
    $this->gamestate->nextState($state);
  }


  /*
   * argPlayerMove: give the list of settlements that can move
   */
  public function argPlayerMove()
  {
    return $this->locationManager->argPlayerMove();
  }

  /*
	 * Build : TODO
   */
  public function playerMove($from, $to)
  {
    // Check if work is possible
    self::checkAction('move');
    $arg = $this->argPlayerMove();
    if(!in_array($from, $arg['hexes']))
      throw new BgaUserException(_("You cannot move this settlement"));

    $arg2 = $this->locationManager->argPlayerMoveTarget($from);
    if(!in_array($to, $arg2['hexes']))
      throw new BgaUserException(_("You cannot move this settlement here"));

    $this->playerManager->getPlayer()->move($from, $to);

    $nextState = count($this->log->getLastBuilds()) == 3? "done" : "build";
    if($nextState == "done" && $this->playerManager->hasTile())
      $nextState = "useTile";
    $this->gamestate->nextState($nextState);
  }


  /////////////////////////////////////////
  /////////////////////////////////////////
  /////////////    Build    ///////////////
  /////////////////////////////////////////
  /////////////////////////////////////////

  public function argPlayerBuildAux($terrain = null)
  {
    $player = $this->playerManager->getPlayer();
    $terrain = $terrain ?? $player->getTerrain();
    $tiles = $player->getTilesInHand();
    $location = $this->locationManager->getActiveLocation();
    $nbr = count($this->log->getLastBuilds());

    return [
      'terrain' => $terrain,
      'terrainName' => $this->terrainNames[$terrain],
      'hexes' => $this->board->getAvailableHexes($terrain),
      'cancelable' => $this->log->getLastActions() != null,
      'nbr' => "(". ($nbr + 1)."/3)",
      'tiles' => ($nbr == 0 && is_null($location))? $tiles : [],

      'tileName' => is_null($location)? '' : $location->getName(),
    ];
  }

  /*
   * argPlayerBuild: give the list of accessible unnocupied spaces for builds
   */
  public function argPlayerBuild()
  {
    // Not using a tile => classic build
    if(is_null($this->locationManager->getActiveLocation()))
      return $this->argPlayerBuildAux();
    else
      return $this->locationManager->argPlayerBuild();
  }


  /*
   * stBeforeWork: Check if a build is possible
   */
  public function stBeforeBuild()
  {
/*
    if ($this->stCheckEndOfGame()) {
      return;
    }
*/
  }


  /*
	 * Build : TODO
   */
  public function playerBuild($pos)
  {
    // Check if work is possible
    self::checkAction('build');
    $arg = $this->argPlayerBuild();
    if(!in_array($pos, $arg['hexes']))
      throw new BgaUserException(_("You cannot build here"));

    $player = $this->playerManager->getPlayer();
    $player->build($pos);

    $nextState = (count($this->log->getLastBuilds()) == 3 || $player->getSettlementsInHand() == 0)? "done" : "build";
    if($nextState == "done" && $this->playerManager->hasTile())
      $nextState = "useTile";
    $this->gamestate->nextState($nextState);
  }

  ////////////////////////////////////
  ////////////   Zombie   ////////////
  ////////////////////////////////////
  /*
   * zombieTurn:
   *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
   *   You can do whatever you want in order to make sure the turn of this player ends appropriately
   */
  public function zombieTurn($state, $activePlayer)
  {
    if (array_key_exists('zombiePass', $state['transitions'])) {
      $this->playerManager->eliminate($activePlayer);
      $this->gamestate->nextState('zombiePass');
    } else {
      throw new BgaVisibleSystemException('Zombie player ' . $activePlayer . ' stuck in unexpected state ' . $state['name']);
    }
  }

  /////////////////////////////////////
  //////////   DB upgrade   ///////////
  /////////////////////////////////////
  // You don't have to care about this until your game has been published on BGA.
  // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
  // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
  //   update the game database and allow the game to continue to run with your new version.
  /////////////////////////////////////
  /*
   * upgradeTableDb
   *  - int $from_version : current version of this game database, in numerical form.
   *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
   */
  public function upgradeTableDb($from_version)
  {
  }
}
