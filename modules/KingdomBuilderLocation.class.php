<?php

abstract class KingdomBuilderLocation extends APP_GameClass
{

  protected $game;
  protected $playerId;

  public function __construct($game, $playerId)
  {
    $this->game = $game;
    $this->playerId = $playerId;
  }


  protected $id = 0;
  protected $name = '';
  protected $text;

  public function getId() { return $this->id;  }
  public function getName() { return $this->name; }
  public function getText() { return $this->text; }
  public function getUiData()
  {
    return [
      'id'        => $this->id,
      'name'      => $this->name,
      'text'      => $this->text,
    ];
  }

/*
  public function isSupported($nPlayers, $optionPowers)
  {
    $isHero = $this instanceof SantoriniHeroPower;
    return $this->implemented
      && in_array($nPlayers, $this->getPlayerCount())
      && (($optionPowers == GODS_AND_HEROES)
        || ($optionPowers == SIMPLE && $this->isSimple())
        || ($optionPowers == GODS && !$isHero)
        || ($optionPowers == HEROES && $isHero)
        || ($optionPowers == GOLDEN_FLEECE && $this->isGoldenFleece()));
  }
*/
  public function getPlayer()
  {
    return $this->game->playerManager->getPlayer($this->playerId);
  }

  public function stateTile() { return null; }
  public function argPlayerBuild() { return []; }
  public function argPlayerMove() { return []; }
}
