<div class="personalization">
    <h4>Тема</h4>
    <div class="theme-switch">
        <div class="theme-wrapper dark<?=$this->GlobalData['DarkTheme'] ? ' active' : ' inactive'?>">
            <div class="theme">
                <i class="bi bi-moon-stars"></i>
            </div>
            <p>Тёмная</p>
        </div>
        <div class="theme-wrapper white<?=$this->GlobalData['DarkTheme'] ? ' inactive' : ' active'?>">
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
        <div class="color red"></div>
        <div class="color orange"></div>
        <div class="color yellow"></div>
        <div class="color green"></div>
        <div class="color teal"></div>
        <div class="color cyan"></div>
        <div class="color gray"></div>
        <div class="color gray-dark"></div>
    </div>
</div>