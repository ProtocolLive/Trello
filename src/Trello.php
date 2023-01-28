<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/Trello
//2023.01.28.01

namespace ProtocolLive\Trello;
use CurlHandle;
use Exception;
use stdClass;

final class Trello{
  private const Url = 'https://api.trello.com/1/';
  private CurlHandle $Curl;

  public function __construct(
    private string $Key,
    private string $Token,
    private string $DirLogs
  ){}

  public function BoardsGet():array{
    $return = $this->Curl('members/me/boards');
    return json_decode($return);
  }
  
  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-boards/#api-boards-id-cards-get
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-lists/#api-lists-id-cards-get
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-members/#api-members-id-cards-get
   */
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

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-get
   */
  public function CardGet(
    string $Card
  ):stdClass{
    $return = $this->Curl('cards/' . $Card);
    return json_decode($return);
  }

  /**
   * @throws Exception
   */
  private function Curl(
    string $Url,
    array $Get = [],
    array $Post = null,
    string $Type = null
  ):string|bool{
    $Get['key'] = $this->Key;
    $Get['token'] = $this->Token;
    $Url = self::Url . $Url . '?' . http_build_query($Get);
    $this->Curl = curl_init($Url);
    curl_setopt($this->Curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->Curl, CURLOPT_USERAGENT, 'Protocol Trello library');
    curl_setopt($this->Curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    if($Post !== null):
      curl_setopt($this->Curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($this->Curl, CURLOPT_POSTFIELDS, json_encode($Post));
    endif;
    if($Type !== null):
      curl_setopt($this->Curl, CURLOPT_CUSTOMREQUEST, $Type);
    endif;
    $this->Log(
      'send',
      $Url . PHP_EOL . json_encode($Post, JSON_PRETTY_PRINT),
      Start: true
    );
    $return = curl_exec($this->Curl);
    $this->Log(
      'response',
      json_encode($return, JSON_PRETTY_PRINT),
      End: true
    );
    if(curl_getinfo($this->Curl, CURLINFO_HTTP_CODE) === 200):
      return $return;
    else:
      throw new Exception($return);
    endif;
  }

  /**
   * https://developer.atlassian.com/cloud/trello/rest/api-group-boards/#api-boards-id-lists-get
   */
  public function ListsGet(
    string $Board
  ):array{
    $return = $this->Curl('boards/' . $Board . '/lists');
    return json_decode($return);
  }

  private function Log(
    string $Event,
    string|stdClass $Msg,
    bool $Start = false,
    bool $End = false
  ):void{
    $msg = '';
    if($Start):
      $msg .= date('Y-m-d H:i:s');
    endif;
    ob_start();
    var_dump($Msg);
    $msg .= ob_get_contents();
    ob_end_clean();
    if($End):
      $msg .= PHP_EOL;
    endif;
    file_put_contents(
      $this->DirLogs . '/' . $Event . '.log',
      $msg,
      FILE_APPEND
    );
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/guides/rest-api/authorization/#authorizing-a-client
   */
  public function MeGet():stdClass{
    $return = $this->Curl('members/me');
    return json_decode($return);
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-members/#api-members-id-get
   */
  public function MemberGet(
    string $Id
  ):stdClass{
    $return = $this->Curl('members/' . $Id);
    return json_decode($return);
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-boards/#api-boards-id-members-get
   */
  public function MembersGet(
    string $Board
  ):array{
    $return = $this->Curl('boards/' . $Board . '/members');
    return json_decode($return);
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-webhooks/#api-webhooks-id-delete
   */
  public function WebhookDel(
    string $Id
  ):void{
    $this->Curl('webhooks/' . $Id, Type: 'DELETE');
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-webhooks/#api-webhooks-id-get
   */
  public function WebhookGet(
    string $Id = null
  ):array|stdClass{
    if($Id !== null):
      $return = $this->Curl('webhooks/' . $Id);
      return json_decode($return);
    endif;
    $return = $this->Curl('members/me/tokens', ['webhooks' => true]);
    $return = json_decode($return);
    return $return[0]->webhooks;
  }

  public function WebhookReceive():array|stdclass{
    $temp = file_get_contents('php://input');
    $return = json_decode($temp);
    $this->Log('webhook', $return);
    return $return;
  }

  /**
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-webhooks/#api-webhooks-post
   */
  public function WebhookSet(
    string $Description,
    string $Url,
    string $Model
  ):stdClass{
    $return = $this->Curl(
      'tokens/' . $this->Token . '/webhooks',
      Post: [
        'description' => $Description,
        'callbackURL' => $Url,
        'idModel' => $Model,
      ]
    );
    return json_decode($return);
  }
}