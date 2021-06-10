<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram;

	use GuzzleHttp\Client as HttpClient;
	use GuzzleHttp\Promise\Promise;
	use GuzzleHttp\Psr7\MultipartStream;
	use stdClass;
	use WeRtOG\BottoGram\Navigation\Button;
	use WeRtOG\BottoGram\Navigation\InlineButton;
	use WeRtOG\BottoGram\Navigation\KeyboardState;
	use WeRtOG\BottoGram\Telegram\Model\InlineQuery;
	use WeRtOG\BottoGram\Telegram\Model\MessageType;
	use WeRtOG\BottoGram\Telegram\Model\Message;
	use WeRtOG\BottoGram\Telegram\Model\ParseMode;
	use WeRtOG\BottoGram\Telegram\Model\PreCheckoutQuery;
	use WeRtOG\BottoGram\Telegram\Model\Request;
	use WeRtOG\BottoGram\Telegram\Model\Response;
	use WeRtOG\BottoGram\Telegram\Model\Update;

	class Telegram implements TelegramInterface
	{
		public string $ApiURL;
		public string $FileApiURL;
		public string $Token;
		public bool $ButtonsAutoSize = true;

		private HttpClient $HttpClient;

		function __construct(string $Token, bool $ButtonsAutoSize = true)
		{
			$this->Token = $Token;
			$this->ApiURL = "https://api.telegram.org/bot" . $Token . "/";
			$this->FileApiURL = "https://api.telegram.org/file/bot" . $Token;
			$this->ButtonsAutoSize = $ButtonsAutoSize;

			$this->HttpClient = new HttpClient([
				'base_uri' => $this->ApiURL,
				'timeout'  => 30,
				'http_errors' => false,
				'verify' => false
			]);
		}

		private function MakeRequest(string $URL, string $Method = 'GET', array $FormData = null, array $CustomOptions = []): Promise
		{
			return $this->HttpClient->requestAsync($Method, $URL, array_merge([
				'form_params' => $FormData,
			], $CustomOptions));
		}

		private function GetInputRequest(): Request
		{
			$JSONInput = '';
			if($Stream = fopen('php://input', 'r')) {
				$JSONInput = stream_get_contents($Stream);
				fclose($Stream);
			}
			
			return new Request($JSONInput);
		}


		public function GetFilename(string $FileID): ?string
		{
			$Response = $this->MakeRequest('getFile?file_id='.$FileID)->wait();

			if($Response != null)
			{
				$Array = json_decode((string)$Response->getBody(), true);
				$Result = $Array['result'] ?? ['file_path' => null];
				return $Result['file_path'] ?? null;
			}

			return null;
		}

		public function GetFile(string $FileName, string $Folder = 'uploads'): string
		{
			$Path = $Folder . '/' . str_replace("_", "", basename($FileName));
			file_put_contents($Path, fopen($this->FileApiURL.'/'.$FileName, 'r'));

			return $Path;
		}

		public function GetBlob(string $FileName): string
		{
			return addslashes(file_get_contents($this->FileApiURL.'/'.$FileName));
		}
		
		private function GenerateReplyMarkup(array|string|null $MainKeyboard, array|string|null $InlineKeyboard): ?string
		{	
			$ReplyMarkup = [];

			if($MainKeyboard != null || $InlineKeyboard != null)
			{
				if(!empty($MainKeyboard) )
				{
					if($MainKeyboard != KeyboardState::RemoveKeyboard)
					{
						if(is_array($MainKeyboard))
						{
							foreach($MainKeyboard as $Row)
							{
								$ReplyKeyboardRow = [];
								
								foreach($Row as $Button)
								{
									if($Button instanceof Button)
									{
										$ReplyKeyboardRow[] = [
											'text' => urlencode($Button->Title),
											'request_contact' => $Button->RequestContact,
											'request_location' => $Button->RequestLocation
										];
									}
								}
		
								$ReplyMarkup['resize_keyboard'] = $this->ButtonsAutoSize;
								$ReplyMarkup['keyboard'][] = $ReplyKeyboardRow;
							}
						}
					}
					else
					{
						$ReplyMarkup = [
							'remove_keyboard' => true
						];
					}
				}
				else
				{
					if(!empty($InlineKeyboard))
					{	
						foreach($InlineKeyboard as $Row)
						{
							$ReplyInlineKeyboardRow = [];
							
							foreach($Row as $Button)
							{
								if($Button instanceof InlineButton)
								{
									$ReplyInlineKeyboardRow[] = [
										'text' => urlencode($Button->Title),
										'callback_data' => urlencode($Button->CallbackData),
										'switch_inline_query_current_chat' => urlencode($Button->SwitchInlineQueryCurrentChat)
									];
								}
							}
	
							$ReplyMarkup['inline_keyboard'][] = $ReplyInlineKeyboardRow;
						}
					}
				}

				return !empty($ReplyMarkup) ? json_encode($ReplyMarkup) : null;
			}

			return null;
		}

		public function SendMessage(string $Message, ?string $ChatID, string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$Query = $this->MakeRequest('sendMessage?chat_id=' . $ChatID . '&text=' . urlencode($Message)  . '&reply_markup=' . $ReplyMarkup . "&parse_mode=" . $ParseMode);
			return new Response($Query);
		}

		public function AnswerPreCheckoutQuery(string $QueryID, bool $Ok, string $ErrorMessage = ''): Response
		{
			$ParametersString = http_build_query([
				'pre_checkout_query_id' => $QueryID,
				'ok' => $Ok,
				'error_message' => $ErrorMessage
			]);
			$Query = $this->MakeRequest('answerPreCheckoutQuery?' . $ParametersString);
			return new Response($Query);
		}

		public function SendInvoice(?string $ChatID, string $Title, string $Description, string $Payload, string $Currency, array $Prices, string $PaymentToken): Response
		{
			$ParametersString = http_build_query([
				'chat_id' => $ChatID,
				'title' => $Title,
				'description' => $Description,
				'payload' => $Payload,
				'provider_token' => $PaymentToken,
				'start_parameter' => 'pb-classic',
				'currency' => $Currency,
				'prices' => json_encode($Prices)
			]);
			$Query = $this->MakeRequest('sendInvoice?' . $ParametersString);
			return new Response($Query);
		}

		public function SendChatAction(string $Action, ?string $ChatID): Response
		{
			$Query = $this->MakeRequest('sendChatAction?chat_id='.$ChatID.'&action='.urlencode($Action));
			return new Response($Query);
		}

		public function SendMediaGroup(array $Content, string $ChatID, string $Caption = "", string $ParseMode = ParseMode::Markdown): Response
		{
			// Строим ссылку
			$URL = 'sendMediaGroup?chat_id=' . $ChatID;

			// Подготавливаем массив для media и для файлов
			$Media = [];
			$Files = [];

			// Проходимся по всем элементам
			foreach ($Content as $Index => $Item)
			{
				// Если текущий элемент файловый - подготавливаем его к отправке
				if(isset($Item['File']))
				{
					// Добавляем файл
					$FileContent = fopen($Item['File'], 'r');
					$Files[basename($Item['File'])] = $FileContent;

					// Создаём массив элемента
					$MediaElement = [
						"type" => $Item['Type'],
						"media" => "attach://" . basename($Item['File'])
					];
				// Если текущий элемент ID-шный, то просто добавляем его
				}
				else
				{
					// Создаём массив элемента
					$MediaElement = [
						"type" => $Item['Type'],
						"media" => $Item['ID']
					];
				}
				// Если это первая элемент, то добавляем нужное описание
				if($Index == 0)
				{
					if(!empty($Caption))
					{
						$MediaElement["caption"] = $Caption;
						$MediaElement["parse_mode"] = $ParseMode;
					}
				}

				// Добавляем массив элемента в массив media
				$Media[] = $MediaElement;
			}

			// Подготавливаем поля к отправке
			$Fields = [
				[
					'name' => 'chat_id',
					'contents' => $ChatID
				],
				[
					'name' => 'media',
					'contents' => json_encode($Media)
				]
			];

			foreach($Files as $FileName => $FileContent)
			{
				$Fields[] = [
					'name' => $FileName,
					'contents' => $FileContent
				];
			}

			// CURL
			
			$Boundary = uniqid();
			$Promise = $this->MakeRequest($URL, 'POST', null, [
				'headers' => [
					'Connection' => 'close',
					'Content-Type' => 'multipart/form-data; boundary='.$Boundary,
				],
				'body' => new MultipartStream($Fields, $Boundary)
			]);
			return new Response($Promise);
		}

		public function SendPhotoByURL(string $Photo, string $ChatID, string $Caption = "", $MainKeyboard = [], $InlineKeyboard = []): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$Query = $this->MakeRequest('sendPhoto?chat_id='.$ChatID.'&photo='.urlencode($Photo)."&caption=".urlencode($Caption) . '&reply_markup=' . $ReplyMarkup . "&parse_mode=markdown");
			return new Response($Query);
		}

		public function SendMedia(string $ApiMethod, string $Path, string $MessageType, string $ChatID, string $Caption = '', string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$URL = $ApiMethod . '?chat_id='. $ChatID . '&reply_markup=' . $ReplyMarkup . '&parse_mode=' . $ParseMode;
			$FileContent = fopen($Path, 'r');

			$Promise = $this->MakeRequest($URL, 'POST', null, [
				'multipart' => [
					[
						'name' => 'chat_id',
						'contents' => $ChatID
					],
					[
						'name' => $MessageType,
						'contents' => $FileContent
					],
					[
						'name' => 'caption',
						'contents' => $Caption
					]
				]
			]);
			return new Response($Promise);
		}


		public function SendPhoto(string $Photo, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			return $this->SendMedia(
				ApiMethod: 'sendPhoto',
				Path: $Photo,
				MessageType: MessageType::Photo,
				ChatID: $ChatID,
				Caption: $Caption,
				MainKeyboard: $MainKeyboard,
				InlineKeyboard: $InlineKeyboard,
				ParseMode: $ParseMode
			);
		}

		public function SendVoice(string $Voice, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			return $this->SendMedia(
				ApiMethod: 'sendVoice',
				Path: $Voice,
				MessageType: MessageType::Voice,
				ChatID: $ChatID,
				Caption: $Caption,
				MainKeyboard: $MainKeyboard,
				InlineKeyboard: $InlineKeyboard,
				ParseMode: $ParseMode
			);
		}

		public function SendDocument(string $Document, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			return $this->SendMedia(
				ApiMethod: 'sendDocument',
				Path: $Document,
				MessageType: MessageType::Document,
				ChatID: $ChatID,
				Caption: $Caption,
				MainKeyboard: $MainKeyboard,
				InlineKeyboard: $InlineKeyboard,
				ParseMode: $ParseMode
			);
		}

		public function SendAudio(string $Audio, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			return $this->SendMedia(
				ApiMethod: 'sendAudio',
				Path: $Audio,
				MessageType: MessageType::Audio,
				ChatID: $ChatID,
				Caption: $Caption,
				MainKeyboard: $MainKeyboard,
				InlineKeyboard: $InlineKeyboard,
				ParseMode: $ParseMode
			);
		}

		public function SendVideo(string $Video, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			return $this->SendMedia(
				ApiMethod: 'sendVideo',
				Path: $Video,
				MessageType: MessageType::Video,
				ChatID: $ChatID,
				Caption: $Caption,
				MainKeyboard: $MainKeyboard,
				InlineKeyboard: $InlineKeyboard,
				ParseMode: $ParseMode
			);
		}


		public function SendLocation(string $Lat, string $Long, string $ChatID): Response
		{
			$query = $this->MakeRequest('sendLocation?chat_id='.$ChatID.'&latitude='.urlencode($Lat).'&longitude='.urlencode($Long));
			return new Response($query);
		}

		
		public function ForwardMessage(string $FromID, int $MessageID, string $ChatID): Response
		{
			$query = $this->MakeRequest('forwardMessage?chat_id='.$ChatID.'&from_chat_id='.$FromID.'&message_id='.$MessageID);
			return new Response($query);
		}

		public function DeleteMessage(int $MessageID, string $ChatID): Response
		{
			$query = $this->MakeRequest('deleteMessage?chat_id='.$ChatID.'&message_id='.$MessageID);
			return new Response($query);
		}

		public function EditMessage(string $MessageID, string $NewText, string $ChatID, string $ParseMode = ParseMode::Markdown): Response
		{
			$query = $this->MakeRequest('editMessageText?chat_id='.$ChatID.'&message_id='.$MessageID.'&text='.urlencode($NewText)."&parse_mode=" . $ParseMode);
			return new Response($query);
		}

		public function EditMessageInlineButtons(int $MessageID, $InlineKeyboard, string $ChatID): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup([], $InlineKeyboard);
			$query = $this->MakeRequest('editMessageReplyMarkup?chat_id='.$ChatID.'&message_id='.$MessageID . '&reply_markup=' . $ReplyMarkup);
			return new Response($query);
		}

		public function AnswerInlineQueryWithArticles(string $qID, array $Articles): Response
		{
			$Results = [];
			foreach($Articles as $Key => $Article)
			{
				$New = [
					'type' => 'article',
					'id' => $Key,
					'title' => $Article['title'],
					'input_message_content' => [
						'message_text' => $Article['command']
					]
				];
				if(!empty($Article['description']))
				{
					$New['description'] = $Article['description'];
				}
				if(!empty($Article['thumb_url']))
				{
					$New['thumb_url'] = $Article['thumb_url'];
				}
				$Results[] = $New;
			}

			$Json = json_encode($Results);
			$Query = $this->MakeRequest('answerInlineQuery?cache_time=1&inline_query_id='.$qID.'&results='.urlencode($Json));
			return new Response($Query);
		}

		public function GetInlineQuery(Request $Request = null): ?InlineQuery
		{
			if($Request == null)
				$Request = $this->GetInputRequest();
			
			if(property_exists($Request->Body, 'inline_query')) {
				return new InlineQuery(
					ID: $Request->Body->inline_query->id,
					ChatID: $Request->Body->inline_query->from->id,
					Query: $Request->Body->inline_query->query
				);
			} else{
				return null;
			}
		}

		public function GetPreCheckoutQuery(Request $Request = null): ?PreCheckoutQuery
		{
			if($Request == null)
				$Request = $this->GetInputRequest();
			
			if(property_exists($Request->Body, 'pre_checkout_query')) {
				return new PreCheckoutQuery(
					ID: $Request->Body->pre_checkout_query->id,
					ChatID: $Request->Body->pre_checkout_query->from->id
				);
			} else {
				return null;
			}
		}

		public function GetUserMessage(Request $Request = null): ?Message
		{
			if($Request == null)
				$Request = $this->GetInputRequest();

			// Если отправлено из канала - покидаем приложение
			if(property_exists($Request->Body, 'channel_post'))
			{
				if($Request->Body->{'channel_post'}->{'chat'}->{'type'} == "channel")
				{
					return new Message(
						ChatID: $Request->Body->{'channel_post'}->{'chat'}->{'id'},
						IsChannelPost: true,
						Data: $Request
					);
				}
			}
			
			// Если не Callback Query
			if(empty($Request->Body->{'callback_query'}))
			{
				if(!isset($Request->Body->{'message'})) exit();

				// Получаем UserName
				$UserName = $Request->Body->{'message'}->{'chat'}->{'username'} ?? '';
				
				// Если он пуст, то им будет ID
				if(empty($UserName)) {
					$UserName = $Request->Body->{'message'}->{'chat'}->{'id'} ?? '';
				}

				// Получаем фото если таковое есть
				$PhotoID = "";
				if(property_exists($Request->Body->{'message'}, 'photo'))
				{
					$PhotoID = (array)$Request->Body->{'message'}->{'photo'}[count($Request->Body->{'message'}->{'photo'}) - 1];
					$PhotoID = $PhotoID['file_id'];
				}

				// Получаем видео если таковое есть
				$VideoID = "";
				if(property_exists($Request->Body->{'message'}, 'video'))
				{
					$VideoID = (array)$Request->Body->{'message'}->{'video'};
					$VideoID = $VideoID['file_id'];
				}

				// Получаем документ если таковой есть
				$DocumentID = "";
				if(property_exists($Request->Body->{'message'}, 'document'))
				{
					$DocumentID = (array)$Request->Body->{'message'}->{'document'};
					$DocumentID = $DocumentID['file_id'];
				}

				// Получаем локацию, если она есть
				$Location = [];
				if(property_exists($Request->Body->{'message'}, 'location'))
				{
					$Location = (array)$Request->Body->{'message'}->{'location'};
				}
				
				// Получаем медиа группу, если она есть
				$MediaGroup = "";
				if(property_exists($Request->Body->{'message'}, 'media_group_id'))
				{
					$MediaGroup = $Request->Body->{'message'}->{'media_group_id'};
				}

				// Получаем текст сообщения
				$Text = "";
				if(property_exists($Request->Body->{'message'}, 'text'))
				{
					$Text = $Request->Body->{'message'}->{'text'};
				}

				// Получаем сообщение об оплате
				$Pay = '';
				if(property_exists($Request->Body->{'message'}, 'successful_payment'))
				{
					$Pay = $Request->Body->{'message'}->{'successful_payment'};
				}

				if(property_exists($Request->Body->{'message'}, 'contact'))
				{
					if(property_exists($Request->Body->{'message'}->{'contact'}, 'phone_number'))
					{
						$Text = $Request->Body->{'message'}->{'contact'}->{'phone_number'};
					}
				}

				$UserFirstName = $Request->Body->{'message'}->{'chat'}->{'first_name'} ?? '';
				$UserLastName = $Request->Body->{'message'}->{'chat'}->{'last_name'} ?? '';

				// Возвращаем результат
				return new Message(
					Text: $Text,
					ChatID: $Request->Body->{'message'}->{'chat'}->{'id'},
					FromID: $Request->Body->{'message'}->{'from'}->{'id'},
					UserName: $UserName,
					Location: $Location,
					MediaGroupID: $MediaGroup,
					PhotoID: $PhotoID,
					VideoID: $VideoID,
					DocumentID: $DocumentID,
					HasAttachments: (!empty($PhotoID) || !empty($VideoID) || !empty($DocumentID)),
					UserFullName: !empty($UserFirstName) && !empty($UserLastName) ? $UserFirstName . ' ' . $UserLastName : '',
					Pay: !empty($Pay) ? $Pay : new stdClass(),
					IsFromGroup: isset($Request->Body->{'message'}->{'chat'}->{'type'}) ? (in_array($Request->Body->{'message'}->{'chat'}->{'type'}, ['supergroup', 'group']) ? true : false) : false,
					Data: $Request
				);
			// Если таки CallbackQuery
			}
			else
			{
				// Получаем UserName
				$UserName = $Request->Body->{'callback_query'}->{'from'}->{'username'};

				// Если он пуст, то им будет ID
				if(empty($UserName))
				{
					$UserName = $Request->Body->{'callback_query'}->{'message'}->{'chat'}->{'id'};
				}

				// Получаем основные сведения
				$CallbackID = $Request->Body->{'callback_query'}->{'id'};
				$MessageID = $Request->Body->{'callback_query'}->{'message'}->{'message_id'};
				$ChatID = $Request->Body->{'callback_query'}->{'message'}->{'chat'}->{'id'};
				$FromID = $Request->Body->{'callback_query'}->{'from'}->{'id'};
				$Text = $Request->Body->{'callback_query'}->{'data'};
				
				// Возвращаем результат
				return new Message(
					Text: $Text,
					ChatID: $ChatID,
					FromID: $FromID,
					MessageID: $MessageID,
					UserName: $UserName,
					IsCallbackQuery: true,
					MediaGroupID: "",
					PhotoID: "",
					VideoID: "",
					CallbackQueryID: $CallbackID,
					UserFullName: ($Request->Body->{'callback_query'}->{'from'}->{'first_name'} ?? '') . ' ' . ($Request->Body->{'callback_query'}->{'from'}->{'last_name'} ?? ''),
					IsFromGroup: isset($Request->Body->{'message'}->{'chat'}->{'type'}) ? (in_array($Request->Body->{'message'}->{'chat'}->{'type'}, ['supergroup', 'group']) ? true : false) : false,
					Data: $Request
				);
			}
		}


		public function GetUpdate(): ?Update
		{
			$Request = $this->GetInputRequest();

			if(isset($Request->Body->{'update_id'}))
			{
				return new Update(
					ID: $Request->Body->{'update_id'},
					Request: $Request,
					Message: $this->GetUserMessage($Request),
					InlineQuery: $this->GetInlineQuery($Request),
					PreCheckoutQuery: $this->GetPreCheckoutQuery($Request)
				);
			}
		}

		public function AnswerCallbackQuery(string $QueryID, string $NotificationText = null, bool $ShowAlert = false): Response
		{
			$ParametersString = http_build_query([
				'callback_query_id' => $QueryID, 
				'text' => $NotificationText,
				'show_alert' => $ShowAlert
			]);
			$Query = $this->MakeRequest('answerCallbackQuery?' . $ParametersString);
			return new Response($Query);
		}
	}
?>
