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
                    ?>
                    <div class="user smart-list-item">
                        <button class="delete">
                            <i class="bi bi-dash-circle-fill"></i>
                        </button>
                        <p class="title"><?=$User->Login?></p>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php
        } else {
            echo '<p>Список пользователей недоступен.</p>';
        }
    ?>
</div>
<div class="modal fade delete-confirm" tabindex="-1" aria-hidden="true" id="deleteModal" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить данного пользователя?
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="modal-button cancel">Отмена</button>
                <button type="button" data-bs-dismiss="modal" class="modal-button accept">Удалить</button>
            </div>
        </div>
    </div>
</div>