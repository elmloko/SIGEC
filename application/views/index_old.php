<script>
    $(function () {
        var o = this;
        var chart = $("#flot-visitors");

        // Elements check
        if (!$.isFunction($.fn.plot) || chart.length === 0) {
            return;
        }

        $.getJSON('/ajax/grafica', function (data) {
            //var data = [data];
            // Chart options
            var labelColor = chart.css('color');
            var options = {
                colors: chart.data('color').split(','),
                series: {
                    shadowSize: 0,
                    lines: {
                        show: true,
                        lineWidth: false,
                        fill: true
                    },
                    curvedLines: {
                        apply: true,
                        active: true,
                        monotonicFit: false
                    }
                },
                legend: {
                    container: $('#flot-visitors-legend')
                },
                xaxis: {
                    mode: "time",
                    timeformat: "%d %b",
                    font: {color: labelColor}
                },
                yaxis: {
                    font: {color: labelColor}
                },
                grid: {
                    borderWidth: 0,
                    color: labelColor,
                    hoverable: true
                }
            };
            chart.width('100%');

            // Create chart
            var plot = $.plot(chart, data, options);

            // Hover function
            var tip, previousPoint = null;
            chart.bind("plothover", function (event, pos, item) {
                if (item) {
                    if (previousPoint !== item.dataIndex) {
                        previousPoint = item.dataIndex;

                        var x = item.datapoint[0];
                        var y = item.datapoint[1];
                        var tipLabel = '<strong>' + $(this).data('title') + '</strong>';
                        var tipContent = Math.round(y) + " " + item.series.label.toLowerCase() + " on " + moment(x).format('dddd');

                        if (tip !== undefined) {
                            $(tip).popover('destroy');
                        }
                        tip = $('<div></div>').appendTo('body').css({left: item.pageX, top: item.pageY - 5, position: 'absolute'});
                        tip.popover({html: true, title: tipLabel, content: tipContent, placement: 'top'}).popover('show');
                    }
                }
                else {
                    if (tip !== undefined) {
                        $(tip).popover('destroy');
                    }
                    previousPoint = null;
                }
            });
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
                        <span class="opacity-50">Documentos</span>
                    </a>
                </div>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <!-- END ALERT - TIME ON SITE -->

</div>

<div class="row">
    <div class="col-md-8">
        <div class="card-head">
            <header>Site activity</header>
        </div><!--end .card-head -->
        <div class="card-body height-8">
            <div id="flot-visitors-legend" class="flot-legend-horizontal stick-top-right no-y-padding"></div>
            <div id="flot-visitors" class="flot height-7" data-title="Activity entry" data-color="#7dd8d2,#0aa89e"></div>
        </div><!--end .card-body -->
    </div><!--end .col -->
    <div class="col-md-4">
        <div class="card-head">
            <header>Today's stats</header>
        </div>
        <div class="card-body height-8">
            <strong>214</strong> members
            <span class="pull-right text-success text-sm">0,18% <i class="md md-trending-up"></i></span>
            <div class="progress progress-hairline">
                <div class="progress-bar progress-bar-primary-dark" style="width:43%"></div>
            </div>
            756 pageviews
            <span class="pull-right text-success text-sm">0,68% <i class="md md-trending-up"></i></span>
            <div class="progress progress-hairline">
                <div class="progress-bar progress-bar-primary-dark" style="width:11%"></div>
            </div>
            291 bounce rates
            <span class="pull-right text-danger text-sm">21,08% <i class="md md-trending-down"></i></span>
            <div class="progress progress-hairline">
                <div class="progress-bar progress-bar-danger" style="width:93%"></div>
            </div>
            32,301 visits
            <span class="pull-right text-success text-sm">0,18% <i class="md md-trending-up"></i></span>
            <div class="progress progress-hairline">
                <div class="progress-bar progress-bar-primary-dark" style="width:63%"></div>
            </div>
            132 pages
            <span class="pull-right text-success text-sm">0,18% <i class="md md-trending-up"></i></span>
            <div class="progress progress-hairline">
                <div class="progress-bar progress-bar-primary-dark" style="width:47%"></div>
            </div>
        </div><!--end .card-body -->
    </div><!--end .col -->
</div><!--end .row -->

<div class="row">

    <!-- BEGIN SITE ACTIVITY -->

    <div class="col-md-8">

        <div class="card card-underline">
            <div class="card-head ">
                <header><i class="fa fa-file-text"></i> Documentos Recientes</header>
                <div class="tools">
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
            <div class="card-body no-padding height-9 scroll style-default-bright">
                <div class="table-responsive no-margin">
                    <table class="table table-striped no-margin">

                        <tbody>
                            <?php foreach ($zdoc as $d): ?>
                                <tr>
                                    <td class=" text-sm">                                        
                                        <small class="text-bold"><?php echo $d['cite_original'] ?></small><br/>
                                        <a href="/route/trace/?hr=<?php echo $d['nur'] ?>" class=" text-primary"><?php echo $d['nur'] ?></a>
                                    </td>
                                    <td class=" text-sm">
                                        <h4><?php echo $d['referencia'] ?></h4>
                                        <span>
                                            <?php echo $d['nombre_destinatario'] ?>
                                            |  <?php echo $d['cargo_destinatario'] ?>        
                                        </span>
                                        <small class="text-default-light pull-right"><?php echo $d['fecha_creacion'] ?></small>

                                    </td>
                                    <td class=" text-sm">
                                        <a href="/documento/edit/<?php echo $d['id'] ?>" class="btn btn-xs text-primary-dark"><i class="fa fa-pencil fa-2x"></i></a>
                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div><!--end .table-responsive -->
            </div><!--end .card-body -->
        </div>
    </div><!--end .col -->

    <!-- END SITE ACTIVITY -->

    <!-- BEGIN TODOS -->
    <div class="col-md-4">
        <div class="card card-underline">
            <div class="card-head">
                <header><i class="fa fa-users"></i> Destinatarios</header>
                <div class="tools">
                    <a class="btn btn-icon-toggle btn-close"><i class="md md-close"></i></a>
                </div>
            </div><!--end .card-head -->
            <div class="card-body no-padding height-9 scroll style-default-bright">
                <table class="table table-no-border">
                    <tbody>
                        <?php foreach ($destinatarios as $d): ?>
                            <tr>
                                <td>
                                    <img alt="" src="/static/img/avatar5.jpg?1404026513"  class="img-circle text-center height-1 width-1">
                                </td>
                                <td class=" no-margin no-padding">
                        <spdan class="text-xs">
                            <?php echo $d['nombre'] ?><br/>
                            <strong><?php echo $d['cargo'] ?></strong> 
                        </spdan>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div><!--end .card-body -->
        </div><!--end .card -->
    </div><!--end .col -->
    <!-- END TODOS -->
