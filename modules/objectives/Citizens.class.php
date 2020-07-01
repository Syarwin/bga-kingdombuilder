<?php

class ObjectiveCitizens extends KingdomBuilderObjective
{
  public function __construct($game)
  {
    parent::__construct($game);
    $this->id    = CITIZENS;
    $this->name  = clienttranslate('Citizens');
    $this->desc  = clienttranslate("Create a large settlement area");
    $this->text  = [
      clienttranslate("1 gold for every 2 of your own settlements in your largest own settlement area")
    ];
  }
}
