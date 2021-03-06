<?php

/*
 * KingdomBuilderLocationManager : allow to easily create and apply powers during play
 */
class KingdomBuilderLocationManager extends APP_GameClass
{
  public static $classes = [
    HEX_BARN => 'Barn',
    HEX_FARM => 'Farm',
    HEX_HARBOR => 'Harbor',
    HEX_OASIS => 'Oasis',
    HEX_ORACLE => 'Oracle',
    HEX_PADDOCK => 'Paddock',
    HEX_TAVERN => 'Tavern',
    HEX_TOWER => 'Tower',
  ];

  public function getLocationTypes()
  {
    return array_merge([HEX_CASTLE], array_keys(self::$classes));
  }

  public $game;
  public function __construct($game)
  {
    $this->game = $game;
  }

  /*
   * getLocation: factory function to create a location by ID
   */
  public function getLocation($locationId, $playerId = null)
  {
    if (!isset(self::$classes[$locationId])) {
      throw new BgaVisibleSystemException("Location $locationId is not implemented ($playerId)");
    }
    return new self::$classes[$locationId]($this->game, $playerId);
  }

  /*
   * getLocations: return all locations (even those not available in this game)
   */
  public function getLocations()
  {
    return array_map(function ($locationId) {
      return $this->getLocation($locationId);
    }, array_keys(self::$classes));
  }


  /*
   * getUiData : get all ui data of all locations : id, name, text
   */
  public function getUiData()
  {
    $ui = [];
    foreach ($this->getLocations() as $location) {
      $ui[$location->getId()] = $location->getUiData();
    }
    return $ui;
  }


  public function obtainTile($pos, $playerId = null)
  {
    $playerId = $playerId ?? $this->game->getActivePlayerId();

    $locations = $this->game->board->getNeighboursIntersect($pos, $this->game->board->getLocations());
    if(empty($locations))
      return;

    // Already obtained a tile from this location before ?
    $location = $locations[0];
    $tile = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$playerId} AND type = 'tile' AND x = {$location['x']} AND y = {$location['y']} LIMIT 1");
    if(!is_null($tile))
      return;

    // Fetch a tile from the position, if any remeaning
    $tile = self::getObjectFromDB("SELECT * FROM piece WHERE type = 'tile' AND x = {$location['x']} AND y = {$location['y']} AND location = 'board' LIMIT 1");
    if(is_null($tile))
      return;

    // Put the tile in the player 'pending' location => will be put in hand at the beggining of next player's turn
    self::DbQuery("UPDATE piece SET player_id = {$playerId}, location = 'pending' WHERE id = {$tile['id']}");
    $this->game->log->addObtainTile($tile);

    $location = $this->getLocation($tile['type_arg'], $playerId);
    $this->game->notifyAllPlayers('obtainTile', clienttranslate('${player_name} obtains a location tile : ${location_name}'), [
      'i18n' => ['location_name'],
      'player_name' => $this->game->playerManager->getPlayer($playerId)->getName(),
      'location_name' => $location->getName(),
      'location' => $location->getId(),
      'status' => 'pending',
      'player_id' => $playerId,
      'x' => $tile['x'],
      'y' => $tile['y'],
      'id' => $tile['id'],
    ]);
  }

  public function looseTile($pos, $playerId = null)
  {
    $playerId = $playerId ?? $this->game->getActivePlayerId();

    $locations = $this->game->board->getNeighboursIntersect($pos, $this->game->board->getLocations());
    if(empty($locations))
      return;

      // Still next to the locations ?
    $location = $locations[0];
    $settlements = $this->game->board->getNeighboursIntersect($location, $this->game->board->getPlacedSettlementsCoords($this->game->getActivePlayerId()));
    if(!empty($settlements))
      return;

    // Already obtained a tile from this location before ?
    $tile = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$playerId} AND type = 'tile' AND location != 'box' AND x = {$location['x']} AND y = {$location['y']}");
    if(is_null($tile))
      return;

    // If it was unused, then mark the potential other tile of same location unused before discarding
    if($tile['location'] == "hand"){
      $tile2 = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$playerId} AND type = 'tile' AND location != 'box' AND type_arg = {$tile['type_arg']} AND id != {$tile['id']}");
      if(!is_null($tile2) && $tile2['location' == "pending"]){
        self::DbQuery("UPDATE piece SET location = 'hand' WHERE id = {$tile2['id']} ");
        $this->game->log->addSwitchTile($tile2);
      }
    }

    // Put this tile to the box
    self::DbQuery("UPDATE piece SET location = 'box' WHERE id = {$tile['id']}");
    $this->game->log->addLoseTile($tile);

    $location = $this->getLocation($tile['type_arg'], $playerId);
    $this->game->notifyAllPlayers('loseTile', clienttranslate('${player_name} loses a location tile : ${location_name}'), [
      'i18n' => ['location_name'],
      'player_name' => $this->game->playerManager->getPlayer($playerId)->getName(),
      'location_name' => $location->getName(),
      'player_id' => $playerId,
      'id' => $tile['id'],
    ]);
  }



  public function useTile($tileId, $playerId = null)
  {
    $playerId = $playerId ?? $this->game->getActivePlayerId();
    $tile = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$playerId} AND type = 'tile' AND id = $tileId LIMIT 1");
    if(is_null($tile))
      throw new BgaUserException(_("This tile does not belong to you"));

    // Update DB
    $location = $this->getLocation($tile['type_arg'], $playerId);
    self::DbQuery("UPDATE piece SET location = 'pending' WHERE id = {$tileId}");
    $this->game->log->addUseTile($tile);
    $this->game->notifyAllPlayers('useTile', clienttranslate('${player_name} uses a location tile : ${location_name}'), [
      'i18n' => ['location_name'],
      'player_name' => $this->game->playerManager->getPlayer($playerId)->getName(),
      'location_name' => $location->getName(),
      'player_id' => $playerId,
      'id' => $tile['id'],
    ]);


    $state = $location->stateTile();
    if(is_null($state))
      throw new BgaUserException(_("Don't know what is the new state to use this tile"));
    return $state;
  }



  public function getActiveLocation($playerId = null)
  {
    $tile = $this->game->log->getCurrentTile();
    if(is_null($tile))
      return null;

    $playerId = $playerId ?? $this->game->getActivePlayerId();
    return $this->getLocation($tile['location'], $playerId);
  }

  public function argPlayerBuild()
  {
    return $this->getActiveLocation()->argPlayerBuild();
  }


  public function argPlayerMove()
  {
    return $this->getActiveLocation()->argPlayerMove();
  }


  public function argPlayerMoveTarget($space)
  {
    self::DbQuery("UPDATE piece SET location = 'pending' WHERE type = 'settlement' AND location = 'board' AND x = {$space['x']} AND y = {$space['y']}");
    $arg = [
      'hexes' => $this->getActiveLocation()->argPlayerMoveTarget($space),
    ];
    self::DbQuery("UPDATE piece SET location = 'board' WHERE type = 'settlement' AND location = 'pending' AND x = {$space['x']} AND y = {$space['y']}");
    return $arg;
  }

  public function playerMoveSelect($space)
  {
    $this->game->notifyPlayer($this->game->getActivePlayerId(), 'argPlayerMoveTarget', '', $this->argPlayerMoveTarget($space));
  }
}
