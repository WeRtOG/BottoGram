<form class="" method="POST" autocomplete="off" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
    <h4 class="mb-4">Базовая информация</h4>
    <div class="mb-3">
        <label for="BottoConfig_Name" class="form-label">Название бота</label>
        <input required type="text" class="form-control" name="BottoConfig_Name" id="BottoConfig_Name" value="<?=$this->GlobalData['BottoConfig']->Name?>">
    </div>
    <div class="mt-4">
        <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox"<?=$this->GlobalData['BottoConfig']->AllowGroups ? ' checked' : ''?> name="BottoConfig_AllowGroups" id="BottoConfig_AllowGroups">
            <label class="form-check-label">Обрабатывать сообщения из групповых чатов</label>
        </div>
        <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox"<?=$this->GlobalData['BottoConfig']->EnableTextLog ? ' checked' : ''?> name="BottoConfig_EnableTextLog" id="BottoConfig_EnableTextLog">
            <label class="form-check-label">Использовать текстовые логи истории запросов</label>
        </div>
        <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox"<?=$this->GlobalData['BottoConfig']->EnableExtendedLog ? ' checked' : ''?> name="BottoConfig_EnableExtendedLog" id="BottoConfig_EnableExtendedLog">
            <label class="form-check-label">Использовать расширенные логи</label>
        </div>
        <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox"<?=$this->GlobalData['BottoConfig']->ButtonsAutoSize ? ' checked' : ''?> name="BottoConfig_ButtonsAutoSize" id="BottoConfig_ButtonsAutoSize">
            <label class="form-check-label">Автоматический размер кнопок клавиатуры бота</label>
        </div>
    </div>
    <div class="mt-4 mb-3">
        <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
    </div>
</form>