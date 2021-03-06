<?php
    use WeRtOG\BottoGram\AdminPanel\AdminPanel;
?>
<div class="personalization">
    <h4>Тема</h4>
    <div class="theme-switch">
        <div onclick="Personalization.ChangeUITheme('dark');" class="theme-wrapper dark<?=$this->GlobalData['DarkTheme'] ? ' active' : ' inactive'?>">
            <div class="theme">
                <i class="bi bi-moon-stars"></i>
            </div>
            <p>Тёмная</p>
        </div>
        <div onclick="Personalization.ChangeUITheme('white')" class="theme-wrapper white<?=$this->GlobalData['DarkTheme'] ? ' inactive' : ' active'?>">
            <div class="theme">
                <i class="bi bi-sun"></i>
            </div>
            <p>Светлая</p>
        </div>
    </div>
    <h4 class="mt-5">Акцентный цвет</h4>
    <div class="color-select">
        <div class="color blue"></div>
        <div class="color indigo"></div>
        <div class="color purple"></div>
        <div class="color pink"></div>
        <div class="color orange"></div>
        <div class="color teal"></div>
        <div class="color gray"></div>
    </div>
</div>

<?php AdminPanel::AsyncConnectJS($this, BOTTOGRAM_ADMIN_ASSETS . '/dev/js/page/Personalization.js'); ?>