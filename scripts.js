document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('customPieChartCanvas');
    if (ctx) {
        var dataAttr = ctx.getAttribute('data-chart');
        if (dataAttr) {
            var data = JSON.parse(dataAttr.replace(/&quot;/g, '"'));
            var chart = new Chart(ctx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: data.colors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: data.title,
                            font: {
                                size: 18
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            },
                            align: 'center'
                        }
                    }
                }
            });
        }
    }
});