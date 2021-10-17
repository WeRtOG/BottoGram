<div class="users-list">
    <?php
        $Users = $this->Data['Users'] ?? null;
        if($Users != null)
        {
        ?>
            <div class="smart-list-group">
                <?php
                foreach($Users as $User)
                {
                    $UserManageNotAllowed = isset($this->GlobalData['CurrentUser']) && (
                        $this->GlobalData['CurrentUser']->Login == $User->Login
                        || $User->Login == 'admin' 
                        || (
                            $User->CanManageUsers 
                            && $this->GlobalData['CurrentUser']->Login != 'admin'
                        )
                    );

                    ?>
                    <div class="user smart-list-item">
                        <button <?=$UserManageNotAllowed ? ' disabled' : ' data-id="' . $User->ID . '"'?> class="delete">
                            <i class="bi bi-dash-circle-fill"></i>
                        </button>
                        <p class="title"><?=$User->Login?></p>
                        <button <?=$UserManageNotAllowed ? ' disabled' : ' data-id="' . $User->ID . '" data-login="' . $User->Login . '" data-flags="[' . implode(', ', [(int)$User->CanManageUsers, (int)$User->CanChangeConfig, (int)$User->CanViewRequestLogs]) . ']"'?> class="edit">
                            <i class="bi bi-pencil-square""></i>
                        </button>
                    </div>
                    <?php
                }
                ?>
                <button class="smart-list-fab add-user">
                    <i class="bi bi-person-plus"></i>
                </button>
            </div>
        <?php
        } else {
            echo '<p>Список пользователей недоступен.</p>';
        }
    ?>
</div>
<div class="modal fade delete-confirm" tabindex="-1" aria-hidden="true" id="deleteUserModal" aria-labelledby="deleteUserModalLabel">
    <form onchange="ValidateForm(this)" oninput="ValidateForm(this)" action="<?=$this->Root?>/settings/deleteUser" method="POST" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Подтверждение удаления</h5>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить данного пользователя?
            </div>
            <div class="modal-footer">
                <input required type="hidden" id="DeleteUserID" name="DeleteUserID" value="" />
                <button type="button" data-bs-dismiss="modal" class="modal-button cancel"><span>Отмена</span></button>
                <button type="submit" class="modal-button accept"><span>Удалить</span></button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade add-user" tabindex="-1" aria-hidden="true" id="addUserModal" aria-labelledby="addUserModalLabel">
    <form onchange="ValidateForm(this)" oninput="ValidateForm(this)" action="<?=$this->Root?>/settings/addUser" method="POST" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Добавление пользователя</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input required type="login" class="form-control" name="Login" id="Login" placeholder="Логин">
                </div>
                <div class="mb-5">
                    <input required type="password" minlength="8" class="form-control" name="Password" id="Password" placeholder="Пароль">
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanManageUsers">
                    <label class="form-check-label">Может управлять пользователями</label>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanChangeConfig">
                    <label class="form-check-label">Может изменять конфигурацию</label>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanViewRequestLogs">
                    <label class="form-check-label">Может просматривать историю запросов</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="modal-button cancel"><span>Отмена</span></button>
                <button type="submit" disabled class="modal-button accept"><span>Добавить</span></button>
            </div>
        </div>
    </form>
</div>
<div class="modal fade edit-user" tabindex="-1" aria-hidden="true" id="editUserModal" aria-labelledby="editUserModalLabel">
    <form onchange="ValidateForm(this)" oninput="ValidateForm(this)" action="<?=$this->Root?>/settings/editUser" method="POST" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Редактирование<br>пользователя «<span id="EditUserLogin"></span>»‎</h5>
            </div>
            <div class="modal-body">
                <div class="mb-5">
                    <input type="password" minlength="8" class="form-control" name="NewPassword" placeholder="Новый пароль (необязательно)">
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanManageUsers">
                    <label class="form-check-label">Может управлять пользователями</label>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanChangeConfig">
                    <label class="form-check-label">Может изменять конфигурацию</label>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="CanViewRequestLogs">
                    <label class="form-check-label">Может просматривать историю запросов</label>
                </div>
            </div>
            <div class="modal-footer">
                <input required type="hidden" id="EditUserID" name="EditUserID" value="" />
                <button type="button" data-bs-dismiss="modal" class="modal-button cancel"><span>Отмена</span></button>
                <button type="submit" disabled class="modal-button accept"><span>Сохранить</span></button>
            </div>
        </div>
    </form>
</div>