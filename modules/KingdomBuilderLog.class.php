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
    public function insert($playerId, $pieceId, $action, $args)
    {
      $playerId = $playerId == -1 ? $this->game->getActivePlayerId() : $playerId;
      $round = $this->game->getGameStateValue("currentRound");
      $actionArgs = is_array($args) ? json_encode($args) : $args;
      self::DbQuery("INSERT INTO log (`round`, `player_id`, `piece_id`, `action`, `action_arg`) VALUES ('$round', '$playerId', '$pieceId', '$action', '$actionArgs')");
    }

    /*
     * initBoard: TODO
     */
    public function initBoard($quadrants)
    {
      $this->insert(0, null, 'initBoard', $quadrants);
    }



    /*
     * starTurn: TODO
     */
    public function startTurn()
    {
      $this->insert(-1, 0, 'startTurn', '{}');
    }


    /*
     * addBuild: add a new build entry to log
     *
    public function addBuild($piece, $space)
    {
      $this->addWork($piece, $space, 'build');
    }
  */

    /*
     * addAction: add a new action to log
     */
    public function addAction($action, $args = '{}')
    {
      $this->insert(-1, 0, $action, $args);
    }


    /////////////////////////////////
    /////////////////////////////////
    //////////   Getters   //////////
    /////////////////////////////////
    /////////////////////////////////
    public function getQuadrants()
    {
      $log = self::getObjectFromDb("SELECT * FROM log WHERE `action` = 'initBoard'");
      return json_decode($log['action_arg'], true);
    }

}
