<script type="text/javascript">

    $(function () {
        $("div#entrada .bandeja").each(function () {
            var t = $(this).text().toLowerCase(); //all row text
            $("<table class='indexColumn'></table>")
                .hide().text(t).appendTo(this);
        });//each tr
        $("#FilterTextBox").keyup(function () {
            var s = $(this).val().toLowerCase().split(" ");
            //show all rows.
            $("div#entrada .bandeja:hidden").show();
            $.each(s, function () {
                $("div#entrada .bandeja .indexColumn:not(:contains('"
                    + this + "'))").parent().hide();

                // sobrepone el modal sobre el <body>
                //$("#myModal").appendTo("body");
            });//each
        });//key up.

//archivo y pendientes
        $('.sel').bind('click', function () {
            var count = $('input:checked').length;
            if (count < 1) {
                $('#opciones').addClass('oculto');
                $('#sup-group,#sup-archive').removeClass('badge');
                $('#sup-group,#sup-archive').removeClass('style-default');
                $('#group,#archive').removeClass('btn-primary-dark').addClass('btn-default ').removeClass('animated').removeClass('bounceIn');
                $('#group,#archive').attr('title', '');

                $('#sup-group,#sup-archive').text('');
            }
            else {
                $('#sup-group,#sup-archive').addClass('badge');
                $('#sup-group,#sup-archive').addClass('style-default');
                $('#sup-group,#sup-archive').text(count);
                var nurs = '';
                $('input:checked').each(function () {
                    if ($(this).is(':checked')) {
                        nurs = nurs + "\n " + $(this).attr('rel');
                    }
                });
                $('#group,#archive').removeClass('btn-defaul').addClass('btn-primary-dark').addClass('animated').addClass('bounceIn');
                $('#group').attr('title', 'Agrupar: \n' + nurs)
                $('#archive').attr('title', 'Archivar: \n' + nurs)
                //$('#seleciones').html(nurs);
                //$('#opciones').removeClass('oculto');
            }
        });

//modal

        //esto se se refresca la pagina
        var count = $('input:checked').length;
        if (count == 0) {
            $('#opciones').addClass('oculto');
        }
        else {
            var nurs = '';
            $('input:checked').each(function () {
                if ($(this).is(':checked')) {
                    nurs = nurs + "" + $(this).attr('rel');
                }
            });
            $('#seleciones').html(nurs);
            $('#opciones').removeClass('oculto');

        }
        $('a#archive').click(function () {
            $('#accion').val('0');
            $('form#doa').submit();
        });
        $('a#group').click(function () {
            $('#accion').val('1');
            var count = $('input:checked').length;
            if (count > 1)
                $('form#doa').submit();
            else {
                alert('Para poder agrupar debe de seleccionar por lo menos 2 hojas de ruta');
                return false;
            }
        });

        $('#tipoCorr').change(function () {
            var tipo = $(this).val();
            if (tipo != '') {
                $('.bandeja').hide();
                $('.' + tipo).fadeIn();
            }
            else
                $('.bandeja').show();
        });
        var copia = $('.tipo0').size();
        var oficial = $('.tipo1').size();
//alert(copia+':'+oficial);
        $('a.link2').click(function () {
            $this = $(this);
            var criterio = $this.attr('id');
            if ($this.is('.asc')) {
                $this.removeClass('asc');
                $this.addClass('desc');
                var sortdir = -1;
            }
            else {

                $this.addClass('asc');
                $this.removeClass('desc');
                var sortdir = 1;
            }
            $(this).siblings().removeClass('asc');
            $(this).siblings().removeClass('desc');
            //sort
            var nurs = $('div.bandeja').get();
            nurs.sort(function (a, b) {
                var val1 = $(a).attr('' + criterio).toUpperCase();
                var val2 = $(b).attr('' + criterio).toUpperCase();
                return (val1 < val2) ? -sortdir : (val1 > val2) ? sortdir : 0;
            });
            $.each(nurs, function (index, row) {
                $('form#doa').append(row);
            });
            return false;
        });
        $('#FilterTextBox').focus();
    });

    $(document).ready(function () {

        $('#alerta-avisos').fadeOut('slow', function () {
            // $('#alerta-avisos').fadeIn('slow');
        });
    });

</script>
<style>
    sup.badge {
        top: 0.1cm !important;
    }
</style>

<?php if (sizeof($entrada) > 0) { ?>

    <div style=";position: fixed; z-index: 10; margin-top: -24px; padding: 2px; background: #fff; width: 100%;"
         class="col-lg-11 col-md-12">

        <div style="display: none; margin: 0 auto; padding: 0;" class="alert alert-danger" id="alerta-avisos">
            <?php
            $mensaje = 'Sr(a): ' . $user->nombre . ', recordarle que a partir de la fecha solo se pueden realizar como máximo 4 derivaciones. Tomar en cuenta esta restricción del Sistema.';
            echo $mensaje; ?>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4">
                <h3><i class="fa fa-clock-o"></i> Correspondencia Pendiente</h3>
            </div>

            <div class="col-lg-2 col-md-2"><i class="fa fa-filter"></i> Filtrar:
                <input type="text" id="FilterTextBox" name="FilterTextBox" class="form-control" size="15"/>
            </div>

            <div class="col-lg-6">
                <div class="btn-group ">
                    <button class="btn ink-reaction btn-sm btn-default" type="button"><i
                                class="fa fa-sort-alpha-asc"></i> Ordenar por
                    </button>
                    <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-default dropdown-toggle"
                            type="button" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li><a href="#" class="link2" id="hojaruta">Hoja Ruta</a></li>
                        <li><a href="#" class="link2" id="fecha">Fecha</a></li>
                        <li><a href="#" class="link2" id="oficina">Oficina</a></li>
                        <li><a href="#" class="link2" id="proceso">Proceso</a></li>
                    </ul>
                </div>
                <a href="javascript:;" class="btn btn-sm btn-default" data-toggle="title" id="group"
                   title="Permite agrupar 2 o + tramites o precesos en uno solo."><i class="fa fa-link"></i> AGRUPAR
                    <sup class="badge " id="sup-group"></sup>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-default" id="archive"
                   title="Permite arhivar 1 o + tramites o procesos."><i class="fa fa-archive"></i> ARCHIVAR
                    <sup class="badge " id="sup-group"></sup>
                </a>
                <!--<a href="#" id="print_hr" ><img src="/media/images/excel.png" align="absmiddle"  /><b> Generar Excel</b></a>         -->

                <a href="/print/pendientes/?id=<?php echo time(); ?>" target="_blank"
                   class="btn btn-sm btn-default-bright "><i class="fa fa-print"></i> Imprimir</a>

            </div>
        </div>
    </div>

    <div id="entrada" class="card" style="margin-top: 42px; position: relative;">
        <form action="/bandeja/doa" method="post" id="doa">
            <?php
            $nro_item = 0;
            foreach ($entrada as $s):
                ?>
                <div class="bandeja tipo<?php echo $s->oficial; ?>" style="display:inline-block; "
                     oficina="<?php echo $s->de_oficina ?>" proceso="<?php echo $s->referencia ?>"
                     fecha="<?php echo $s->fecha2; ?>" hojaruta="<?php echo $s->nur; ?>">
                    <table class="oficial<?php echo $s->oficial; ?> ">
                        <tr>
                            <td width="118" rowspan="2" align="center" valign="top"
                                class="nur<?php echo $s->oficial; ?><?php echo $s->prioridad; ?>">
                                <div class="oficial<?php echo $s->oficial; ?> ">
                                    <div class="checkbox checkbox-styled ">
                                        <label>&nbsp;&nbsp;
                                            <input type="checkbox" name="id_seg[]" value="<?php echo $s->id; ?>"
                                                   rel="<?php echo $s->nur; ?>" class="sel">

                                        </label>
                                    </div>

                                </div>
                            </td>
                            <td valign="top" colspan="3">
                                <h4 class="text-primary-dark"><a
                                            href="/document/detalle/<?php echo $s->id_doc ?>"><?php echo $s->referencia; ?></a>
                                </h4>
                            </td>

                        </tr>
                        <tr>
                            <td width="50%" colspan="2" valign="top">
                                <div>
                                    <span class=" text-light"><b><?php echo $s->nombre_emisor; ?> </b> -
                                        <?php echo $s->cargo_emisor; ?></span><br/>
                                    <span class="oficina opacity-75"><?php echo $s->de_oficina; ?></span>
                                </div>
                            </td>
                            <td class="derecha" valign="top">
                                <span class="proveido text-accent-light"><i
                                            class=" fa fa-comments-o"></i> <?php echo $s->proveido; ?></span>
                                <br/><span class=" text-accent-dark"><?php echo $s->accion; ?></span><br/>
                                <?php if ($s->hijo == 1): ?> <a href="/bandeja/agrupado/?hr=<?php echo $s->nur; ?>"
                                                                class="link agrupado">Agrupado</a><?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="88">
                                <a href="/route/trace/?hr=<?php echo $s->nur; ?>"
                                   class="nur<?php echo $s->oficial; ?>"><?php echo $s->nur ?></a>
                            </td>
                            <td colspan="2">
                                <span class=" opacity-75"><?php echo Date::fecha($s->fecha2); ?></span>
                            </td>
                            <td>

                                <!-- === BADGE DEL [ NUMERO DE DERIVACIONES ] y [NUMERO DE JUSTIFICACIONES] === -->
                                <?php
                                $id_seguimiento = $s->id;
                                $nur = $s->nur;
                                $id_usuario = $user->id;
                                // $estado = $s->estado;

                                // NUMERO DE DERIVACIONES
                                // Sin Procedimientos Almacenados
                                $query_nro_derivaciones = " SELECT 
                                                                COUNT(1) AS nro_derivaciones
                                                            FROM
                                                                seguimiento s USE INDEX (INDEX_NUR)
                                                                    INNER JOIN
                                                                users u ON (s.derivado_por = u.id)
                                                            WHERE
                                                                (s.nur = '$nur')
                                                                    AND (s.oficial != 0)
                                                                    AND (u.nivel != 4);";

                                $resultSet_nro_derivaciones = db::query(Database::SELECT, $query_nro_derivaciones, FALSE)
                                    ->execute()
                                    ->as_array();

                                $nro_derivaciones = $resultSet_nro_derivaciones[0]['nro_derivaciones'];

                                /*
                                // SOLUCION (con procedimientos almacenados)
                                $query_nro_derivaciones = "SELECT nro_derivaciones_por_hoja_de_ruta(:hr) AS nro_derivaciones";
                                $resultSet_nro_derivaciones = DB::query(Database::SELECT, $query_nro_derivaciones)->bind(':hr', $nur)->execute();

                                $nro_derivaciones = $resultSet_nro_derivaciones[0]['nro_derivaciones'];
                                //
                                */

                                $titulo_boton_derivar = 'El proceso tiene: ' . $nro_derivaciones . ' derivacion(es)';

                                // NUMERO DE JUSTIFICACIONES
                                $query_nro_justificaciones = "  SELECT
                                                                    COUNT(1) AS nro_justificaciones
                                                                FROM
                                                                    observacion_seguimiento USE INDEX (IDX_NUR)
                                                                WHERE
                                                                    (nur = '$nur') AND (id_estado = 2);";

                                $resultSet_nro_justificaciones = db::query(Database::SELECT, $query_nro_justificaciones, FALSE)
                                    ->execute()
                                    ->as_array();

                                $nro_justificaciones = $resultSet_nro_justificaciones[0]['nro_justificaciones'];

                                $titulo_boton_justificar = 'El proceso tiene: ' . $nro_justificaciones . ' justificacion(es)';
                                ?>

                                <span class="opciones">

                                    <?php if (intval($nro_derivaciones) >= 0) { ?>

                                        <a href="/route/deriv/?hr=<?php echo $s->nur; ?>"
                                           class=" btn btn-sm btn-primary-dark"
                                           title="<?php echo $titulo_boton_derivar; ?>"
                                           id_nur="<?php echo $s->nur; ?>" id_seg="<?php echo $s->id; ?>"
                                           nuri="<?php echo $s->nur ?>"><i class="fa fa-share"></i> Derivar

                                            <sup class="badge style-default"
                                                 id="badge-numero-derivaciones"><?php echo $nro_derivaciones; ?></sup>
                                        </a>
                                    <?php } else { ?>

                                        <a href="/route/deriv/?hr=<?php echo $s->nur; ?>"
                                           class=" btn btn-sm btn-primary-dark"
                                           title="<?php echo $titulo_boton_derivar; ?>"
                                           id_nur="<?php echo $s->nur; ?>" id_seg="<?php echo $s->id; ?>"
                                           nuri="<?php echo $s->nur ?>" disabled="disabled"><i class="fa fa-share"></i>
                                            Derivar

                                            <sup style="background-color: red; color: yellow;"
                                                 class="badge style-default"
                                                 id="badge-numero-derivaciones"><?php echo $nro_derivaciones; ?></sup>
                                        </a>
                                    <?php } ?>

                                    <!-- === OBSERVACION DE JUSTIFICACION === -->
                                    <?php
                                    $query = "  SELECT
                                                    *
                                                FROM
                                                    observacion_seguimiento USE INDEX (IDX_ID_SEGUIMIENTO , IDX_NUR , IDX_ID_ESTADO)
                                                WHERE
                                                    (id_seguimiento = '$id_seguimiento')
                                                        AND (nur = '$nur')
                                                        AND (id_usuario = '$id_usuario')
                                                        AND (id_estado = '2');";

                                    $resultSet = db::query(Database::SELECT, $query, FALSE)
                                        ->execute()
                                        ->as_array();

                                    // Ocultamos el boton si ya tiene una justificacion
                                    // if (empty($resultSet)) {
                                    ?>

                                    <!-- Boton JUSTIFICAR -->
                                         <a href="#"
                                            class="btn btn-sm btn-primary-dark btn-modal-justificacion"
                                            data-toggle="modal"
                                            title="<?php echo $titulo_boton_justificar; ?>"
                                            data-id-seguimiento="<?php echo $id_seguimiento; ?>"
                                            data-nur="<?php echo $nur; ?>"
                                            data-id-usuario="<?php echo $id_usuario; ?>"
                                            data-target="#myModal-<?php echo $id_seguimiento; ?>">
                                                <i class="fa fa-share"></i>JUSTIFICAR

                                            <sup class="badge style-default"
                                                 id="badge-numero-justificaciones"><?php echo $nro_justificaciones; ?></sup>
                                        </a>

                                    <!-- Modal -->
                                            <div class="modal fade"
                                                 id="myModal-<?php echo $id_seguimiento; ?>" role="dialog">
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
                                                                Ingrese una justificación, por la cual su persona tiene un
                                                                retraso en la derivación.
                                                            </p>
                                                            <textarea style="height: 100px; width: 100%;" type="text"
                                                                      class="form-control texto-justificacion"
                                                                      id="texto-justificacion-<?php echo $id_seguimiento; ?>"
                                                                      name="texto-justificacion"
                                                                      size="15"
                                                                      placeholder="Ingrese su justificación..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button id="btn-insertar-justificacion" type="button"
                                                                    class="btn btn-default"
                                                                    onclick="guardar_justificacion()">GUARDAR
                                                            </button>
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Cerrar
                                                            </button>

                                                            <input type="hidden" class="id_seguimiento"
                                                                   value="<?php echo $id_seguimiento; ?>"/>

                                                            <input type="hidden" class="nur"
                                                                   value="<?php echo $nur; ?>"/>
                                                            <input type="hidden" class="id_user"
                                                                   value="<?php echo $id_usuario; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <!-- [FIN] MODAL 2 -->

                                    <?php
                                    // }
                                    ?>
                                    <!-- === FIN OBSERVACION DE JUSTIFICACION === -->

                                    <div class="btn-group">
                                        <button class="btn ink-reaction btn-sm btn-default" type="button">Responder
                                            con
                                        </button>
                                        <button data-toggle="dropdown"
                                                class="btn ink-reaction btn-sm btn-default dropdown-toggle"
                                                type="button" aria-expanded="false"><i class="fa fa-caret-down"></i>
                                        </button>
                                        <ul role="menu" class="dropdown-menu dropdown-menu-right">
                                            <?php foreach ($tipos as $t): ?>

                                                <li>
                                                    <a href="/route/responder/?id_seg=<?php echo $s->id; ?>&d=<?php echo $t['id']; ?>&n=<?php echo $s->nur; ?>"><?php echo $t['tipo'] ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <!-- === MOSTRAR EL TEXTO DE LA JUSTIFICACION DE RECHAZO === -->
                                    <?php
                                    /*
                                    $query_rechazo = "  SELECT
                                                            *
                                                        FROM
                                                            observacion_seguimiento
                                                        WHERE
                                                            (nur = '$nur')
                                                                AND (id_estado = '1')
                                                        ORDER BY fecha_observacion DESC";
                                    */

                                    $query_rechazo = "  SELECT 
                                                            os.*, u.nombre, u.cargo
                                                        FROM
                                                            observacion_seguimiento os USE INDEX (IDX_NUR)
                                                                INNER JOIN
                                                            users u USE KEY (PRIMARY) ON os.id_usuario = u.id
                                                        WHERE
                                                            (os.nur = '$nur')
                                                                AND (os.id_estado = '1')
                                                        ORDER BY fecha_observacion DESC;";

                                    $resultSet_rechazo = db::query(Database::SELECT, $query_rechazo, FALSE)
                                        ->execute()
                                        ->as_array();

                                    // Ocultamos el boton si ya tiene justificacion
                                    if (!empty($resultSet_rechazo)) {

                                        // $observacion_rechazo = $resultSet_rechazo[0]['observacion'];
                                        $observacion_rechazo = '';

                                        foreach ($resultSet_rechazo as $id => $row) {
                                            $id_modal_observacion = "modal-texto-rechazo" . '-';
                                            $id_modal_observacion .= $row['id'];

                                            $fecha_observacion = new DateTime($row['fecha_observacion']);

                                            //$observacion_rechazo .= '[' . $fecha_observacion->format('d/m/Y  H:i') . ']: ' . $row['observacion'] . '<br>';

                                            $tab = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
                                            $observacion_rechazo .= '[' . $fecha_observacion->format('d/m/Y  H:i:s') . ']: '
                                                . $row['observacion'] . '<br>'
                                                . $tab
                                                . '- ' . $row['nombre'] . '<br>'
                                                . $tab
                                                . '- ' . $row['cargo'] . '<br>';
                                        }

                                        // solo creamos el boton de 'exclamacion' si tuviera una observacion
                                        if (strlen($observacion_rechazo) > 0) {
                                            ?>

                                            <a title="Ver Justificación de Rechazo" href="#"
                                               style="color:red"
                                               class="fa fa-exclamation-triangle fa-2x"
                                               data-toggle="modal"
                                               data-target="<?php echo '#' . $id_modal_observacion; ?>"></a>

                                            <!-- Modal -->






                                            <div class="modal fade" id="<?php echo $id_modal_observacion; ?>"
                                                 role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">JUSTIFICACIÓN DEL RECHAZO</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                <?php echo $observacion_rechazo; ?>
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
                                    <!-- === FIN MOSTRAR TEXTO DE LA JUSTIFICACION DE RECHAZO === -->

                                </span>
                            </td>
                        </tr>
                        <?php
                        //$dias = floor((($segundos / 3600) / 24));
                        switch ($s->dias) {
                            case 0:
                                $color = "style-success";
                                break;
                            case 1:
                                $color = "style-success";
                                break;
                            case 2:
                                $color = "style-warning";
                                break;
                            default:
                                $color = "style-danger";
                                break;
                        }
                        ?>
                        <sup class="badge pull-2 <?php echo $color; ?> pull-right"><?php echo $s->dias; ?>
                            dias</sup>
                    </table>

                    <?php // $segundos = (time() - strtotime($s->fecha2));
                    ?>
                </div>
                <?php
                $nro_item++;
            endforeach; ?>
            <?php echo Form::hidden('accion', '', array('id' => 'accion')); ?>
        </form>
    </div>

<?php } else { ?>
    <div class="alert alert-info">
        <p>
            <i class="fa fa-info-circle"></i> Lista Vacia!, Usted no tiene correspondencia pendiente.</p>
    </div>
<?php } ?>

<script>

    $(document).ready(function () {
        $(".btn-modal-justificacion").click(function (event) {

            console.log('id-seguimiento:', $(this).data('id-seguimiento'));
            console.log('nur:', $(this).data('nur'));
            console.log('id-usuario:', $(this).data('id-usuario'));

            var id_seguimiento = $(this).data('id-seguimiento');
            var nur = $(this).data('nur');
            var id_usuario = $(this).data('id-usuario');

            $(".id_seguimiento").val(id_seguimiento);
            $(".nur").val(nur);
            $(".id_user").val(id_usuario);

            //$('#myModal').modal('show');
            // sobrepone el modal sobre el <body>
            $('#myModal').appendTo("body").modal('show');
        });
    });

    // === OBSERVACION DE JUSTIFICACION ===
    function guardar_justificacion() {
        var id_seguimiento = $('.id_seguimiento').val();
        var nur = $('.nur').val();
        var observacion = $('#texto-justificacion-' + id_seguimiento).val();
        var id_user = $('.id_user').val();

        console.log('id_seguimiento:', id_seguimiento);
        console.log('nur:', nur);
        console.log('observacion:', observacion);
        console.log('id_user:', id_user);

        if (observacion.length > 0) {

            $.ajax({
                type: "POST",
                data: {
                    id_seguimiento: id_seguimiento,
                    nur: nur,
                    observacion: observacion,
                    id_usuario: id_user
                },
                url: "/ajax/guardar_justificacion",
                // dataType: "html",
                success: function (data) {
                    location.reload(true);
                }
            });
        }
        else {
            alert("(*) Usted debe llenar una justificación de retraso");
        }

        $('.texto-justificacion').val('');
        $('#myModal').modal('toggle');
    }
    // === FIN OBSERVACION DE JUSTIFICACION ===
</script>

