<?php

class Paddock extends KingdomBuilderLocation
{
  public function __construct($game, $playerId)
  {
    parent::__construct($game, $playerId);
    $this->id    = HEX_PADDOCK;
    $this->name  = clienttranslate('Paddock');
    $this->text  = [
      clienttranslate('Move any one of your existing settlements two hexes in a straight line in any direction (horizontally or diagonally) to an eligible hex.'),
      clienttranslate('You may jump across any terrain type hex, even water, mountain, castle and location, and/or your own and other players’ settlements.'),
      clienttranslate('The target hex must not necessarily be adjacent to one of your own settlement.'),
    ];
  }

  public function stateTile() { return 'move'; }
}
