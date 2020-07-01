<?php

/*
 * State constants
 */
define('ST_GAME_SETUP', 1);

define('ST_NEXT_PLAYER', 3);
define('ST_START_OF_TURN', 4);
define('ST_BUILD', 5);
define('ST_MOVE', 9);
define('ST_USE_TILE', 6);
define('ST_END_OF_TURN', 7);
define('ST_PRE_END_OF_TURN', 8);

define('ST_SCORING_END', 98);
define('ST_GAME_END', 99);

/*
 * Options constants
 */
define('OPTION_SETUP', 102);
define('BASIC', 0);
define('RANDOM', 1);

/*
 * Global game variables
 */
define('CURRENT_ROUND', 20);
define('FIRST_PLAYER', 21);



/*
 * Hex types
 */
define('HEX_GRASS', 0);
define('HEX_CANYON', 1);
define('HEX_DESERT', 2);
define('HEX_FLOWER', 3);
define('HEX_FOREST', 4);

define('HEX_CASTLE', 5);
define('HEX_WATER', 6);
define('HEX_MOUNTAIN', 7);

define('HEX_ORACLE', 8);
define('HEX_FARM', 9);
define('HEX_TAVERN', 10);
define('HEX_TOWER', 11);
define('HEX_HARBOR', 12);
define('HEX_PADDOCK', 13);
define('HEX_OASIS', 14);
define('HEX_BARN', 15);


/*
 * KB Cards
 */
define('CASTLE', 0);
define('FISHERMEN', 1);
define('MERCHANTS', 2);
define('DISCOVERERS', 3);
define('HERMITS', 4);
define('CITIZENS', 5);
define('MINERS', 6);
define('WORKERS', 7);
define('KNIGHTS', 8);
define('LORDS', 9);
define('FARMERS', 10);
