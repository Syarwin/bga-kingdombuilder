<?php

class ObjectiveLords extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = LORDS;
    $this->name  = clienttranslate('Lords');
    $this->desc  = clienttranslate("Build the most settlements in each sector");
    $this->stat  = clienttranslate('Gold from Lords');
    $this->text  = [
       clienttranslate("Each sector : 12 gold for maximum number of settlements there, 6 golds for the next highest number of settlements")
    ];
  }

  public function scoringEndPlayer($playerId){
    parent::scoringEndPlayer($playerId);

    $sectors = [];
    for($i = 0; $i < 4; $i++)
      $sectors[$i] = [];

    foreach($this->game->playerManager->getPlayers() as $player){
      for($i = 0; $i < 4; $i++)
        $sectors[$i][$player->getId()] = 0;

      $settlements = $this->game->board->getPlacedSettlementsCoords($player->getId());
      foreach($settlements as $settlement){
        $sectors[$this->game->board->getQuadrant($settlement)][$player->getId()]++;
      }
    }

    $centers = [ ['x' => 4, 'y' => 4], ['x' => 4, 'y' => 14], ['x' => 14, 'y' => 4], ['x' => 14, 'y' => 14]];
    for($i = 0; $i < 4; $i++){
      $values = array_unique(array_values($sectors[$i]));
      rsort($values);
      if($sectors[$i][$playerId] == $values[0])
        $this->addScoring($centers[$i], 12);
      elseif($sectors[$i][$playerId] == $values[1])
        $this->addScoring($centers[$i], 6);
    }
  }
}
