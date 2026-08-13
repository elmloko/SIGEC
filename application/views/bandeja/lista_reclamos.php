<style>
    sup.badge {
        top: 0.1cm !important;
    }
</style>

<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
?>

<?php if (sizeof($result_observaciones_externas) > 0) { ?>

    <div style=";position: fixed; z-index: 10; margin-top: -24px; padding: 2px; background: #fff; width: 100%;"
         class="col-lg-11 col-md-12">

        <div class="row">
            <div class="col-lg-4 col-md-4">
                <h3><i class="fa fa-clock-o"></i> Correspondencia con Reclamos</h3>
            </div>

            <div class="col-lg-2 col-md-2"><i class="fa fa-filter"></i> Filtrar:
                <input type="text" id="FilterTextBox" name="FilterTextBox" class="form-control" size="15"/>
            </div>
        </div>

        <!--        <div id="form_errors" class="alert alert-warning fade in col-lg-4 col-md-4" style="display:block">-->
    </div>

    <div id="entrada" class="card" style="margin-top: 42px; position: relative;">
        <form action="/bandeja/reclamos" method="post">
            <?php
            $nro_item = 0;
            foreach ($result_observaciones_externas as $s):
                ?>

                <div class="bandeja tipo<?php echo $s->oficial; ?>" style="display:inline-block;">

                    <table class="oficial<?php echo $s->oficial; ?> ">
                        <tr>
                            <td width="118" rowspan="2" align="center" valign="top" class="nur10">
                                <div class="oficial1 ">
                                </div>
                            </td>
                            <td valign="top" colspan="3">
                                <h4 class="text-primary-dark">
                                    <?php echo $s->referencia; ?>
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" colspan="2" valign="top">
                            </td>
                            <td class="derecha" valign="top">
                                <span class="proveido text-accent-light">
                                    <i class=" fa fa-comments-o"></i> <?php echo $s->observacion; ?></span>
                                <br/>
                            </td>
                        </tr>
                        <tr>
                            <td width="88">
                                <a href="/externo/seguimientoExterno/?hr=<?php echo $s->nur; ?>"
                                   class="nur<?php echo $s->oficial; ?>">
                                    <?php echo $s->nur ?>
                                </a>
                            </td>
                            <td colspan="2">
                                <span class=" opacity-75"><?php echo Date::fecha($s->fecha_observacion); ?></span>
                            </td>
                            <td>
                                <!-- === [INICIO] RESPUESTA AL RECLAMO === -->
                                <?php
                                $id_observacion = $s->id;
                                $nur = $s->nur;
                                $id_usuario = $user->id;
                                $respuesta = $s->respuesta;
                                ?>
                                <!-- Boton RESPONDER RECLAMO -->
                                <?php
                                if ($respuesta) {
                                    ?>
                                    <!-- Se desabilita el boton cuando ya tenga respuestas el reclamo -->
                                    <a href="#"
                                       id="btn-responder-<?php echo $id_observacion; ?>"
                                       class="btn btn-sm btn-primary-dark btn-modal-responder"
                                       data-toggle="modal"
                                       title="Responder Reclamo"
                                       data-id-observacion="<?php echo $id_observacion; ?>"
                                       data-nur="<?php echo $nur; ?>"
                                       data-id-usuario="<?php echo $id_usuario; ?>"
                                       data-target="#myModal-<?php echo $id_observacion; ?>"
                                       disabled="disabled">
                                        <i class="fa fa-share"></i>RESPONDER
                                    </a>
                                <?php } else { ?>

                                    <a href="#"
                                       id="btn-responder-<?php echo $id_observacion; ?>"
                                       class="btn btn-sm btn-primary-dark btn-modal-responder"
                                       data-toggle="modal"
                                       title="Responder Reclamo"
                                       data-id-observacion="<?php echo $id_observacion; ?>"
                                       data-nur="<?php echo $nur; ?>"
                                       data-id-usuario="<?php echo $id_usuario; ?>"
                                       data-target="#myModal-<?php echo $id_observacion; ?>">
                                        <i class="fa fa-share"></i>RESPONDER
                                    </a>
                                <?php } ?>

                                <!-- Modal -->
                                <div class="modal fade"
                                     id="myModal-<?php echo $id_observacion; ?>" role="dialog">

                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close"
                                                        data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">RESPONDER RECLAMO</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    Ingrese una respuesta respecto al reclamo del ciudadano.
                                                </p>
                                                <textarea style="height: 100px; width: 100%;" type="text"
                                                          class="form-control texto-respuesta"
                                                          id="texto-respuesta-<?php echo $id_observacion; ?>"
                                                          name="texto-respuesta"
                                                          size="15"
                                                          placeholder="Ingrese su respuesta..."></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button id="btn-insertar-respuesta" type="button"
                                                        class="btn btn-default"
                                                        onclick="guardar_respuesta()">GUARDAR
                                                </button>
                                                <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Cerrar
                                                </button>

                                                <input type="hidden" class="id_observacion"
                                                       value="<?php echo $id_observacion; ?>"/>
                                                <input type="hidden" class="nur"
                                                       value="<?php echo $nur; ?>"/>
                                                <input type="hidden" class="id_user"
                                                       value="<?php echo $id_usuario; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [FIN] MODAL 2 -->
                                <!-- === [FIN] RESPUESTA AL RECLAMO === -->
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                $nro_item++;
            endforeach; ?>
        </form>
    </div>

<?php } else { ?>
    <div class="alert alert-info">
        <p>
            <i class="fa fa-info-circle"></i> Lista Vacia!, Usted no tiene correspondencia con reclamos.</p>
    </div>
<?php } ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
<script>

    $(document).ready(function () {

        /*
         // Success
         $.notify("Access granted", "success");
         // Info
         $.notify("Do not press this button", "info");
         // Warning
         $.notify("Warning: Self-destruct in 3.. 2..", "warn");
         // Error
         $.notify("BOOM!", "error");
         */

        $(".btn-modal-responder").click(function (event) {

            /*
             console.log('id-observacion:', $(this).data('id-observacion'));
             console.log('nur:', $(this).data('nur'));
             console.log('id-usuario:', $(this).data('id-usuario'));
             */

            var id_observacion = $(this).data('id-observacion');
            var nur = $(this).data('nur');
            var id_usuario = $(this).data('id-usuario');

            $(".id_observacion").val(id_observacion);
            $(".nur").val(nur);
            $(".id_user").val(id_usuario);

            //$('#myModal').modal('show');
            // sobrepone el modal sobre el <body>
            $('#myModal').appendTo("body").modal('show');
        });
    });

    // === [INICIO] GUARDAR RESPUESTA AL RECLAMO ===
    function guardar_respuesta() {
        var id_observacion = $('.id_observacion').val();
        var nur = $('.nur').val();
        var respuesta_observacion = $('#texto-respuesta-' + id_observacion).val();
        var id_user = $('.id_user').val();
        var btn_responder_observacion = $("#btn-responder-" + id_observacion);

        console.log('id_observacion:', id_observacion);
        console.log('nur:', nur);
        console.log('respuesta_observacion:', respuesta_observacion);
        console.log('id_user:', id_user);


        if (respuesta_observacion.length > 0) {

            $.ajax({
                url: '/externo/guardarRespuestaReclamo',
                data: {
                    id_observacion: id_observacion,
                    respuesta_observacion: respuesta_observacion
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data));
                    var resultado = json.resultado;
                    var mensaje = json.mensaje;
                    //console.log("resultado: " + json.resultado);
                    //console.log("mensaje: " + json.mensaje)
                    //alert("Respuesta enviada correctamente");

                    if (resultado == 1) {
                        $.notify(mensaje, "success");
                        //location.reload();

                        $('.texto-respuesta').val('');
                        $('#myModal-' + id_observacion).modal('toggle');
                        btn_responder_observacion.attr("disabled","disabled");
                    }

                    if (resultado == -1) {
                        $.notify(mensaje, "error");
                    }
                },
                error: function (xhr, status) {
                    console.log("status: " + status);
                    console.log("xhr: " + xhr);
                }
            });
        }
        else {
            alert("(*) Usted debe llenar una respuesta al reclamo");
        }
    }
    // === [FIN] GUARDAR RESPUESTA AL RECLAMO ===
</script>

