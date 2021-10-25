class SmartCharts
{
    activeCharts = [];

    RenderChartFromElement(chartElement)
    {
        const labels = JSON.parse(chartElement.getAttribute('data-labels'));
        const title1 = chartElement.getAttribute('data-title1');
        const title2 = chartElement.getAttribute('data-title2');
        const data1 = JSON.parse(chartElement.getAttribute('data-set1'));
        const data2 = JSON.parse(chartElement.getAttribute('data-set2'));

        var chart = this.CreateLineChart(chartElement, labels, title1, title2, data1, data2, [UIAccent, UIAccentAlt]);
        chart.render();

        this.activeCharts.push(chart);
    }

    Init()
    {
        document.querySelectorAll('.smart-chart').forEach(chartElement => {
            this.RenderChartFromElement(chartElement);
        });
    }

    ReloadActiveCharts()
    {
        if(this.activeCharts != null && this.activeCharts.length > 0)
        {
            this.activeCharts.forEach(chart => {
                chart.destroy();
            });
        }

        this.activeCharts = [];
        this.Init();
    }

    CreateLineChart(element, labels, title1, title2, data1, data2, colors)
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
}