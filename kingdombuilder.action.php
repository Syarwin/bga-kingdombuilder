<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * KingdomBuilder implementation : © Timothée Pecatte tim.pecatte@gmail.com
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


  public function useTile()
  {
    self::setAjaxMode();
    $tileId = (int) self::getArg('tileId', AT_posint, true);
    $this->game->useTile($tileId);
    self::ajaxResponse();
  }

  public function skip()
  {
    self::setAjaxMode();
    $this->game->skip();
    self::ajaxResponse();
  }

  public function moveSelect()
  {
    self::setAjaxMode();
    $x = (int) self::getArg('x', AT_posint, true);
    $y = (int) self::getArg('y', AT_posint, true);
    $this->game->locationManager->playerMoveSelect(['x' => $x, 'y' => $y]);
    self::ajaxResponse();
  }

  public function move()
  {
    self::setAjaxMode();
    $fromX = (int) self::getArg('fromX', AT_posint, true);
    $fromY = (int) self::getArg('fromY', AT_posint, true);
    $toX = (int) self::getArg('toX', AT_posint, true);
    $toY = (int) self::getArg('toY', AT_posint, true);
    $this->game->playerMove(['x' => $fromX, 'y' => $fromY], ['x' => $toX, 'y' => $toY]);
    self::ajaxResponse();
  }



  public function undoAction()
  {
    self::setAjaxMode();
    $this->game->undoAction();
    self::ajaxResponse();
  }

  public function restartTurn()
  {
    self::setAjaxMode();
    $this->game->restartTurn();
    self::ajaxResponse();
  }


  public function confirmTurn()
  {
    self::setAjaxMode();
    $this->game->confirmTurn();
    self::ajaxResponse();
  }
}
