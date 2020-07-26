<?php

class ObjectiveDiscoverers extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = DISCOVERERS;
    $this->name  = clienttranslate('Discoverers');
    $this->desc  = clienttranslate("Build settlements on many horizontal lines");
    $this->stat  = clienttranslate('Gold from Discoverers');
    $this->text  = [
      clienttranslate("1 gold for each horizontal line on which you have built at least one of your own settlement")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);
    $settlements = $this->game->board->getPlacedSettlementsCoords($playerId);
    $lines = [];
    for($i = 0; $i < 20; $i++)
      $lines[$i] = 0;

    foreach($settlements as $settlement){
      $lines[$settlement['x']]++;
    }

    for($i = 0; $i < 20; $i++){
      if($lines[$i] > 0){
        $this->addScoring(['x' => $i, 'y' => 0], 1);
        for($j = 0; $j < 20; $j++){
          $this->addHighlight(['x' => $i, 'y' => $j]);
        }
      }
    }
  }
}
