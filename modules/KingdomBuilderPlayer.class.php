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
  private $score;
  private $eliminated = false;
  private $zombie = false;

  public function __construct($game, $row)
  {
    $this->game = $game;
    $this->id = (int) $row['id'];
    $this->no = (int) $row['no'];
    $this->name = $row['name'];
    $this->color = $row['color'];
    $this->score = $row['score'];
    $this->eliminated = $row['eliminated'] == 1;
    $this->zombie = $row['zombie'] == 1;
  }


  public function setupNewGame()
  {
    $sqlSettlements = 'INSERT INTO piece (player_id, location) VALUES ';
    $values = [];
    for ($i = 0; $i < 40; $i++) {
      $values[] = "('" . $this->id . "','hand')";
    }
    self::DbQuery($sqlSettlements . implode(',', $values));

    $this->drawTerrain();
  }


  public function getId()
  {
    return $this->id;
  }
  public function getNo()
  {
    return $this->no;
  }
  public function getName()
  {
    return $this->name;
  }
  public function getColor()
  {
    return $this->color;
  }
  public function getScore()
  {
    return $this->score;
  }
  public function isEliminated()
  {
    return $this->eliminated;
  }
  public function isZombie()
  {
    return $this->zombie;
  }
  public function getSettlements()
  {
    return $this->game->board->getSettlements($this->id);
  }
  public function getSettlementsInHand()
  {
    return count(array_filter($this->getSettlements(), function ($settlement) {
      return $settlement['location'] == 'hand';
    }));
  }
  public function getTilesInHand($alsoPending = false)
  {
    $locations = "'hand'" . ($alsoPending ? ", 'pending'" : "");
    return self::getObjectListFromDB("SELECT id, location as status, type_arg AS location, x, y, player_id FROM piece WHERE player_id = {$this->id} AND type = 'tile' AND location IN ($locations)");
  }
  public function getPlayableTilesInHand()
  {
    $tiles = $this->getTilesInHand();
    $tiles = array_values(array_filter($tiles, function ($tile) {
      return $this->game->locationManager->getLocation($tile['location'], $this->id)->isPlayable();
    }));
    return $tiles;
  }


  public function getUiData($currentPlayerId = null)
  {
    $colorToNo = [
      "ff0000" => 1, "008000" => 2, "0000ff" => 3, "ffa500" => 4, "ffffff" => 5,
      "e94190" => 6, "982fff" => 7, "72c3b1" => 8, "f07f16" => 9, "bdd002" => 10, "7b7b7b" => 11
    ];
    return [
      'id'        => $this->id,
      'no'        => $this->no,
      'cno'       => $colorToNo[$this->color],
      'name'      => $this->name,
      'color'     => $this->color,
      'settlements' => $this->getSettlementsInHand(),
      'tiles' => $this->getTilesInHand(true),
      'terrain' => ($this->id == $currentPlayerId || $this->game->getActivePlayerId() == $this->id) ? $this->getTerrain() : 'back',
    ];
  }


  public function startOfTurn()
  {
    // Make tiles obtained before available
    self::DbQuery("UPDATE piece SET location = 'hand' WHERE player_id = {$this->id} AND type = 'tile' AND location = 'pending'");
    $this->game->notifyAllPlayers('enableTiles', '', ['pId' => $this->id]);

    // Show terrain card to everyone
    $this->game->notifyAllPlayers('showTerrain', '', ['pId' => $this->id, 'terrain' =>  $this->getTerrain()]);
  }


  public function endOfTurn()
  {
    $this->game->notifyAllPlayers('showTerrain', '', ['pId' => $this->id, 'terrain' =>  'back']); // hide terrain
    $this->drawTerrain();
  }



  public function drawTerrain()
  {
    if ($this->game->log->isLastTurn())
      return;

    // Discard already owned card
    $terrain = $this->getTerrainCard();
    if ($terrain !== false)
      $this->game->cards->terrains->playCard($terrain['id']);

    // Draw a terrain card
    $card = $this->game->cards->terrains->pickCard('deck', $this->id);

    $stats = [HEX_GRASS => 'grass', HEX_CANYON => 'canyon', HEX_DESERT => 'desert', HEX_FLOWER => 'flower', HEX_FOREST => 'forest'];
    $statName = $stats[$card["type"]];
    $stats = [['table', $statName], [$this->getId(), $statName]];
    $this->game->log->incrementStats($stats);
    $this->game->notifyPlayer($this->id, 'showTerrain', clienttranslate('At your next turn, you will be building on a ${terrainName}'), [
      'i18n' => ['terrainName'],
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
    if (is_null($settlement))
      throw new BgaUserException(_("You have no more settlements left in your hand"));

    self::DbQuery("UPDATE piece SET x = {$pos['x']}, y = {$pos['y']}, location = 'board' WHERE id = {$settlement['id']}");
    $this->game->log->addBuild($settlement, $pos);
    $this->game->notifyAllPlayers('build', clienttranslate('${player_name} build a settlement on ${terrainName}'), [
      'i18n' => ['terrainName'],
      'player_name' => $this->getName(),
      'player_id' => $this->getId(),
      'terrainName' => $this->game->terrainNames[$this->game->board->getBoard()[$pos['x']][$pos['y']]],
      'x' => $pos['x'],
      'y' => $pos['y'],
    ]);

    // Obtain a new tile ?
    $this->game->locationManager->obtainTile($pos, $this->id);

    // End of the game
    if ($this->getSettlementsInHand() == 0 && !$this->game->log->isLastTurn()) {
      $this->game->notifyAllPlayers('message', clienttranslate('${player_name} build its last settlement, the game will end at the end of the round'), [
        'player_name' => $this->getName(),
      ]);
      $this->game->log->lastTurn();
    }
  }

  public function move($from, $to)
  {
    $settlement = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$this->id} AND location = 'board' AND type = 'settlement' AND x = {$from['x']} AND y = {$from['y']} LIMIT 1");
    if (is_null($settlement))
      throw new BgaUserException(_("You have no settlement to move at that position"));

    self::DbQuery("UPDATE piece SET x = {$to['x']}, y = {$to['y']} WHERE id = {$settlement['id']}");
    $this->game->log->addMove($settlement, $to);
    $this->game->notifyAllPlayers('move', clienttranslate('${player_name} move a settlement to ${terrainName}'), [
      'i18n' => ['terrainName'],
      'player_name' => $this->getName(),
      'player_id' => $this->getId(),
      'terrainName' => $this->game->terrainNames[$this->game->board->getBoard()[$to['x']][$to['y']]],
      'from' => $from,
      'x' => $to['x'],
      'y' => $to['y'],
    ]);

    // Loose a tile ?
    $this->game->locationManager->looseTile($from, $this->id);
    // Obtain a new tile ?
    $this->game->locationManager->obtainTile($to, $this->id);
  }
}
