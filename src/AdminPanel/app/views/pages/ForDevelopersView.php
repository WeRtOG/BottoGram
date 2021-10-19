<div class="settings-page fordevelopers-page">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "logs" ? ' active' : '' ?>" href="<?=$this->Root?>/fordevelopers/logs">Логи</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "botusers" ? ' active' : '' ?>" href="<?=$this->Root?>/fordevelopers/botusers">Пользователи бота</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "systeminfo" ? ' active' : '' ?>" href="<?=$this->Root?>/fordevelopers/systeminfo">О системе</a>
        </li>
    </ul>
    
    <div class="sub-page">
        <?php 
            $SubPage = $this->Data['SubPage'] ?? null;

            if($SubPage != null && file_exists($SubPage))
            {
                include $SubPage;
            }
            else
            {
                echo '<p>Подстраница не найдена.</p>';
            }
        ?>
    </div>
</div>