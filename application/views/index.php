<script>
    $(function () {
        //alert(options.series);
        $("#imprime").click(function () {
            window.print();
            return false;
        });
        //var gestion = $('#gestion').val();
        //torta
        // Radialize the colors
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
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


        var options = {
            chart: {
                renderTo: 'grafica',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                height: 398,
            },
            title: {
                text: ''
            },
            colors: [
                '#FF0F00',
                '#FF6600',
                '#FF9E01',
                '#FCD202',
                '#F8FF01',
                '#B0DE09',
                '#04D215',
                '#0D8ECF',
                '#0D52D1',
                '#2A0CD0',
                '#151E35',
                '#1aadce',
                '#492970',
                '#f28f43',
                '#77a1e5',
                '#c42525',
                '#a6c96a'
            ],
            subtitle: {
                text: ''
            },
            tooltip: {
                formatter: function () {
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
                        formatter: function () {
                            var str = this.point.name;
                            var e = str.split("(");
                            //return '<a href="/documents/?tipo=' + e[0] + '  " class="doc" >' + this.point.name + ': </a> ' + this.percentage.toFixed(1) + ' %';
                            return   this.point.name + ': ' + this.percentage.toFixed(1) + ' %';
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
        jQuery.getJSON('/ajax/misdocumentos/', {}, function (data) {
            var datitos = [];
            var valor = 0;
            $.each(data, function (i, e) {
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

    });

</script>
<style>
    .mt:hover,.mt:focus{ text-decoration: none !important;  ;   }
</style>
<div class="row">
    <?php foreach ($estados as $k => $v): ?>      
        <!-- BEGIN ALERT - TIME ON SITE -->
        <div class="col-md-6 col-sm-6 col-lg-3">
            <div class="card animated lightSpeedIn">
                <div class="card-body   no-padding">
                    <div class="alert alert-callout  alert-<?php echo $v['color'] ?> no-margin">
                        <a  href="<?php echo $v['accion'] ?>" style=" display: block;" class="mt">  <strong class="text-<?php echo $v['color'] ?> text-lg"><?php echo $v['titulo'] ?> :  <i class="md md-audiotracs"></i></strong>
                            <h1 class="pull-right text-<?php echo $v['color'] ?>"><i class="<?php echo $v['icon'] ?>"></i></h1>
                            <strong class="text-xl"><?php echo $v['cantidad']; ?></strong><br/>
                            <span class="opacity-50"><?php echo $v['descripcion'] ?></span>
                        </a>
                    </div>
                </div><!--end .card-body -->
            </div><!--end .card -->
        </div><!--end .col -->
        <!-- END ALERT - TIME ON SITE -->

    <?php endforeach; ?>
    <!-- BEGIN ALERT - TIME ON SITE -->
    <div class="col-md-6 col-sm-6 col-lg-3">
        <div class="card animated lightSpeedIn">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-success no-margin">
                    <a  href="/document" style=" display: block;" class="mt">  <strong class="text-success text-lg">Documentos :  <i class="md md-audiotracs"></i></strong>                    
                        <h1 class="pull-right text-success"><i class="fa fa-file-text-o"></i></h1>
                        <strong class="text-xl"><?php echo $documentos; ?></strong><br/>
                        <span class="opacity-50">Documentos generados</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <!-- END ALERT - TIME ON SITE -->

</div>

<div class="row">
    <div class="col-md-6">
         <div class="card card-underline">
            <div class="card-head ">
                <header class=" text-default-light" ><i class="fa fa-pie-chart"></i> Documentos por tipo</header>
            </div>
            <div class="card-body">
                <div id="grafica">

                </div>
            </div>
        </div><!--end .col -->
    </div><!--end .col -->

    <div class="col-md-6 ">
        <div class="card card-underline">
            <div class="card-head ">
                <header class=" text-default-light "><i class="fa fa-file-text"></i> Documentos recientes</header>
                <div class="tools ">
                    <div class="btn-group">
                        <button class="btn ink-reaction btn-sm btn-success" type="button"><i class="fa fa-file-text"></i> Generar</button>
                        <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-success dropdown-toggle" type="button" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                            <?php foreach ($tipos as $t): ?> 
                                <li><a href="/documento/generar/<?php echo $t['accion'] ?>"  id="hojaruta"><?php echo $t['tipo'] ?></a></li>
                            <?php endforeach; ?>
                            <li class="divider"></li>
                        </ul>
                    </div>

                </div>
            </div><!--end .card-head -->
            <div class="card-body no-padding height-11 scroll style-default-bright">
                <div class="table-responsive no-margin">
                    <table class="table table-striped no-margin ">

                        <tbody>
                            <?php foreach ($zdoc as $d): ?>
                                <tr>
                                    <td class=" text-sm">                                        
                                        <small class="text-bold"><?php echo $d['cite_original'] ?></small><br/>
                                        <a href="/route/trace/?hr=<?php echo $d['nur'] ?>" class=" text-primary"><?php echo $d['nur'] ?></a>
                                    </td>
                                    <td class=" text-sm">
                                        <h5 class=" text-primary-dark"><?php echo $d['referencia'] ?></h5>
                                        <span>
                                            <?php echo $d['nombre_destinatario'] ?>
                                            |  <?php echo $d['cargo_destinatario'] ?>        
                                        </span>
                                        <small class="text-default-light pull-right"><?php echo $d['fecha_creacion'] ?></small>

                                    </td>
                                    <td class=" text-sm">
                                        <a href="/documento/edit/<?php echo $d['id'] ?>" class="btn btn-xs text-default-light"><i class="fa fa-pencil fa-2x"></i></a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div>
    </div><!--end .col -->

   