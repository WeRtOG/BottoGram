var UIAccent = null;
var UIAccentAlt = null;

const rebuildingEvent = new Event('DOMContentRebuilding');
const rebuildedEvent = new Event('DOMContentRebuilded');

document.addEventListener("DOMContentLoaded", function(event) {
    LoadUIAccentColor();
    document.dispatchEvent(rebuildedEvent);
    anix.Init(document.body.querySelectorAll('.anix'));
});

function ChangeUIAccentColor(color) {
    UIAccent = color;
    Cookies.set('ui-accent', color);

    UIAccentAlt = GetComplementColor(UIAccent);

    document.querySelector('html').style.setProperty('--ui-accent', UIAccent);
    document.querySelector('html').style.setProperty('--ui-accent-alt', UIAccentAlt);

    document.querySelectorAll('.cabinet .personalization .color-select .color').forEach(colorButton => {
        var currentColor = window.getComputedStyle(colorButton).backgroundColor;

        if(currentColor == color)
        {
            colorButton.classList.add('active');
        }
        else
        {
            colorButton.classList.remove('active');
        }
    });
}

function GetComplementColor(source)
{
    console.log(source);

    switch(source)
    {
        case 'rgb(13, 110, 253)':
            return 'rgb(0, 227, 150)';

        case 'rgb(111, 66, 193)':
            return 'rgb(0, 227, 150)';

        case 'rgb(111, 66, 193)':
            return 'rgb(0, 227, 150)';

        case 'rgb(214, 51, 132)':
            return 'rgb(0, 143, 251)';

        case 'rgb(220, 53, 69)':
            return 'rgb(111, 66, 193)';

        case 'rgb(253, 126, 20)':
            return 'rgb(255, 193, 7)';

        case 'rgb(108, 117, 125)':
            return 'rgb(0, 227, 150)';
        
        case 'rgb(52, 58, 64)':
            return 'rgb(0, 227, 150)';
     
        default:
            var sourceObject = tinycolor(source);
            var result = sourceObject.clone().triad()[1];
        
            return result.toRgbString();
    }

}

function LoadUIAccentColor() {
    UIAccent = Cookies.get('ui-accent');

    if(UIAccent == undefined || UIAccent == null)
    {
        UIAccent = getComputedStyle(document.querySelector('html'))?.getPropertyValue('--ui-accent');
    }

    UIAccentAlt = GetComplementColor(UIAccent);

    document.querySelector('html').style.setProperty('--ui-accent', UIAccent);
    document.querySelector('html').style.setProperty('--ui-accent-alt', UIAccentAlt);

    if(document.querySelector('.cabinet .personalization .color-select'))
    {
        ShowActiveUIAccentColorInPersonalization();
    }
}

document.addEventListener("DOMContentRebuilded", function(event) {
    var i = 0;
    document.querySelectorAll('.smart-chart').forEach(chartElement => {
        const labels = JSON.parse(chartElement.getAttribute('data-labels'));
        const title1 = chartElement.getAttribute('data-title1');
        const title2 = chartElement.getAttribute('data-title2');
        const data1 = JSON.parse(chartElement.getAttribute('data-set1'));
        const data2 = JSON.parse(chartElement.getAttribute('data-set2'));

        var chart = CreateLineChart(chartElement, labels, title1, title2, data1, data2, [UIAccent, UIAccentAlt]);
        chart.render();

        i++;
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
            enabled: false,
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

function CreateLineChart(element, labels, title1, title2, data1, data2, colors)
{
    var options = {
        colors: colors,
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
        },
    };

    return new ApexCharts(element, options);
}