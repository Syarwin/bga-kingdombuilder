<?php

class Paddock extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_PADDOCK;
    $this->name  = clienttranslate('Paddock');
    $this->text  = [
      clienttranslate('Move any one of your existing settlements two hexes in a straight line in any direction (horizontally or diagonally) to an eligible hex.'),
      clienttranslate('You may jump across any terrain type hex, even water, mountain, castle and location, and/or your own and other players’ settlements.'),
      clienttranslate('The target hex must not necessarily be adjacent to one of your own settlement.'),
    ];
  }

  public function stateTile() { return 'move'; }
  public function isPlayable(){ return true; }

  public function argPlayerMoveTarget($settlement){
    $settlements = $this->game->board->getPlacedSettlementsCoords();
    $board = $this->game->board->getBoard();

    $hexes = [];
    for($i = 0; $i < 6; $i++){
      $n1 = $this->game->board->getNeighbours($settlement, false);
      if(!array_key_exists($i, $n1))
        continue;
      $n2 = $this->game->board->getNeighbours($n1[$i], false);
      if(!array_key_exists($i, $n2))
        continue;

      $hex = $n2[$i];
      if(in_array($hex, $settlements) || !in_array($board[$hex['x']][$hex['y']], [HEX_GRASS, HEX_CANYON, HEX_DESERT, HEX_FLOWER, HEX_FOREST]) )
        continue;

      array_push($hexes, $hex);
    }

    return $hexes;
  }

  public function argPlayerMove()
  {
    $settlements = $this->game->board->getPlacedSettlementsCoords($this->playerId);
    $hexes = [];
    foreach($settlements as $settlement){
      if(count($this->argPlayerMoveTarget($settlement)) > 0)
        array_push($hexes, $settlement);
    }

    return [
      'i18n' => ['tileName'],
      'cancelable' => $this->game->log->getLastActions() != null,
      'hexes' => $hexes,
      'tileName' => $this->getName(),
    ];
  }
}
