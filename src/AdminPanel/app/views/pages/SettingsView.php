<div class="settings-page">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "main" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/main">Основные настройки</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "config" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/config">Конфигурация</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "personalization" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/personalization">Персонализация</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "users" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/users">Пользователи админ. панели</a>
        </li>
    </ul>
    
    <div class="sub-page p-4 container">
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