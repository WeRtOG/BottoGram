<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram;

	// Используем нужные зависимости
	use stdClass;
	use WeRtOG\BottoGram\Navigation\Button;
	use WeRtOG\BottoGram\Navigation\InlineButton;
	use WeRtOG\BottoGram\Navigation\KeyboardState;
	use WeRtOG\BottoGram\Telegram\Model\InlineQuery;
	use WeRtOG\BottoGram\Telegram\Model\Message;
	use WeRtOG\BottoGram\Telegram\Model\ParseMode;
	use WeRtOG\BottoGram\Telegram\Model\PreCheckoutQuery;
	use WeRtOG\BottoGram\Telegram\Model\Request;
	use WeRtOG\BottoGram\Telegram\Model\Response;
	use WeRtOG\BottoGram\Telegram\Model\Update;

	/**
	 * Класс для взаимодействия с API Telegram
	 * @property string $ApiURL Ссылка на API
	 * @property string $FileApiURL Ссылка на файловое API
	 * @property string $Token Токен
	 * @property bool $ButtonsAutoSize Флаг автоматической смены размера кнопок
	 */
	class Telegram
	{
		public string $ApiURL;
		public string $FileApiURL;
		public string $Token;
		public bool $ButtonsAutoSize = true;
		
		/**
		 * Конструктор класса
		 * @param string $Token Токен
		 * @param bool $ButtonsAutoSize Флаг автоматической смены размера кнопок
		 */
		function __construct(string $Token, bool $ButtonsAutoSize = true)
		{
			// Запоминаем токен и генерим ссылки
			$this->Token = $Token;
			$this->ApiURL = "https://api.telegram.org/bot" . $Token;
			$this->FileApiURL = "https://api.telegram.org/file/bot" . $Token;
			$this->ButtonsAutoSize = $ButtonsAutoSize;
		}

		/**
		 * Метод для выполнения HTTP-запроса (для API)
		 * @param string $URL URL
		 * @return string Ответ
		 */
		private function MakeRequest(string $URL, bool $isPOST = false, array $Data = []): string
		{
			if($isPOST)
			{
				$Curl = curl_init();

				curl_setopt_array($Curl, [
					CURLOPT_URL => $URL,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POST => 1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_POSTFIELDS => $Data
				]);

				return curl_exec($Curl);
			}
			else
			{
				$Context = stream_context_create([
					'http' => ['ignore_errors' => true],
				]);
				return file_get_contents($URL, false, $Context);
			}
		}

		public function GetRequest(): Request
		{
			$JSONInput = $this->MakeRequest('php://input');
			return new Request($JSONInput);
		}

		/**
		 * Метод для получения пути к файлу на серверах Telegram
		 * @param string $FileID ID файла
		 * @return string Путь к файлу на серверах Telegram
		 */
		public function GetFilename(string $FileID): ?string
		{
			$Query = $this->MakeRequest($this->ApiURL.'/getFile?file_id='.$FileID);
			$Array = json_decode($Query, true);
			$Result = $Array['result'] ?? ['file_path' => null];
			return $Result['file_path'] ?? null;
		}

		/**
		 * Метод для получения файла с серверов Telegram
		 * 
		 * **ВАЖНО:** в папке с проектом должна быть папка uploads*
		 * 
		 * @param string $FileName Имя файла
		 * @param string $Folder Путь к папке, в которую нужно разместить файл (необязательно)
		 * @return string Конечный путь к файлу
		 */
		public function GetFile(string $FileName, string $Folder = 'uploads'): string
		{
			$Path = $Folder . '/' . str_replace("_", "", basename($FileName));
			file_put_contents($Path, fopen($this->FileApiURL.'/'.$FileName, 'r'));

			return $Path;
		}

		/**
		 * Метод для получения BLOB файла с серверов Telegram
		 * @param string $FileName Имя файла
		 * @return string BLOB
		 */
		public function GetBlob(string $FileName): string
		{
			return addslashes($this->MakeRequest($this->FileApiURL.'/'.$FileName));
		}
		
		/**
		 * Метод для генерации клавиатур Telegram
		 * @param array $MainKeyboard Основная клавиатура
		 * @param array $Inlinenew KeyboardState(Keyboard Инлайновая клавиатура
		 * @return string Клавиатура Telegram
		 */
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

		/**
		 * Метод для отправки сообщения
		 * @param string $Message Сообщение
		 * @param string $ChatID ID чата
		 * @param array|string $MainKeyboard Массив кнопок основной клавиатуры
		 * @param array|string $InlineKeyboard Инлайновая клавиатура
		 * @param string $ParseMode Метод парсинга текста
		 * @return Response Ответ от Telegram
		 */
		public function SendMessage(string $Message, ?string $ChatID, $MainKeyboard = [], $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$Query = $this->MakeRequest($this->ApiURL . '/sendMessage?chat_id=' . $ChatID . '&text=' . urlencode($Message)  . '&reply_markup=' . $ReplyMarkup . "&parse_mode=" . $ParseMode);
			return new Response($Query);
		}

		/**
		 * Метод для подтверждения оплаты
		 * @param string $QueryID ID запроса
		 * @param bool $Ok Подтверждаем ли
		 * @param string $ErrorMessage Текст ошибки
		 * @return Response Ответ от Telegram
		 */
		public function AnswerPreCheckoutQuery(string $QueryID, bool $Ok, string $ErrorMessage = ''): Response
		{
			$ParametersString = http_build_query([
				'pre_checkout_query_id' => $QueryID,
				'ok' => $Ok,
				'error_message' => $ErrorMessage
			]);
			$Query = $this->MakeRequest($this->ApiURL . '/answerPreCheckoutQuery?' . $ParametersString);
			return new Response($Query);
		}
		
		/**
		 * Метод для отправки запроса на оплату
		 * @param string|null $ChatID ID чата
		 * @param string $Title Заголовок
		 * @param string $Description Описание
		 * @param string $Payload Скрытый ключ
		 * @param string $Currency Валюта
		 * @param array $Prices Массив цен
		 * @param string $PaymentToken Платёжный токен
		 * @return Response Ответ от Telegram
		 */
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
			$Query = $this->MakeRequest($this->ApiURL . '/sendInvoice?' . $ParametersString);
			return new Response($Query);
		}

		/**
		 * Метод для отправки описания выполняемого действия
		 * @param string $Action Действие
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function SendChatAction(string $Action, ?string $ChatID): Response
		{
			$Query = $this->MakeRequest($this->ApiURL.'/sendChatAction?chat_id='.$ChatID.'&action='.urlencode($Action));
			return new Response($Query);
		}
		
		/**
		 * Метод для отправки медиагруппы
		 * @param array $Content Контент
		 * @param string $ChatID ID чата
		 * @param string $Caption Подпись
		 * @param string $ParseMode Метод парсинга текста
		 * @return Response Ответ от Telegram
		 */
		public function SendMediaGroup(array $Content, string $ChatID, string $Caption = "", string $ParseMode = ParseMode::Markdown): Response
		{
			// Строим ссылку
			$URL = $this->ApiURL.'/sendMediaGroup?chat_id=' . $ChatID;

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
					$FileContent = $this->MakeRequest($Item['File']);
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
				"chat_id" => $ChatID, 
				"media" => json_encode($Media)
			];

			// CURL
			$Curl = curl_init();
			$Boundary = uniqid();
			$Delimiter = '-------------' . $Boundary;

			// Строим данные запроса
			$POST_DATA = $this->RequestBuildDataFiles($Boundary, $Fields, $Files);


			curl_setopt_array($Curl, array(
				CURLOPT_URL => $URL,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POST => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POSTFIELDS => $POST_DATA,
				CURLOPT_HTTPHEADER => array(
					"Content-Type: multipart/form-data; boundary=" . $Delimiter,
					"Content-Length: " . strlen($POST_DATA)
				)
			));

			// Получаем ответ, декодируем его и возвращаем результат
			$response = curl_exec($Curl);
			return new Response($response);
		}

		/**
		 * Метод для отправки медиагруппы только с фото
		 * @param array $Photos фотографии
		 * @param string $ChatID ID чата
		 * @param string $Caption Подпись
		 * @param bool $isID Являются ли фото ID
		 * @param string $ParseMode Метод парсинга текста
		 * @return Response Ответ от Telegram
		 */
		public function SendPhotoGroup(array $Photos, string $ChatID, string $Caption = "", bool $isID = false, string $ParseMode = ParseMode::Markdown): Response
		{
			$Content = [];

			foreach($Photos as $Photo)
			{
				if($isID)
				{
					$Content[] = [
						'ID' => $Photo,
						'Type' => 'photo'
					];
				}
				else
				{
					$Content[] = [
						'File' => $Photo,
						'Type' => 'photo'
					];
				}
			}

			return $this->SendMediaGroup($Content, $ChatID, $Caption, $ParseMode);
		}

		/**
		 * Метод для отправки фотографии
		 * @param string $Photo Фотография
		 * @param string $ChatID ID чата
		 * @param string $Caption Подпись
		 * @param array $MainKeyboard Основная клавиатура
		 * @param array $InlineKeyboard Инлайновая клавиатура
		 * @return Response Ответ от Telegram
		 */
		public function SendPhoto(string $Photo, string $ChatID, string $Caption = "", $MainKeyboard = [], $InlineKeyboard = []): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$Query = $this->MakeRequest($this->ApiURL.'/sendPhoto?chat_id='.$ChatID.'&photo='.urlencode($Photo)."&caption=".urlencode($Caption) . '&reply_markup=' . $ReplyMarkup . "&parse_mode=markdown");
			return new Response($Query);
		}

		/**
		 * Альтернативныйетод для отправки фотографии
		 * @param string $Photo Фотография
		 * @param string $ChatID ID чата
		 * @param string $Caption Подпись
		 * @param array $MainKeyboard Основная клавиатура
		 * @param array $InlineKeyboard Инлайновая клавиатура
		 * @return Response Ответ от Telegram
		 */
		public function SendPhotoAlt(string $Photo, string $ChatID, string $Caption = "", $MainKeyboard = [], $InlineKeyboard = []): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			
			// Строим ссылку
			$URL = $this->ApiURL.'/sendPhoto?chat_id='.$ChatID . '&reply_markup=' . $ReplyMarkup . "&parse_mode=markdown";

			// Подготавливаем массив для media и для файлов
			$Files = [];

			$FileContent = $this->MakeRequest($Photo);
			$Files[basename($Photo)] = $FileContent;

			// Подготавливаем поля к отправке
			$Fields = array("chat_id" => $ChatID, "photo" => "attach://" . basename($Photo), "caption" => $Caption);

			// CURL
			$Curl = curl_init();
			$Boundary = uniqid();
			$Delimiter = '-------------' . $Boundary;

			// Строим данные запроса
			$POST_DATA = $this->RequestBuildDataFiles($Boundary, $Fields, $Files);


			curl_setopt_array($Curl, [
				CURLOPT_URL => $URL,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POST => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POSTFIELDS => $POST_DATA,
				CURLOPT_HTTPHEADER => [
					"Content-Type: multipart/form-data; boundary=" . $Delimiter,
					"Content-Length: " . strlen($POST_DATA)
				]
			]);

			// Получаем ответ, декодируем его и возвращаем результат
			$response = curl_exec($Curl);
			return new Response($response);
		}

		/**
		 * Метод для отправки голосового сообщения
		 * @param string $URL URL
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function SendVoice(string $URL, string $ChatID): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/sendVoice?chat_id='.$ChatID.'&voice='.$URL);
			return new Response($query);
		}

		/**
		 * Метод для отправки документа
		 * @param string $Document Документ
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function SendDocument(string $Document, string $ChatID): Response
		{
			// Строим ссылку
			$URL = $this->ApiURL.'/sendDocument?chat_id='.$ChatID;

			// Подготавливаем массив для media и для файлов
			$Files = [];

			$FileContent = $this->MakeRequest($Document);
			$Files[basename($Document)] = $FileContent;

			// Подготавливаем поля к отправке
			$Fields = ["chat_id" => $ChatID, "document" => "attach://" . basename($Document)];

			// CURL
			$Curl = curl_init();
			$Boundary = uniqid();
			$Delimiter = '-------------' . $Boundary;

			// Строим данные запроса
			$POST_DATA = $this->RequestBuildDataFiles($Boundary, $Fields, $Files);


			curl_setopt_array($Curl, [
				CURLOPT_URL => $URL,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POST => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POSTFIELDS => $POST_DATA,
				CURLOPT_HTTPHEADER => [
					"Content-Type: multipart/form-data; boundary=" . $Delimiter,
					"Content-Length: " . strlen($POST_DATA)
				]
			]);

			// Получаем ответ, декодируем его и возвращаем результат
			$response = curl_exec($Curl);
			return new Response($response);
		}

		/**
		 * Метод для отправки аудио
		 * @param string $URL URL
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function SendAudio(string $URL, string $ChatID): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/sendAudio?chat_id='.$ChatID.'&audio='.$URL);
			return new Response($query);
		}

		/**
		 * Метод для отправки геолокации
		 * @param string $Lat Широта
		 * @param string $Long Долгота
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function SendLocation(string $Lat, string $Long, string $ChatID): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/sendLocation?chat_id='.$ChatID.'&latitude='.urlencode($Lat).'&longitude='.urlencode($Long));
			return new Response($query);
		}

		/**
		 * Метод для отправки видео
		 * @param string $Video Видео
		 * @param string $ChatID ID чата
		 * @param string $Caption Подпись
		 * @param array $MainKeyboard Основная клавиатура
		 * @param array $InlineKeyboard Инлайновая клавиатура
		 * @param string $ParseMode Метод парсинга
		 * @return Response Ответ от Telegram
		 */
		public function SendVideo(string $Video, string $ChatID, string $Caption = "", $MainKeyboard = [], $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup($MainKeyboard, $InlineKeyboard);
			$Query = $this->MakeRequest($this->ApiURL.'/sendVideo?chat_id='.$ChatID.'&video='.urlencode($Video)."&caption=".urlencode($Caption) . '&reply_markup=' . $ReplyMarkup . "&parse_mode=" . $ParseMode);
			return new Response($Query);
		}

		/**
		 * Метод для пересылки сообщения
		 * @param string $FromID Чат из которого нужно переслать сообщение 
		 * @param string $MessageID ID сообщения
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function ForwardMessage(string $FromID, int $MessageID, string $ChatID): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/forwardMessage?chat_id='.$ChatID.'&from_chat_id='.$FromID.'&message_id='.$MessageID);
			return new Response($query);
		}

		/**
		 * Метод для удаления сообщения
		 * @param int $MessageID ID сообщения
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function DeleteMessage(int $MessageID, string $ChatID): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/deleteMessage?chat_id='.$ChatID.'&message_id='.$MessageID);
			return new Response($query);
		}

		/**
		 * Метод для редактирования сообщения
		 * @param string $MessageID ID сообщения
		 * @param string $NewText Новый текст
		 * @param string $ChatID ID чата
		 * @param string $ParseMode Метод парсинга
		 * @return Response Ответ от Telegram
		 */
		public function EditMessage(string $MessageID, string $NewText, string $ChatID, string $ParseMode = ParseMode::Markdown): Response
		{
			$query = $this->MakeRequest($this->ApiURL.'/editMessageText?chat_id='.$ChatID.'&message_id='.$MessageID.'&text='.urlencode($NewText)."&parse_mode=" . $ParseMode);
			return new Response($query);
		}

		/**
		 * Метод для редактирования инлайновых кнопок сообщения
		 * @param int $MessageID ID сообщения
		 * @param array $InlineKeyboard Инлайновая клавиатура
		 * @param string $ChatID ID чата
		 * @return Response Ответ от Telegram
		 */
		public function EditMessageInlineButtons(int $MessageID, $InlineKeyboard, string $ChatID): Response
		{
			$ReplyMarkup = $this->GenerateReplyMarkup([], $InlineKeyboard);
			$query = $this->MakeRequest($this->ApiURL.'/editMessageReplyMarkup?chat_id='.$ChatID.'&message_id='.$MessageID . '&reply_markup=' . $ReplyMarkup);
			return new Response($query);
		}

		/**
		 * Метод для ответа на инлайновый запрос статьями
		 * @param string $qID ID запроса
		 * @param array $Articles Статьи
		 * @return Response Ответ от Telegram
		 */
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
			$Query = $this->MakeRequest($this->ApiURL.'/answerInlineQuery?cache_time=1&inline_query_id='.$qID.'&results='.urlencode($Json));
			return new Response($Query);
		}

		/**
		 * Метод для получения инлайнового запроса
		 * @return InlineQuery Инлайновый запрос
		 */
		public function GetInlineQuery(Request $Request = null): ?InlineQuery
		{
			if($Request == null)
				$Request = $this->GetRequest();
			
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
		
		/**
		 * Метод для получения запроса на подтверждение оплаты
		 * @return PreCheckoutQuery Запрос
		 */
		public function GetPreCheckoutQuery(Request $Request = null): ?PreCheckoutQuery
		{
			if($Request == null)
				$Request = $this->GetRequest();
			
			if(property_exists($Request->Body, 'pre_checkout_query')) {
				return new PreCheckoutQuery(
					ID: $Request->Body->pre_checkout_query->id,
					ChatID: $Request->Body->pre_checkout_query->from->id
				);
			} else {
				return null;
			}
		}
		
		/**
		 * Метод для получения пользовательского сообщения
		 * @return Message Сообщение
		 */
		public function GetUserMessage(Request $Request = null): ?Message
		{
			if($Request == null)
				$Request = $this->GetRequest();

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
					IsPhoto: !empty($PhotoID),
					IsVideo: !empty($VideoID),
					IsDocument: !empty($DocumentID),
					IsMediaGroup: !empty($MediaGroup),
					IsLocation: !empty($Location),
					IsPay: !empty($Pay),
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
					IsPhoto: false,
					IsVideo: false,
					IsMediaGroup: false,
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
			$Request = $this->GetRequest();

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
			$Query = $this->MakeRequest($this->ApiURL . '/answerCallbackQuery?' . $ParametersString);
			return new Response($Query);
		}

		/**
		 * Служебная функция для постройки CURL запроса с файлами и данными
		 * @param string $Boundary Неведомый ID
		 * @param array $Fields Основные поля
		 * @param array $Files Файлы
		 */
		private function RequestBuildDataFiles(string $Boundary, array $Fields, array $Files)
		{
			$Data = '';
			$Eol = "\r\n";
			$Delimiter = '-------------' . $Boundary;
		
			foreach ($Fields as $Name => $Content)
			{
				$Data .= "--" . $Delimiter . $Eol
					. 'Content-Disposition: form-data; name="' . $Name . "\"".$Eol.$Eol
					. $Content . $Eol;
			}
			foreach ($Files as $Name => $Content)
			{
				$Data .= "--" . $Delimiter . $Eol
					. 'Content-Disposition: form-data; name="' . $Name . '"; filename="' . $Name . '"' . $Eol
					. 'Content-Transfer-Encoding: binary'.$Eol
					;
		
				$Data .= $Eol;
				$Data .= $Content . $Eol;
			}
			$Data .= "--" . $Delimiter . "--". $Eol;
			return $Data;
		}
	}
?>
