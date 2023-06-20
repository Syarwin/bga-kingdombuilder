<?php

class Village extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id = HEX_VILLAGE;
    $this->name = clienttranslate('Village');
    $this->text = [
      clienttranslate(
        'Build one additional settlement on an empty space eligible for building that is adjacent to at least 3 of your settlements.'
      ),
    ];
  }

  public function stateTile()
  {
    return 'build';
  }
  public function isPlayable()
  {
    return $this->canBuild() && !empty($this->getAvailableHexes());
  }

  public function argPlayerBuild()
  {
    $arg = $this->game->argPlayerBuildAux(0);
    $arg['terrainName'] = clienttranslate('an hex adjacent to at least 3 of your settlements');
    $arg['hexes'] = $this->getAvailableHexes();
    return $arg;
  }

  public function getAvailableHexes()
  {
    $hexes = $this->game->board->getFreeHexes();
    $this->game->board->keepAdjacentIfPossible($hexes, $this->playerId);
    $settlements = $this->game->board->getPlacedSettlementsCoords();

    $fHexes = [];
    foreach ($hexes as $hex) {
      $n = 0;
      foreach ($this->game->board->getNeighbours($hex) as $neighbour) {
        if (in_array($neighbour, $settlements)) {
          $n++;
        }
      }

      if ($n >= 3) {
        $fHexes[] = $hex;
      }
    }

    return $fHexes;
  }
}
