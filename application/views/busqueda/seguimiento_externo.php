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
        <header><i class="fa fa-tags"></i> Hoja de Ruta : <?php echo $detalle['nur'] ?></header>
        <div class="toolss pull-right">
            <a href="/externo/seguimiento/?hr=<?php echo $detalle['nur']; ?>" target="_blank"
               class="btn btn-sm btn-primary"><i class="md md-print"></i> Imprimir</a>
        </div>
    </div>

    <div class=" card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Referencia: </span>
            </div>

            <div class="col-lg-10 col-md-10">
                <span class="text-medium text-primary-dark"><?php echo $detalle['referencia']; ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Documento Original: </span>
            </div>

            <div class="col-lg-5 col-md-5">
                <span class="text-medium"><a
                            href="/document/detalle/<?php echo $detalle['id_documento']; ?>"><?php echo $detalle['codigo']; ?></a></span>
            </div>

            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Proceso: </span>
            </div>

            <div class="col-lg-3 col-md-3">
                <?php echo $detalle['proceso']; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 col-md-2">
                <span class=" opacity-50">Destinatario: </span>
            </div>

            <div class="col-lg-5 col-md-5">
                <span class="text-medium"><?php echo $detalle['destinatario']; ?>
                    / <?php echo $detalle['cargo_destinatario']; ?></span>
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
                <span class="text-medium"><?php echo $detalle['remitente']; ?>
                    / <?php echo $detalle['cargo_remitente']; ?></span>
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
                                                            href="/route/oficina/<?php echo $s->id_de_oficina ?>"><?php echo $s->de_oficina; ?></a>
                                                    <br/> <a href="/user/profile/"
                                                             class="text-primary-dark"><?php echo $s->nombre_emisor; ?></a></span><br>
                                                <span class="opacity-75">
                                                    <?php echo $s->cargo_emisor; ?>
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
                                                            href="/route/oficina/<?php echo $s->id_a_oficina ?>"><?php echo $s->a_oficina; ?></a>

                                                    <br/> <a href="/user/profile/"
                                                             class="text-primary-dark"><?php echo $s->nombre_receptor; ?></a></span><br>

                                                <span class="opacity-75">
                                                    <?php echo $s->cargo_receptor; ?>
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

                                    <!-- === [INICIO] MODAL: LISTADO DE OBSERVACIONES === -->
                                    <?php
                                    $id_seguimiento = $s->id;
                                    $sql_observaciones = "  SELECT 
                                                                *
                                                            FROM
                                                                observacion_seguimiento_externo
                                                            WHERE
                                                                id_seguimiento = '$id_seguimiento';";
                                    $observaciones_result = db::query(Database::SELECT, $sql_observaciones, FALSE)
                                        ->execute()
                                        ->as_array();

                                    if (count($observaciones_result) > 0) {
                                        ?>
                                        <span href="#" style="color:red" class="fa fa-search fa-2x"
                                              data-toggle="modal"
                                              data-target="#modal-observaciones-<?php echo $id_seguimiento; ?>"></span>

                                        <!-- [INICIO] Modal OBSERVACIONES -->
                                        <div class="modal fade modal-observaciones"
                                             id="modal-observaciones-<?php echo $id_seguimiento; ?>" role="dialog">
                                            <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close"
                                                                data-dismiss="modal">&times;
                                                        </button>
                                                        <h4 class="modal-title">LISTADO DE OBSERVACIONES</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" id="id_seguimiento_para_observacion"
                                                               value="0">
                                                        <table id="tabla-observaciones" class="table">
                                                            <thead>
                                                            <tr>
                                                                <th>OBSERVACIÓN</th>
                                                                <th>CORREO</th>
                                                                <th>TELÉFONO</th>
                                                                <th>OPCIONES</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            foreach ($observaciones_result as $key => $fila_observacion) {
                                                                ?>
                                                                <tr id="observacion-<?php echo $fila_observacion['id']; ?>">
                                                                    <td id="observacion-seguimiento-observacion"><?php echo $fila_observacion['observacion']; ?></td>
                                                                    <td id="observacion-seguimiento-correo"><?php echo $fila_observacion['correo']; ?></td>
                                                                    <td id="observacion-seguimiento-telefono"><?php echo $fila_observacion['telefono']; ?></td>
                                                                    <td>
                                                                        <span id="btn-editar-observacion-<?php echo $fila_observacion['id']; ?>"
                                                                              class="text-xl text-primary-dark btn-editar-observacion"
                                                                              title="Editar">
                                                                            <i class="md md-mode-edit"></i>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- [FIN] Modal OBSERVACIONES -->
                                        <?php
                                    }
                                    ?>
                                    <!-- === [FIN] MODAL: LISTADO DE OBSERVACIONES === -->

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

                                            <a href="/vista/?doc=<?php echo $d->cite_original; ?>&id_seg=<?php echo $s->id; ?>"
                                               target="_blank"><?php echo $d->codigo; ?></a><br/>
                                        <?php endforeach;
                                        ?>
                                        </span>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <span class=" text-medium opacity-75 text-light"><i
                                                    class="md md-message"></i><?php echo $s->proveido ?></span>
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

<a href="/externo/seguimiento/?hr=<?php echo $detalle['nur']; ?>" target="_blank" class="btn btn-sm btn-primary">
    <i class="md md-print"></i> Imprimir
</a>

<a href="#" class="btn btn-sm btn-warning btn-modal-reclamar" data-toggle="modal" title="Reclamos/Consultas/Sugerencias"
   data-id-seguimiento="682747"
   data-nur="E/2017-03198"
   data-id-usuario="163">
    <i class="fa fa-share"></i>RECLAMO
</a>

<!-- Modal -->
<div class="modal fade" id="modal-reclamo" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">&times;
                </button>
                <h4 class="modal-title">RECLAMO/CONSULTAS/SUGERENCIAS</h4>
            </div>
            <div class="modal-body">
                <form id="form">
                    <div>
                        <label class="requiredField" for="observacion-persona">
                            Observación
                            <span class="asteriskField">(*)</span>
                        </label>
                        <textarea class="form-control" id="observacion-persona" name="observacion-persona"
                                  required></textarea>
                    </div>
                    <div>
                        <label class="requiredField" for="telefono-persona">
                            Teléfono
                            <span class="asteriskField">(*)</span>
                        </label>
                        <input class="form-control" type="number" id="telefono-persona" required/>
                    </div>
                    <div>
                        <label class="requiredField" for="email-persona">
                            Correo Electrónico
                            <span class="asteriskField">(*)</span>
                        </label>
                        <input class="form-control" type="email" id="email-persona" required/>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">Cerrar
                        </button>

                        <button id="btn-guardar-reclamo" class="btn btn-default">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- UPDATE RECLAMO -->
<div class="modal fade" id="modal-editar-reclamo" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">&times;
                </button>
                <h4 class="modal-title">EDITAR RECLAMO</h4>
            </div>
            <div class="modal-body">
                <form id="form-editar-reclamo">
                    <div id="alerta-success" class="alert alert-success" style="display: none">
                        <p id="texto-alerta-success"></p>
                    </div>
                    <div id="alerta-danger" class="alert alert-danger" style="display: none">
                        <p id="texto-alerta-danger"></p>
                    </div>

                    <input type="hidden" id="id_observacion_a_editar"/>
                    <input type="hidden" id="id_seguimiento_a_editar"/>
                    <div>
                        <label class="requiredField" for="editar-observacion-persona">
                            Observación
                            <span class="asteriskField">(*)</span>
                        </label>
                        <textarea class="form-control" id="editar-observacion-persona" name="editar-observacion-persona"
                                  required></textarea>
                    </div>
                    <div>
                        <label class="requiredField" for="editar-telefono-persona">
                            Teléfono
                            <span class="asteriskField">(*)</span>
                        </label>
                        <input class="form-control" type="number" id="editar-telefono-persona" required/>
                    </div>
                    <div>
                        <label class="requiredField" for="editar-email-persona">
                            Correo Electrónico
                            <span class="asteriskField">(*)</span>
                        </label>
                        <input class="form-control" type="email" id="editar-email-persona" required/>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">Cerrar
                        </button>

                        <button id="btn-guardar-reclamo-editado" class="btn btn-default">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // MODAL
    $(document).ready(function () {

        $("#form").submit(function (event) {
            event.preventDefault();
        });

        $("#form-editar-reclamo").submit(function (event) {
            event.preventDefault();
        });

        $(".btn-modal-reclamar").click(function (event) {

            var id_seguimiento = $("#param_id_seguimiento").val();
            var nur = $("#param_nur").val();

            //console.log('id_seguimiento: ' + id_seguimiento);
            //console.log('nur: ' + nur);

            $('#modal-reclamo').modal('show');
        });
    });

    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    function validarFormularioCrear() {

        var isValidoFormulario = false;

        // Observacion
        var input_observacion = $("#observacion-persona");
        var observacion = input_observacion.val();

        if (input_observacion.length < 0) {
            input_observacion[0].setCustomValidity("Debe ingresar observación");
            isValidoFormulario = false;
        }
        else {
            input_observacion[0].setCustomValidity("");
            isValidoFormulario = true;
        }

        // Email
        var input_email = $("#email-persona");
        var email = input_email.val();

        if (!isEmail(email)) {
            input_email[0].setCustomValidity("Este email es incorrecto");
            isValidoFormulario = false;
        }
        else {
            input_email[0].setCustomValidity("");
            isValidoFormulario = true;
        }
        return isValidoFormulario;
    }

    function validarFormularioEditar() {

        var isValidoFormulario = false;

        // Observacion
        var input_observacion = $("#editar-observacion-persona");
        var observacion = input_observacion.val();

        if (input_observacion.length < 0) {
            input_observacion[0].setCustomValidity("Debe ingresar observación");
            isValidoFormulario = false;
        }
        else {
            input_observacion[0].setCustomValidity("");
            isValidoFormulario = true;
        }

        // Email
        var input_email = $("#editar-email-persona");
        var email = input_email.val();

        if (!isEmail(email)) {
            input_email[0].setCustomValidity("Este email es incorrecto");
            isValidoFormulario = false;
        }
        else {
            input_email[0].setCustomValidity("");
            isValidoFormulario = true;
        }
        return isValidoFormulario;
    }

    // OnClick: GUARDAR RECLAMO
    $("#btn-guardar-reclamo").on("click", function () {

        if (validarFormularioCrear()) {

            var id_seguimiento = $("#param_id_seguimiento").val();
            var nur = $("#param_nur").val();
            var input_observacion = $("#observacion-persona").val();
            var input_telefono = $("#telefono-persona").val();
            var input_email = $("#email-persona").val();

            /*
             console.log('id_seguimiento: ' + id_seguimiento);
             console.log('nur: ' + nur);
             console.log('input_observacion: ' + input_observacion);
             console.log('input_telefono: ' + input_telefono);
             console.log('input_email: ' + input_email);
             */

            $.ajax({
                url: '/externo/guardarReclamo',
                data: {
                    id_seguimiento: id_seguimiento,
                    nur: nur,
                    observacion: input_observacion,
                    telefono: input_telefono,
                    email: input_email
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data));
                    alert("Guardado correctamente");

                    location.reload();
                    //console.log("json: " + json);
                    //console.log("resultado: " + json.resultado);
                },
                error: function (xhr, status) {
                    //alert(status);
                    console.log("status: " + status);
                    console.log("xhr: " + xhr);
                }
            });
        }
        else {
            alert("El formulario contiene errores");
        }
    });

    // OnClick: OBSERVACIONES
    $(".btn-observaciones").on("click", function () {
        var id_btn_ver_observaciones = this.id;
        var id_seguimiento_para_observaciones = this.id.replace('btn-observaciones-', '');

        //console.log('id_btn_ver_observaciones', id_btn_ver_observaciones);
        //console.log('id_seguimiento_para_observaciones', id_seguimiento_para_observaciones);

        $('#id_seguimiento_para_observacion').val(id_seguimiento_para_observaciones);

        console.log('id_seguimiento_para_observacion', $('#id_seguimiento_para_observacion').val());
        //console.log('id_seguimiento_para_observacion', id_seguimiento_para_observacion);

        $('.modal-observaciones').modal('show');
    });

    // OnClick: EDITAR OBSERVACION
    $(".btn-editar-observacion").on("click", function () {
        var id_btn_editar_observacion = this.id;
        var id_observacion = id_btn_editar_observacion.replace("btn-editar-observacion-", "");
        //console.log('id_observacion', id_observacion);

        // id_seguimiento_para_observacion
        var element_oculto_id_seguimiento = $("#id_seguimiento_para_observacion");
        var id_seguimiento_con_observacion = element_oculto_id_seguimiento.val();
        //console.log('id_seguimiento_para_observacion', id_seguimiento_para_observacion);

        // Se obtiene la 'OBSERVACION' que se va a 'EDITAR'
        $.ajax({
            url: '/externo/getObservacionAEditar',
            data: {
                id_observacion: id_observacion
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var json = JSON.parse(JSON.stringify(data));

                var param_id_seguimiento = json.id_seguimiento;
                var param_observacion = json.observacion;
                var param_telefono = json.telefono;
                var param_correo = json.correo;

                $("#id_observacion_a_editar").val(id_observacion);
                $("#id_seguimiento_a_editar").val(param_id_seguimiento);
                $("#editar-observacion-persona").val(param_observacion);
                $("#editar-telefono-persona").val(param_telefono);
                $("#editar-email-persona").val(param_correo);

                /*
                 console.log("json: " + json);
                 console.log("observacion: " + param_observacion);
                 console.log("telefono: " + param_telefono);
                 console.log("correo: " + param_correo);
                 */
            },
            error: function (xhr, status) {
                //alert(status);
                console.log("status: " + status);
                console.log("xhr: " + xhr);
            }
        });

        $('.modal-observaciones').modal('hide');
        $('#modal-editar-reclamo').modal('show');
    });

    // OnClick: EDITAR EN LA BASE DE DATOS EL RECLAMO
    $("#btn-guardar-reclamo-editado").on("click", function () {

        if (validarFormularioEditar()) {

            // Valores que seran actualizados en la Base de Datos
            var id_observacion_actual = $("#id_observacion_a_editar").val();
            var id_seguimiento_actual = $("#id_seguimiento_a_editar").val();
            var nur = $("#param_nur").val();
            var observacion_actual = $("#editar-observacion-persona").val();
            var telefono_actual = $("#editar-telefono-persona").val();
            var email_actual = $("#editar-email-persona").val();

            /*
             console.log('id_observacion_actual: ' + id_observacion_actual);
             console.log('id_seguimiento_actual: ' + id_seguimiento_actual);
             console.log('nur: ' + nur);
             console.log('observacion_actual: ' + observacion_actual);
             console.log('telefono_actual: ' + telefono_actual);
             console.log('email_actual: ' + email_actual);
             */

            // 'UPDATE' EN LA BASE DE DATOS
            $.ajax({
                url: '/externo/editarReclamo',
                data: {
                    id_observacion: id_observacion_actual,
                    id_seguimiento: id_seguimiento_actual,
                    nur: nur,
                    observacion: observacion_actual,
                    telefono: telefono_actual,
                    correo: email_actual
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data));

                    /*
                     console.log('<jsonEditarReclamo>: ' + json)
                     console.log('resultado: ' + json.resultado)
                     */
                    if (json.resultado == 1) {
                        //alert("Editado correctamente");
                        var resultado = json.resultado;
                        var mensaje = json.mensaje;
                        var mensajebd = json.mensajebd;

                        $("#alerta-success").css("display", "block");
                        var texto_alerta = $('#texto-alerta-success');
                        texto_alerta.text(mensaje);

                        $("#alerta-danger").css("display", "none");

                        location.reload();
                    }
                },
                error: function (xhr, status) {
                    //alert(status);
                    console.log("status: " + status);
                    console.log("xhr: " + xhr);

                    $("#alerta-success").css("display", "none");
                    $("#alerta-danger").css("display", "block");
                    var texto_alerta_danger = $('#texto-alerta-danger');
                    texto_alerta_danger.text('No fue editado correctamente');
                }
            });
        }
        else {
            alert("El formulario contiene errores");
        }
    });

</script>
</body>
</html>