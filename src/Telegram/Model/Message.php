<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class Message
{
	public string $Command = '';
	public array $Arguments = [];

	public function __construct(
		public ?int $MessageID,
		public DateTime $Date,
		public ?Chat $Chat,
		public bool $IsChannelPost = false,
		public bool $IsFromGroup = false,
		public bool $IsCallbackQuery = false,
		public ?User $From = null,
		public ?Chat $SenderChat = null,
		public ?User $ForwardFrom = null,
		public ?Chat $ForwardFromChat = null,
		public ?int $ForwardFromMessageID = null,
		public ?string $ForwardSignature = null,
		public ?string $ForwardSenderName = null,
		public ?DateTime $ForwardDate = null,
		public ?Message $ReplyToMessage = null,
		public ?User $ViaBot = null,
		public ?DateTime $EditDate = null,
		public ?string $MediaGroupID = null,
		public ?string $AuthorSignature = null,
		public string $Text = '',
		public ?MessageEntities $Entities = null,
		public ?Animation $Animation = null,
		public ?Audio $Audio = null,
		public ?Document $Document = null,
		public ?Photo $Photo = null,
		public ?Sticker $Sticker = null,
		public ?Video $Video = null,
		public ?VideoNote $VideoNote = null,
		public ?Voice $Voice = null,
		public ?string $Caption = null,
		public ?MessageEntities $CaptionEntities = null,
		public ?Contact $Contact = null,
		public ?Dice $Dice = null,
		public ?Game $Game = null,
		public ?Poll $Poll = null,
		public ?Venue $Venue = null,
		public ?Location $Location = null,
		public ?UsersArray $NewChatMembers = null,
		public ?User $LeftChatMember = null,
		public ?string $NewChatTitle = null,
		public ?Photo $NewChatPhoto = null,
		public ?bool $DeleteChatPhoto = null,
		public ?bool $GroupChatCreated = null,
		public ?bool $SupergroupChatCreated = null,
		public ?bool $ChannelChatCreated = null,
		public ?MessageAutoDeleteTimerChanged $MessageAutoDeleteTimerChanged = null,
		public ?int $MigrateToChatID = null,
		public ?int $MigrateFromChatID = null,
		public ?Message $PinnedMessage = null,
		public ?Invoice $Invoice = null,
		public ?SuccessfulPayment $SuccessfulPayment = null,
		public ?string $ConnectedWebsite = null,
		public ?PassportData $PassportData = null,
		public ?ProximityAlertTriggered $ProximityAlertTriggered = null,
		public ?VoiceChatScheduled $VoiceChatScheduled = null,
		public ?VoiceChatStarted $VoiceChatStarted = null,
		public ?VoiceChatEnded $VoiceChatEnded = null,
		public ?VoiceChatParticipantsInvited $VoiceChatParticipantsInvited = null,
		public ?string $CallbackQueryID = null,
		#ToDo: public ?InlineKeyboardMarkup $ReplyMarkup = null
	)
	{
		$TextParts = explode(' ', $this->Text);

		if(is_array($TextParts))
		{
			$this->Command = $TextParts[0];

			array_shift($TextParts);
			$this->Arguments = $TextParts;
		}

		print_r($this);
	}

	public static function FromTelegramFormat(?object $Object, bool $IsChannelPost = false): ?self
    {
        if($Object != null)
        {
			return new Message(
				MessageID: $Object->{'message_id'},
				Date: DateTime::createFromFormat('U', $Object->{'date'}),
				Chat: Chat::FromTelegramFormat($Object->{'chat'}),
				IsChannelPost: $IsChannelPost,
				IsFromGroup: !$IsChannelPost && isset($Object->{'chat'}->{'type'}) ? (in_array($Object->{'chat'}->{'type'}, ['supergroup', 'group']) ? true : false) : false,
				From: User::FromTelegramFormat($Object->{'from'} ?? null),
				SenderChat: Chat::FromTelegramFormat($Object->{'sender_chat'} ?? null),
				ForwardFrom: User::FromTelegramFormat($Object->{'forward_from'} ?? null),
				ForwardFromChat: Chat::FromTelegramFormat($Object->{'forward_from_chat'} ?? null),
				ForwardFromMessageID: $Object->{'forward_from_message_id'} ?? null,
				ForwardSignature: $Object->{'forward_signature'} ?? null,
				ForwardSenderName: $Object->{'forward_sender_name'} ?? null,
				ForwardDate: isset($Object->{'forward_date'}) ? DateTime::createFromFormat('U', $Object->{'forward_date'}) : null,
				ReplyToMessage: Message::FromTelegramFormat($Object->{'reply_to_message'} ?? null),
				ViaBot: User::FromTelegramFormat($Object->{'via_bot'} ?? null),
				EditDate: isset($Object->{'edit_date'}) ? DateTime::createFromFormat('U', $Object->{'edit_date'}) : null,
				MediaGroupID: $Object->{'media_group_id'} ?? null,
				AuthorSignature: $Object->{'author_signature'} ?? null,
				Text: $Object->{'text'} ?? '',
				Entities: MessageEntities::FromTelegramFormat($Object->{'entities'} ?? null),
				Animation: Animation::FromTelegramFormat($Object->{'animation'} ?? null),
				Audio: Audio::FromTelegramFormat($Object->{'audio'} ?? null),
				Document: Document::FromTelegramFormat($Object->{'document'} ?? null),
				Photo: Photo::FromTelegramFormat($Object->{'photo'} ?? null),
				Sticker: Sticker::FromTelegramFormat($Object->{'sticker'} ?? null),
				Video: Video::FromTelegramFormat($Object->{'video'} ?? null),
				VideoNote: VideoNote::FromTelegramFormat($Object->{'video_note'} ?? null),
				Voice: Voice::FromTelegramFormat($Object->{'voice'} ?? null),
				Caption: $Object->{'caption'} ?? null,
				CaptionEntities: MessageEntities::FromTelegramFormat($Object->{'caption_entities'} ?? null),
				Contact: Contact::FromTelegramFormat($Object->{'contact'} ?? null),
				Dice: Dice::FromTelegramFormat($Object->{'dice'} ?? null),
				Game: Game::FromTelegramFormat($Object->{'game'} ?? null),
				Poll: Poll::FromTelegramFormat($Object->{'poll'} ?? null),
				Venue: Venue::FromTelegramFormat($Object->{'venue'} ?? null),
				Location: Location::FromTelegramFormat($Object->{'location'} ?? null),
				NewChatMembers: UsersArray::FromTelegramFormat($Object->{'new_chat_members'} ?? null),
				LeftChatMember: User::FromTelegramFormat($Object->{'left_chat_member'} ?? null),
				NewChatTitle: $Object->{'new_chat_title'} ?? null,
				NewChatPhoto: Photo::FromTelegramFormat($Object->{'new_chat_photo'} ?? null),
				DeleteChatPhoto: $Object->{'delete_chat_photo'} ?? null,
				GroupChatCreated: $Object->{'group_chat_created'} ?? null,
				SupergroupChatCreated: $Object->{'supergroup_chat_created'} ?? null,
				ChannelChatCreated: $Object->{'channel_chat_created'} ?? null,
				MessageAutoDeleteTimerChanged: $Object->{'message_auto_delete_timer_changed'} ?? null,
				MigrateToChatID: $Object->{'migrate_to_chat_id'} ?? null,
				MigrateFromChatID: $Object->{'migrate_from_chat_id'} ?? null,
				PinnedMessage: Message::FromTelegramFormat($Object->{'pinned_message'} ?? null),
				Invoice: Invoice::FromTelegramFormat($Object->{'invoice'} ?? null),
				SuccessfulPayment: SuccessfulPayment::FromTelegramFormat($Object->{'successful_payment'} ?? null),
				ConnectedWebsite: $Object->{'connected_website'} ?? null,
				PassportData: PassportData::FromTelegramFormat($Object->{'passport_data'} ?? null),
				ProximityAlertTriggered: ProximityAlertTriggered::FromTelegramFormat($Object->{'proximity_alert_triggered'} ?? null),
				VoiceChatScheduled: VoiceChatScheduled::FromTelegramFormat($Object->{'voice_chat_scheduled'} ?? null),
				VoiceChatStarted: isset($Object->{'voice_chat_started'}) ? VoiceChatStarted::FromTelegramFormat($Object->{'voice_chat_started'} ?? null) : null,
				VoiceChatEnded: VoiceChatEnded::FromTelegramFormat($Object->{'voice_chat_ended'} ?? null),
				VoiceChatParticipantsInvited: VoiceChatParticipantsInvited::FromTelegramFormat($Object->{'voice_chat_participants_invited'} ?? null),
			);
        }

        return null;
    }

	public static function FromTelegramCallbackQueryFormat(?object $Object, bool $IsChannelPost = false): ?self
	{
		if($Object != null)
        {
			return new Message(
				MessageID: $Object->{'message'}->{'message_id'} ?? null,
				Date: new DateTime(),
				Chat: Chat::FromTelegramFormat($Object->{'message'}->{'chat'} ?? null),
				IsChannelPost: $IsChannelPost,
				IsFromGroup: !$IsChannelPost && isset($Object->{'chat'}->{'type'}) ? (in_array($Object->{'chat'}->{'type'}, ['supergroup', 'group']) ? true : false) : false,
				From: User::FromTelegramFormat($Object->{'from'} ?? null),
				Text: $Object->{'data'} ?? '',
				CallbackQueryID: $Object->{'id'},
				IsCallbackQuery: true,
			);
		}

		return null;
	}
}

?>