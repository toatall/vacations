import ApexCharts from 'apexcharts';
import * as ru from 'apexcharts/dist/locales/ru.json';

let labelsX = [];
let dataX = [];

let chartTestDiv = document.getElementById('chartTest');

labelsX = JSON.parse(chartTestDiv.getAttribute('data-labels'));
dataX = JSON.parse(chartTestDiv.getAttribute('data-values'));
let year = JSON.parse(chartTestDiv.getAttribute('data-year'));

var options = {
    series: [{
        name: "Сотрудников в отпуске",
        data: dataX
    }],
    chart: {        
        type: 'area',
        height: 350,
        zoom: {
            enabled: false
        },
        locales: [ru],
        defaultLocale: 'ru'
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'straight'
    },

    title: {
        text: 'График нахождения сотрудников в отпуске на ' + year + ' год',
        align: 'left'
    },
    subtitle: {
        text: 'УФНС России по ХМАО-Югре',
        align: 'left'
    },
    labels: labelsX,
    xaxis: {
        type: 'datetime',
    },
    yaxis: {
        opposite: true
    },
    legend: {
        horizontalAlign: 'left'
    }
};

  var chart = new ApexCharts(document.querySelector("#chartTest"), options); 
  chart.render();