<?php

/*
 * KingdomBuilderBoard: all utility functions concerning space on the board are here
 */
class KingdomBuilderBoard extends APP_GameClass
{
    public $game;
    public function __construct($game)
    {
        $this->game = $game;
    }

    public function getUiData()
    {
        return [
            'settlements' => $this->getPlacedSettlements(),
            'locations' => $this->getLocations(),
            'tiles' => $this->getTilesOnBoard(),
        ];
    }

    // 0 - 4 : building, 5 : castle, 6 water, 7 mine, >= 8 location
    public static $boards = [
        [
            [1, 2, 2, 2, 2, 2, 2, 2, 2, 2],
            [1, 1, 1, 2, 2, 2, 2, 2, 1, 2],
            [7, 7, 7, 2, 7, 7, 15, 2, 2, 1],
            [1, 7, 7, 7, 7, 7, 3, 3, 1, 1],
            [1, 1, 1, 7, 7, 6, 3, 3, 3, 1],
            [0, 1, 1, 1, 7, 3, 3, 6, 4, 1],
            [0, 0, 15, 4, 3, 3, 6, 3, 3, 4],
            [0, 0, 4, 4, 3, 3, 0, 5, 4, 4],
            [0, 0, 0, 4, 4, 6, 0, 0, 4, 4],
            [0, 0, 0, 4, 6, 0, 0, 4, 4, 4],
        ],
        [
            [2, 2, 1, 6, 6, 4, 4, 4, 0, 0],
            [2, 5, 1, 6, 4, 4, 4, 9, 0, 0],
            [1, 1, 1, 3, 3, 3, 4, 1, 3, 3],
            [1, 1, 3, 3, 6, 2, 2, 1, 1, 3],
            [1, 0, 0, 6, 3, 3, 2, 2, 1, 1],
            [0, 0, 9, 3, 6, 3, 6, 2, 2, 1],
            [0, 0, 0, 4, 3, 3, 6, 6, 2, 2],
            [0, 0, 4, 4, 7, 6, 6, 6, 2, 6],
            [0, 7, 4, 4, 6, 6, 6, 6, 6, 6],
            [4, 4, 4, 6, 6, 6, 6, 6, 6, 6],
        ],
        [
            [0, 0, 0, 4, 4, 6, 0, 4, 4, 4],
            [0, 0, 0, 5, 4, 6, 0, 4, 4, 4],
            [0, 3, 3, 0, 4, 4, 6, 0, 0, 4],
            [3, 3, 1, 0, 4, 6, 3, 8, 4, 4],
            [3, 3, 3, 1, 1, 6, 3, 3, 6, 6],
            [7, 7, 1, 0, 0, 6, 6, 6, 2, 2],
            [1, 1, 1, 7, 0, 3, 3, 3, 2, 2],
            [1, 1, 5, 2, 7, 2, 3, 3, 1, 1],
            [6, 6, 6, 2, 2, 2, 2, 7, 1, 1],
            [6, 6, 6, 6, 2, 2, 2, 2, 2, 1],
        ],
        [
            [0, 0, 4, 4, 4, 6, 0, 4, 4, 3],
            [0, 3, 4, 4, 6, 0, 4, 4, 3, 3],
            [0, 3, 3, 4, 6, 0, 0, 3, 3, 3],
            [3, 3, 4, 4, 6, 0, 7, 3, 2, 2],
            [1, 3, 5, 4, 6, 0, 2, 2, 2, 2],
            [1, 1, 4, 6, 0, 0, 7, 7, 2, 2],
            [1, 1, 6, 6, 6, 0, 2, 2, 2, 1],
            [6, 6, 0, 0, 6, 6, 12, 1, 7, 1],
            [6, 2, 5, 0, 6, 7, 6, 1, 1, 1],
            [6, 2, 2, 6, 6, 6, 6, 1, 1, 1],
        ],
        [
            [4, 4, 4, 4, 7, 7, 0, 7, 1, 1],
            [4, 7, 4, 4, 3, 0, 7, 7, 7, 1],
            [3, 3, 4, 3, 3, 3, 0, 0, 6, 7],
            [2, 3, 3, 3, 6, 11, 0, 6, 7, 7],
            [2, 2, 2, 2, 3, 6, 0, 6, 1, 1],
            [2, 1, 2, 2, 2, 6, 6, 1, 0, 1],
            [2, 2, 1, 2, 2, 6, 3, 5, 0, 1],
            [1, 1, 11, 2, 6, 3, 3, 3, 0, 0],
            [2, 1, 6, 6, 6, 4, 4, 3, 0, 0],
            [2, 1, 1, 6, 4, 4, 4, 0, 0, 0],
        ],
        [
            [2, 2, 1, 6, 6, 4, 4, 0, 0, 0],
            [2, 1, 6, 3, 3, 4, 4, 4, 0, 0],
            [2, 2, 6, 3, 3, 4, 4, 14, 3, 0],
            [6, 6, 6, 3, 0, 4, 3, 3, 3, 3],
            [6, 6, 6, 6, 0, 0, 0, 0, 3, 3],
            [6, 4, 4, 6, 0, 0, 1, 1, 2, 1],
            [6, 4, 1, 4, 6, 0, 1, 1, 2, 1],
            [6, 5, 1, 3, 6, 14, 2, 2, 1, 6],
            [6, 6, 1, 3, 6, 6, 6, 2, 2, 6],
            [6, 6, 6, 6, 6, 6, 6, 6, 6, 6],
        ],
        [
            [1, 1, 1, 2, 2, 6, 2, 2, 2, 2],
            [7, 7, 1, 2, 2, 6, 2, 2, 2, 2],
            [7, 7, 1, 7, 7, 6, 2, 2, 13, 3],
            [7, 1, 7, 7, 6, 7, 2, 3, 3, 3],
            [1, 1, 4, 4, 6, 7, 7, 1, 3, 3],
            [1, 4, 4, 6, 1, 1, 1, 7, 3, 3],
            [1, 13, 4, 4, 6, 3, 3, 3, 3, 3],
            [0, 0, 4, 6, 0, 5, 0, 3, 0, 4],
            [0, 0, 4, 4, 6, 0, 0, 0, 0, 4],
            [0, 0, 4, 4, 6, 0, 0, 0, 4, 4],
        ],
        [
            [3, 2, 2, 7, 7, 2, 2, 1, 1, 1],
            [3, 3, 2, 2, 2, 7, 7, 1, 1, 1],
            [3, 3, 3, 3, 3, 3, 3, 7, 7, 7],
            [6, 6, 3, 5, 0, 0, 4, 4, 7, 7],
            [3, 3, 6, 6, 0, 0, 0, 4, 4, 1],
            [3, 1, 1, 6, 0, 4, 4, 1, 1, 1],
            [2, 3, 10, 1, 6, 4, 4, 10, 1, 0],
            [2, 2, 1, 6, 4, 4, 0, 0, 0, 0],
            [2, 2, 2, 6, 4, 4, 4, 0, 0, 0],
            [2, 2, 6, 6, 4, 4, 4, 0, 0, 0],
        ],
        // NOMADS
        [
            [6, 6, 0, 0, 6, 6, 6, 3, 3, 3],
            [6, 0, 0, 4, 6, 6, 6, 3, 3, 3],
            [6, 6, 6, 0, 4, 6, 6, 2, 2, 3],
            [4, 4, 6, 20, 4, 6, 2, 7, 0, 0],
            [4, 4, 4, 6, 6, 6, 2, 2, 0, 0],
            [4, 4, 4, 4, 1, 2, 0, 0, 0, 0],
            [4, 4, 16, 1, 1, 1, 0, 0, 7, 0],
            [4, 1, 1, 1, 1, 0, 16, 2, 2, 0],
            [1, 7, 1, 1, 1, 3, 3, 3, 2, 2],
            [1, 1, 1, 3, 3, 3, 3, 2, 2, 2],
        ],
        [
            [0, 0, 0, 6, 6, 6, 7, 1, 1, 1],
            [0, 0, 6, 4, 2, 6, 6, 6, 1, 1],
            [0, 7, 6, 4, 19, 2, 2, 2, 6, 7],
            [3, 6, 4, 4, 1, 1, 2, 3, 3, 3],
            [3, 6, 0, 0, 1, 1, 2, 2, 7, 3],
            [3, 6, 20, 0, 1, 1, 2, 20, 3, 3],
            [3, 3, 6, 0, 1, 1, 1, 3, 3, 3],
            [2, 6, 0, 0, 1, 20, 4, 4, 3, 3],
            [2, 2, 6, 6, 0, 4, 4, 7, 4, 4],
            [2, 2, 2, 6, 6, 4, 7, 4, 4, 4],
        ],
        [
            [1, 1, 1, 6, 6, 6, 6, 2, 2, 2],
            [1, 1, 1, 6, 18, 3, 2, 2, 7, 2],
            [1, 1, 1, 1, 1, 1, 3, 2, 7, 2],
            [1, 0, 0, 1, 1, 7, 3, 7, 2, 2],
            [1, 0, 0, 0, 2, 7, 7, 3, 3, 3],
            [4, 3, 0, 18, 2, 2, 3, 3, 3, 3],
            [4, 4, 3, 3, 2, 7, 3, 0, 0, 3],
            [4, 4, 7, 3, 7, 4, 20, 0, 0, 0],
            [4, 7, 4, 4, 4, 4, 6, 6, 0, 0],
            [4, 4, 4, 4, 6, 6, 6, 0, 0, 0],
        ],
        [
            [2, 2, 2, 2, 2, 2, 2, 0, 0, 0],
            [2, 2, 2, 7, 2, 3, 3, 3, 0, 0],
            [2, 7, 2, 2, 2, 3, 6, 3, 0, 4],
            [1, 4, 6, 2, 20, 6, 3, 4, 4, 4],
            [1, 4, 4, 6, 6, 6, 2, 17, 1, 4],
            [1, 4, 4, 4, 6, 6, 3, 1, 1, 1],
            [1, 1, 1, 17, 4, 6, 6, 3, 7, 1],
            [0, 1, 0, 0, 4, 6, 3, 1, 1, 1],
            [0, 0, 0, 0, 4, 4, 4, 4, 1, 7],
            [0, 0, 0, 4, 4, 4, 4, 4, 7, 7],
        ],
    ];

    public function setupNewGame($optionSetup)
    {
        // Create board
        $quadrants = [];
        if ($optionSetup == BASIC) {
            $quadrants = [7, 6, 5, 1];
        } else {
            $ids = [0, 1, 2, 3, 4, 5, 6, 7];
            if ($this->game->isNomads()) {
                $ids = array_merge($ids, [8, 9, 10, 11]);
            }

            $quadrants = array_rand($ids, 4);
            shuffle($quadrants);
            for ($i = 0; $i < 4; $i++) {
                if (rand(0, 1) == 1) {
                    $quadrants[$i] += 100;
                }
            }
        }

        $this->game->log->initBoard($quadrants);

        // Create location token
        $board = $this->getBoard();
        for ($x = 0; $x < 20; $x++) {
            for ($y = 0; $y < 20; $y++) {
                if ($board[$x][$y] >= HEX_ORACLE) {
                    self::DbQuery(
                        "INSERT INTO piece (type, type_arg, location, x, y) VALUES ('tile', '{$board[$x][$y]}', 'board', $x, $y)"
                    );
                    self::DbQuery(
                        "INSERT INTO piece (type, type_arg, location, x, y) VALUES ('tile', '{$board[$x][$y]}', 'board', $x, $y)"
                    );
                }
            }
        }
    }

    /*##################
    #### DB Getters ####
    ##################*/
    public function getQuadrants()
    {
        return $this->game->log->getQuadrants();
    }

    public function getTile($id)
    {
        return self::getObjectFromDB(
            "SELECT id, type_arg AS location, x, y, player_id FROM piece WHERE id = $id LIMIT 1"
        );
    }

    public function getTilesOnBoard()
    {
        return self::getObjectListFromDB(
            "SELECT id, type_arg AS location, x, y FROM piece WHERE type = 'tile' AND location = 'board'"
        );
    }

    public function getLocations()
    {
        $locations = [];
        $board = $this->getBoard();
        for ($x = 0; $x < 20; $x++) {
            for ($y = 0; $y < 20; $y++) {
                if (
                    $board[$x][$y] >= HEX_ORACLE ||
                    $board[$x][$y] == HEX_CASTLE
                ) {
                    array_push($locations, [
                        'x' => $x,
                        'y' => $y,
                        'n' => 0,
                        'location' => $board[$x][$y],
                    ]);
                }
            }
        }

        $tiles = $this->getTilesOnBoard();
        foreach ($tiles as $tile) {
            foreach ($locations as &$location) {
                if (
                    $tile['x'] == $location['x'] &&
                    $tile['y'] == $location['y']
                ) {
                    $location['n']++;
                }
            }
        }

        return $locations;
    }

    public function getPlacedSettlements($pId = null)
    {
        $where = is_null($pId) ? '' : " AND player_id = $pId";
        return self::getObjectListFromDB(
            "SELECT * FROM piece WHERE type = 'settlement' AND location = 'board'" .
                $where
        );
    }

    public function getPlacedSettlementsCoords($pId = null)
    {
        $settlements = $this->getPlacedSettlements($pId);
        return array_map(['KingdomBuilderBoard', 'getCoords'], $settlements);
    }

    public function getSettlements($pId)
    {
        return self::getObjectListFromDB(
            "SELECT * FROM piece WHERE type = 'settlement' AND player_id = $pId"
        );
    }

    /*##################
#### Grid utils ####
##################*/

    public static function getCoords($piece)
    {
        return ['x' => (int) $piece['x'], 'y' => (int) $piece['y']];
    }

    public static function compareCoords($a, $b)
    {
        $dx = (int) $b['x'] - (int) $a['x'];
        $dy = (int) $b['y'] - (int) $a['y'];
        if ($dx != 0) {
            return $dx;
        }
        return $dy;
    }

    public function getNeighbours($space, $reindex = true)
    {
        $x = (int) $space['x'];
        $y = (int) $space['y'];
        $hexes = [];
        $hexes[] = ['x' => $x, 'y' => $y - 1];
        $hexes[] = ['x' => $x - 1, 'y' => $y + ($x % 2 == 0 ? -1 : 0)];
        $hexes[] = ['x' => $x - 1, 'y' => $y + ($x % 2 == 0 ? 0 : 1)];
        $hexes[] = ['x' => $x, 'y' => $y + 1];
        $hexes[] = ['x' => $x + 1, 'y' => $y + ($x % 2 == 0 ? 0 : 1)];
        $hexes[] = ['x' => $x + 1, 'y' => $y + ($x % 2 == 0 ? -1 : 0)];
        $hexes = array_filter($hexes, function ($hex) {
            return $hex['x'] >= 0 &&
                $hex['y'] >= 0 &&
                $hex['x'] < 20 &&
                $hex['y'] < 20;
        });
        if ($reindex) {
            $hexes = array_values($hexes);
        }
        return $hexes;
    }

    public function getNeighboursIntersect($space, $positions)
    {
        return array_values(
            array_uintersect($this->getNeighbours($space), $positions, [
                'KingdomBuilderBoard',
                'compareCoords',
            ])
        );
    }

    public function getBoard()
    {
        $board = [];
        for ($i = 0; $i < 20; $i++) {
            $board[$i] = [];
        }

        $quadrants = $this->getQuadrants();
        for ($k = 0; $k < 4; $k++) {
            $nBoard = (int) $quadrants[$k];
            $flipped = $nBoard >= 100;
            if ($flipped) {
                $nBoard -= 100;
            }

            for ($i = 0; $i < 10; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    $x = $flipped ? 9 - $i : $i;
                    $y = $flipped ? 9 - $j : $j;
                    $type = self::$boards[$nBoard][$i][$j];

                    if ($k == 1 || $k == 3) {
                        $y += 10;
                    }
                    if ($k == 2 || $k == 3) {
                        $x += 10;
                    }
                    $board[$x][$y] = $type;
                }
            }
        }

        return $board;
    }

    public function getQuadrant($hex)
    {
        if ($hex['x'] < 10 && $hex['y'] < 10) {
            return 0;
        }
        if ($hex['x'] < 10 && $hex['y'] >= 10) {
            return 1;
        }
        if ($hex['x'] >= 10 && $hex['y'] < 10) {
            return 2;
        }
        if ($hex['x'] >= 10 && $hex['y'] >= 10) {
            return 3;
        }
        return -1;
    }

    /*#################################
#### Computing available hexes ####
#################################*/
    public function getHexesOfType($types)
    {
        $hexes = [];
        $board = $this->getBoard();
        if (!is_array($types)) {
            $types = [$types];
        }

        for ($x = 0; $x < 20; $x++) {
            for ($y = 0; $y < 20; $y++) {
                if (in_array($board[$x][$y], $types)) {
                    $hexes[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        return $hexes;
    }

    public function keepFreeHexes(&$hexes)
    {
        $settlements = $this->getPlacedSettlementsCoords();
        $hexes = array_values(
            array_udiff($hexes, $settlements, [
                'KingdomBuilderBoard',
                'compareCoords',
            ])
        );
    }

    public function getFreeHexesOfType($type)
    {
        $hexes = $this->getHexesOfType($type);
        $this->keepFreeHexes($hexes);
        return $hexes;
    }

    public function getFreePerimeterHexes($types = [])
    {
        $hexes = [];
        for ($x = 0; $x < 20; $x++) {
            array_push($hexes, ['x' => $x, 'y' => 0], ['x' => $x, 'y' => 19]);
        }

        for ($y = 1; $y < 19; $y++) {
            array_push($hexes, ['x' => 0, 'y' => $y], ['x' => 19, 'y' => $y]);
        }

        $this->keepFreeHexes($hexes);

        if (!empty($types)) {
            $board = $this->getBoard();
            Utils::filter($hexes, function ($hex) use ($board, $types) {
                return in_array($board[$hex['x']][$hex['y']], $types);
            });
        }

        return $hexes;
    }

    public function getPlacedSettlementsNeighbouringSpaces($pId)
    {
        $settlements = $this->getPlacedSettlements($pId);
        $hexes = [];
        foreach ($settlements as $settlement) {
            $hexes = array_merge($hexes, $this->getNeighbours($settlement));
        }
        return $hexes;
    }

    public function keepAdjacentIfPossible(&$hexes, $pId)
    {
        $hexesNeighbouring = array_values(
            array_uintersect(
                $hexes,
                $this->game->board->getPlacedSettlementsNeighbouringSpaces(
                    $pId
                ),
                ['KingdomBuilderBoard', 'compareCoords']
            )
        );
        if (count($hexesNeighbouring) > 0) {
            $hexes = $hexesNeighbouring;
        }
    }

    public function getAvailableHexes($type, $pId = null)
    {
        $pId = $pId ?? $this->game->getActivePlayerId();

        $hexes = $this->getFreeHexesOfType($type);
        $this->keepAdjacentIfPossible($hexes, $pId);
        return $hexes;
    }

    public function getConnectedComponents($pId, $withLocations = true)
    {
        $settlements = $this->getPlacedSettlements($pId);
        if ($withLocations) {
            $settlements = array_merge(
                $settlements,
                $this->getHexesOfType(
                    $this->game->locationManager->getLocationTypes()
                )
            );
        }

        $board = [];
        for ($i = 0; $i < 20; $i++) {
            $board[$i] = [];
            for ($j = 0; $j < 20; $j++) {
                $board[$i][$j] = 0;
            }
        }

        $k = 1;
        foreach ($settlements as $settlement) {
            if ($board[$settlement['x']][$settlement['y']] != 0) {
                continue;
            }

            $todo = [];
            array_push($todo, $settlement);
            $board[$settlement['x']][$settlement['y']] = $k;
            while (!empty($todo)) {
                $cell = array_pop($todo);

                foreach (
                    $this->getNeighboursIntersect($cell, $settlements)
                    as $n
                ) {
                    if ($board[$n['x']][$n['y']] == 0) {
                        $board[$n['x']][$n['y']] = $k;
                        array_push($todo, $n);
                    }
                }
            }

            $k++;
        }

        return $board;
    }
}
