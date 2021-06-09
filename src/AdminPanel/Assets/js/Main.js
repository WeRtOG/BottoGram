const rebuildingEvent = new Event('DOMContentRebuilding');
const rebuildedEvent = new Event('DOMContentRebuilded');

document.addEventListener("DOMContentLoaded", function(event) {
    document.dispatchEvent(rebuildedEvent);
    anix.Init(document.body.querySelectorAll('.anix'));
});
document.addEventListener("DOMContentRebuilded", function(event) {
    document.querySelectorAll('.smart-chart').forEach(chartElement => {
        const labels = JSON.parse(chartElement.getAttribute('data-labels'));
        const title1 = chartElement.getAttribute('data-title1');
        const title2 = chartElement.getAttribute('data-title2');
        const data1 = JSON.parse(chartElement.getAttribute('data-set1'));
        const data2 = JSON.parse(chartElement.getAttribute('data-set2'));

        var chart = CreateLineChart(chartElement, labels, title1, title2, data1, data2);
        chart.render();
    });
});
if(UITheme == 'dark')
{
    window.Apex = {
        chart: {
            foreColor: '#ccc',
            toolbar: {
                show: false
            },
        },
        stroke: {
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            theme: 'dark'
        },
        grid: {
            borderColor: "#111",
            xaxis: {
                lines: {
                    show: true
                }
            }
        }
    };
}
else
{
    window.Apex = {
        chart: {
            toolbar: {
                show: false
            },
        },
        stroke: {
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        grid: {
            xaxis: {
                lines: {
                    show: true
                }
            }
        }
    };
}

function HEXToRGB(hex)
{
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function CreateLineChart(element, labels, title1, title2, data1, data2)
{
    var options = {
        series: [{
            name: title1,
            data: data1
        }, {
            name: title2,
            data: data2
        }],
        chart: {
            type: 'area',
            stacked: false,
            height: 300,
            zoom: {
                enabled: false
            },
        },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 0,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            },
        },
        xaxis: {
            categories: labels
        },
        tooltip: {
            shared: true
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetX: -10
        }
    };

    return new ApexCharts(element, options);
}