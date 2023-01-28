<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/Trello
//2023.01.28.00

namespace ProtocolLive\Trello;

/**
 * Webhook events
 */
enum Events:string{
  case CardMemberAdd = 'addMemberToCard';
  case CardMemberDel = 'removeMemberFromCard';
  case CardUpdated = 'updateCard';
}