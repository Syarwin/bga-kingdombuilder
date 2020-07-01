<?php

class ObjectiveMiners extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = MINERS;
    $this->name  = clienttranslate('Miners');
    $this->desc  = clienttranslate("Build settlements next to a mountain");
    $this->text  = [
      clienttranslate("1 gold for each of your own settlements built adjacents to one or more mountain hexes")
    ];
  }


  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $mountains = $this->game->board->getHexesOfType(HEX_MOUNTAIN);
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    foreach($settlements as $settlement){
      if(count($this->game->board->getNeighboursIntersect($settlement, $mountains)) == 0)
        continue;

      $this->addScoring($settlement, 1);
    }
  }
}
