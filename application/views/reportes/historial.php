<script type="text/javascript">
    $(function() {
var color=$('#bos-main-blocks h2 a, h2.titulo v, .colorcito').css('color');
//alert(color);
        Highcharts.setOptions({
            lang: {
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado']
            },
            rangeSelectorFrom: 'De',
            resetZoomTitle: "Zoom"
        })


        $.getJSON('/ajax/historial', function(data) {

            // create the chart
            $('#container').highcharts('StockChart', {
                chart: {
                    alignTicks: false
                },
                rangeSelector: {
                    selected: 1
                },
                colors: [
                    '#333',
                ],
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Documentación Generada'
                },
                series: [{
                        type: 'column',
                        name: 'Documentos generados',
                        data: data,
                        dataGrouping: {
                            units: [[
                                    'week', // unit name
                                    [1] // allowed multiples
                                ], [
                                    'month',
                                    [1, 2, 3, 4, 6]
                                ]]
                        }
                    }]
                
                
            });
        });
    });
</script>
<div id="container" style="height: 500px"></div>