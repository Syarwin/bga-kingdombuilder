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
      'kbCards'   => $this->cards->getKbCards(),
      'board' => $this->board->getUiData(),
      'fplayers' => $this->playerManager->getUiData($currentPlayerId),
      'cancelMoveIds' => $this->log->getCancelMoveIds(),
      'locations' => $this->locations,
    ];
  }

  /*
   * getGameProgression:
   *  Compute and return the current game progression approximation
   *  This method is called each time we are in a game state with the "updateGameProgression" property set to true
   */
  public function getGameProgression()
  {
		// TODO
//    return count($this->board->getPlacedPieces()) / 100;
return 0.3;
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


  /*
   * announceWin: TODO
   *
  public function announceWin($playerId, $win = true)
  {
    $players = $win ? $this->playerManager->getTeammates($playerId) : $this->playerManager->getOpponents($playerId);
    if (count($players) == 2) {
      self::notifyAllPlayers('message', clienttranslate('${player_name} and ${player_name2} win!'), [
        'player_name' => $players[0]->getName(),
        'player_name2' => $players[1]->getName(),
      ]);
    } else {
      self::notifyAllPlayers('message', clienttranslate('${player_name} wins!'), [
        'player_name' => $players[0]->getName(),
      ]);
    }
    self::DbQuery("UPDATE player SET player_score = 1 WHERE player_team = {$players[0]->getTeam()}");
    $this->gamestate->nextState('endgame');
  }
*/


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
      'fplayers' => $this->playerManager->getUiData(),
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
  /*
   * useTile: called when a player decide to a tile location
   */
  public function useTile($tileId)
  {
    $this->gamestate->nextState($this->playerManager->getPlayer()->useTile($tileId));
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
    $state = $this->powerManager->stateAfterSkipPower() ?: 'move';
    $this->gamestate->nextState($state);
  }



  /////////////////////////////////////////
  /////////////////////////////////////////
  /////////////    Build    ///////////////
  /////////////////////////////////////////
  /////////////////////////////////////////

  /*
   * argPlayerBuild: give the list of accessible unnocupied spaces for builds
   */
  public function argPlayerBuild()
  {
    $tile = $this->log->getCurrentTile();
    if(!is_null($tile)){

    }

    $player = $this->playerManager->getPlayer();
    $terrain = $player->getTerrain();
    $tiles = $player->getTilesInHand();
    $builds = $this->log->getLastBuilds();
    $arg = [
      'terrain' => $terrain,
      'terrainName' => $this->terrainNames[$terrain],
      'hexes' => $this->board->getAvailableHexes($terrain),
      'cancelable' => $this->log->getLastActions() != null,
      'tiles' => count($builds) == 0? $tiles : [],
    ];

    return $arg;
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

    $this->playerManager->getPlayer()->build($pos);

    $nextState = count($this->log->getLastBuilds()) == 3? "done" : "build";
    $this->gamestate->nextState($nextState);
  }


  /*
   * playerBuild: build a piece to a location on the board
   *  - obj $worker : the piece id we want to use to build
   *  - obj $space : the location and building type we want to build
   *
  public function playerBuild($worker, $space)
  {
    // Build piece
    $pId = self::getActivePlayerId();
    $type = 'lvl' . $space['arg'];
    self::DbQuery("INSERT INTO piece (`player_id`, `type`, `location`, `x`, `y`, `z`) VALUES ('$pId', '$type', 'board', '{$space['x']}', '{$space['y']}', '{$space['z']}') ");
    $this->log->addBuild($worker, $space);

    // Notify
    $piece = self::getObjectFromDB("SELECT * FROM piece ORDER BY id DESC LIMIT 1");
    self::notifyAllPlayers('blockBuilt', clienttranslate('${player_name} builds a ${piece_name} on ${level_name}'), [
      'i18n' => ['piece_name', 'level_name'],
      'player_name' => self::getActivePlayerName(),
      'piece' => $piece,
      'piece_name' => ($space['arg'] == 3) ? clienttranslate('dome') : clienttranslate('block'),
      'level_name' => $this->levelNames[intval($space['z'])],
    ]);
  }
*/

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
