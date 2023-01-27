<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/Trello
//2023.01.27.02

namespace ProtocolLive\Trello;
use stdClass;

final class Trello{
  private const Url = 'https://api.trello.com/1/';

  public function __construct(
    private string $Key,
    private string $Token,
    private string $DirLogs
  ){}

  public function BoardsGet():array{
    $return = $this->Curl('members/me/boards');
    return json_decode($return);
  }
  
  public function CardsGet(
    string $List = null,
    string $Board = null,
    string $Member = null
  ):array{
    if($Board !== null):
      $return = $this->Curl('boards/' . $Board . '/cards');
    elseif($List !== null):
      $return = $this->Curl('lists/' . $List . '/cards');
    elseif($Member !== null):
      $return = $this->Curl('members/' . $Member . '/cards');
    endif;
    return json_decode($return);
  }

  public function CardGet(
    string $Card
  ):stdClass{
    $return = $this->Curl('cards/' . $Card);
    return json_decode($return);
  }

  private function Curl(
    string $Url,
    array $Post = null
  ):string|bool{
    $Url = self::Url . $Url . '?key=' . $this->Key . '&token=' . $this->Token;
    $curl = curl_init($Url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Protocol Trello library');
    curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    if($Post !== null):
      curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($Post));
    endif;
    return curl_exec($curl);
  }

  public function ListsGet(
    string $Board
  ):array{
    $return = $this->Curl('boards/' . $Board . '/lists');
    return json_decode($return);
  }

  private function Log(
    string $Msg
  ):void{
    file_put_contents(
      $this->DirLogs . '/send.log',
      $Msg . PHP_EOL,
      FILE_APPEND
    );
  }

  public function MemberGet(
    string $Id
  ):stdClass{
    $return = $this->Curl('members/' . $Id);
    return json_decode($return);
  }
}