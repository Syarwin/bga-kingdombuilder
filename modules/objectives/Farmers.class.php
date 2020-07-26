<?php

class ObjectiveFarmers extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = FARMERS;
    $this->name  = clienttranslate('Farmers');
    $this->desc  = clienttranslate("Build settlements in all sectors");
    $this->stat  = clienttranslate('Gold from Farmers');
    $this->text  = [
      clienttranslate("3 gold for each of your own settlements in that sector with the fewest of your own settlements")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    $sectors = [0,0,0,0];

    foreach($settlements as $settlement){
      $sectors[$this->game->board->getQuadrant($settlement)]++;
    }

    $leastSector = array_search(min($sectors),$sectors);
    foreach($settlements as $settlement){
      if($this->game->board->getQuadrant($settlement) == $leastSector)
        $this->addScoring($settlement, 3);
    }
  }

}
