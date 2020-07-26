<?php

class ObjectiveMerchants extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = MERCHANTS;
    $this->name  = clienttranslate('Merchants');
    $this->desc  = clienttranslate("Connect location and castle hexes");
    $this->stat  = clienttranslate('Gold from Merchants');
    $this->text  = [
      clienttranslate("4 gold for each location and/or castle hex linked contiguously by your own settlements to other locations and/or castle hexes")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $components = $this->game->board->getConnectedComponents($playerId);
    $locations = $this->game->board->getHexesOfType($this->game->locationManager->getLocationTypes());

    foreach($locations as $location){
      $found = false;
      foreach($locations as $target){
        if($target == $location)
          continue;
        $found = $found || $components[$location['x']][$location['y']] == $components[$target['x']][$target['y']];
      }

      if($found)
        $this->addScoring($location, 4);
    }
  }
}
