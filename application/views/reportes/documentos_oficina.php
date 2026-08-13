
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
     
        $('#oficina').change(function() {
            var oficina = $('#oficina').val();
            gauges(oficina);
        });

        function gauges(oficina) {         

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
                                return '<a href="/reports/jqwdocumentos/?tipo=' + e[0] + '&oficina=' + oficina + '  " class="doc" >' + this.point.name + ': </a> ' + this.percentage.toFixed(1) + ' %';
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
            jQuery.getJSON('/ajax/jsondocumentos/', {oficina: oficina}, function(data) {
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
        $('#oficina').trigger('change');
        $('select#oficina').select2();
    });
</script>

<style>

    .doc{font-weight: 500; }
    .doc:hover{text-decoration: underline; }
    #taco1{width: 250px;}
    fieldset{padding: 10px;}
    .tacometros{width: 100%; margin-top:20px; }
    .tacometros h5{font-size: 18px; margin-bottom: 10px;}
    // .money{text-align: center; font-size: 18px; color: #2AB2AC;}
    .tacometros tr td {text-align: center;}    
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
        </table>
    </div>
</fieldset>
<div class="row-fluid">
    <div class="span6 border-box">
        <div id="container" style="background:#fff url('/media/jqwidgets/resources/loader.gif') center no-repeat ; width:100%; height: 400px; border:1px solid #80699B;"></div>        
    </div>
    <div>
        <table>

        </table>
    </div>
</div>
