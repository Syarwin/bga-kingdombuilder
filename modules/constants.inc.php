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
define('FISHERMEN', 0);
define('MERCHANTS', 1);
define('DISCOVERERS', 2);
define('HERMITS', 3);
define('CITIZENS', 4);
define('MINERS', 5);
define('WORKERS', 6);
define('KNIGHTS', 7);
define('LORDS', 8);
define('FARMERS', 9);
