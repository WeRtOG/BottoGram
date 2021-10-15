<div class="bot-binding m-4">
    <form class="token-change-form mb-5" action="<?=$this->Root?>/binding/changeBotToken" method="POST" autocomplete="off" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
        <div class="mb-3">
            <label for="BotToken" class="form-label">Токен</label>
            <div class="form-group">
                <input type="password" autocomplete="off" class="form-control" name="BotToken" id="BotToken" placeholder="Здесь можно ввести новый токен.">
                <button type="submit" disabled class="btn btn-primary">Сохранить</button>
            </div>
            <?=isset($_GET['tokenError']) && $_GET['tokenError'] == 1 ? '<p class="error mt-1 text-danger">Неверный токен</p>' : ''?>
        </div>
    </form>
    <h4 class="mb-4">Информация о боте</h4>
    <div class="bot-info disabled mb-5">
        <p><b>ID бота: </b><span class="bot-id">недоступно</span></p>
        <p><b>Ссылка на бота: </b><a target="_blank" class="bot-username">недоступно</a></p>
        <p><b>Может присоединяться к групповым чатам: </b><span class="groups-flag">недоступно</span></p>
        <p><b>Может читать все сообщения групповых чатов: </b><span class="read-group-messages-flag">недоступно</span></p>
        <p><b>Поддерживает Inline: </b><span class="inline-flag">недоступно</span></p>
    </div>
    <form action="<?=$this->Root?>/binding/changeWebhookSettings" method="POST" onchange="ValidateBotModeForm(this)" oninput="ValidateBotModeForm(this)">
        <h4 class="mb-4">Режим работы</h4>
        <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" name="WebhookEnabled" id="WebhookEnabled">
            <label class="form-check-label">Использовать Webhook</label>
        </div>
        <div class="webhook-input-wrapper disabled">
            <label for="WebhookURL" class="form-label">Адрес Webhook</label>
            <input class="form-control" disabled name="WebhookURL" id="WebhookURL" placeholder="Введите адрес">
            <?=isset($_GET['webhookError']) && !empty($_GET['webhookError']) ? '<p class="error mt-2 mb-3 text-danger">' . htmlspecialchars($_GET['webhookError']) . '</p>' : ''?>
        </div>
        <div class="mt-4 mb-3">
            <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
        </div>
    </form>

</div>