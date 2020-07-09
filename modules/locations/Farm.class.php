<?php

class Farm extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_FARM;
    $this->name  = clienttranslate('Farm');
    $this->text  = [
      clienttranslate('Build one settlement on a grass hex.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile() { return 'build'; }
  public function isPlayable(){ return $this->canBuild() && !empty($this->game->board->getAvailableHexes(HEX_GRASS)); }

  public function argPlayerBuild()
  {
    return $this->game->argPlayerBuildAux(HEX_GRASS);
  }
}
