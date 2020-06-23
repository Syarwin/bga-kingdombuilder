<?php

/*
 * KingdomBuilderPlayer: all utility functions concerning a player
 */
class KingdomBuilderPlayer extends APP_GameClass
{
  private $game;
  private $id;
  private $no; // natural order
  private $name;
  private $color;
  private $eliminated = false;
  private $zombie = false;

  public function __construct($game, $row)
  {
    $this->game = $game;
    $this->id = (int) $row['id'];
    $this->no = (int) $row['no'];
    $this->name = $row['name'];
    $this->color = $row['color'];
    $this->eliminated = $row['eliminated'] == 1;
    $this->zombie = $row['zombie'] == 1;
  }


  public function setupNewGame()
  {
    $sqlSettlements = 'INSERT INTO piece (player_id, location) VALUES ';
    $values = [];
    for($i = 0; $i < 40; $i++){
      $values[] = "('" . $this->id . "','hand')";
    }
    self::DbQuery($sqlSettlements . implode($values, ','));

    $this->drawTerrain();
  }


  public function getId(){ return $this->id; }
  public function getNo(){ return $this->no; }
  public function getName(){ return $this->name; }
  public function getColor(){ return $this->color; }
  public function isEliminated(){ return $this->eliminated; }
  public function isZombie(){ return $this->zombie; }
  public function getSettlements(){ return $this->game->board->getSettlements($this->id); }
  public function getSettlementsInHand()
  {
    return count(array_filter($this->getSettlements(), function($settlement){
      return $settlement['location'] == 'hand';
    }));
  }
  public function getTilesInHand()
  {
    return self::getObjectListFromDB("SELECT id, type_arg AS location, x, y, player_id FROM piece WHERE player_id = {$this->id} AND type = 'tile' AND location = 'hand'");
  }

  public function getUiData($currentPlayerId = null)
  {
    return [
      'id'        => $this->id,
      'no'        => $this->no,
      'name'      => $this->name,
      'color'     => $this->color,
      'settlements' => $this->getSettlementsInHand(),
      'tiles' => $this->getTilesInHand(),
      'terrain' => ($this->id == $currentPlayerId)? $this->getTerrain() : 'back',
    ];
  }


  public function startOfTurn()
  {
    // Make tiles obtained before available
    self::DbQuery("UPDATE piece SET location = 'hand' WHERE player_id = {$this->id} AND type = 'tile' AND location = 'pending'");

    // Show terrain card to everyone
    $this->game->notifyAllPlayers('showTerrain', '', ['pId' => $this->id, 'terrain' =>  $this->getTerrain() ]);
  }


  public function endOfTurn()
  {
    $this->game->notifyAllPlayers('showTerrain', '', ['pId' => $this->id, 'terrain' =>  'back']); // hide terrain
    $this->drawTerrain();
  }



  public function drawTerrain()
  {
    // Discard already owned card
    $terrain = $this->getTerrainCard();
    if($terrain !== false)
      $this->game->cards->terrains->playCard($terrain['id']);

    // Draw a terrain card
    $card = $this->game->cards->terrains->pickCard('deck', $this->id);
    $this->game->notifyPlayer($this->id, 'showTerrain', clienttranslate('At your next turn, you will be building on a ${terrainName}'), [
      'pId' => $this->id,
      'terrain' => $card['type'],
      'terrainName' => $this->game->terrainNames[$card['type']],
    ]);
  }

  public function getTerrainCard()
  {
    $cards = $this->game->cards->terrains->getPlayerHand($this->id);
    return reset($cards);
  }

  public function getTerrain()
  {
    return $this->getTerrainCard()['type'];
  }


  public function build($pos)
  {
    $settlement = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$this->id} AND location = 'hand' AND type = 'settlement' LIMIT 1");
    if(is_null($settlement))
      throw new BgaUserException(_("You have no more settlements left in your hand"));

    self::DbQuery("UPDATE piece SET x = {$pos['x']}, y = {$pos['y']}, location = 'board' WHERE id = {$settlement['id']}");
    $this->game->log->addBuild($settlement, $pos);
    $this->game->notifyAllPlayers('build', clienttranslate('${player_name} build a settlement'), [
      'player_name' => $this->getName(),
      'player_id' => $this->getId(),
      'x' => $pos['x'],
      'y' => $pos['y'],
    ]);

    // Obtains a tile ?
    $neighbours = $this->game->board->getNeighbours($pos);
    $locations = array_values(array_filter($this->game->board->getLocations(), function($location) use ($neighbours){
      return in_array($this->game->board->getCoords($location), $neighbours);
    }));
    if(count($locations) > 0){
      $location = $locations[0];
      // Already obtained a tile from this location before ?
      $tile = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$this->id} AND type = 'tile' AND x = {$location['x']} AND y = {$location['y']} LIMIT 1");
      if(is_null($tile)){
        $tile = self::getObjectFromDB("SELECT * FROM piece WHERE type = 'tile' AND x = {$location['x']} AND y = {$location['y']} LIMIT 1");
        self::DbQuery("UPDATE piece SET player_id = {$this->id}, location = 'pending' WHERE id = {$tile['id']}");
        $this->game->log->addObtainTile($tile);

        $this->game->notifyAllPlayers('obtainTile', clienttranslate('${player_name} obtains a location tile : ${location_name}'), [
          'i18n' => ['location_name'],
          'player_name' => $this->getName(),
          'location_name' => $this->game->locations[$tile['type_arg']]['name'],
          'location' => $tile['type_arg'],
          'player_id' => $this->getId(),
          'x' => $tile['x'],
          'y' => $tile['y'],
          'id' => $tile['id'],
        ]);
      }
    }
  }


  public function useTile($tileId)
  {
    $tile = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$this->id} AND type = 'tile' AND id = $tileId LIMIT 1");
    if(is_null($tile))
      throw new BgaUserException(_("This tile does not belong to you"));

    // Update DB
    self::DbQuery("UPDATE piece SET location = 'box' WHERE id = {$tileId}");
    $this->game->log->addUseTile($tile);
    $this->game->notifyAllPlayers('useTile', clienttranslate('${player_name} uses a location tile : ${location_name}'), [
      'i18n' => ['location_name'],
      'player_name' => $this->getName(),
      'location_name' => $this->game->locations[$tile['type_arg']]['name'],
      'player_id' => $this->getId(),
      'id' => $tile['id'],
    ]);

    $buildingLocations = [HEX_ORACLE, HEX_FARM, HEX_OASIS, HEX_TOWER, HEX_TAVERN];
    return in_array($tile['type_arg'], $buildingLocations)? "build" : "move";
  }
}
