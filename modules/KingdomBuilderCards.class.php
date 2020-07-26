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
    $this->terrains->autoreshuffle = true;

    $this->objectives = $this->game->getNew("module.common.deck");
    $this->objectives->init("objectives");
  }

  public function setupNewGame($players, $optionSetup)
  {
    // Create terrains cards
    $terrains = [];
    for($i = 0; $i < 5; $i++){
      $terrains[] = ['type' => $i, 'type_arg' => 0, 'nbr' => 5];
    }
    $this->terrains->createCards($terrains, 'deck');
    $this->terrains->shuffle('deck');

    // Choose 3 KingdomBuilder cards
    $this->objectives->createCards([ ['type' => 0, 'type_arg' => 0, 'nbr' => 10] ], 'deck');
    $this->objectives->shuffle('deck');
    if($optionSetup == BASIC){
      $this->objectives->moveCards([FISHERMEN,  MERCHANTS, KNIGHTS], 'board');
    } else {
      $this->objectives->pickCardsForLocation(3, 'deck', 'board');
    }
  }



  public static $objectiveClasses = [
    CASTLE => 'Castle',
    FISHERMEN => 'Fishermen',
    MERCHANTS => 'Merchants',
    DISCOVERERS => 'Discoverers',
    HERMITS => 'Hermits',
    CITIZENS => 'Citizens',
    MINERS => 'Miners',
    WORKERS => 'Workers',
    KNIGHTS => 'Knights',
    LORDS => 'Lords',
    FARMERS => 'Farmers',
  ];

  /*
   * getObjective: factory function to create a objective by ID
   */
  public function getObjective($objectiveId)
  {
    if (!isset(self::$objectiveClasses[$objectiveId])) {
      throw new BgaVisibleSystemException("getPower: Unknown objective $objectiveId");
    }
    $className = "Objective".self::$objectiveClasses[$objectiveId];
    return new $className($this->game);
  }

  /*
   * getObjectives: return all current objectives
   */
  public function getObjectives()
  {
    $cardsIds = array_values(array_map(function($card){ return $card['id']; }, $this->objectives->getCardsInLocation("board")));
    $cardsIds[] = CASTLE;
    return array_map(function ($objectiveId) {
      return $this->getObjective($objectiveId);
    }, $cardsIds);
  }

  /*
   * getUiData : get all ui data of all powers : id, name, title, text, hero
   */
  public function getUiData()
  {
    $ui = [];
    foreach ($this->getObjectives() as $objective) {
      $ui[] = $objective->getUiData();
    }
    return $ui;
  }

}
