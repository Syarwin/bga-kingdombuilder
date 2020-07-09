<?php

class Harbor extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_HARBOR;
    $this->name  = clienttranslate('Harbor');
    $this->text  = [
      clienttranslate('Move any one of your existing settlements to a water hex.'),
      clienttranslate('Build adjacent if possible.'),
      clienttranslate('This is the only way to build settlements on water hexes.'),
    ];
  }

  public function stateTile() { return 'move'; }
  public function isPlayable(){ return true; }

  public function argPlayerMoveTarget($settlement)
  {
    return $this->game->board->getAvailableHexes(HEX_WATER);
  }

  public function argPlayerMove()
  {
    return [
      'cancelable' => $this->game->log->getLastActions() != null,
      'hexes' => $this->game->board->getPlacedSettlementsCoords($this->playerId),
      'tileName' => $this->getName(),
    ];
  }
}
