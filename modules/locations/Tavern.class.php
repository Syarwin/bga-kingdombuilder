<?php

class Tavern extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_TAVERN;
    $this->name  = clienttranslate('Tavern');
    $this->text  = [
      clienttranslate('Build one settlement at one end of a line of at least 3 of your own settlements.'),
      clienttranslate('The orientation of the line does not matter (horizontally or diagonally).'),
      clienttranslate('The chosen hex must be suitable for building.'),
    ];
  }

  public function stateTile() { return 'build'; }

  public function argPlayerBuild()
  {
    $arg = $this->game->argPlayerBuildAux(0);
    $arg['terrainName'] = clienttranslate("end of a line");
    $arg['hexes'] = $this->getAvailableHexes();
    return $arg;
  }


  public function getAvailableHexes()
  {
    $settlements = array_map(array('KingdomBuilderBoard','getCoords'), $this->game->board->getPlacedSettlements($this->playerId));
    $hexes = [];
    foreach($settlements as $settlement){
    for($i = 0; $i < 6; $i++){
      $n1 = $this->game->board->getNeighbours($settlement, false);
      if(!array_key_exists($i, $n1) || !in_array($n1[$i], $settlements))
        continue;
      $n2 = $this->game->board->getNeighbours($n1[$i], false);
      if(!array_key_exists($i, $n2) || !in_array($n2[$i], $settlements))
        continue;

      $n3 = $this->game->board->getNeighbours($n2[$i], false);
      if(!array_key_exists($i, $n3))
        continue;
      array_push($hexes, $n3[$i]);
    }}

    // Keep only the
    $freeHexes = $this->game->board->getFreeHexesOfType([HEX_GRASS, HEX_CANYON, HEX_DESERT, HEX_FLOWER, HEX_FOREST]);
    $hexes = array_values(array_uintersect($hexes, $freeHexes, array('KingdomBuilderBoard','compareCoords')));
    return $hexes;
  }
}
