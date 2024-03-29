<?php

/*
 * KingdomBuilderLog: a class that allows to log some actions
 *   and then fetch these actions latter
 */
class KingdomBuilderLog extends APP_GameClass
{
  public $game;
  public function __construct($game)
  {
    $this->game = $game;
  }


////////////////////////////////
////////////////////////////////
//////////   Stats   ///////////
////////////////////////////////
////////////////////////////////

  /*
   * initStats: initialize statistics to 0 at start of game
   */
  public function initStats($players)
  {
    $this->game->initStat('table', 'move', 0);
    $this->game->initStat('table', 'build', 0);

    $this->game->initStat('table', 'grass', 0);
    $this->game->initStat('table', 'canyon', 0);
    $this->game->initStat('table', 'desert', 0);
    $this->game->initStat('table', 'flower', 0);
    $this->game->initStat('table', 'forest', 0);

    foreach ($players as $pId => $player) {
      $this->game->initStat('player', 'obtainTile', 0, $pId);
      $this->game->initStat('player', 'useTile', 0, $pId);
      $this->game->initStat('player', 'move', 0, $pId);
      $this->game->initStat('player', 'build', 0, $pId);
      $this->game->initStat('player', 'largestBuild', 0, $pId);

      $this->game->initStat('player', 'grass', 0, $pId);
      $this->game->initStat('player', 'canyon', 0, $pId);
      $this->game->initStat('player', 'desert', 0, $pId);
      $this->game->initStat('player', 'flower', 0, $pId);
      $this->game->initStat('player', 'forest', 0, $pId);

      foreach($this->game->cards->getObjectives() as $objective){
        $this->game->initStat('player', $objective->getName(), 0, $pId);
      }
    }
  }

  /*
   * gameEndStats: compute end-of-game statistics
   */
  public function gameEndStats()
  {
//    $this->game->setStat($this->game->board->getCompleteTowerCount(), 'buildTower');
  }


  /*
   * incrementStats: adjust individual game statistics
   *   - array $stats: format is array of [ playerId, name, value ].
   *     example: [ ['table', 'move'], [23647584, 'move'], ... ]
   *       - playerId: the player ID for a player state, or 'table' for a table stat
   *       - name: the state name, such as 'move' or 'usePower'
   *       - value (optional): amount to add, defaults to 1
   *   - boolean $subtract: true if the values should be decremented
   */
  public function incrementStats($stats, $subtract = false)
  {
    // $this->game->notifyAllPlayers('message', "incrementStats: " . json_encode($stats, JSON_PRETTY_PRINT), []);
    foreach ($stats as $stat) {
      if (!is_array($stat)) {
        throw new BgaVisibleSystemException("incrementStats: Not an array");
      }

      $pId = $stat[0];
      if ($pId == 'table' || empty($pId)) {
        $pId = null;
      }

      $name = $stat[1];
      if (empty($name)) {
        throw new BgaVisibleSystemException("incrementStats: Missing name");
      }

      $value = 1;
      if (count($stat) > 2) {
        $value = $stat[2];
      }
      if ($subtract) {
        $value = $value * -1;
      }

      $this->game->incStat($value, $name, $pId);
    }
  }


////////////////////////////////
////////////////////////////////
//////////   Adders   //////////
////////////////////////////////
////////////////////////////////

  /*
   * insert: add a new log entry
   * params:
   *   - $playerId: the player who is making the action
   *   - $pieceId : the piece whose is making the action
   *   - string $action : the name of the action
   *   - array $args : action arguments (eg space)
   */
  public function insert($playerId, $pieceId, $action, $args = [], $stats = [])
  {
    $playerId = $playerId == -1 ? $this->game->getActivePlayerId() : $playerId;
    $moveId = self::getUniqueValueFromDB("SELECT `global_value` FROM `global` WHERE `global_id` = 3");
    $round = $this->game->getGameStateValue("currentRound");


    if ($action == 'move') {
      $stats[] = ['table','move'];
      $stats[] = [$playerId, 'move'];
    } else if (in_array($action, ['build','tileBuild'])) {
      $stats[] = ['table','build'];
      $stats[] = [$playerId, 'build'];
    } else if ($action == 'obtainTile') {
      $stats[] = [$playerId, 'obtainTile'];
    } else if ($action == 'useTile') {
      $stats[] = [$playerId, 'useTile'];
    }


    if (!empty($stats)) {
      $this->incrementStats($stats);
      $args['stats'] = $stats;
    }

    $actionArgs = json_encode($args);

    self::DbQuery("INSERT INTO log (`round`, `move_id`, `player_id`, `piece_id`, `action`, `action_arg`) VALUES ('$round', '$moveId', '$playerId', '$pieceId', '$action', '$actionArgs')");
  }


  /*
   * initBoard: store the quadrants in the log
   */
  public function initBoard($quadrants)
  {
    $this->insert(0, null, 'initBoard', $quadrants);
  }



  /*
   * starTurn: logged whenever a player start its turn, very useful to fetch last actions
   */
  public function startTurn()
  {
    $this->insert(-1, 0, 'startTurn');
  }


  /*
   * addBuild: add a new build entry to log
   */
  public function addBuild($piece, $space)
  {
    if(is_null($this->getCurrentTile()))
      $this->insert(-1, $piece['id'], 'build', $this->game->board->getCoords($space));
    else
      $this->insert(-1, $piece['id'], 'tileBuild', $this->game->board->getCoords($space));
  }

  /*
   * addMove: add a new build entry to log
   */
  public function addMove($piece, $space)
  {
    $this->insert(-1, $piece['id'], 'move', [
      'from' => $this->game->board->getCoords($piece),
      'to' => $this->game->board->getCoords($space)
    ]);
  }


  /*
   * addObtainTile: add a new build entry to log
   */
  public function addObtainTile($tile)
  {
    $this->insert(-1, $tile['id'], 'obtainTile');
  }

  /*
   * addObtainTile: add a new build entry to log
   */
  public function addUseTile($tile)
  {
    $this->insert(-1, $tile['id'], 'useTile');
  }

  /*
   * addLoseTile: add a new build entry to log
   */
  public function addLoseTile($tile)
  {
    $this->insert(-1, $tile['id'], 'loseTile', ['location' => $tile['location'] ]);
  }


  /*
   * addSwitchTile: add a new entry to log when a tile is switched before lost
   */
  public function addSwitchTile($tile)
  {
    $this->insert(-1, $tile['id'], 'switchTile', []);
  }


  /*
   * addAction: add a new action to log
   */
  public function addAction($action, $args = [])
  {
    $this->insert(-1, 0, $action, $args);
  }


  /*
   * lastTurn: logged whenever a player build its last settlement
   */
  public function lastTurn()
  {
    $this->insert(-1, 0, 'lastTurn');
  }


/////////////////////////////////
/////////////////////////////////
//////////   Getters   //////////
/////////////////////////////////
/////////////////////////////////
  /*
   * getQuadrants: fetch the quadrants of this game
   */
  public function getQuadrants()
  {
    $log = self::getObjectFromDb("SELECT * FROM log WHERE `action` = 'initBoard'");
    return json_decode($log['action_arg'], true);
  }


  /*
   * getLastBuild: fetch the last build of current player
   */
  public function getLastBuilds($pId = null, $limit = -1)
  {
    $pId = $pId ?? $this->game->getActivePlayerId();
    $limitClause = ($limit == -1) ? '' : "LIMIT $limit";
    $works = self::getObjectListFromDb("SELECT * FROM log WHERE `action` = 'build' AND `player_id` = '$pId' AND `round` = (SELECT round FROM log WHERE `player_id` = $pId AND `action` = 'startTurn' ORDER BY log_id DESC LIMIT 1) ORDER BY log_id DESC " . $limitClause);

    return array_map(function ($work) {
      return [
        'action' => $work['action'],
        'pieceId' => $work['piece_id'],
        'pos' => json_decode($work['action_arg'], true),
      ];
    }, $works);
  }


  /*
   * getLastActions : get works and actions of player (used to cancel previous action)
   */
  public function getLastActions($actions = ['build', 'useTile'], $pId = null, $offset = null)
  {
    $pId = $pId ?? $this->game->getActivePlayerId();
    $offset = $offset ?? 0;
    $actionsNames = "'" . implode("','", $actions) . "'";

    return self::getObjectListFromDb("SELECT * FROM log WHERE `action` IN ($actionsNames) AND `player_id` = '$pId' AND `round` = (SELECT round FROM log WHERE `player_id` = $pId AND `action` = 'startTurn' ORDER BY log_id DESC LIMIT 1) - $offset ORDER BY log_id DESC");
  }



  /*
   * getCurrentTile : get the current used tile if it exists
   */
  public function getCurrentTile()
  {
    $action = self::getObjectFromDb("SELECT * FROM log ORDER BY log_id DESC LIMIT 1");
    if(is_null($action) || $action['action'] != 'useTile')
      return null;
    else
      return $this->game->board->getTile($action["piece_id"]);
  }


  public function isLastTurn()
  {
    $action = self::getObjectFromDb("SELECT * FROM log WHERE `action` = 'lastTurn'");
    return is_null($action)? false : true;
  }

////////////////////////////////
////////////////////////////////
//////////   Cancel   //////////
////////////////////////////////
////////////////////////////////

  public function cancelLogs($logs, $pId)
  {
    $ids = [];
    $moveIds = [];
    foreach ($logs as $log) {
      $args = json_decode($log['action_arg'], true);

      switch($log['action']){
        // Build : remove piece from board
        case 'build':
        case 'tileBuild':
          self::DbQuery("UPDATE piece SET x = NULL, y = NULL, location = 'hand' WHERE id = {$log['piece_id']}");
          break;

        // ObtainTile : put tile back on board
        case 'obtainTile':
          self::DbQuery("UPDATE piece SET location = 'board', player_id = NULL WHERE id = {$log['piece_id']}");
          break;

        // LoseTile : put tile back on board
        case 'loseTile':
          self::DbQuery("UPDATE piece SET location = '{$args["location"]}' WHERE id = {$log['piece_id']}");
          break;

        // SwitchTile : put tile back on pending
        case 'switchTile':
          self::DbQuery("UPDATE piece SET location = 'pending' WHERE id = {$log['piece_id']}");
          break;

        // UseTile : put tile back in hand
        case 'useTile':
          self::DbQuery("UPDATE piece SET location = 'hand' WHERE id = {$log['piece_id']}");
          break;

        // move : move back
        case 'move':
          self::DbQuery("UPDATE piece SET x = {$args['from']['x']}, y = {$args['from']['y']} WHERE id = {$log['piece_id']}");
          break;
      }


      // Undo statistics
      if (array_key_exists('stats', $args)) {
        $this->incrementStats($args['stats'], -1);
      }

      $ids[] = intval($log['log_id']);
      if ($log['action'] != 'startTurn') {
        $moveIds[] = array_key_exists('move_id', $log)? intval($log['move_id']) : 0; // TODO remove the array_key_exists
      }
    }

    // Remove the logs
    self::DbQuery("DELETE FROM log WHERE `player_id` = '$pId' AND `log_id` IN (" . implode(',', $ids) . ")");

    // Cancel the game notifications
    self::DbQuery("UPDATE gamelog SET `cancel` = 1 WHERE `gamelog_move_id` IN (" . implode(',', $moveIds) . ")");
    return $moveIds;
  }

  /*
   * cancelTurn: cancel the last actions of active player of current turn
   */
  public function cancelTurn($limit = null)
  {
    $limitClause = is_null($limit)? "" : (" LIMIT ".$limit);
    $pId = $this->game->getActivePlayerId();
    $logs = self::getObjectListFromDb("SELECT * FROM log WHERE `player_id` = '$pId' AND `round` = (SELECT round FROM log WHERE `player_id` = $pId AND `action` = 'startTurn' ORDER BY log_id DESC LIMIT 1) ORDER BY log_id DESC".$limitClause);
    return $this->cancelLogs($logs, $pId);
  }


  public function cancelLastAction()
  {
    $pId = $this->game->getActivePlayerId();
    $action = self::getObjectFromDb("SELECT * FROM log WHERE `action` IN ('build', 'useTile') AND `player_id` = '$pId' AND `round` = (SELECT round FROM log WHERE `player_id` = $pId AND `action` = 'startTurn' ORDER BY log_id DESC LIMIT 1) ORDER BY log_id DESC LIMIT 1");
    $logs = self::getObjectListFromDb("SELECT * FROM log WHERE `player_id` = '$pId' AND log_id >= {$action['log_id']} ORDER BY log_id DESC");
    return $this->cancelLogs($logs, $pId);
  }



  /*
   * getCancelMoveIds : get all cancelled move IDs from BGA gamelog, used for styling the notifications on page reload
   */
  public function getCancelMoveIds()
  {
    $moveIds = self::getObjectListFromDb("SELECT `gamelog_move_id` FROM gamelog WHERE `cancel` = 1 ORDER BY 1", true);
    return array_map('intval', $moveIds);
  }
}
