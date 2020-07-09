<?php

class Oasis extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_OASIS;
    $this->name  = clienttranslate('Oasis');
    $this->text  = [
      clienttranslate('Build one settlement on a desert hex.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile() { return 'build'; }
  public function isPlayable(){ return $this->canBuild() && !empty($this->game->board->getAvailableHexes(HEX_DESERT)); }

  public function argPlayerBuild()
  {
    return $this->game->argPlayerBuildAux(HEX_DESERT);
  }
}
