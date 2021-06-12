<?php
/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram;

use WeRtOG\BottoGram\Telegram\Model\InlineQuery;
use WeRtOG\BottoGram\Telegram\Model\Message;
use WeRtOG\BottoGram\Telegram\Model\ParseMode;
use WeRtOG\BottoGram\Telegram\Model\PreCheckoutQuery;
use WeRtOG\BottoGram\Telegram\Model\Request;
use WeRtOG\BottoGram\Telegram\Model\Response;
use WeRtOG\BottoGram\Telegram\Model\Update;

interface TelegramInterface
{
    public function __construct(string $Token, bool $ButtonsAutoSize = true);
    public function GetFilename(string $FileID): ?string;
    public function GetFile(string $FileName, string $Folder = 'uploads'): string;
    public function GetBlob(string $FileName): string;

    public function AnswerPreCheckoutQuery(string $QueryID, bool $Ok, string $ErrorMessage = ''): Response;
    public function AnswerInlineQueryWithArticles(string $qID, array $Articles): Response;
    public function AnswerCallbackQuery(string $QueryID, string $NotificationText = null, bool $ShowAlert = false): Response;

    public function SendMessage(string $Message, ?string $ChatID, string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendInvoice(?string $ChatID, string $Title, string $Description, string $Payload, string $Currency, array $Prices, string $PaymentToken): Response;
    public function SendChatAction(string $Action, ?string $ChatID): Response;
    public function SendMediaGroup(array $Content, string $ChatID, string $Caption = "", string $ParseMode = ParseMode::Markdown): Response;
    public function SendMedia(string $ApiMethod, string $Path, string $MediaType, string $ChatID, string $Caption = '', string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendPhotoByURL(string $Photo, string $ChatID, string $Caption = "", $MainKeyboard = [], $InlineKeyboard = []): Response;
    public function SendPhoto(string $Photo, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendVoice(string $Voice, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendDocument(string $Document, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendAudio(string $Audio, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendVideo(string $Video, string $ChatID, string $Caption = "", string|array|null $MainKeyboard = [], array|null $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): Response;
    public function SendLocation(string $Lat, string $Long, string $ChatID): Response;

    public function ForwardMessage(string $FromID, int $MessageID, string $ChatID): Response;
    public function DeleteMessage(int $MessageID, string $ChatID): Response;
    public function EditMessage(string $MessageID, string $NewText, string $ChatID, string $ParseMode = ParseMode::Markdown): Response;
    public function EditMessageInlineButtons(int $MessageID, $InlineKeyboard, string $ChatID): Response;

    public function GetInlineQuery(Request $Request = null): ?InlineQuery;
    public function GetPreCheckoutQuery(Request $Request = null): ?PreCheckoutQuery;
    public function GetUserMessage(Request $Request = null): ?Message;

    public function GetUpdate(): ?Update;
}
?>