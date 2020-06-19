<?php

/*
    From this file, you can edit the various meta-information of your game.

    Once you modified the file, don't forget to click on "Reload game informations" from the Control Panel in order in can be taken into account.

    See documentation about this file here:
    http://en.doc.boardgamearena.com/Game_meta-information:_gameinfos.inc.php

*/

$gameinfos = [
	'game_name' => "Kingdom Builder",
	'designer' => 'Donald X. Vaccarino',
	'artist' => 'Oliver Schlemmer',
	'year' => 2011,
	'publisher' => 'Queen Games',
	'publisher_website' => 'https://ssl.queen-games.com/',
	'publisher_bgg_id' => 47,
	'bgg_id' => 107529,
	'players' => [2,3,4],    

	'suggest_player_number' => null,
	'not_recommend_player_number' => null,

	'estimated_duration' => 30,
	'fast_additional_time' => 30,
	'medium_additional_time' => 40,
	'slow_additional_time' => 50,

// If you are using a tie breaker in your game (using "player_score_aux"), you must describe here
// the formula used to compute "player_score_aux". This description will be used as a tooltip to explain
// the tie breaker to the players.
// Note: if you are NOT using any tie breaker, leave the empty string.
//
// Example: 'tie_breaker_description' => totranslate( "Number of remaining cards in hand" ),
'tie_breaker_description' => "",

	'losers_not_ranked' => false,

	// Game is "beta". A game MUST set is_beta=1 when published on BGA for the first time, and must remains like this until all bugs are fixed.
	'is_beta' => 1,
	'is_coop' => 0,

	'complexity' => 2,
	'luck' => 2,
	'strategy' => 3,
	'diplomacy' => 3,

	'player_colors' => [ "ff0000", "008000", "0000ff", "ffa500", "773300" ],

	'favorite_colors_support' => true,

	// When doing a rematch, the player order is swapped using a "rotation" so the starting player is not the same
	// If you want to disable this, set this to false
	'disable_player_order_swap_on_rematch' => false,

	'game_interface_width' => array(
		  'min' => 740,
		  'max' => null
	),

	// Game presentation
	// Short game presentation text that will appear on the game description page, structured as an array of paragraphs.
	// Each paragraph must be wrapped with totranslate() for translation and should not contain html (plain text without formatting).
	// A good length for this text is between 100 and 150 words (about 6 to 9 lines on a standard display)
	'presentation' => [
	//    totranslate("This wonderful game is about geometric shapes!"),
	//    totranslate("It was awarded best triangle game of the year in 2005 and nominated for the Spiel des Jahres."),
	//    ...
	],

	// Games categories
	//  You can attribute a maximum of FIVE "tags" for your game.
	//  Each tag has a specific ID (ex: 22 for the category "Prototype", 101 for the tag "Science-fiction theme game")
	//  Please see the "Game meta information" entry in the BGA Studio documentation for a full list of available tags:
	//  http://en.doc.boardgamearena.com/Game_meta-information:_gameinfos.inc.php
	//  IMPORTANT: this list should be ORDERED, with the most important tag first.
	//  IMPORTANT: it is mandatory that the FIRST tag is 1, 2, 3 and 4 (= game category)
	'tags' => [ 2 ],


//////// BGA SANDBOX ONLY PARAMETERS (DO NOT MODIFY)

// simple : A plays, B plays, C plays, A plays, B plays, ...
// circuit : A plays and choose the next player C, C plays and choose the next player D, ...
// complex : A+B+C plays and says that the next player is A+B
'is_sandbox' => false,
'turnControl' => 'simple'

////////
];
