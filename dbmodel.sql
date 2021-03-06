
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- KingdomBuilder implementation : © Timothée Pecatte tim.pecatte@gmail.com
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

CREATE TABLE IF NOT EXISTS `piece` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(11),
   `type` varchar(16) NOT NULL DEFAULT 'settlement',
   `type_arg` varchar(16),
   `location` varchar(16) NOT NULL,
   `x` int(2),
   `y` int(2),
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `terrains` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `objectives` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `round` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `move_id` int(11) NOT NULL,
  `action` varchar(16) NOT NULL,
  `piece_id` int(11),
  `action_arg` json,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;
