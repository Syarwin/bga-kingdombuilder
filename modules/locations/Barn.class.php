<?php

class Barn extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_BARN;
    $this->name  = clienttranslate('Barn');
    $this->text  = [
      clienttranslate('Move any one of your existing settlements to a hex of the same terrain type as your played terrain card.'),
      clienttranslate('Build adjacent if possible.'),
    ];
  }

  public function stateTile() { return 'move'; }

  public function argPlayerMoveTarget($settlement)
  {
    $player = $this->playerManager->getPlayer();
    $terrain = $player->getTerrain();
    return $this->game->board->getAvailableHexes($terrain);
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
