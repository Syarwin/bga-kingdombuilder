<?php

class Caravan extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id = HEX_CARAVAN;
    $this->name = clienttranslate('Caravan');
    $this->text = [
      clienttranslate(
        'Move one of your own previously built settlements in a straight line, either horizontally or diagonally, until it is blocked by an obstacle.'
      ),
      clienttranslate(
        '(Obstacles are water, mountains, castles, location spaces, nomad spaces, and each space occupied by a settlement.)'
      ),
      clienttranslate(
        'Place this settlement on the empty space eligible for building directly in front of the obstacle.'
      ),
    ];
  }

  public function stateTile()
  {
    return 'move';
  }
  public function isPlayable()
  {
    return true;
  }

  public function argPlayerMoveTarget($settlement)
  {
    $settlements = $this->game->board->getPlacedSettlementsCoords();
    $board = $this->game->board->getBoard();

    $hexes = [];
    for ($i = 0; $i < 6; $i++) {
      $n = 0;
      $hex = $settlement;
      $neighbours = $this->game->board->getNeighbours($hex, false);
      while (
        array_key_exists($i, $neighbours) &&
        !in_array($neighbours[$i], $settlements) &&
        in_array($board[$neighbours[$i]['x']][$neighbours[$i]['y']], [
          HEX_GRASS,
          HEX_CANYON,
          HEX_DESERT,
          HEX_FLOWER,
          HEX_FOREST,
        ])
      ) {
        $hex = $neighbours[$i];
        $neighbours = $this->game->board->getNeighbours($hex, false);
        $n++;
      }

      if ($n > 0) {
        array_push($hexes, $hex);
      }
    }

    return $hexes;
  }

  public function argPlayerMove()
  {
    $settlements = $this->game->board->getPlacedSettlementsCoords($this->playerId);
    $hexes = [];
    foreach ($settlements as $settlement) {
      if (count($this->argPlayerMoveTarget($settlement)) > 0) {
        array_push($hexes, $settlement);
      }
    }

    return [
      'i18n' => ['tileName'],
      'cancelable' => $this->game->log->getLastActions() != null,
      'hexes' => $hexes,
      'tileName' => $this->getName(),
    ];
  }
}
