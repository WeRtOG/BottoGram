<div class="settings-page">
    <ul class="nav nav-pills nav-fill">
        <?php if(isset($this->GlobalData['CurrentUser']) && $this->GlobalData['CurrentUser']->CanChangeConfig) { ?>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "config" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/config">Конфигурация</a>
        </li>
        <?php } ?>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "profile" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/profile">Моя учётная запись</a>
        </li>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "personalization" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/personalization">Персонализация</a>
        </li>
        <?php if(isset($this->GlobalData['CurrentUser']) && $this->GlobalData['CurrentUser']->CanManageUsers) { ?>
        <li class="nav-item">
            <a data-reload=".settings-page .sub-page" class="async nav-link<?=CurrentMVCAction == "users" ? ' active' : '' ?>" href="<?=$this->Root?>/settings/users">Пользователи</a>
        </li>
        <?php } ?>
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