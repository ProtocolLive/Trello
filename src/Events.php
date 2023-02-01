<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/Trello
//2023.02.01.00

namespace ProtocolLive\Trello;

/**
 * Webhook events - translationKey, because its more detailed
 */
enum Events:string{
  case CardArchived = 'action_archived_card';
  case CardDescription = 'action_changed_description_of_card';
  case CardAdd = 'action_create_card';
  case CardAddAttach = 'action_add_attachment_to_card';
  case CardDel = 'action_delete_card';
  case CardLabelAdd = 'action_add_label_to_card';
  case CardMemberAdd = 'action_added_member_to_card';
  case CardMemberDel = 'action_removed_member_from_card';
  case CardMoved = 'action_move_card_from_list_to_list';
  case CardRenamed = 'action_renamed_card';
}