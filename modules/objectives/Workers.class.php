<?php

class ObjectiveWorkers extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = WORKERS;
    $this->name  = clienttranslate('Workers');
    $this->desc  = clienttranslate("Build settlements next to location or castle hex");
    $this->stat  = clienttranslate('Gold from Workers');
    $this->text  = [
      clienttranslate("1 gold for each of your own settlements built adjacents to a location or castle hex")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $locations = $this->game->board->getHexesOfType($this->game->locationManager->getLocationTypes());
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    foreach($settlements as $settlement){
      if(count($this->game->board->getNeighboursIntersect($settlement, $locations)) == 0)
        continue;

      $this->addScoring($settlement, 1);
    }
  }
}
