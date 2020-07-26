<?php

abstract class KingdomBuilderObjective extends APP_GameClass
{
  protected $game;

  public function __construct($game)
  {
    $this->game = $game;
  }


  protected $id = 0;
  protected $name = '';
  protected $desc = '';
  protected $stat = '';
  protected $text = [];

  public function getId() { return $this->id;  }
  public function getStatId() { return $this->id + 100;  }
  public function getStatName() { return $this->stat; }
  public function getName() { return $this->name; }
  public function getDesc() { return $this->desc; }
  public function getText() { return $this->text; }
  public function getUiData()
  {
    return [
      'id'        => $this->id,
      'name'      => $this->name,
      'desc'      => $this->desc,
      'text'      => $this->text,
    ];
  }

  protected $result;
  protected function scoringEndPlayer($playerId){
    $this->result = [
      'detail' => [],
      'highlights' => [],
      'total' => 0,
    ];
  }

  protected function addScoring($hexes, $score, $highlights = null)
  {
    $hexes = array_key_exists('x', $hexes)? [$hexes] : $hexes;
    array_push($this->result['detail'], [
      'hexes' => $hexes,
      'score' => $score,
    ]);
    $highlights = $highlights ?? $hexes;
    $this->result['highlights'] = array_merge($this->result['highlights'], $highlights);
    $this->result['total'] += $score;
  }
  protected function addHighlight($hexes)
  {
    if(array_key_exists('x', $hexes))
      array_push($this->result['highlights'], $hexes);
    else
      $this->result['highlights'] = array_merge($this->result['highlights'], $hexes);
  }

  public function scoringEnd()
  {
    $row = [ ['str' => '${objective_name}', 'args' => ['objective_name' => $this->getName()] ] ];
    foreach($this->game->playerManager->getPlayers() as $player){
      $this->scoringEndPlayer($player->getId());
      $row[] = $this->result['total'];
      $this->game->log->incrementStats([ [$player->getId(), $this->getName(), $this->result['total']] ]);


      self::DbQuery("UPDATE player SET player_score = player_score + {$this->result['total']} WHERE player_id='{$player->getId()}'" );
      $msg = clienttranslate('${objective_name}: ${player_name} obtains ${total} gold');
      if($this->result['total'] == 1) $msg = clienttranslate('${objective_name}: ${player_name} obtains 1 gold');
      if($this->result['total'] == 0) $msg = clienttranslate('${objective_name}: ${player_name} doesn\'t obtain any gold');
      $this->game->notifyAllPlayers('scoringEnd', $msg, [
        'i18n' => ['objective_name'],
        'objective_name' => $this->getName(),
        'player_name' => $player->getName(),
        'playerId' => $player->getId(),
        'objectiveId' => $this->getId(),
        'total' => $this->result['total'],
        'detail' => $this->result['detail'],
        'highlights' => $this->result['highlights'],
      ]);
    }

    return $row;
  }
}
