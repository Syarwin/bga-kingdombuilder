<?php

class Quarry extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id = HEX_QUARRY;
    $this->name = clienttranslate('Quarry');
    $this->text = [
      clienttranslate('Build 1 or 2 stone walls on empty terrain spaces of the same type as your played terrain card.'),
      clienttranslate('These stone walls must be adjacent to at least one of your settlements on the game board.'),
      clienttranslate(
        'Stone walls are not owned by any player, and they generate no gold for anyone. Instead, they simply block the spaces they\'re on for the remainder of the game.'
      ),
    ];
  }

  public function stateTile()
  {
    return 'buildQuarry';
  }
  public function isPlayable()
  {
    return !empty($this->getAvailableHexes());
  }

  public function argPlayerBuild()
  {
    $arg = $this->game->argPlayerBuildAux(0);
    $arg['terrainName'] = clienttranslate('an hex adjacent to at least 3 of your settlements');
    $arg['hexes'] = $this->getAvailableHexes();
    return $arg;
  }

  public function getAvailableHexes()
  {
    $hexes = $this->game->board->getFreeHexes();
    $this->game->board->keepAdjacentIfPossible($hexes, $this->playerId);
    return $hexes;
  }
}
