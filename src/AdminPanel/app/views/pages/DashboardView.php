<div class="dashboard">
    <section class="engine-analytics">
        <h3>
            Статистика использования
        </h3>
        <section class="double-group">
            <div class="double-group-block">
                <div class="chart-wrapper">
                    <div
                        data-labels='<?=json_encode($this->Data['Analytics']['NewUsersGraph']['Labels'])?>'
                        data-title1="Динамика новых пользователей (за день)"
                        data-title2="Количество новых пользователей (за день)"
                        data-set1='<?=json_encode($this->Data['Analytics']['NewUsersGraph']['Data']['Dynamic'])?>'
                        data-set2='<?=json_encode($this->Data['Analytics']['NewUsersGraph']['Data']['Count'])?>'
                        class="smart-chart analytics-chart-1"
                    ></div>
                </div>
            </div>
            <div class="double-group-block">
                <div class="chart-wrapper">
                    <div
                        data-labels='<?=json_encode($this->Data['Analytics']['DailyUsersGraph']['Labels'])?>'
                        data-title1="Кол-во активных пользователей (за час)"
                        data-title2="Кол-во запросов (за час)"
                        data-set1='<?=json_encode($this->Data['Analytics']['DailyUsersGraph']['Data']['Users'])?>'
                        data-set2='<?=json_encode($this->Data['Analytics']['DailyUsersGraph']['Data']['Requests'])?>'
                        class="smart-chart analytics-chart-2"
                    ></div>
                </div>
            </div>
        </section>
        <div class="double-group-block">
            <div class="chart-wrapper">
                <div
                    data-labels='<?=json_encode($this->Data['Analytics']['WeeklyUsersGraph']['Labels'])?>'
                    data-title1="Кол-во активных пользователей (за день)"
                    data-title2="Кол-во запросов (за день)"
                    data-set1='<?=json_encode($this->Data['Analytics']['WeeklyUsersGraph']['Data']['Users'])?>'
                    data-set2='<?=json_encode($this->Data['Analytics']['WeeklyUsersGraph']['Data']['Requests'])?>'
                    class="smart-chart analytics-chart-3"
                ></div>
            </div>
        </div>
    </section>
</div>