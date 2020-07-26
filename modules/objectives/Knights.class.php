<?php

class ObjectiveKnights extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = KNIGHTS;
    $this->name  = clienttranslate('Knights');
    $this->desc  = clienttranslate("Build many settlements on one horizontal line");
    $this->stat  = clienttranslate('Gold from Knights');
    $this->text  = [
      clienttranslate("2 gold for each of your own settlements built on that horizontal line with the most of your own settlements")
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

    $bestLine = array_search(max($lines),$lines);
    foreach($settlements as $settlement){
      if($settlement['x'] == $bestLine)
        $this->addScoring($settlement, 2);
    }

    for($i = 0; $i < 20; $i++)
      $this->addHighlight(['x' => $bestLine, 'y' => $i]);
  }
}
