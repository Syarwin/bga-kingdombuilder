<?php

class ObjectiveCastle extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = CASTLE;
    $this->name  = clienttranslate('Castle');
    $this->desc  = "";
    $this->stat  = clienttranslate('Gold from Castles');
    $this->text  = [
      clienttranslate("3 gold for each castle with your own settlements built adjacents to")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $locations = $this->game->board->getHexesOfType(HEX_CASTLE);
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    foreach($locations as $castle){
      if(count($this->game->board->getNeighboursIntersect($castle, $settlements)) == 0)
        continue;

      $this->addScoring($castle, 3);
    }
  }
}
