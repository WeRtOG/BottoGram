<div class="">
    <h4>Тема</h4>
    <div class="theme-switch">
        <div class="theme-wrapper dark<?=$this->GlobalData['BottoConfig']->DarkTheme ? ' active' : ''?>">
            <div class="theme">
                <div class="particle big">
                </div>
                <div class="particle-rows">
                    <div class="particle row particle-1"></div>
                    <div class="particle row particle-2"></div>
                    <div class="particle row particle-3"></div>
                </div>
            </div>
            <p>Тёмная</p>
        </div>
        <div class="theme-wrapper white<?=$this->GlobalData['BottoConfig']->DarkTheme ? '' : ' active'?>">
            <div class="theme">
                <div class="particle big">
                </div>
                <div class="particle-rows">
                    <div class="particle row particle-1"></div>
                    <div class="particle row particle-2"></div>
                    <div class="particle row particle-3"></div>
                </div>
            </div>
            <p>Светлая</p>
        </div>
    </div>
</div>