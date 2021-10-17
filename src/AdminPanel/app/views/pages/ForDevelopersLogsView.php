<div class="app-logs">
    <?php if(!empty($this->Data['LogsList'])) { ?>
    <pre class="log-raw">
    <?=$this->Data['LogsList']?>
    </pre>
    <?php } else { ?>
    <p class="empty">Здесь пока ничего нет...</p>
    <?php } ?>
    
    <button class="smart-list-fab fab-danger clear-logs"<?=empty($this->Data['LogsList']) ? ' disabled' : ''?>>
        <i class="bi bi-trash"></i>
    </button>
</div>
<div class="modal fade delete-confirm" tabindex="-1" aria-hidden="true" id="clearLogsModal" aria-labelledby="clearLogsModalLabel">
    <form onchange="ValidateForm(this)" oninput="ValidateForm(this)" action="<?=$this->Root?>/fordevelopers/clearLogs" method="POST" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Подтверждение действия</h5>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите очистить логи?
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="modal-button cancel"><span>Отмена</span></button>
                <button type="submit" class="modal-button accept"><span>Очистить</span></button>
            </div>
        </div>
    </form>
</div>