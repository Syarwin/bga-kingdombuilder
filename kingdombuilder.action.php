<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * KingdomBuilder implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * KingdomBuilder.action.php
 *
 * KingdomBuilder main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/KingdomBuilder/KingdomBuilder/myAction.html", ...)
 *
 */


class action_kingdombuilder extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if( self::isArg( 'notifwindow') )
    {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
    }
    else
    {
      $this->view = "kingdombuilder_kingdombuilder";
      self::trace( "Complete reinitialization of board game" );
    }
  }

  public function build()
  {
    self::setAjaxMode();
    $x = (int) self::getArg('x', AT_posint, true);
    $y = (int) self::getArg('y', AT_posint, true);
    $this->game->playerBuild(['x' => $x, 'y' => $y]);
    self::ajaxResponse();
  }
}
