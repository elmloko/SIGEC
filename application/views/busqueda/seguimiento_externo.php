<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!--[if IE]>
    <script> (function () {
        var html5 = ("abbr,article,aside,audio,canvas,datalist,details," + "figure,footer,header,hgroup,mark,menu,meter,nav,output," + "progress,section,time,video").split(',');
        for (var i = 0; i < html5.length; i++) {
            document.createElement(html5[i]);
        }
    })(); </script> <![endif]-->
    <meta http-equiv="Content-Language" content="es"/>
    <link rel="shortcut icon" href="/media/images/icon.png"/>
    <title>SIGEC / Seguimiento E/2017-03198</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta name="copyright" content=""/>
    <link type="text/css" href="/media/css/style.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="/media/css/print.css" rel="stylesheet" media="print"/>
    <link type="text/css" href="/static/css/theme-1/bootstrap.css" rel="stylesheet" media="all"/>
    <link type="text/css" href="/static/css/theme-1/materialadmin.css" rel="stylesheet" media="all"/>
    <link type="text/css" href="/static/css/theme-1/font-awesome.min.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="/static/css/theme-1/material-design-iconic-font.min.css" rel="stylesheet"
          media="screen"/>
    <link type="text/css" href="/static/css/theme-1/libs/rickshaw/rickshaw.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="/static/css/theme-1/libs/morris/morris.core.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="/static/css/animate.css" rel="stylesheet" media="screen"/>
    <link type="text/css" href="/media/css/tablas.css" rel="stylesheet" media="all"/>
    <script type="text/javascript" src="/static/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/spin.js/spin.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/autosize/jquery.autosize.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/moment/moment.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
    <!--<script type="text/javascript" src="/static/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>-->
    <script type="text/javascript" src="/static/js/libs/d3/d3.min.js"></script>
    <script type="text/javascript" src="/static/js/libs/d3/d3.v3.js"></script>
    <script type="text/javascript" src="/static/js/libs/rickshaw/rickshaw.min.js"></script>
    <script type="text/javascript" src="/static/js/core/source/App.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppNavigation.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppOffcanvas.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppCard.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppForm.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppNavSearch.js"></script>
    <script type="text/javascript" src="/static/js/core/source/AppVendor.js"></script>
    <style type="text/css">
        .asteriskField {
            color: red;
        }

        #modx-topbar {
            border-bottom: 2px solid #2c8fd8;
        }

        #bos-main-blocks h2 a, h2.titulo v, .colorcito {
            color: #2c8fd8;
        }

        #menu-left ul li a:hover, #menu-left ul li:hover {
            color: #fff;
            background: #2c8fd8;
            font-weight: bold;
        }

        html #modx-topnav ul.modx-subnav li a:hover {
            background-color: #2c8fd8;
        }

        input#searchsubmit:hover {
            background-color: #2c8fd8;
        }

        #icon-logo {
            background: #2c8fd8 url(/media/images/icon_user.png) scroll left no-repeat;
        }

        .button2 {
            border: 1px solid #2c8fd8;
            background-color: #2c8fd8;
        }

        .button2:hover, .button2:focus {
            background: #2c8fd8;
        }

        .jOrgChart .node {
            background-color: #2c8fd8;
        }

        .widget .title {
            background: none repeat scroll 0 0 #2c8fd8;
        }

        legend {
            border: 1px solid #2c8fd8;
        }

        fieldset {
            border: 2px solid #2c8fd8;
        }

        .proveido {
            color: #2c8fd8;
        }

        span.dias4 {
            background: #2c8fd8 url("/media/images/fondo_transparente.png") no-repeat top left;
        } </style>
</head>

<div>
    <input type="hidden" id="param_id_seguimiento"
           value="<?php echo $id_seguimiento_oficial; ?>"/>
    <input type="hidden" id="param_nur" value="<?php echo $hoja_de_ruta; ?>"/>
</div>

<?php if (sizeof($seguimiento) > 0) { ?>

<div class="card card-underline">
    <div class=" card-head">
        <header><i class="fa fa-tags"></i> Hoja de Ruta : <?php echo HTML::chars($detalle['nur']) ?></header>
        <div class="toolss pull-right">
            <a href="/externo/seguimiento/?hr=<?php echo HTML::chars($detalle['nur']); ?>" target="_blank"
               class="btn btn-sm btn-primary"><i class="md md-print"></i> Imprimir</a>
        </div>
    </div>

    <div class=" card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Referencia: </span>
            </div>

            <div class="col-lg-10 col-md-10">
                <span class="text-medium text-primary-dark"><?php echo HTML::chars($detalle['referencia']); ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Documento Original: </span>
            </div>

            <div class="col-lg-5 col-md-5">
                <span class="text-medium"><a
                            href="/document/detalle/<?php echo $detalle['id_documento']; ?>"><?php echo HTML::chars($detalle['codigo']); ?></a></span>
            </div>

            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Proceso: </span>
            </div>

            <div class="col-lg-3 col-md-3">
                <?php echo HTML::chars($detalle['proceso']); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Destinatario: </span>
            </div>

            <div class="col-lg-5 col-md-5">
                <span class="text-medium"><?php echo HTML::chars($detalle['destinatario']); ?>
                    / <?php echo HTML::chars($detalle['cargo_destinatario']); ?></span>
            </div>

            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Tipo Documento: </span>
            </div>

            <div class="col-lg-3 col-md-3">
                <span><?php echo $detalle['tipo'] ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Remitente: </span>
            </div>

            <div class="col-lg-5 col-md-5">
                <span class="text-medium"><?php echo HTML::chars($detalle['remitente']); ?>
                    / <?php echo HTML::chars($detalle['cargo_remitente']); ?></span>
            </div>

            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Fecha: </span>
            </div>

            <div class="col-lg-3 col-md-3">
                    <span class=" opacity-50">
                        <?php
                        echo Date::fecha($detalle['fecha']) . ' ' . date('H:i:s', strtotime($detalle['fecha']));;
                        ?>
                    </span>
            </div>
        </div>

        <!--
        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Archivos adjuntos: </span>
            </div>

            <div class="col-lg-10 col-md-10">
                    <span class="text-medium">
                        <?php foreach ($archivo as $a): ?>
                            <a href="/download/?file=<?php echo $a->id; ?>" title="Descargar adjunto">
                                <span class=" badge">
                                    <?php echo substr($a->nombre_archivo, 13); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </span>
            </div>
        </div>
        -->
    </div>
</div>

<!--  Seguimiento -->
<div class="card card-underline">
    <div class=" card-head">
        <header><i class="fa fa-bookmark-o"></i> Seguimiento del proceso</header>
        <div class="tools">
            <?php if (isset($agrupado->id)): ?>
                <div id="padre" style="text-align: center;">
                        <span class="text-xl text-primary-dark">
                            <i class="fa fa-folder-o"></i>
                            <a href="/route/trace/?hr=<?php echo $agrupado->padre; ?>">
                                <span class=" text-primary-dark"><?php echo $agrupado->padre; ?></span>
                            </a>
                        </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class=" card-body">
        <?php if (isset($agrupado->id)): ?>
            <div role="alert" class="alert alert-callout alert-warning">
                <strong>Una copia pertenece a la Hoja de Ruta principal: </strong> <a
                        href="/route/trace/?hr=<?php echo $agrupado->padre; ?>"
                        style="color: #275592; font-weight: bold;  "><?php echo $agrupado->padre; ?></a>
            </div>
        <?php endif; ?>

        <ul class="timeline collapse-lg">
            <?php
            $count = 0;
            $hijo = 0;

            foreach ($seguimiento as $s):
                ?>

                <li class="timeline-inverted">
                    <div class="timeline-circ circ-xl style-<?php
                    if ($s->oficial > 0)
                        echo 'primary-dark';
                    else
                        echo $s->color

                    ?>"><span class="fa fa-leaf"></span></div>


                    <div class="timeline-entry">
                        <div class="card style-<?php echo $s->color ?>">
                            <div class="card-body small-padding">
                                <div class="col-lg-5 col-md-5">
                                    <?php
                                    $pasos = "";

                                    if ($s->oficial > 0) {
                                        if ($s->id_estado == 1) {
                                            $pasos = 'fa fa-flag fa-2x';
                                            $scrolltop = 'scroll';
                                        } else
                                            $pasos = 'fa fa-paw';
                                    }
                                    ?>

                                    <span class="text-warning text-xl stick-top-right"><i
                                                class="<?php echo $pasos ?>"></i></span>
                                    <span class="text-medium">
                                            <p>
                                                <?php if (file_exists(DOCROOT . 'static/fotos/' . $s->u1 . '.jpg')): ?>
                                                    <img class="img-circle img-responsive pull-left width-1 "
                                                         width="110" src="/static/fotos/<?php echo $s->u1 ?>.jpg"
                                                         alt=""/>
                                                    <?php
                                                else:
                                                    ?>
                                                    <img class="img-circle img-responsive pull-left width-1 "
                                                         width="110" src="/static/fotos/<?php echo $s->s1 . '.jpg' ?>"
                                                         alt=""/>
                                                <?php endif; ?>

                                                <span class="text-medium"><a
                                                            href="/route/oficina/<?php echo $s->id_de_oficina ?>"><?php echo HTML::chars($s->de_oficina); ?></a>
                                                    <br/> <a href="/user/profile/"
                                                             class="text-primary-dark"><?php echo HTML::chars($s->nombre_emisor); ?></a></span><br>
                                                <span class="opacity-75">
                                                    <?php echo HTML::chars($s->cargo_emisor); ?>
                                                </span>
                                            </p>

                                            <span class="opacity-50 pull-right text-light"><i
                                                        class="fa fa-arrow-up"></i>
                                                <?php
                                                echo Date::fecha_medium($s->fecha_emision)
                                                    . ' - ' . $s->hora_emision;
                                                ?>
                                            </span>
                                        </span>
                                </div>

                                <div class="col-lg-5 col-md-5">
                                    <?php
                                    $pasos = "";

                                    if (($s->oficial > 0) && ($s->id_estado == 6)) {
                                        $pasos = 'fa fa-flag fa-2x';
                                        $scrolltop = 'scroll';
                                    }

                                    $pasos = "";

                                    if (($s->oficial > 0) && ($s->id_estado == 2)) {
                                        $pasos = 'fa fa-flag fa-2x';
                                        $scrolltop = 'scroll';
                                    }

                                    if (($s->oficial > 0) && ($s->id_estado == 4)) {
                                        $pasos = 'fa fa-paw';
                                    }

                                    if (($s->oficial > 0) && ($s->id_estado == 10)) {
                                        $pasos = 'fa fa-flag fa-2x';
                                        $scrolltop = 'scroll';
                                    }
                                    ?>

                                    <span class="text-warning text-xl stick-top-right"><i class="<?php echo $pasos ?>"
                                                                                          id="<?php echo $scrolltop ?>"></i></span>
                                    <span class="text-medium">
                                            <p>
                                                <?php if (file_exists(DOCROOT . 'static/fotos/' . $s->u2 . '.jpg')): ?>
                                                    <img class="img-circle img-responsive pull-left width-1 "
                                                         width="110" src="/static/fotos/<?php echo $s->u2 ?>.jpg"
                                                         alt=""/>
                                                    <?php
                                                else:
                                                    ?>
                                                    <img class="img-circle img-responsive pull-left width-1 "
                                                         width="110" src="/static/fotos/<?php echo $s->s2 . '.jpg' ?>"
                                                         alt=""/>
                                                <?php endif; ?>

                                                <span class="text-medium"><a
                                                            href="/route/oficina/<?php echo $s->id_a_oficina ?>"><?php echo HTML::chars($s->a_oficina); ?></a>

                                                    <br/> <a href="/user/profile/"
                                                             class="text-primary-dark"><?php echo HTML::chars($s->nombre_receptor); ?></a></span><br>

                                                <span class="opacity-75">
                                                    <?php echo HTML::chars($s->cargo_receptor); ?>
                                                </span>
                                            </p>

                                            <span class="opacity-50 pull-right text-light">Enviado:
                                                <?php
                                                //echo Date::fecha_medium($s->fecha_recepcion)
                                                //. ' - ' . $s->hora_recepcion;

                                                if (!is_null($s->fecha_recepcion) && !is_null($s->hora_recepcion)) {
                                                    echo Date::fecha_medium($s->fecha_recepcion) . ' - ' . $s->hora_recepcion;
                                                } else {
                                                    echo $s->fecha_recepcion . ' - ' . $s->hora_recepcion;
                                                }
                                                ?>
                                            </span>
                                        </span>
                                </div>

                                <div class="col-lg-2 col-md-2">
                                        <span class=" badge  style-<?php

                                        if ($s->oficial > 0)
                                            echo 'info';
                                        else
                                            'default-dark';
                                        ?> text-medium">

                                              <?php echo $s->estado; ?>
                                        </span>

                                    

                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal" role="dialog">
                                        <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close"
                                                            data-dismiss="modal">&times;
                                                    </button>

                                                    <h4 class="modal-title">JUSTIFICACIÓN POR EL RETRASO</h4>
                                                </div>

                                                <div class="modal-body">
                                                    <p>
                                                        <?php echo $observacion_justificacion; ?>
                                                    </p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">Cerrar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <span class=" text-xs">
                                            <?php
                                            //if (($s->oficial == 1) && ($s->id_estado == 10)) {
                                            if (($s->id_estado == 10)) {
                                                //obtenemos donde se archivo
                                                $mSeguimiento = new Model_Seguimiento();
                                                $archivado = $mSeguimiento->hrArchivada($s->nur, $s->derivado_a);

                                                if ($archivado) {
                                                    echo '<div class="nomfol">' . $archivado['carpeta'] . '</div>';
                                                    echo '<div class="obs"><b>OBS: </b>' . $archivado['observaciones'] . '</div>';

                                                    $count++;
                                                }
                                            }
                                            ?>
                                        <br/>
                                        Adjunto:<br/>
                                        <?php foreach (json_decode($s->adjuntos) as $k => $a): ?>
                                            <a href="/vista/?doc=<?php echo $a; ?>&id_seg=<?php echo $s->id; ?>"
                                               target="_blank"><?php echo $a; ?></a><br/>
                                            <br/>
                                        <?php endforeach; ?>

                                        <?php
                                        $documentos = ORM::factory('documentos')->where('id_seguimiento', '=', $s->id)->find_all();

                                        foreach ($documentos as $d):
                                            ?>

                                            <a href="/vista/?doc=<?php echo HTML::chars($d->cite_original); ?>&id_seg=<?php echo $s->id; ?>"
                                               target="_blank"><?php echo HTML::chars($d->codigo); ?></a><br/>
                                        <?php endforeach;
                                        ?>
                                        </span>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <span class=" text-medium opacity-75 text-light"><i
                                                    class="md md-message"></i><?php echo HTML::chars($s->proveido) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <?php
                $hijo += $s->hijo;
            endforeach;
            ?></ul>
        <hr/>

        <div style="text-align:center;">
            <?php if ($hijo > 0): ?>
                <div role="alert" class="alert alert-callout alert-warning">
                    <input type="hidden" id="hijo" value="1" name="hijo"/>
                    <strong>Agrupado con: </strong>

                    <?php
                    $hijos = ORM::factory('agrupaciones')->where('padre', '=', $detalle['nur'])->find_all();

                    foreach ($hijos as $h):
                        ?>

                        <a href="/route/trace/?hr=<?php echo $h->hijo; ?>"
                           style="color:#1C4781; font-size: 14px; text-decoration: underline;  "><?php echo $h->hijo; ?></a>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <input type="hidden" id="hijo" value="0" name="hijo"/>
            <?php endif; ?>
        </div>

        <div class="alert alert-info" style="text-align:center;">
            <p><span style="float: left; margin-right: .3em;" class=""></span>
                &larr;<a onclick="javascript:history.back(); return false;"
                         href="javascript:;" style="">
                    Regresar
                    <a/>
            </p>
        </div>

        <?php
        }

        else {
            ?>
            <!-- mostrar mensajes -->
            <div class="alert alert-info">
                <p><span class=""></span>
                    <strong>Mensaje: </strong> Hoja de ruta aun no derivada. &larr;<a
                            onclick="javascript:history.back();return false;" href="#" style=""> Regresar</a></p>
            </div>

            <br/>
        <?php } ?>
        <?php ?>
    </div>

</div>

<a href="/externo/seguimiento/?hr=<?php echo HTML::chars($detalle['nur']); ?>" target="_blank" class="btn btn-sm btn-primary">
    <i class="md md-print"></i> Imprimir
</a>


</body>
</html>