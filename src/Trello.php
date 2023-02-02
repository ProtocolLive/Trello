<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/Trello
//2023.02.02.00

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

  /**
   * @param string $Member The ID or username of the member
   * @param string $Filter all or a comma-separated list of: closed, members, open, organization, public, starred
   * @param string $Fields all or a comma-separated list of board fields
   * 
   * Valid values: id, name, desc, descData, closed, idMemberCreator, idOrganization, pinned, url, shortUrl, prefs, labelNames, starred, limits, memberships, enterpriseOwned
   * @param string $Lists Which lists to include with the boards. One of: all, closed, none, open
   * 
   * Default: none
   * 
   * Valid values: all, closed, none, open
   * @param bool $Organization Whether to include the Organization object with the Boards
   * 
   * Default: false
   * @param string $OrganizationFields all or a comma-separated list of organization fields
   * 
   * Style: form
   * Valid values: id, name
   * @throws Exception
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-members/#api-members-id-boards-get
   */
  public function BoardsGet(
    string $Member = 'me',
    string $Filter = null,
    string $Fields = null,
    string $Lists = null,
    bool $Organization = false,
    string $OrganizationFields = null
  ):array{
    if($Filter !== null):
      $params['filter'] = $Filter;
    endif;
    if($Fields !== null):
      $params['fields'] = $Fields;
    endif;
    if($Lists !== null):
      $params['lists'] = $Lists;
    endif;
    if($Organization !== null):
      $params['organization'] = $Organization;
    endif;
    if($OrganizationFields !== null):
      $params['organization_fields'] = $OrganizationFields;
    endif;
    $return = $this->Curl('members/' . $Member . '/boards', $params);
    return json_decode($return);
  }

  /**
   * @param string $Name The name for the card
   * @param string $Description The description for the card
   * @param string $Position The position of the new card. top or bottom
   * @param string $DueDate A due date for the card
   * @param string $StartDate The start date of a card
   * @param string $List The ID of the list the card should be created in
   * @param array $Members Comma-separated list of member IDs to add to the card
   * @param array $Labels Comma-separated list of label IDs to add to the card
   * @param string $UrlSource A URL starting with http:// or https://
   * @param string $MimeType The mimeType of the attachment. Max length 256
   * @param string $CardSource The ID of a card to copy into the new card
   * @param string $KeepFromSource If using idCardSource you can specify which properties to copy over. all or comma-separated list of: attachments,checklists,customFields,comments,due,start,labels,members,start,stickers
   * @param string $Address For use with/by the Map View
   * @param string $LocationName For use with/by the Map View
   * @param string $Coordinates For use with/by the Map View. Should take the form latitude,longitude
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-post
   */
  public function CardAdd(
    string $List,
    string $Name,
    string $Description = null,
    string $Position = 'top',
    string $DueDate = null,
    string $StartDate = null,
    bool $DueComplete = null,
    array $Members = null,
    array $Labels = null,
    string $UrlSource = null,
    string $FileSource = null,
    string $MimeType = null,
    string $CardSource = null,
    string $KeepFromSource = null,
    string $Address = null,
    string $LocationName = null,
    string $Coordinates = null
  ):stdClass{
    $params['idList'] = $List;
    $params['name'] = $Name;
    $params['desc'] = $Description;
    $params['pos'] = $Position;
    $params['due'] = $DueDate;
    $params['start'] = $StartDate;
    $params['dueComplete'] = $DueComplete;
    $params['idMembers'] = $Members;
    $params['idLabels'] = $Labels;
    $params['urlSource'] = $UrlSource;
    $params['fileSource'] = $FileSource;
    $params['mimeType'] = $MimeType;
    $params['idCardSource'] = $CardSource;
    $params['keepFromSource'] = $KeepFromSource;
    $params['address'] = $Address;
    $params['locationName'] = $LocationName;
    $params['coordinates'] = $Coordinates;
    $return = $this->Curl('cards', $params, Type: 'POST');
    return json_decode($return);
  }

  /**
   * @param string $Card The ID of the Card
   * @param string $Name The new name for the card
   * @param string $Description The new description for the card
   * @param bool $Closed Whether the card should be archived
   * @param array $Members Comma-separated list of member IDs
   * @param string $CoverImage The ID of the image attachment the card should use as its cover, or null for none
   * @param string $List The ID of the list the card should be in
   * @param array $Labels Comma-separated list of label IDs
   * @param string $Board The ID of the board the card should be on
   * @param string $Position The position of the card in its list. top, bottom, or a positive float
   * @param string $DueDate When the card is due, or null
   * @param string $StartDate The start date of a card, or null
   * @param bool $DueComplete Whether the due date should be marked complete
   * @param bool $Subscribed Whether the member is should be subscribed to the card
   * @param string $Address For use with/by the Map View
   * @param string $LocationName For use with/by the Map View
   * @param string $Coordinates For use with/by the Map View. Should be latitude,longitude
   * @param string $Cover Updates the card's cover
   * 
   * color - pink, yellow, lime, blue, black, orange, red, purple, sky, green - Makes the cover a solid color
   * 
   * brightness - dark, light - Determines whether the text on the cover should be dark or light.
   * 
   * url - An unsplash URL: https://images.unsplash.com - Used if making an image the cover. Only Unsplash URLs work.
   * 
   * idAttachment - ID of an attachment on the card	Used if setting an attached image as the cover.
   *
   * size - normal, full - Determines whether to show the card name on the cover, or below it.
   * @link https://developer.atlassian.com/cloud/trello/rest/api-group-cards/#api-cards-id-put
   */
  public function CardEdit(
    string $Card,
    string $Name = null,
    string $Description = null,
    bool $Closed = null,
    array $Members = null,
    string $CoverImage = null,
    string $List = null,
    array $Labels = null,
    string $Board = null,
    string $Position = 'top',
    string $DueDate = null,
    string $StartDate = null,
    bool $DueComplete = null,
    bool $Subscribed = null,
    string $Address = null,
    string $LocationName = null,
    string $Coordinates = null,
    string $Cover = null
  ):stdClass{
    $params['name'] = $Name;
    $params['desc'] = $Description;
    $params['closed'] = $Closed;
    $params['idMembers'] = $Members;
    $params['idAttachmentCover'] = $CoverImage;
    $params['idList'] = $List;
    $params['idLabels'] = $Labels;
    $params['idBoard'] = $Board;
    $params['pos'] = $Position;
    $params['due'] = $DueDate;
    $params['start'] = $StartDate;
    $params['dueComplete'] = $DueComplete;
    $params['subscribed'] = $Subscribed;
    $params['address'] = $Address;
    $params['address'] = $LocationName;
    $params['coordinates'] = $Coordinates;
    $params['cover'] = $Cover;
    $return = $this->Curl('cards/' . $Card, $params, Type: 'PUT');
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
      $Type . ': ' . $Url . PHP_EOL . json_encode($Post, JSON_PRETTY_PRINT) . PHP_EOL,
      Start: true
    );
    $return = curl_exec($this->Curl);
    $this->Log(
      'Response: ' . PHP_EOL . json_encode(json_decode($return), JSON_PRETTY_PRINT),
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
    string $Msg,
    bool $Start = false,
    bool $End = false
  ):void{
    self::Log2($Msg, $Start, $End, $this->DirLogs);
  }

  private static function Log2(
    string $Msg,
    bool $Start,
    bool $End,
    string $DirLogs
  ):void{
    $msg = '';
    if($Start):
      $msg .= date('Y-m-d H:i:s') . ' ';
    endif;
    $msg .= $Msg;
    if($End):
      $msg .= PHP_EOL . PHP_EOL;
    endif;
    file_put_contents(
      $DirLogs . '/trello.log',
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

  public static function WebhookReceive(
    string $DirLogs
  ):array|stdclass|null{
    $temp = file_get_contents('php://input');
    $return = json_decode($temp);
    self::Log2(
      'Webhook:' . PHP_EOL . json_encode($return, JSON_PRETTY_PRINT),
      true,
      true,
      $DirLogs
    );
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