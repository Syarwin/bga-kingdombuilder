<?php

class Tower extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_TOWER;
    $this->name  = clienttranslate('Tower');
    $this->text  = [
      clienttranslate('Build one settlement at the edge of the game board.'),
      clienttranslate('Choose any of the 5 suitable terrain type hexes.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile() { return 'build'; }

  public function argPlayerBuild()
  {
    $arg = $this->game->argPlayerBuildAux(0);
    $arg['terrainName'] = clienttranslate("edge of the board");
    $arg['hexes'] = $this->getAvailableHexes();
    return $arg;
  }


  public function getAvailableHexes()
  {
    $hexes = $this->game->board->getFreePerimeterHexes([HEX_GRASS, HEX_CANYON, HEX_DESERT, HEX_FLOWER, HEX_FOREST]);
    $this->game->board->keepAdjacentIfPossible($hexes, $this->playerId);
    return $hexes;
  }
}
