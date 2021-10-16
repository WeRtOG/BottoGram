<div class="">
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
</div>