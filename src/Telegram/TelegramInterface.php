<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram;

use DateTime;
use WeRtOG\BottoGram\Telegram\Model\BotCommandArray;
use WeRtOG\BottoGram\Telegram\Model\Chat;
use WeRtOG\BottoGram\Telegram\Model\ChatInviteLink;
use WeRtOG\BottoGram\Telegram\Model\ChatMember;
use WeRtOG\BottoGram\Telegram\Model\ChatMemberArray;
use WeRtOG\BottoGram\Telegram\Model\ChatPermissions;
use WeRtOG\BottoGram\Telegram\Model\File;
use WeRtOG\BottoGram\Telegram\Model\GameHighScoreArray;
use WeRtOG\BottoGram\Telegram\Model\InlineKeyboardMarkup;
use WeRtOG\BottoGram\Telegram\Model\InlineQueryResultArray;
use WeRtOG\BottoGram\Telegram\Model\InputFile;
use WeRtOG\BottoGram\Telegram\Model\InputMedia;
use WeRtOG\BottoGram\Telegram\Model\InputMediaArray;
use WeRtOG\BottoGram\Telegram\Model\LabeledPriceArray;
use WeRtOG\BottoGram\Telegram\Model\MaskPosition;
use WeRtOG\BottoGram\Telegram\Model\Message;
use WeRtOG\BottoGram\Telegram\Model\MessageArray;
use WeRtOG\BottoGram\Telegram\Model\MessageEntities;
use WeRtOG\BottoGram\Telegram\Model\MessageID;
use WeRtOG\BottoGram\Telegram\Model\ParseMode;
use WeRtOG\BottoGram\Telegram\Model\PassportElementErrorArray;
use WeRtOG\BottoGram\Telegram\Model\Poll;
use WeRtOG\BottoGram\Telegram\Model\ReplyMarkup;
use WeRtOG\BottoGram\Telegram\Model\ShippingOptionArray;
use WeRtOG\BottoGram\Telegram\Model\StickerSetArray;
use WeRtOG\BottoGram\Telegram\Model\Update;
use WeRtOG\BottoGram\Telegram\Model\UpdatesArray;
use WeRtOG\BottoGram\Telegram\Model\User;
use WeRtOG\BottoGram\Telegram\Model\WebhookInfo;

interface TelegramInterface
{
    public function __construct(string $Token);

    public function GetUpdateFromInput(): ?Update;

    public function GetUpdates(
        ?int $Offset = null,
        ?int $Limit = null,
        ?int $Timeout = null,
        ?array $AllowedUpdates = null
    ): UpdatesArray|null;

    public function SetWebhook(
        string $Url,
        InputFile|null $Certificate = null,
        ?string $IPAddress = null,
        ?int $MaxConnections = null,
        ?array $AllowedUpdates = null,
        bool $DropPendingUpdates = false
    ): void;

    public function DeleteWebhook(
        bool $DropPendingUpdates = false
    ): void;

    public function GetWebhookInfo(): WebhookInfo|null;

    public function GetMe(): User|null;

    public function LogOut(): void;

    public function Close(): void;
    
    public function SendMessage(
        int|string $ChatID,
        string $Text,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $Entities = null,
        bool $DisableWebPagePreview = false,
        bool $DisableNotification = false, 
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ReplyMarkup|null $ReplyMarkup = null
    ): Message|null;

    public function ForwardMessage(
        int|string $ChatID,
        int|string $FromChatID,
        int $MessageID,
        bool $DisableNotification = false,
    ): Message|null;

    public function CopyMessage(
        int|string $ChatID,
        int|string $FromChatID,
        int $MessageID,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): MessageID|null;

    public function SendPhoto(
        int|string $ChatID,
        InputFile|string $Photo,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendAudio(
        int|string $ChatID,
        InputFile|string $Audio,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        ?int $Duration = null,
        ?string $Performer = null,
        ?string $Title = null,
        InputFile|string|null $Thumb = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendDocument(
        int|string $ChatID,
        InputFile|string $Document,
        InputFile|string|null $Thumb,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        bool $DisableContentTypeDetection = false,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendVideo(
        int|string $ChatID,
        InputFile|string $Video,
        ?int $Duration = null,
        ?int $Width = null,
        ?int $Height = null,
        InputFile|string|null $Thumb = null,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        bool $SupportsStreaming = false,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendAnimation(
        int|string $ChatID,
        InputFile|string $Animation,
        ?int $Duration = null,
        ?int $Width = null,
        ?int $Height = null,
        InputFile|string|null $Thumb,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendVoice(
        int|string $ChatID,
        InputFile|string $Voice,
        ?string $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $CaptionEntities = null,
        ?int $Duration = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendVideoNote(
        int|string $ChatID,
        InputFile|string $VideoNote,
        ?int $Duration = null,
        ?int $Length = null,
        InputFile|string|null $Thumb = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendMediaGroup(
        int|string $ChatID,
        InputMediaArray $Media,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false
    ): MessageArray|null;

    public function SendLocation(
        int|string $ChatID,
        float $Latitude,
        float $Longitude,
        ?float $HorizontalAccuracy = null,
        ?int $LivePeriod = null,
        ?int $Heading = null,
        ?int $ProximityAlertRadius = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function EditMessageLiveLocation(
        float $Latitude,
        float $Longitude,
        int|string|null $ChatID = null,
        ?int $MessageID = null,
        ?string $InlineMessageID = null,
        ?float $HorizontalAccuracy = null,
        ?int $Heading = null,
        ?int $ProximityAlertRadius = null,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function StopMessageLiveLocation(
        int|string $ChatID,
        ?int $MessageID = null,
        ?string $InlineMessageID = null,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendVenue(
        int|string $ChatID,
        float $Latitude,
        float $Longitude,
        string $Title,
        string $Address,
        ?string $FoursquareID = null,
        ?string $FoursquareType = null,
        ?string $GooglePlaceID = null,
        ?string $GooglePlaceType = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendContact(
        int|string $ChatID,
        string $PhoneNumber,
        string $FirstName,
        ?string $LastName = null,
        ?string $Vcard = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendPoll(
        int|string $ChatID,
        string $Question,
        array $Options,
        bool $IsAnonymous = true,
        ?string $Type = null,
        bool $AllowsMultipleAnswers = false,
        ?int $CorrectOptionID = null,
        ?string $Explanation = null,
        string $ExplanationParseMode = ParseMode::Markdown,
        ?MessageEntities $ExplanationEntities = null,
        ?int $OpenPeriod = null,
        ?DateTime $CloseDate = null,
        ?bool $IsClosed = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendDice(
        int|string $ChatID,
        ?string $Emoji = null,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function SendChatAction(
        int|string $ChatID,
        string $Action
    ): void;

    public function GetUserProfilePhotos(
        int $UserID,
        ?int $Offset = null,
        ?int $Limit = null
    ): array|null;

    public function GetFile(
        string $FileID
    ): File|null;

    public function DownloadFile(
        File $File,
        string $Folder
    ): ?string;

    public function KickChatMember(
        int|string $ChatID,
        int $UserID,
        ?DateTime $UntilDate = null,
        ?bool $RevokeMessages = null
    ): void;

    public function UnbanChatMember(
        int|string $ChatID,
        int $UserID,
        bool $OnlyIfBanned = true
    ): void;

    public function RestrictChatMember(
        int|string $ChatID,
        int $UserID,
        ChatPermissions $Permissions,
        ?DateTime $UntilDate = null
    ): void;

    public function PromoteChatMember(
        int|string $ChatID,
        int $UserID,
        bool $IsAnonymous = false,
        ?bool $CanManageChat = null,
        ?bool $CanPostMessages = null,
        ?bool $CanEditMessages = null,
        ?bool $CanDeleteMessages = null,
        ?bool $CanManageVoiceChats = null,
        ?bool $CanRestrictMembers = null,
        ?bool $CanPromoteMembers = null,
        ?bool $CanChangeInfo = null,
        ?bool $CanInviteUsers = null,
        ?bool $CanPinMessages = null
    ): void;

    public function SetChatAdministratorCustomTitle(
        int|string $ChatID,
        int $UserID,
        string $CustomTitle
    ): void;

    public function SetChatPermissions(
        int|string $ChatID,
        ChatPermissions $Permissions
    ): void;

    public function ExportChatInviteLink(
        int|string $ChatID
    ): string|null;

    public function CreateChatInviteLink(
        int|string $ChatID,
        ?DateTime $ExpireDate = null,
        ?int $MemberLimit = null
    ): ChatInviteLink|null;

    public function EditChatInviteLink(
        int|string $ChatID,
        string $InviteLink,
        ?DateTime $ExpireDate = null,
        ?int $MemberLimit = null
    ): ChatInviteLink|null;

    public function RevokeChatInviteLink(
        int|string $ChatID,
        string $InviteLink
    ): ChatInviteLink|null;

    public function SetChatPhoto(
        int|string $ChatID,
        InputFile $Photo
    ): void;

    public function DeleteChatPhoto(
        int|string $ChatID
    ): void;

    public function SetChatTitle(
        int|string $ChatID,
        string $Title
    ): void;

    public function SetChatDescription(
        int|string $ChatID,
        string $Description
    ): void;

    public function PinChatMessage(
        int|string $ChatID,
        int $MessageID,
        bool $DisableNotification = false
    ): void;

    public function UnpinChatMessage(
        int|string $ChatID,
        int $MessageID
    ): void;

    public function UnpinAllChatMessages(
        int|string $ChatID
    ): void;

    public function LeaveChat(
        int|string $ChatID
    ): void;

    public function GetChat(
        int|string $ChatID
    ): Chat|null;

    public function GetChatAdministrators(
        int|string $ChatID
    ): ChatMemberArray|null;

    public function GetChatMembersCount(
        int|string $ChatID
    ): int|null;

    public function GetChatMember(
        int|string $ChatID,
        int $UserID
    ): ChatMember|null;

    public function SetChatStickerSet(
        int|string $ChatID,
        string $StickerSetName
    ): void;

    public function DeleteChatStickerSet(
        int|string $ChatID
    ): void;

    public function AnswerCallbackQuery(
        string $CallbackQueryID,
        ?string $Text = null,
        bool $ShowAlert = false,
        ?string $Url = null,
        ?int $CacheTime = null
    ): void;

    public function SetMyCommands(
        BotCommandArray $Commands
    ): void;

    public function GetMyCommands(): BotCommandArray|null;

    public function EditMessageText(
        string $Text,
        int|string|null $ChatID = null,
        int|null $MessageID = null,
        string|null $InlineMessageID = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $Entities = null,
        bool $DisableWebPagePreview = false,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function EditMessageCaption(
        int|string|null $ChatID = null,
        int|null $MessageID = null,
        string|null $InlineMessageID = null,
        string|null $Caption = null,
        string $ParseMode = ParseMode::Markdown,
        ?MessageEntities $Entities = null,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function EditMessageMedia(
        InputMedia $Media,
        int|string|null $ChatID = null,
        int|null $MessageID = null,
        string|null $InlineMessageID = null,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function EditMessageReplyMarkup(
        int|string|null $ChatID = null,
        int|null $MessageID = null,
        string|null $InlineMessageID = null,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function StopPoll(
        int|string $ChatID,
        int $MessageID,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Poll|null;

    public function DeleteMessage(
        int|string $ChatID,
        int $MessageID
    ): void;

    public function SendSticker(
        int|string $ChatID,
        InputFile|string $Sticker,
        bool $DisableNotification = false,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?ReplyMarkup $ReplyMarkup = null
    ): Message|null;

    public function GetStickerSet(
        string $Name
    ): StickerSetArray|null;

    public function UploadStickerFile(
        int $UserID,
        InputFile $PNGSticker
    ): File|null;

    public function CreateNewStickerSet(
        int $UserID,
        string $Name,
        string $Title,
        string $Emojis,
        InputFile|string|null $PNGSticker = null,
        ?InputFile $TGSSticker = null,
        bool $ContainsMasks = false,
        ?MaskPosition $MaskPosition = null
    ): void;

    public function AddStickerToSet(
        int $UserID,
        string $Name,
        string $Emojis,
        InputFile|string|null $PNGSticker = null,
        ?InputFile $TGSSticker = null,
        ?MaskPosition $MaskPosition = null
    ): void;

    public function SetStickerPositionInSet(
        string $Sticker,
        int $Position
    ): void;

    public function DeleteStickerFromSet(
        string $Sticker
    ): void;

    public function SetStickerSetThumb(
        string $Name,
        int $UserID,
        ?InputFile $Thumb
    ): void;

    public function AnswerInlineQuery(
        string $InlineQueryID,
        InlineQueryResultArray $Results,
        ?int $CacheTime = null,
        bool $IsPersonal = false,
        ?string $NextOffset = null,
        ?string $SwitchPmText = null,
        ?string $SwitchPmParameter = null
    ): void;

    public function SendInvoice(
        int|string $ChatID,
        string $Title,
        string $Description,
        string $Payload,
        string $ProviderToken,
        string $Currency,
        LabeledPriceArray $Prices,
        ?int $MaxTipAmount = null,
        ?array $SuggestedTipAmounts = null,
        ?string $StartParameter = null,
        ?string $ProviderData = null,
        ?string $PhotoUrl = null,
        ?int $PhotoSize = null,
        ?int $PhotoWidth = null,
        ?int $PhotoHeight = null,
        ?bool $NeedName = null,
        ?bool $NeedPhoneNumber = null,
        ?bool $NeedEmail = null,
        ?bool $NeedShippingAddress = null,
        ?bool $SendPhoneNumberToProvider = null,
        ?bool $SendEmailToProvider = null,
        ?bool $IsFlexible = null,
        ?bool $DisableNotification = null,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function AnswerShippingQuery(
        string $ShippingQueryID,
        bool $Ok,
        ?ShippingOptionArray $ShippingOptions = null,
        ?string $ErrorMessage = null
    ): void;

    public function AnswerPreCheckoutQuery(
        string $PreC1heckoutQueryID,
        bool $Ok,
        ?string $ErrorMessage = null
    ): void;

    public function SetPassportDataErrors(
        int $UserID,
        PassportElementErrorArray $Errors
    ): void;

    public function SendGame(
        int|string $ChatID,
        string $GameShortName,
        ?bool $DisableNotification = null,
        ?int $ReplyToMessageID = null,
        bool $AllowSendingWithoutReply = false,
        ?InlineKeyboardMarkup $ReplyMarkup = null
    ): Message|null;

    public function SetGameScore(
        int $UserID,
        int $Score,
        bool $Force = false,
        bool $DisableEditMessage = false,
        ?int $ChatID = null,
        ?int $MessageID = null,
        ?int $InlineMessageID = null
    ): Message|null;

    public function GetGameHighScores(
        int $UserID,
        ?int $ChatID = null,
        ?int $MessageID = null,
        ?int $InlineMessageID
    ): GameHighScoreArray|null;
}
?>