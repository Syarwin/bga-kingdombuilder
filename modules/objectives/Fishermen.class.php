<?php

class ObjectiveFishermen extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = FISHERMEN;
    $this->name  = clienttranslate('Fishermen');
    $this->desc  = clienttranslate("Build settlements on the waterfront");
    $this->text  = [
      clienttranslate("1 gold for each of your own settlements built adjacents to one or more water hexes")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $water = $this->game->board->getHexesOfType(HEX_WATER);
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    foreach($settlements as $settlement){
      if(in_array($settlement, $water) || (count($this->game->board->getNeighboursIntersect($settlement, $water)) == 0))
        continue;

      $this->addScoring($settlement, 1);
    }
  }
}
