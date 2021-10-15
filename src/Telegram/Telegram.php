<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Promise\Promise;
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
use WeRtOG\BottoGram\Telegram\Model\MessageEntities;
use WeRtOG\BottoGram\Telegram\Model\Message;
use WeRtOG\BottoGram\Telegram\Model\MessageArray;
use WeRtOG\BottoGram\Telegram\Model\MessageID;
use WeRtOG\BottoGram\Telegram\Model\ParseMode;
use WeRtOG\BottoGram\Telegram\Model\PassportElementErrorArray;
use WeRtOG\BottoGram\Telegram\Model\Poll;
use WeRtOG\BottoGram\Telegram\Model\ReplyMarkup;
use WeRtOG\BottoGram\Telegram\Model\Request;
use WeRtOG\BottoGram\Telegram\Model\Response;
use WeRtOG\BottoGram\Telegram\Model\ShippingOptionArray;
use WeRtOG\BottoGram\Telegram\Model\StickerSetArray;
use WeRtOG\BottoGram\Telegram\Model\TelegramModel;
use WeRtOG\BottoGram\Telegram\Model\TelegramModelArray;
use WeRtOG\BottoGram\Telegram\Model\Update;
use WeRtOG\BottoGram\Telegram\Model\UpdatesArray;
use WeRtOG\BottoGram\Telegram\Model\User;
use WeRtOG\BottoGram\Telegram\Model\WebhookInfo;

class Telegram implements TelegramInterface
{
	public string $ApiUrl;
	public string $FileApiUrl;
	public string $Token;

	private $OnResponseAction = null;
	private ?ReplyMarkup $DefaultReplyMarkup = null;

	private HttpClient $HttpClient;

	function __construct(string $Token)
	{
		$this->Token = $Token;
		$this->ApiUrl = "https://api.telegram.org/bot" . $Token . "/";
		$this->FileApiUrl = "https://api.telegram.org/file/bot" . $Token;

		$this->HttpClient = new HttpClient([
			'base_uri' => $this->ApiUrl,
			'timeout'  => 100,
			'http_errors' => false,
			'verify' => false
		]);
	}

	private function MakeRequest(string $Url, string $Method = 'GET', array $FormData = null, array $CustomOptions = []): Promise
	{
		return $this->HttpClient->requestAsync($Method, $Url, array_merge([
			'form_params' => $FormData,
		], $CustomOptions));
	}

	public function OnResponse(?callable $Action): void
	{
		$this->OnResponseAction = $Action;
	}

	public function SetDefaultReplyMarkup(?ReplyMarkup $ReplyMarkup): void
	{
		$this->DefaultReplyMarkup = $ReplyMarkup;
	}

	public static function Decamelize(string $Source): string
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $Source));
    }

	public static function ConvertToTelegramFormat(array $Data, ?array &$FilesOutput = null): array
	{
		$NewData = [];
        foreach($Data as $Name => $Value)
        {
			if($Value == null) continue;

            $Name = self::Decamelize($Name);

            if($Value instanceof TelegramModel || $Value instanceof TelegramModelArray)
            {
				$ValueInTelegramFormat = $Value->ToTelegramFormat($FilesOutput);
				
				if($Value instanceof InputFile)
				{
					$Basename = $Value->GetBasename();

					if(isset($FilesOutput))
					{
						$FilesOutput[$Basename] = $ValueInTelegramFormat;
					}
					$NewData[$Name] = 'attach://' . $Basename;
				}
				else
				{
					$NewData[$Name] = $ValueInTelegramFormat;
				}
            }
            else
            {
				if(is_array($Value)) $Value = $Value;
                $NewData[$Name] = $Value;
            }
        }

		return $NewData;
	}

	private function FormDataToMultipart(array $FormData): array
	{
		$Multipart = [];
		foreach($FormData as $Name => $Contents)
		{
			$Multipart[] = [
				'name' => $Name,
				'contents' => $Contents
			];
		}

		return $Multipart;
	}

    private function TriggerGenericAPIMethod(string $MethodName, array $Data): Response
    {
        $FilesOutput = [];
		$NewData = self::ConvertToTelegramFormat($Data, $FilesOutput);
		if(isset($NewData['reply_markup']) && is_object($Data['ReplyMarkup']) && $Data['ReplyMarkup'] instanceof InlineKeyboardMarkup)
			$NewData['reply_markup'] = json_encode($NewData['reply_markup']);
		
        //print_r($NewData);
		//print_r($FilesOutput);

		$Promise = null;
		if(count($FilesOutput) > 0)
		{
			$Multipart = $this->FormDataToMultipart(array_merge($NewData, $FilesOutput));
			$Promise = $this->MakeRequest($MethodName, 'POST', CustomOptions: [
				'multipart' => $Multipart
			]);

			//print_r($Multipart);
		}
		else
		{
			$Promise = $this->MakeRequest($MethodName, 'POST', $NewData);
		}
		$Response = new Response($Promise);
		if($this->OnResponseAction != null)
		{
			call_user_func($this->OnResponseAction, $Response);
		}

        return $Response;
    }

	private function GetModelObjectFromResponse(Response $Response, string $ClassName): mixed
	{
		$ResponseData = $Response->GetData();

		if($ResponseData != null)
		{
			if($ResponseData->ok && $ResponseData->result != null)
			{
				return $ClassName::FromTelegramFormat($ResponseData->result);
			}
		}

		return null;
	}

	private function GetInputRequest(): ?Request
	{
		$JSONInput = '';
		if($Stream = fopen('php://input', 'r')) {
			$JSONInput = stream_get_contents($Stream);
			fclose($Stream);
		}
		
		return $JSONInput != null ? new Request($JSONInput) : null;
	}

	public function GetUpdateFromInput(): ?Update
    {
		$Request = $this->GetInputRequest();

		if($Request != null)
		{
			if(isset($Request->Body->{'update_id'}))
			{
				return Update::FromTelegramFormat($Request->Body, $Request);
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}

	public function GetUpdates(?int $Offset = null, ?int $Limit = null, ?int $Timeout = null, ?array $AllowedUpdates = null): UpdatesArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, UpdatesArray::class);
	}

	public function SetWebhook(string $Url, ?InputFile $Certificate = null, ?string $IPAddress = null, ?int $MaxConnections = null, ?array $AllowedUpdates = null, bool $DropPendingUpdates = false): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function DeleteWebhook(bool $DropPendingUpdates = false): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function GetWebhookInfo(): WebhookInfo|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, WebhookInfo::class);
	}

	public function GetMe(): User|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, User::class);
	}

	public function LogOut(): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function Close(): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SendMessage(int|string $ChatID, string $Text, string $ParseMode = ParseMode::Markdown, ?MessageEntities $Entities = null, bool $DisableWebPagePreview = false, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function ForwardMessage(int|string $ChatID, int|string $FromChatID, int $MessageID, bool $DisableNotification = false): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function CopyMessage(int|string $ChatID, int|string $FromChatID, int $MessageID, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): MessageID|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, MessageID::class);
	}

	public function SendPhoto(int|string $ChatID, InputFile|string $Photo, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendAudio(int|string $ChatID, InputFile|string $Audio, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, ?int $Duration = null, ?string $Performer = null, ?string $Title = null, InputFile|string|null $Thumb = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendDocument(int|string $ChatID, InputFile|string $Document, InputFile|string|null $Thumb, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, bool $DisableContentTypeDetection = false, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendVideo(int|string $ChatID, InputFile|string $Video, ?int $Duration = null, ?int $Width = null, ?int $Height = null, InputFile|string|null $Thumb = null, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, bool $SupportsStreaming = false, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): ?Message
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendAnimation(int|string $ChatID, InputFile|string $Animation, ?int $Duration = null, ?int $Width = null, ?int $Height = null, InputFile|string|null $Thumb, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendVoice(int|string $ChatID, InputFile|string $Voice, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $CaptionEntities = null, ?int $Duration = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendVideoNote(int|string $ChatID, InputFile|string $VideoNote, ?int $Duration = null, ?int $Length = null, InputFile|string|null $Thumb = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendMediaGroup(int|string $ChatID, InputMediaArray $Media, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false): MessageArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, MessageArray::class);
	}

	public function SendLocation(int|string $ChatID, float $Latitude, float $Longitude, ?float $HorizontalAccuracy = null, ?int $LivePeriod = null, ?int $Heading = null, ?int $ProximityAlertRadius = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function EditMessageLiveLocation(float $Latitude, float $Longitude, int|string|null $ChatID = null, ?int $MessageID = null, ?string $InlineMessageID = null, ?float $HorizontalAccuracy = null, ?int $Heading = null, ?int $ProximityAlertRadius = null, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function StopMessageLiveLocation(int|string $ChatID, ?int $MessageID = null, ?string $InlineMessageID = null, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendVenue(int|string $ChatID, float $Latitude, float $Longitude, string $Title, string $Address, ?string $FoursquareID = null, ?string $FoursquareType = null, ?string $GooglePlaceID = null, ?string $GooglePlaceType = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendContact(int|string $ChatID, string $PhoneNumber, string $FirstName, ?string $LastName = null, ?string $Vcard = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendPoll(int|string $ChatID, string $Question, array $Options, bool $IsAnonymous = true, ?string $Type = null, bool $AllowsMultipleAnswers = false, ?int $CorrectOptionID = null, ?string $Explanation = null, string $ExplanationParseMode = ParseMode::Markdown, ?MessageEntities $ExplanationEntities = null, ?int $OpenPeriod = null, ?DateTime $CloseDate = null, ?bool $IsClosed = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Options = json_encode($Options);
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendDice(int|string $ChatID, ?string $Emoji = null, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SendChatAction(int|string $ChatID, string $Action): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function GetUserProfilePhotos(int $UserID, ?int $Offset = null, ?int $Limit = null): array|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		$ResponseData = $Response->GetData();

		if($ResponseData != null)
		{
			if($ResponseData->ok && $ResponseData->result != null)
			{
				return [];
			}
		}

		return $Response;
	}

	public function GetFile(string $FileID): File|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, File::class);
	}

	public function DownloadFile(File $File, string $Folder): ?string
	{
		if($File->FilePath != null)
		{
			$Path = $Folder . '/' . str_replace("_", "", basename($File->FilePath));
			file_put_contents($Path, fopen($this->FileApiUrl . '/' . $File->FilePath, 'r'));
			
			return $Path;
		}
		else
		{
			return null;
		}
	}

	public function KickChatMember(int|string $ChatID, int $UserID, ?DateTime $UntilDate = null, ?bool $RevokeMessages = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function UnbanChatMember(int|string $ChatID, int $UserID, bool $OnlyIfBanned = true): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function RestrictChatMember(int|string $ChatID, int $UserID, ChatPermissions $Permissions, ?DateTime $UntilDate = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function PromoteChatMember(int|string $ChatID, int $UserID, bool $IsAnonymous = false, ?bool $CanManageChat = null, ?bool $CanPostMessages = null, ?bool $CanEditMessages = null, ?bool $CanDeleteMessages = null, ?bool $CanManageVoiceChats = null, ?bool $CanRestrictMembers = null, ?bool $CanPromoteMembers = null, ?bool $CanChangeInfo = null, ?bool $CanInviteUsers = null, ?bool $CanPinMessages = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetChatAdministratorCustomTitle(int|string $ChatID, int $UserID, string $CustomTitle): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetChatPermissions(int|string $ChatID, ChatPermissions $Permissions): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function ExportChatInviteLink(int|string $ChatID): string|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		$ResponseData = $Response->GetData();

		if($ResponseData != null)
		{
			if($ResponseData->ok && $ResponseData->result != null)
			{
				return $ResponseData->result;
			}
		}

		return null;
	}

	public function CreateChatInviteLink(int|string $ChatID, ?DateTime $ExpireDate = null, ?int $MemberLimit = null): ChatInviteLink|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, ChatInviteLink::class);
	}

	public function EditChatInviteLink(int|string $ChatID, string $InviteLink, ?DateTime $ExpireDate = null, ?int $MemberLimit = null): ChatInviteLink|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, ChatInviteLink::class);
	}

	public function RevokeChatInviteLink(int|string $ChatID, string $InviteLink): ChatInviteLink|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, ChatInviteLink::class);
	}

	public function SetChatPhoto(int|string $ChatID, InputFile $Photo): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function DeleteChatPhoto(int|string $ChatID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetChatTitle(int|string $ChatID, string $Title): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetChatDescription(int|string $ChatID, string $Description): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function PinChatMessage(int|string $ChatID, int $MessageID, bool $DisableNotification = false): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function UnpinChatMessage(int|string $ChatID, int $MessageID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function UnpinAllChatMessages(int|string $ChatID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function LeaveChat(int|string $ChatID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function GetChat(int|string $ChatID): Chat|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Chat::class);
	}

	public function GetChatAdministrators(int|string $ChatID): ChatMemberArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, ChatMemberArray::class);
	}

	public function GetChatMembersCount(int|string $ChatID): int|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		$ResponseData = $Response->GetData();

		if($ResponseData != null)
		{
			if($ResponseData->ok && $ResponseData->result != null)
			{
				return (int)$ResponseData->result;
			}
		}

		return null;
	}

	public function GetChatMember(int|string $ChatID, int $UserID): ChatMember|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, ChatMember::class);
	}

	public function SetChatStickerSet(int|string $ChatID, string $StickerSetName): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function DeleteChatStickerSet(int|string $ChatID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function AnswerCallbackQuery(string $CallbackQueryID, ?string $Text = null, bool $ShowAlert = false, ?string $Url = null, ?int $CacheTime = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetMyCommands(BotCommandArray $Commands): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function GetMyCommands(): BotCommandArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, BotCommandArray::class);
	}

	public function EditMessageText(string $Text, int|string|null $ChatID = null, ?int $MessageID = null, ?string $InlineMessageID = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $Entities = null, bool $DisableWebPagePreview = false, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function EditMessageCaption(int|string|null $ChatID = null, ?int $MessageID = null, ?string $InlineMessageID = null, ?string $Caption = null, string $ParseMode = ParseMode::Markdown, ?MessageEntities $Entities = null, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function EditMessageMedia(InputMedia $Media, int|string|null $ChatID = null, ?int $MessageID = null, ?string $InlineMessageID = null, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function EditMessageReplyMarkup(int|string|null $ChatID = null, ?int $MessageID = null, ?string $InlineMessageID = null, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function StopPoll(int|string $ChatID, int $MessageID, ?InlineKeyboardMarkup $ReplyMarkup = null): Poll|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Poll::class);
	}

	public function DeleteMessage(int|string $ChatID, int $MessageID): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SendSticker(int|string $ChatID, InputFile|string $Sticker, bool $DisableNotification = false, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?ReplyMarkup $ReplyMarkup = null): Message|null
	{
		$ReplyMarkup = $ReplyMarkup ?? $this->DefaultReplyMarkup;
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function GetStickerSet(string $Name): StickerSetArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, StickerSetArray::class);
	}

	public function UploadStickerFile(int $UserID, InputFile $PNGSticker): File|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, File::class);
	}

	public function CreateNewStickerSet(int $UserID, string $Name, string $Title, string $Emojis, InputFile|string|null $PNGSticker = null, ?InputFile $TGSSticker = null, bool $ContainsMasks = false, ?MaskPosition $MaskPosition = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function AddStickerToSet(int $UserID, string $Name, string $Emojis, InputFile|string|null $PNGSticker = null, ?InputFile $TGSSticker = null, ?MaskPosition $MaskPosition = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetStickerPositionInSet(string $Sticker, int $Position): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function DeleteStickerFromSet(string $Sticker): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetStickerSetThumb(string $Name, int $UserID, ?InputFile $Thumb): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function AnswerInlineQuery(string $InlineQueryID, InlineQueryResultArray $Results, ?int $CacheTime = null, bool $IsPersonal = false, ?string $NextOffset = null, ?string $SwitchPmText = null, ?string $SwitchPmParameter = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SendInvoice(int|string $ChatID, string $Title, string $Description, string $Payload, string $ProviderToken, string $Currency, LabeledPriceArray $Prices, ?int $MaxTipAmount = null, ?array $SuggestedTipAmounts = null, ?string $StartParameter = null, ?string $ProviderData = null, ?string $PhotoUrl = null, ?int $PhotoSize = null, ?int $PhotoWidth = null, ?int $PhotoHeight = null, ?bool $NeedName = null, ?bool $NeedPhoneNumber = null, ?bool $NeedEmail = null, ?bool $NeedShippingAddress = null, ?bool $SendPhoneNumberToProvider = null, ?bool $SendEmailToProvider = null, ?bool $IsFlexible = null, ?bool $DisableNotification = null, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function AnswerShippingQuery(string $ShippingQueryID, bool $Ok, ?ShippingOptionArray $ShippingOptions = null, ?string $ErrorMessage = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function AnswerPreCheckoutQuery(string $PreC1heckoutQueryID, bool $Ok, ?string $ErrorMessage = null): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SetPassportDataErrors(int $UserID, PassportElementErrorArray $Errors): void
	{
		$this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
	}

	public function SendGame(int|string $ChatID, string $GameShortName, ?bool $DisableNotification = null, ?int $ReplyToMessageID = null, bool $AllowSendingWithoutReply = false, ?InlineKeyboardMarkup $ReplyMarkup = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function SetGameScore(int $UserID, int $Score, bool $Force = false, bool $DisableEditMessage = false, ?int $ChatID = null, ?int $MessageID = null, ?int $InlineMessageID = null): Message|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, Message::class);
	}

	public function GetGameHighScores(int $UserID, ?int $ChatID = null, ?int $MessageID = null, ?int $InlineMessageID): GameHighScoreArray|null
	{
		$Response = $this->TriggerGenericAPIMethod(lcfirst(__FUNCTION__), get_defined_vars());
		return $this->GetModelObjectFromResponse($Response, GameHighScoreArray::class);
	}
}