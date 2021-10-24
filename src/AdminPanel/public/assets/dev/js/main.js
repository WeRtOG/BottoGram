const rebuildingEvent = new Event('DOMContentRebuilding');
const rebuildedEvent = new Event('DOMContentRebuilded');

const charts = new SmartCharts();
const asyncEvents = new AsyncEvents();

document.addEventListener("DOMContentLoaded", function(event) {
    Personalization.LoadUIAccentColor();
    document.dispatchEvent(rebuildedEvent);
    anix.Init(document.body.querySelectorAll('.anix'));
});

document.addEventListener("DOMContentRebuilded", function(event) {
    charts.ReloadActiveCharts();
});

var personalizationDaemon = Personalization.StartDaemon({
    charts: charts
});

Personalization.UpdateApexTheme();