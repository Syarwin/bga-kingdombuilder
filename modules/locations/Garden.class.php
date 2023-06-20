<?php

class Garden extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id = HEX_GARDEN;
    $this->name = clienttranslate('Garden');
    $this->text = [
      clienttranslate('Build one settlement on a flower field hex.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile()
  {
    return 'build';
  }
  public function isPlayable()
  {
    return $this->canBuild() && !empty($this->game->board->getAvailableHexes(HEX_FLOWER));
  }

  public function argPlayerBuild()
  {
    return $this->game->argPlayerBuildAux(HEX_FLOWER);
  }
}
