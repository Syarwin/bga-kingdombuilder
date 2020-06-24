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
}
