<?php

class ObjectiveHermits extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = HERMITS;
    $this->name  = clienttranslate('Hermits');
    $this->desc  = clienttranslate("Create many settlements area");
    $this->stat  = clienttranslate('Gold from Hermits');
    $this->text  = [
      clienttranslate("1 gold for each of your own separate settlement and for each separate settlement area")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $components = $this->game->board->getConnectedComponents($playerId, false);
    $granted = [];

    for($i = 0; $i < 20; $i++){
    for($j = 0; $j < 20; $j++){
      $val = $components[$i][$j];
      if(($val == 0) || in_array($val, $granted))
        continue;

      array_push($granted, $val);
      $this->addScoring(['x' => $i, 'y' => $j], 1);
    }}
  }
}
