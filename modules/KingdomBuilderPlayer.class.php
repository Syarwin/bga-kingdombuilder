<?php

/*
 * KingdomBuilderPlayer: all utility functions concerning a player
 */
class KingdomBuilderPlayer extends APP_GameClass
{
  private $game;
  private $id;
  private $no; // natural order
  private $name;
  private $color;
  private $eliminated = false;
  private $zombie = false;

  public function __construct($game, $row)
  {
    $this->game = $game;
    $this->id = (int) $row['id'];
    $this->no = (int) $row['no'];
    $this->name = $row['name'];
    $this->color = $row['color'];
    $this->eliminated = $row['eliminated'] == 1;
    $this->zombie = $row['zombie'] == 1;
  }


  public function setupNewGame()
  {
    $sqlSettlements = 'INSERT INTO piece (player_id, location) VALUES ';
    $values = [];
    for($i = 0; $i < 40; $i++){
      $values[] = "('" . $this->id . "','hand')";
    }
    self::DbQuery($sqlSettlements . implode($values, ','));
  }


  public function getId(){ return $this->id; }
  public function getNo(){ return $this->no; }
  public function getName(){ return $this->name; }
  public function getColor(){ return $this->color; }
  public function isEliminated(){ return $this->eliminated; }
  public function isZombie(){ return $this->zombie; }
  public function getSettlements(){ return $this->game->board->getSettlements($this->id); }
  public function getSettlementsInHand()
  {
    return count(array_filter($this->getSettlements(), function($settlement){
      return $settlement['location'] == 'hand';
    }));
  }


  public function getUiData()
  {
    return [
      'id'        => $this->id,
      'no'        => $this->no,
      'name'      => $this->name,
      'color'     => $this->color,
      'settlements' => $this->getSettlementsInHand(),
    ];
  }


  public function build($pos)
  {
    $settlement = self::getObjectFromDB("SELECT * FROM piece WHERE player_id = {$this->id} AND location = 'hand' LIMIT 1");
    if(is_null($settlement))
      throw new BgaUserException(_("You have no more settlements left in your hand"));

    self::DbQuery("UPDATE piece SET x = {$pos['x']}, y = {$pos['y']}, location = 'board' WHERE id = {$settlement['id']}");
    $this->game->notifyAllPlayers('build', clienttranslate('${player_name} build a settlement'), [
      'player_name' => $this->getName(),
      'player_id' => $this->getId(),
      'x' => $pos['x'],
      'y' => $pos['y'],
    ]);
  }
}
