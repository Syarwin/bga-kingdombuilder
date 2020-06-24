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
}
