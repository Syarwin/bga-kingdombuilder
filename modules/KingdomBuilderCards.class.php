<?php

/*
 * KingdomBuilderCards: all utility functions concerning cards are here
 */
class KingdomBuilderCards extends APP_GameClass
{
  public $game;
  public function __construct($game)
  {
    $this->game = $game;

    $this->terrains = $this->game->getNew("module.common.deck");
    $this->terrains->init("terrains");

    $this->kbCards = $this->game->getNew("module.common.deck");
    $this->kbCards->init("kb_cards");
  }

  public function setupNewGame($optionSetup)
  {
    // Create terrains cards
    $this->terrains->autoreshuffle = true;
    $terrains = [];
    for($i = 0; $i < 5; $i++){
      $terrains[] = ['type' => $i, 'type_arg' => 0, 'nbr' => 5];
    }
    $this->terrains->createCards($terrains, 'deck');
    $this->terrains->shuffle('deck');

    // Choose 3 KingdomBuilder cards
    $this->kbCards->createCards(['type' => 0, 'type_arg' => 0, 'nbr' => 10], 'deck');
    $this->kbCards->shuffle('deck');
    if($optionSetup == BASIC){
      $this->kbCards->moveCards([1, 2, 8], 'board'); // Fisherman, merchant and knight
    } else {
      $this->kbCards->pickCardsForLocation(3, 'deck', 'board');
    }
  }



  public function getKbCards()
  {
    return array_values(array_map(function($card){ return $card['id'] - 1; }, $this->kbCards->getCardsInLocation("board")));
  }
}
