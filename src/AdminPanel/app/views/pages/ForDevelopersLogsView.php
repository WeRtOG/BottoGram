<div class="p-4 container">
    <div class="app-logs">
        <div class="loading">
            <?php 
                $PreloaderPath = BOTTOGRAM_ADMIN_ASSETS . '/images/preloader.svg';
                if(file_exists($PreloaderPath))
                    include $PreloaderPath;
            ?>
        </div>
        <pre class="log-raw faded"></pre>
        <p class="empty hidden">Здесь пока ничего нет...</p>
        <button class="smart-list-fab fab-danger clear-logs" disabled>
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
</div>