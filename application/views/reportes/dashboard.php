
<script type="text/javascript">

    var Chart;
    var ChartX;
    var Chart2;
    $(function() {
        //alert(options.series);
        $("#imprime").click(function() {
            window.print();
            return false;
        });
        //var gestion = $('#gestion').val();
        //torta
        // Radialize the colors
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
            return {
                radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                stops: [
                    [0, color],
                    [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        });




        //  $('#theTable tbody tr:odd').addClass('odd');
        function formatNumber(num, prefix) {
            prefix = prefix || '';
            num += '';
            var splitStr = num.split(".");
            var splitLeft = splitStr[0];
            var splitRight = splitStr.length > 1 ? "." + splitStr[1] : '';
            var regx = /(\d+)(\d{3})/;
            while (regx.test(splitLeft)) {
                splitLeft = splitLeft.replace(regx, "$1" + "," + "$2");
            }
            return prefix + splitLeft + splitRight;
        }
        //gauges

        var gestion = 2013;
        var labels = {visible: true, position: 'outside', border: 0},
        theme = 'metro';
        $('#gauge1').jqxGauge({
            ranges: [{startValue: 0, endValue: 40, style: {fill: '#AA4643', stroke: '#AA4643'}, startDistance: '5%', endDistance: '5%', endWidth: 5, startWidth: 1},
                {startValue: 40, endValue: 70, style: {fill: '#f6de54', stroke: '#f6de54'}, startDistance: '5%', endDistance: '5%', endWidth: 10, startWidth: 5},
                {startValue: 70, endValue: 110, style: {fill: '#2E895B', stroke: '#2E895B'}, startDistance: '5%', endDistance: '5%', endWidth: 15, startWidth: 10},
                //{startValue: 100, endValue: 110, style: {fill: '#4572A7', stroke: '#4572A7'}, startDistance: '5%', endDistance: '5%', endWidth: 16, startWidth: 14}
            ],
            cap: {radius: 0.04},
            caption: {offset: [0, -15], value: 'Anual', position: 'bottom'},
            //      value: 0,
            style: {stroke: '#666666', 'stroke-width': '1px', fill: '#f2f2f2'},
            animationDuration: 1500,
//                colorScheme: 'scheme04',
            labels: labels,
            ticksMinor: {interval: 5, size: '5%'},
            ticksMajor: {interval: 10, size: '10%'},
            min: 0,
            max: 110,
            height: 180,
            width: 180,
            border: {size: '5%', style: {stroke: '#CDCDCD'}, visible: true}
        });
        //acumulado
        $('#gauge2').jqxGauge({
            ranges: [{startValue: 0, endValue: 40, style: {fill: '#AA4643', stroke: '#AA4643'}, startDistance: '5%', endDistance: '5%', endWidth: 5, startWidth: 1},
                {startValue: 40, endValue: 70, style: {fill: '#f6de54', stroke: '#f6de54'}, startDistance: '5%', endDistance: '5%', endWidth: 10, startWidth: 5},
                {startValue: 70, endValue: 110, style: {fill: '#2E895B', stroke: '#2E895B'}, startDistance: '5%', endDistance: '5%', endWidth: 15, startWidth: 10},
                // {startValue: 100, endValue: 110, style: {fill: '#4572A7', stroke: '#4572A7'}, startDistance: '5%', endDistance: '5%', endWidth: 16, startWidth: 14}
            ],
            cap: {radius: 0.04},
            caption: {offset: [0, -15], value: 'Acumulado', position: 'bottom'},
            value: 0,
            style: {stroke: '#666666', 'stroke-width': '1px', fill: '#f2f2f2'},
            animationDuration: 1500,
            //colorScheme: 'scheme04',
            labels: labels,
            ticksMinor: {interval: 5, size: '5%'},
            ticksMajor: {interval: 10, size: '10%'},
            min: 0,
            max: 110,
            height: 180,
            width: 180,
            border: {size: '5%', style: {stroke: '#CDCDCD'}, visible: true}
        });
        //mes
        $('#gauge3').jqxGauge({
            ranges: [{startValue: 0, endValue: 40, style: {fill: '#AA4643', stroke: '#AA4643'}, startDistance: '5%', endDistance: '5%', endWidth: 5, startWidth: 1},
                {startValue: 40, endValue: 70, style: {fill: '#f6de54', stroke: '#f6de54'}, startDistance: '5%', endDistance: '5%', endWidth: 10, startWidth: 5},
                {startValue: 70, endValue: 110, style: {fill: '#2E895B', stroke: '#2E895B'}, startDistance: '5%', endDistance: '5%', endWidth: 15, startWidth: 10},
                //{startValue: 100, endValue: 110, style: {fill: '#4572A7', stroke: '#4572A7'}, startDistance: '5%', endDistance: '5%', endWidth: 16, startWidth: 14}
            ],
            cap: {radius: 0.04},
            caption: {offset: [0, -15], value: 'Mes', position: 'bottom'},
            //value: 0,
            style: {stroke: '#666666', 'stroke-width': '1px', fill: '#f2f2f2'},
            animationDuration: 1500,
            //colorScheme: 'scheme04',
            labels: labels,
            ticksMinor: {interval: 5, size: '5%'},
            ticksMajor: {interval: 10, size: '10%'},
            min: 0,
            max: 110,
            height: 180,
            width: 180,
            border: {size: '5%', style: {stroke: '#CDCDCD'}, visible: true}
        });
        $('#gauge3').jqxGauge('value', 0);
        $('#gauge1').jqxGauge('value', 0);
        $('#gauge2').jqxGauge('value', 0);



        $('#mess').change(function() {
            var mes = $('#mes').val();
            //entregas(mes);
            //gauges
            $.ajax({
                dataType: 'json',
                url: '/ajax/gauges',
                data: 'gestion=' + gestion + '&mes=' + mes,
                success: function(data)
                {
                    $('#programadoAnual').text('Bs' + data.prgAnual);
                    $('#programadoAcumulado').text('Bs' + data.prgAcumulado);
                    $('#programadoMes').text('Bs' + data.prgMes);
                    $('#ejecutadoAnual').text('Bs' + data.ejeAnual);
                    $('#ejecutadoAcumulado').text('Bs' + data.ejeAcumulado);
                    $('#ejecutadoMes').text('Bs' + data.ejeMes);
                    $('#cumplimientoAnual').text('Bs' + data.generadoMes);
                    $('#cumplimientoAcumulado').text('Bs' + data.concluidosMes);
                    $('#cumplimientoMes').text('Bs' + data.g1);
                    //actulizamos los gauges                               
                    $('#gauge1').jqxGauge({caption: {value: data.cumAnual + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.cumAnual});
                    $('#gauge2').jqxGauge({caption: {value: data.cumAcumulado + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.cumAcumulado});
                    $('#gauge3').jqxGauge({caption: {value: data.cumMes + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.cumMes});
                }
            });
            //fin gauges



        });

        ///mapa

        $('#gestion').change(function() {
            var gestion = parseInt($(this).val());
            if (gestion == 0) {
                $('#mes option[value=' + 0 + ']').attr("selected", true);
            }
        });

        $('#reporte').click(function() {
            var oficina = $('#oficina').val();
            var gestion = $('#gestion').val();
            var mes = $('#mes').val();
            gauges(oficina, gestion, mes);
        });

        function gauges(oficina, gestion, mes) {
            $.ajax({
                dataType: 'json',
                url: '/ajax/gauges',
                data: 'oficina=' + oficina + '&gestion=' + gestion + '&mes=' + mes,
                success: function(data)
                {
                    $('#pacu').text('' + data.generadosAcumulado);
                    $('#cacu').text('' + data.concluidosAcumulado);
                    $('#g2').text('' + data.g2);
                    $('#pano').text('' + data.generadosAnio);
                    $('#cano').text(data.concluidosAnio);
                    $('#g3').text('' + data.g3);
                    $('#pmes').text('' + data.generadosMes);
                    $('#cmes').text('' + data.concluidosMes);
                    $('#g1').text('' + data.g1);
                    //actulizamos los gauges                               
                    $('#gauge1').jqxGauge({caption: {value: data.g3 + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.g1});
                    $('#gauge2').jqxGauge({caption: {value: data.g2 + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.g1});
                    $('#gauge3').jqxGauge({caption: {value: data.g1 + ' %', position: 'bottom', offset: [0, -15], visible: true},
                        value: data.g1});
                }
            });






            var options = {
                chart: {
                    renderTo: 'container',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    height: 398,
                },
                title: {
                    text: 'Documentos Generados'
                },
                /*  colors: [
                 '#75C4B0',
                 '#4572A7',
                 '#AA4643',
                 '#FDEDD0',
                 '#BCF1ED',
                 '#FF634D',
                 '#0C6BA1',
                 '#8bbc21',
                 '#FF7500',
                 '#8AA693',
                 '#910000',
                 '#1aadce',
                 '#492970',
                 '#f28f43',
                 '#77a1e5',
                 '#c42525',
                 '#a6c96a'
                 ],*/
                subtitle: {
                    text: 'Puede hacer un click en el tipo de documento para ver en detalle.'
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b> <br/> ' + this.point.percentage.toFixed(2) + '%';
                    },
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    /* series: {
                     animation: {
                     duration: 2000,
                     easing: 'easeOutBounce'
                     }
                     },*/
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                var str = this.point.name;
                                var e = str.split("(")
                                return '<a href="/reports/jqwdoc/?documento=' + e[0] + ' &oficina=' + oficina + '&gestion=' + gestion + '&mes=' + mes + '  " class="doc" >' + this.point.name + ': </a> ' + this.percentage.toFixed(1) + ' %';
                            },
                            style: {
                                fontSize: '11px'
                            },
                            useHTML: true
                        }
                    },
                },
                series: []
            }
            jQuery.getJSON('/ajax/tDocumentos/', {oficina: oficina, gestion: gestion, mes: mes}, function(data) {
                var datitos = [];
                var valor = 0;
                $.each(data, function(i, e) {
                    valor = parseFloat(e.cantidad);
                    datitos.push([e.documento + ' (<b>' + valor + '</b>)', valor]);
                });
                options.series.push({
                    type: 'pie',
                    name: 'UH',
                    data: datitos
                });
                chart = new Highcharts.Chart(options);
            });
        }
        $('#reporte').trigger('click');
        $('select#oficina').select2();

        $('#explosion').click(function() {
            var oficina = $('#oficina').val();
            var gestion = $('#gestion').val();
            var mes = $('#mes').val();
            location.href = "/reports/explosion/?oficina=" + oficina+ "&gestion=" + gestion + "&mes=" + mes;
        });
    });
</script>

<style>

    .doc{font-weight: 500; }
    .doc:hover{text-decoration: underline; }
    #taco1{width: 250px;}
    fieldset{padding: 10px;}
    .tacometros{width: 100%; margin-top:20px; }
    .tacometros h5{font-size: 18px; margin-bottom: 10px;}
    /*// .money{text-align: center; font-size: 18px; color: #2AB2AC;}*/
    .tacometros tr td {text-align: center;}
    table.monto{margin-top: 20px; text-align: center;}
    table.monto tr td{
        font-size: 16px;
        background: none repeat scroll 0 0 #0D233A;
        border: 1px solid #FFFFFF;
        color: #FFFFFF;
        margin: 0 suto;
        padding: 5px;}
    table.monto tr td a{ color: #EFEFEF; display: block;}
    table.monto tr td.money {
        background: none repeat scroll 0 0 #EFEFEF;
        color: #111111;
        font-family: monospace;
        font-weight: bold;
        width: 150px;
    }
    select#oficina{width: 500px !important; display: inline-block; }
    .select2-chosen{width: 500px;}
    .row-fluid{margin-bottom: 10px; }
</style>
<!--<p style="float: right; margin-top: -15px;"><a href="javascript:void(0)" id="imprime" class="uiButton noprint" title="imprimir"><img src="/media/images/print.png" align="absmiddle" alt=""/>Imprimir</a></p>-->
<fieldset>
    <div class="row-fluid">
        <table >
            <tr>
                <td><b>Oficina:</b></td>
                <td colspan="4"><?php echo Form::select('oficina', $oficinas, $user->id_oficina, array('id' => 'oficina')); ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>                
            </tr>
            <tr>
                <td><b>Gestión:</b></td>
                <td><?php echo Form::select('gestion', $gestiones, $gestion, array('id' => 'gestion')); ?></td>
                <td>Mes:</td>
                <td><?php echo Form::select('mes', $meses, $mes, array('id' => 'mes')); ?></td>
                <td><a href="javascript:void(0);" id="reporte" clasS="button2">Generar</a></td>
            </tr>
        </table>
    </div>
</fieldset>

<!-- Tacometros-->
<div class="row-fluid">
    <table class="tacometros">
        <tr>
            <td>
                <div class="span4 " style=" text-align: center;">
                    <h5>Indicador Procesos Anual</h5>
                    <div id="gauge1" style="background: url('/media/jqwidgets/resources/loader.gif') center no-repeat ; margin: 0 auto; width:100%; height: 200px;"></div> 
                    <table class="monto">
                        <tr>
                            <td>Iniciados: </td><td class="der money" id="pano"><b> </b></td> <td rowspan="2"><a href="javascript:void(0);" id="explosion">Revisar</a></td>
                        </tr>
                        <tr>
                            <td>Concluidos: </td><td class="der money" id="cano"> </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="span4" style=" text-align: center !important; margin: 0 auto;">
                    <h5>Indicador Procesos Acumulado</h5>
                    <div id="gauge2" style="background: url('/media/jqwidgets/resources/loader.gif') center no-repeat ; margin: 0 auto; width:100%; height: 200px;"></div>        
                    <table class="monto">
                        <tr>
                            <td>Iniciados: </td><td class="der money" id="pacu"> </td> <td rowspan="2"><a href="javascript:void(0);">Revisar</a></td>
                        </tr>
                        <tr>
                            <td>Concluidos : </td><td class="der money" id="cacu"> </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="span4" style=" text-align: center;">

                    <h5>Indicador Procesos Mes</h5>
                    <div id="gauge3" style="background: url('/media/jqwidgets/resources/loader.gif') center no-repeat ; margin: 0 auto; width:100%; height: 200px; "></div>        
                    <table class="monto">
                        <tr>
                            <td>Iniciados: </td><td class="der money" id="pmes"> </td> <td rowspan="2"><a href="javascript:void(0);">Revisar</a></td>
                        </tr>
                        <tr>
                            <td>Concluidos: </td><td class="der money" id="cmes"> </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>









<div class="row-fluid">
    <div class="span6 border-box">
        <div id="container" style="background:#fff url('/media/jqwidgets/resources/loader.gif') center no-repeat ; width:100%; height: 400px; border:1px solid #80699B;"></div>        
    </div>
    <div>
        <table>

        </table>
    </div>
</div>
