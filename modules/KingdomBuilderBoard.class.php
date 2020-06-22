<?php

/*
 * KingdomBuilderBoard: all utility functions concerning space on the board are here
 */
class KingdomBuilderBoard extends APP_GameClass
{
  public $game;
  public function __construct($game)
  {
    $this->game = $game;
  }

  public static $boards = [
    [
      [1, 2, 2, 2, 2, 2, 2, 2, 2, 2],
      [1, 1, 1, 2, 2, 2, 2, 2, 1, 2],
      [7, 7, 7, 2, 7, 7, 15,2, 2, 1],
      [1, 7, 7, 7, 7, 7, 3, 3, 1, 1],
      [1, 1, 1, 7, 7, 6, 3, 3, 3, 1],
      [0, 1, 1, 1, 7, 3, 3, 6, 4, 1],
      [0, 0,15, 4, 3, 3, 6, 3, 3, 4],
      [0, 0, 4, 4, 3, 3, 0, 5, 4, 4],
      [0, 0, 0, 4, 4, 6, 0, 0, 4, 4],
      [0, 0, 0, 4, 6, 0, 0, 4, 4, 4],
    ],
    [
      [ 2, 2, 1, 6, 6, 4, 4, 4, 0, 0],
      [ 2, 5, 1, 6, 4, 4, 4, 9, 0, 0],
      [ 1, 1, 1, 3, 3, 3, 4, 1, 3, 3],
      [ 1, 1, 3, 3, 6, 2, 2, 1, 1, 3],
      [ 1, 0, 0, 6, 3, 3, 2, 2, 1, 1],
      [ 0, 0, 9, 3, 6, 3, 6, 2, 2, 1],
      [ 0, 0, 0, 4, 3, 3, 6, 6, 2, 2],
      [ 0, 0, 4, 4, 7, 6, 6, 6, 2, 6],
      [ 0, 7, 4, 4, 6, 6, 6, 6, 6, 6],
      [ 4, 4, 4, 6, 6, 6, 6, 6, 6, 6],
    ],
    [
      [ 0, 0, 0, 4, 4, 6, 0, 4, 4, 4],
      [ 0, 0, 0, 5, 4, 6, 0, 4, 4, 4],
      [ 0, 3, 3, 0, 4, 4, 6, 0, 0, 4],
      [ 3, 3, 1, 0, 4, 6, 3, 8, 4, 4],
      [ 3, 3, 3, 1, 1, 6, 3, 3, 6, 6],
      [ 7, 7, 1, 0, 0, 6, 6, 6, 2, 2],
      [ 1, 1, 1, 7, 0, 3, 3, 3, 2, 2],
      [ 1, 1, 5, 2, 7, 2, 3, 3, 1, 1],
      [ 6, 6, 6, 2, 2, 2, 2, 7, 1, 1],
      [ 6, 6, 6, 6, 2, 2, 2, 2, 2, 1],
    ],
    [
      [ 0, 0, 4, 4, 4, 6, 0, 4, 4, 3],
      [ 0, 3, 4, 4, 6, 0, 4, 4, 3, 3],
      [ 0, 3, 3, 4, 6, 0, 0, 3, 3, 3],
      [ 3, 3, 4, 4, 6, 0, 7, 3, 2, 2],
      [ 1, 3, 5, 4, 6, 0, 2, 2, 2, 2],
      [ 1, 1, 4, 6, 0, 0, 7, 7, 2, 2],
      [ 1, 1, 6, 6, 6, 0, 2, 2, 2, 1],
      [ 6, 6, 0, 0, 6, 6,12, 1, 7, 1],
      [ 6, 2, 5, 0, 6, 7, 6, 1, 1, 1],
      [ 6, 2, 2, 6, 6, 6, 6, 1, 1, 1],
    ],
    [
      [ 4, 4, 4, 4, 7, 7, 0, 7, 1, 1],
      [ 4, 7, 4, 4, 3, 0, 7, 7, 7, 1],
      [ 3, 3, 4, 3, 3, 3, 0, 0, 6, 7],
      [ 2, 3, 3, 3, 6,11, 0, 6, 7, 7],
      [ 2, 2, 2, 2, 3, 6, 0, 6, 1, 1],
      [ 2, 1, 2, 2, 2, 6, 6, 1, 0, 1],
      [ 2, 2, 1, 2, 2, 6, 3, 5, 0, 1],
      [ 1, 1,11, 2, 6, 3, 3, 3, 0, 0],
      [ 2, 1, 6, 6, 6, 4, 4, 3, 0, 0],
      [ 2, 1, 1, 6, 4, 4, 4, 0, 0, 0],
    ],
    [
      [ 2, 2, 1, 6, 6, 4, 4, 0, 0, 0],
      [ 2, 1, 6, 3, 3, 4, 4, 4, 0, 0],
      [ 2, 2, 6, 3, 3, 4, 4,14, 3, 0],
      [ 6, 6, 6, 3, 0, 4, 3, 3, 3, 3],
      [ 6, 6, 6, 6, 0, 0, 0, 0, 3, 3],
      [ 6, 4, 4, 6, 0, 0, 1, 1, 2, 1],
      [ 6, 4, 1, 4, 6, 0, 1, 1, 2, 1],
      [ 6, 5, 1, 3, 6,14, 2, 2, 1, 6],
      [ 6, 6, 1, 3, 6, 6, 6, 2, 2, 6],
      [ 6, 6, 6, 6, 6, 6, 6, 6, 6, 6],
    ],
    [
      [ 1, 1, 1, 2, 2, 6, 2, 2, 2, 2],
      [ 7, 7, 1, 2, 2, 6, 2, 2, 2, 2],
      [ 7, 7, 1, 7, 7, 6, 2, 2,13, 3],
      [ 7, 1, 7, 7, 6, 7, 2, 3, 3, 3],
      [ 1, 1, 4, 4, 6, 7, 7, 1, 3, 3],
      [ 1, 4, 4, 6, 1, 1, 1, 7, 3, 3],
      [ 1,13, 4, 4, 6, 3, 3, 3, 3, 3],
      [ 0, 0, 4, 6, 0, 5, 0, 3, 0, 4],
      [ 0, 0, 4, 4, 6, 0, 0, 0, 0, 4],
      [ 0, 0, 4, 4, 6, 0, 0, 0, 4, 4],
    ],
    [
      [ 3, 2, 2, 7, 7, 2, 2, 1, 1, 1],
      [ 3, 3, 2, 2, 2, 7, 7, 1, 1, 1],
      [ 3, 3, 3, 3, 3, 3, 3, 7, 7, 7],
      [ 6, 6, 3, 5, 0, 0, 4, 4, 7, 7],
      [ 3, 3, 6, 6, 0, 0, 0, 4, 4, 1],
      [ 3, 1, 1, 6, 0, 4, 4, 1, 1, 1],
      [ 2, 3,10, 1, 6, 4, 4,10, 1, 0],
      [ 2, 2, 1, 6, 4, 4, 0, 0, 0, 0],
      [ 2, 2, 2, 6, 4, 4, 4, 0, 0, 0],
      [ 2, 2, 6, 6, 4, 4, 4, 0, 0, 0],
    ]
  ];


  public function setupNewGame($optionSetup)
  {
    $quadrants = [];
    if ($optionSetup == BASIC) {
      $quadrants = [7, 6, 5, 1];
    }

    $this->game->log->initBoard($quadrants);
  }

  public static function getCoords($piece)
  {
    return ['x' => (int) $piece['x'], 'y' => (int) $piece['y']];
  }

  public static function compareCoords($a, $b)
  {
    $dx = (int) $b['x'] - (int) $a['x'];
    $dy = (int) $b['y'] - (int) $a['y'];
    if($dx != 0) return $dx;
    return $dy;
  }

  public function getQuadrants()
  {
    return $this->game->log->getQuadrants();
  }


  public function getPlacedSettlements($pId = null)
  {
    $where = is_null($pId)? '' : " AND player_id = $pId";
    return self::getObjectListFromDB("SELECT * FROM piece WHERE type = 'settlement' AND location = 'board'" . $where);
  }

  public function getSettlements($pId)
  {
    return self::getObjectListFromDB("SELECT * FROM piece WHERE type = 'settlement' AND player_id = $pId");
  }


  public function getNeighbours($space)
  {
    $x = (int) $space['x'];
    $y = (int) $space['y'];
    $hexes = [];
    $hexes[] = ['x' => $x, 'y' => $y - 1];
    $hexes[] = ['x' => $x, 'y' => $y + 1];
    $hexes[] = ['x' => $x - 1, 'y' => $y];
    $hexes[] = ['x' => $x - 1, 'y' => $y + ($x % 2 == 0? -1 : 1)];
    $hexes[] = ['x' => $x + 1, 'y' => $y];
    $hexes[] = ['x' => $x + 1, 'y' => $y + ($x % 2 == 0? -1 : 1)];
    return array_values(array_filter($hexes, function($hex){
      return $hex['x'] >= 0 && $hex['y'] >= 0 && $hex['x'] < 20 && $hex['y'] < 20;
    }));
  }

  public function getPlacedSettlementsNeighbouringSpaces($pId)
  {
    $settlements = $this->getPlacedSettlements($pId);
    $hexes = [];
    foreach($settlements as $settlement){
      $hexes = array_merge($hexes, $this->getNeighbours($settlement));
    }
    return $hexes;
  }

  public function getHexesOfType($type)
  {
    $hexes = [];
    $quadrants = $this->getQuadrants();
    for($k = 0; $k < 4; $k++){
    for($i = 0; $i < 10; $i++){
    for($j = 0; $j < 10; $j++){
      $flipped = $quadrants[$k] >= count(self::$boards);
      if($flipped)
        $quadrants[$k] -= 8;

      $x = $flipped? (9 - $i) : $i;
      $y = $flipped? (9 - $j) : $j;
      if(self::$boards[$quadrants[$k]][$x][$y] != $type)
        continue;

      if($k == 1 || $k == 3) $y += 10;
      if($k == 2 || $k == 3) $x += 10;
      $hexes[] = ['x' => $x, 'y' => $y];
    }}}

    return $hexes;
  }


  public function getFreeHexesOfType($type)
  {
    $hexes = $this->getHexesOfType($type);
    $settlements = array_map(array('KingdomBuilderBoard','getCoords'), $this->getPlacedSettlements());
    $hexes = array_values(array_udiff($hexes, $settlements, array('KingdomBuilderBoard','compareCoords')));
    return $hexes;
  }


  public function getAvailableHexes($type, $pId = null)
  {
    $pId = $pId ?: $this->game->getActivePlayerId();

    $hexes = $this->getFreeHexesOfType($type);
    $hexesNeighbouring = array_values(array_uintersect($hexes, $this->getPlacedSettlementsNeighbouringSpaces($pId), array('KingdomBuilderBoard','compareCoords')));
    return count($hexesNeighbouring) > 0 ? $hexesNeighbouring : $hexes;
  }
}
