<div class="settings-main-page">
    <form class="mb-5" method="POST" autocomplete="off" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
        <input type="hidden" name="form" value="UpdateMainInfo" />
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
    <form class="mb-5" method="POST" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
        <input type="hidden" name="form" value="UpdateDatabase" />
        <h4 class="mb-4">База данных</h4>
        <div class="mb-3">
            <label for="DatabaseServer" class="form-label">Сервер</label>
            <input required type="text" class="form-control" name="DatabaseServer" id="DatabaseServer" value="<?=$this->GlobalData['BottoConfig']->DatabaseConnection->Server?>">
        </div>
        <div class="mb-3">
            <label for="DatabaseUser" class="form-label">Логин</label>
            <input required type="login" class="form-control" name="DatabaseUser" id="DatabaseUser" value="<?=$this->GlobalData['BottoConfig']->DatabaseConnection->User?>">
        </div>
        <div class="mb-3">
            <label for="DatabasePassword" class="form-label">Пароль</label>
            <input required type="password" minlength="8" class="form-control" name="DatabasePassword" id="DatabasePassword" placeholder="••••••••">
        </div>
        <div class="mb-2">
            <label for="DatabaseName" class="form-label">Название БД</label>
            <input required type="text" class="form-control" name="DatabaseName" id="DatabaseName" value="<?=$this->GlobalData['BottoConfig']->DatabaseConnection->Database?>">
        </div>
        <?php if($this->Data['DatabaseFormError'] != null) { ?>
            <div class="mb-3">
                <p class="text-danger"><?=$this->Data['DatabaseFormError']?></p>
            </div>
        <?php } ?>
        <div class="mt-4 mb-3">
            <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
        </div>
    </form>

    <form class="" method="POST" onchange="ValidateForm(this)" oninput="ValidateForm(this)">
        <input type="hidden" name="form" value="UpdateAdminPanelSettings" />
        <h4 class="mb-4">Административная панель</h4>
        <div class="mb-2">
            <label for="SessionUser" class="form-label">Ключ сессии</label>
            <input required type="text" class="form-control" name="SessionUser" id="SessionUser" value="<?=$this->GlobalData['BottoConfig']->SessionUser?>">
        </div>
        <?php if($this->Data['AdminPanelFormError'] != null) { ?>
            <div class="mb-3">
                <p class="text-danger"><?=$this->Data['AdminPanelFormError']?></p>
            </div>
        <?php } ?>
        <p class="text-warning">После сохранения изменений возможен выход из системы</p>
        <div class="mt-4 mb-3">
            <button type="submit" disabled class="btn btn-primary">Сохранить изменения</button>
        </div>
    </form>
</div>