<script>
    $(function () {

        $('a.documento').click(function () {

            var codigo = $(this).attr('alt');
            var left = screen.availWidth;
            var top = screen.availHeight;

            left = (left - 600) / 2;
            top = (top - 500) / 2;

            var r = window.showModalDialog("" + codigo, "", "center:0;dialogWidth:700px;dialogHeight:500px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");

            if (r[0] != null) {

            }
        });

        if ($('#hijo').val() > 0) {
            $('#agrupado').show();
        }

        $('html, body').animate({
            scrollTop: $("#scroll").offset().top
        }, 1000);

    });

</script>


<?php if (sizeof($seguimiento) > 0) { ?>

<div class="card card-underline">
    <div class=" card-head">
        <header><i class="fa fa-tags"></i> Hoja de Ruta : <?php echo $detalle['nur'] ?></header>
        <div class="toolss pull-right">
            <a href="/print/seguimiento/?hr=<?php echo $detalle['nur']; ?>" target="_blank"
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
    </div>
</div>

<!--  INFORMACION DEL TIEMPO DEL TRAMITE  -->
<section style="margin-top: 10px;margin-bottom: 10px;">
    <fieldset
            style=" border-radius: 5px;

                    padding: 5px;

                    min-height:50px;

                    border:4px solid #1f497d;

                    background-color:#eeece1;">
        <legend
                style=" margin-left:20px;

                        background-color:#1f497d;

                        padding-left:10px;

                        padding-top:5px;

                        padding-right:15px;

                        padding-bottom:5px;

                        color:white;

                        border-radius:15px;

                        border:4px solid #eeece1;

                        width: auto;

                        font-size:12px;">
            <b style="color: white"> INFORMACIÓN DEL TIEMPO DEL TRÁMITE </b>
        </legend>

        <div style="text-align: center;">
                <span style="margin-right: 25px;">
                    <span style="margin-right: 5px; font-weight: bold;font-size: 15px;">TRÁMITE:</span>
                    <span
                            style="font-family: Tahoma; font-size: 16px;"><?php echo $detalleTiempoDelTramite['tipo_tramite']; ?></span>
                    </span>

            <span style="margin-right: 25px;">
                    <span style="margin-right: 5px; font-weight: bold;font-size: 15px;">FECHA DE PLAZO:</span>
                    <span
                            style="font-family: Tahoma; font-size: 16px;"><?php echo $detalleTiempoDelTramite['fecha_plazo_urgente']; ?></span>
                </span>

            <span style="margin-right: 25px;">
                    <span
                            style="margin-right: 5px; font-weight: bold;font-size: 15px;">Tiempo Transcurrido (Días):</span>
                    <span
                            style="font-family: Tahoma; font-size: 16px;"><?php echo $detalleTiempoDelTramite['tiempo_transcurrido']; ?></span>
                </span>
        </div>
    </fieldset>
</section>

<!--  Seguimiento -->
<div class="card card-underline">
    <div class=" card-head">
        <header><i class="fa fa-bookmark-o"></i> Seguimiento del proceso</header>
        <div class="tools">
            <?php if (isset($agrupado->id)): ?>
                <div id="padre" style="text-align: center;">
                        <span class="text-xl text-primary-dark">
                            <i class="fa fa-folder-o"></i>
                            <a href="/route/trace/?hr=<?php echo $agrupado->padre; ?>"><span
                                        class=" text-primary-dark"><?php echo $agrupado->padre; ?></span></a>
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
                                             id="modal-observaciones-<?php echo $id_seguimiento; ?>" role="dialog"
                                             data-backdrop="false">
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

                                    <!-- === OBSERVACION DE JUSTIFICACION === -->
                                    <?php
                                    $id_seguimiento = $s->id;
                                    $nur = $s->nur;
                                    $id_estado = $s->id_estado;

                                    /*
                                    $query = "  SELECT
                                                    *
                                                FROM
                                                    observacion_seguimiento
                                                WHERE
                                                    (id_seguimiento = '$id_seguimiento')
                                                        AND (nur = '$nur')
                                                        AND (id_estado = '2')
                                                ORDER BY fecha_observacion DESC;";
                                    */

                                    $query = "  SELECT 
                                                    *, u.nombre, u.cargo
                                                FROM
                                                    observacion_seguimiento os
                                                        INNER JOIN
                                                    users u ON os.id_usuario = u.id
                                                WHERE
                                                    (os.id_seguimiento = '$id_seguimiento')
                                                        AND (os.nur = '$nur')
                                                        AND (os.id_estado = '2')
                                                ORDER BY os.fecha_observacion DESC;";

                                    $resultSet = db::query(Database::SELECT, $query, FALSE)
                                        ->execute()
                                        ->as_array();

                                    if (!empty($resultSet)) {

                                        // $observacion_justificacion = $resultSet[0]['observacion'];

                                        $observacion_justificacion = '';

                                        foreach ($resultSet as $id => $row) {

                                            $fecha_observacion = new DateTime($row['fecha_observacion']);

                                            //$observacion_justificacion .= '[' . $fecha_observacion->format('d/m/Y  H:i') . ']: ' . $row['observacion'] . '<br>';

                                            $tab = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
                                            $observacion_justificacion .= '[' . $fecha_observacion->format('d/m/Y  H:i:s') . ']: '
                                                . $row['observacion'] . '<br>'
                                                . $tab
                                                . '- ' . $row['nombre'] . '<br>'
                                                . $tab
                                                . '- ' . $row['cargo'] . '<br>';
                                        }

                                        // solo creamos el boton de 'exclamacion' si tuviera una observacion
                                        // if (intval($id_estado) === 2 && strlen($observacion_justificacion) > 0) {
                                        if (strlen($observacion_justificacion) > 0) {
                                            ?>

                                            <a href="#" style="color:red" class="fa fa-exclamation-triangle fa-2x"
                                               data-toggle="modal" data-target="#myModal-<?php echo $id_seguimiento ?>"></a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="myModal-<?php echo $id_seguimiento ?>" role="dialog">
                                                <div class="modal-dialog">

                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>

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
                                            <?php
                                        }
                                    }
                                    ?>
                                    <!-- === FIN OBSERVACION DE RECHAZO === -->
                                    <span class=" text-xs">
                                            <?php
                                            //if (($s->oficial == 1) && ($s->id_estado == 10)) {
                                            if (($s->id_estado == 10)) {
                                                //obtenemos donde se archivo
                                                $mSeguimiento = new Model_Seguimiento();
                                                $archivado = $mSeguimiento->hrArchivada($s->nur, $s->derivado_a);

                                                if ($archivado) {
                                                    if ($user->id_oficina == $s->id_a_oficina) {
                                                        echo '<a href="/bandeja/folder/' . $archivado['id'] . '"><div class="archivado"></div></a>';
                                                        echo '<div class="nomfol"><i class="fa fa-text-o"></i>' . $archivado['carpeta'] . '</div>';
                                                        echo '<div class="obs"><b>OBS: </b>' . $archivado['observaciones'] . '</div>';
                                                    } else {
                                                        echo '<div class="nomfol">' . $archivado['carpeta'] . '</div>';
                                                        echo '<div class="obs"><b>OBS: </b>' . $archivado['observaciones'] . '</div>';
                                                    }

                                                    $count++;
                                                }
                                            }
                                            ?>
                                        <br/>
                                            Adjunto:
                                            <br/>

                                        <?php
                                        $archivos_paso = isset($archivos_por_seguimiento[$s->id]) ? $archivos_por_seguimiento[$s->id] : array();
                                        if (empty($archivos_paso)):
                                            ?>
                                            <span class="opacity-50">Sin adjuntos</span><br/>
                                        <?php else: ?>
                                            <?php foreach ($archivos_paso as $af): ?>
                                                <a href="/download/?file=<?php echo $af->id; ?>" title="Descargar adjunto">
                                                    <span class="badge"><?php echo substr($af->nombre_archivo, 13); ?></span>
                                                </a><br/>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

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
                &larr;<a onclick="javascript:history.back();

                            return false;" href="javascript:;" style=""> Regresar<a/></p>
        </div>

        <?php
        }

        else {
            ?>
            <!-- mostrar mensajes -->
            <div class="alert alert-info">
                <p><span class=""></span>
                    <strong>Mensaje: </strong> Hoja de ruta aun no derivada. &larr;<a onclick="javascript:history.back();

                            return false;" href="#" style=""> Regresar</a></p>
            </div>

            <br/>
        <?php } ?>
        <?php ?>
    </div>

</div>

<a href="/print/seguimiento/?hr=<?php echo $detalle['nur']; ?>" target="_blank" class="btn btn-sm btn-primary"><i
            class="md md-print"></i> Imprimir</a>


