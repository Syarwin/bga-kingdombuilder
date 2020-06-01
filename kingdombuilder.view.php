<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * KingdomBuilder implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * KingdomBuilder.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in KingdomBuilder_KingdomBuilder.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

require_once( APP_BASE_PATH."view/common/game.view.php" );

class view_kingdombuilder_kingdombuilder extends game_view
{
  function getGameName() {
      return "kingdombuilder";
  }

	function build_page( $viewArgs )
	{
    // Get players & players number
    $players = $this->game->loadPlayersBasicInfos();
    $players_nbr = count( $players );

    // Create the board
    $this->page->begin_block( "kingdombuilder_kingdombuilder", "cell");
    for($i = 0; $i < 20; $i++)
    for($j = 0; $j < 20; $j++){
      $this->page->insert_block( "cell", [
        'I' => $i,
        'J' => $j,
      ]);
    }

    // Get kbcardsr
    $kbcards = $this->game->cards->getKbCards();
    $this->page->begin_block( "kingdombuilder_kingdombuilder", "kbcard");
    for($i = 0; $i < count($kbcards); $i++){
      $card = $this->game->kbcards[$kbcards[$i]];
      $this->page->insert_block( "kbcard", [
        'ID' => $kbcards[$i],
        'NAME'  => $card['name'],
        'SHORT' => $card['short'],
        'POINTS' => $card['points'],
      ]);
    }

	}
}
