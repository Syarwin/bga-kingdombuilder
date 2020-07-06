<?php

class ObjectiveCitizens extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = CITIZENS;
    $this->name  = clienttranslate('Citizens');
    $this->desc  = clienttranslate("Create a large settlement area");
    $this->text  = [
      clienttranslate("1 gold for every 2 of your own settlements in your largest own settlement area")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $components = $this->game->board->getConnectedComponents($playerId, false);
    $areas = [];

    for($i = 0; $i < 20; $i++){
    for($j = 0; $j < 20; $j++){
      $val = $components[$i][$j];
      if($val == 0)
        continue;

      if(!array_key_exists($val, $areas))
        $areas[$val] = 0;
      $areas[$val]++;
    }}

    $maxArea = array_search(max($areas), $areas);
    for($i = 0; $i < 20; $i++){
    for($j = 0; $j < 20; $j++){
      $val = $components[$i][$j];
      if($val == 0 || $val != $maxArea)
        continue;

      $this->addScoring(['x' => $i, 'y' => $j], 2);
    }}
  }
}
