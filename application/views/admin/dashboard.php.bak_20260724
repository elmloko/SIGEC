<script>

    $(document).ready(function () {
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        var options = {
            chart: {
                renderTo: 'users',
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
            },
            title: {
                text: ''
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'Usuarios conectados'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.series.name + '</b><br/>' +
                            Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                            //Highcharts.dateFormat('%H:%M:%S', this.x) + '<br/>' +
                            Highcharts.numberFormat(this.y, 0);
                }
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: []
        }



        //data
        var chart = null;
        function callAjax() {
           /* $.getJSON('/admin/ajax/conectados', function (data) {
                if (chart === null) { // first call, create the chart
                    options.series = data;
                    chart = new Highcharts.Chart(options);
                } else {
                    var seriesOneNewPoint = data[0].data[0]; // subsequent calls, just get the point and add it                                           
                    //  var seriesTwoNewPoint = data[1].data[0];
                    chart.series[0].addPoint(seriesOneNewPoint, true, false); // first false is don't redraw until both series are updated
                    //chart.series[1].addPoint(seriesTwoNewPoint, true, false); // second false is don't shift
                }
                // queue up next ajax call
            });*/
            $.getJSON('/admin/ajax/usuariosconectados', function (data) {
                $('table#usuarios tbody tr').remove();
                var cantidad=0;
                $.each(data, function (i, e) {
                    $('table#usuarios tbody').append('<tr><td>' + data[i].nombre + '</td><td>' + data[i].cargo + '</td><td>' + data[i].segundos + '</tr>');
                    cantidad++;
                });
                $('span#cantidad').text(cantidad);
                // setTimeout(callAjax, 10000);  // queue up next ajax call
            });
            $.getJSON('/admin/ajax/bitacora', function (data) {
                $('table#bitacora tbody tr').remove();
                var cantidad=0;
                $.each(data, function (i, e) {
                    $('table#bitacora tbody').append('<tr><td>' + data[i].fecha + '</td><td>' + data[i].accion_realizada + '</td><td>' + data[i].ip + '</tr>');
                    cantidad++;
                });
               // $('span#cantidad').text(cantidad);
                // setTimeout(callAjax, 10000);  // queue up next ajax call
            });

            setTimeout(callAjax, 5000);
        }
        callAjax();



        var current = new Date().getTime();
        //alert(current);


    });


</script>

<style>
    .mt:hover,.mt:focus{ text-decoration: none !important;  ;   }
</style>
<div class="row">
    <?php foreach ($estados as $k => $v): ?>      
        <!-- BEGIN ALERT - TIME ON SITE -->
        <div class="col-md-3 col-sm-6">
            <div class="card animation-expand">
                <div class="card-body   no-padding">
                    <div class="alert alert-callout   alert-<?php echo $v['color'] ?> no-margin">
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
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-link no-margin">
                    <a  href="/admin/user" style=" display: block;" class="mt">  <strong class="text-danger text-lg">Usuarios :  <i class="md md-audiotracs"></i></strong>                    
                        <h1 class="pull-right text-danger"><i class="fa fa-users"></i></h1>
                        <strong class="text-xl"><?php echo $usuarios; ?></strong><br/>
                        <span class="opacity-50">Usuarios registrados</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-warning no-margin">
                    <a  href="/admin/document" style=" display: block;" class="mt">  <strong class="text-warning text-lg">Documentos :  <i class="md md-audiotracs"></i></strong>                    
                        <h1 class="pull-right text-warning"><i class="fa fa-file-word-o"></i></h1>
                        <strong class="text-xl"><?php echo number_format($documentos, 0); ?></strong><br/>
                        <span class="opacity-50">Documentos generados</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-info no-margin">
                    <a  href="/admin/entidades" style=" display: block;" class="mt">  <strong class="text-info text-lg">Entidades:  <i class="md md-audiotracs"></i></strong>                    
                        <h1 class="pull-right text-info"><i class="fa fa-building-o"></i></h1>
                        <strong class="text-xl"><?php echo number_format($entidades, 0); ?></strong><br/>
                        <span class="opacity-50">Entidades administradas</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <div class="col-md-3 col-sm-6">
        <div class="card">
            <div class="card-body no-padding">
                <div class="alert alert-callout alert-info no-margin">
                    <a  href="/admin/entidades
                        " style=" display: block;" class="mt">  <strong class="text-info text-lg">Documentos :  <i class="md md-audiotracs"></i></strong>                    
                        <h1 class="pull-right text-info"><i class="fa fa-tag"></i></h1>
                        <strong class="text-xl"><?php echo number_format($hojasruta, 0); ?></strong><br/>
                        <span class="opacity-50">Hojas de Ruta</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->

    <!-- END ALERT - TIME ON SITE -->

</div>

<div class="row">
    
    <div class=" col-sm-12 col-md-6">
        <div class="card style-default-bright no-padding card-underline">
            <div class="card-head">
                <header>Actividad Reciente: </header>
            </div>
            <div class="card-body height-12 scroll no-padding">
                <div id="users2" class="height-6 ">
                    <table id="bitacora" class="table table-condensed table-bordered ">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Acción Relizada</th>
                                <th>IP </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <div class=" col-sm-12 col-md-6">
        <div class="card style-default-bright no-padding card-underline">
            <div class="card-head">
                <header>Usuarios conectados: <span id="cantidad">0</span></header>
            </div>
            <div class="card-body height-12 scroll no-padding">
                <div id="users2" class="height-6 ">
                    <table id="usuarios" class="table table-condensed table-bordered ">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Seg. atras</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>


