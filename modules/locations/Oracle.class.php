<?php

class Oracle extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_ORACLE;
    $this->name  = clienttranslate('Oracle');
    $this->text  = [
      clienttranslate('Build one settlement on a hex of the same terrain type as your played terrain card.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile() { return 'build'; }
  public function isPlayable(){ return $this->canBuild() && !empty($this->game->board->getAvailableHexes($this->game->playerManager->getPlayer($this->playerId)->getTerrain())); }

  public function argPlayerBuild()
  {
    return $this->game->argPlayerBuildAux($this->game->playerManager->getPlayer($this->playerId)->getTerrain());
  }
}
