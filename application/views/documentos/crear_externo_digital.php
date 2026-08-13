<div class="row">
    <div class="col-lg-8">
        <div class="card card-underline ">
            <form class="form form-validate" action="/documento/generarExternoDigital" method="post"
                  id="frmCreate">
                <!-- [Inicio] Card Head -->
                <div class="card-head">
                    <header><i class="fa fa-plus"></i> Generar <span class="text-primary"> Nota Externa</span>
                    </header>
                    <div class="tools">
                        <input style="display: none" type="submit" class="btn btn-sm btn-primary-dark" name="submit"
                               value="Generar Documento con Hoja de Ruta"/>
                        <input style="display: none" type="submit" id="noHojaRuta"
                               title="Genera el documento sin hora de ruta para su posterior asignaci[on"
                               class="btn btn-sm btn-default" name="submit" value="Generar Documento sin Hoja de Ruta"/>
                        <input id="btn-generar-cite-externo-digital"
                               type="button"
                               title="Generar Cite de Externo Digital"
                               onclick="generarCiteDeExternoDigital()"
                               class="btn btn-sm btn-warning" value="Generar Cite de Externo Digital"/>
                    </div>
                </div>
                <!-- [Fin] Card Head -->
                <div class="card-body no-padding">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <?php
                            echo Form::input('destinatario', '', array('id' => 'destinatario', 'class' => 'form-control required', 'title' => '(*) Campo requerido'));
                            echo Form::label('destinatario', 'Nombre del destinatario:', array('class' => 'form'));
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo Form::input('cargo-destinatario', '', array('id' => 'cargo-destinatario', 'size' => 48, 'class' => 'form-control required', 'title' => '(*) Campo requerido'));
                            echo Form::label('cargo-destinatario', 'Cargo Destinatario:', array('class' => 'form'));
                            ?>
                        </div>
                        <div class="form-group">
                        <textarea name="referencia" id="referencia" class="form-control required"
                                  title="(*) Campo requerido"></textarea>
                            <label for="referencia">Referencia</label>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <?php
                            echo Form::input('entidad-remitente', '0006', array('id' => 'entidad-remitente', 'size' => 35, 'class' => 'form-control required', 'title' => '(*) Campo requerido'));
                            echo Form::label('entidad-remitente', 'Entidad Remitente:', array('class' => 'form'));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php
                            echo Form::input('nro-hojas', '', array('id' => 'nro-hojas', 'class' => 'form-control required', 'title' => 'Ejemplo: 50', 'title' => '(*) Campo requerido'));
                            echo Form::label('nro-hojas', 'Cantidad Hojas Adjuntas:', array('class' => 'form'));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php
                            echo Form::label('prioridades', 'Prioridad:', array('class' => 'form'));
                            ?>
                            <select id="prioridades">
                                <option value="2">Alta</option>
                                <option value="1">Media</option>
                                <option value="0">Baja</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-lg-10">
                                <?php
                                echo Form::label('entidades', 'Seleccione una Entidad:', array('class' => 'form'));
                                ?>

                                <div id="vias">
                                    <ul>
                                        <!-- destinatarios -->
                                        <?php foreach ($destinatarios as $v) { ?>
                                            <!--
                                            <li class="<?php echo $v['genero'] ?> "><?php echo HTML::anchor('#', $v['nombre'], array('class' => 'destino1 destinatario', 'nombre' => $v['nombre'], 'title' => $v['cargo'], 'cargo' => $v['cargo'], 'via' => '', 'cargo_via' => '')); ?></li>
                                            -->

                                            <!--
                                            <li class="fa fa-home">
                                                <a class="entidad-seleccionada" onclick="obtenerEntidadSeleccionada()"
                                                   nombre="ministro"
                                                   style="font-family: Arial,Helvetica,sans-serif;">
                                                    <?php echo $v['nombre'] ?>
                                                </a>
                                            </li>
                                            -->
                                            <li class="entidad fa fa-home"
                                                id_entidad="<?php echo $v['id_entidad'] ?>"
                                                nombre_entidad="<?php echo $v['nombre_entidad'] ?>"
                                                sigla_entidad="<?php echo $v['sigla_entidad'] ?>"
                                                estado="<?php echo $v['estado'] ?>"
                                                codigo_entidad="<?php echo $v['codigo_entidad'] ?>"
                                                nombre_mae="<?php echo $v['nombre_mae'] ?>"
                                                cargo_mae="<?php echo $v['cargo_mae'] ?>">

                                                <a class="entidad-seleccionada" onclick="obtenerEntidadSeleccionada()"
                                                   style="font-family: Arial,Helvetica,sans-serif;">
                                                    <?php echo $v['nombre_entidad'] ?>
                                                </a>
                                            </li>
                                            <br>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        //$("#btn-generar-cite-externo-digital").css("display", "block");
        //$("#btn-derivar-externo-digital").css("display", "none");
    });

    function validarFormulario() {

        var isFormularioValido = true;

        var nombreDestinatario = $('#destinatario').val();
        var cargoDestinatario = $('#cargo-destinatario').val();
        var referencia = $('#referencia').val();
        var entidadRemitente = $('#entidad-remitente').val();
        var nroDeHojas = $('#nro-hojas').val();

        if (!nombreDestinatario) {
            alert("Usted debe ingresar el nombre del destinatario");
            isFormularioValido = false;
        }
        if (!cargoDestinatario) {
            alert("Usted debe ingresar el cargo del destinatario");
            isFormularioValido = false;
        }
        if (!referencia) {
            alert("Usted debe ingresar el referencia");
            isFormularioValido = false;
        }
        if (!entidadRemitente) {
            alert("Usted debe seleccionar la Entidad Remitente");
            isFormularioValido = false;
        }
        if (!nroDeHojas) {
            alert("Usted debe ingresar la cantidad de hojas");
            isFormularioValido = false;
        }
        return isFormularioValido;
    }

    function obtenerEntidadSeleccionada() {

        //alert("prueba");

        var nombreDestinatario = $('#destinatario');
        var cargoDestinatario = $('#cargo-destinatario');
        var entidadRemitente = $('#entidad-remitente');

        $('.entidad').click(function () {
            var nombre_mae = $(this).attr("nombre_mae");
            var cargo_mae = $(this).attr("cargo_mae");
            var codigo_entidad = $(this).attr("codigo_entidad");

            nombreDestinatario.val(nombre_mae);
            cargoDestinatario.val(cargo_mae);
            entidadRemitente.val(codigo_entidad);
        });

        /*
         var nombreEntidad = destino.attr('nombre');
         $('.entidad-seleccionada').val(nombreEntidad);
         */
    }

    /**
     * Onclick Button derivarExternoDigital
     * */
    function generarCiteDeExternoDigital() {

        var tipo_documento = '70';
        var nombre_destinatario = $('#destinatario').val();
        var cargo_destinatario = $('#cargo-destinatario').val();
        var referencia = $('#referencia').val();
        var nro_de_hojas = $('#nro-hojas').val();
        var entidad_remitente = $('#entidad-remitente').val();
        var prioridad = $('#prioridades option:selected').val();

        /*
         $.ajax({
         url: '/documento/ajaxGenerarCiteDeExternoDigital/',
         type: 'POST',
         data: {
         tipo_documento: tipo_documento,
         nombre_destinatario: nombre_destinatario,
         cargo_destinatario: cargo_destinatario,
         referencia: referencia,
         nro_de_hojas: nro_de_hojas,
         entidad_remitente: entidad_remitente,
         prioridad: prioridad
         },
         dataType: 'json',
         success: function (data) {
         var json = JSON.parse(JSON.stringify(data));
         var id_documento = json.resultado;

         console.log('json: ', json.resultado);
         console.log('id_documento: ', id_documento);

         window.location.replace("/documento/editExternoDigital/" + id_documento);
         },
         error: function (jqXhr, textStatus, errorThrown) {
         console.log('Error: ' + errorThrown);
         }
         });
         */

        if (validarFormulario()) {
            $.ajax({
                url: '/documento/ajaxGenerarCiteDeExternoDigital/',
                type: 'POST',
                data: {
                    tipo_documento: tipo_documento,
                    nombre_destinatario: nombre_destinatario,
                    cargo_destinatario: cargo_destinatario,
                    referencia: referencia,
                    nro_de_hojas: nro_de_hojas,
                    entidad_remitente: entidad_remitente,
                    prioridad: prioridad
                },
                dataType: 'json',
                success: function (data) {
                    var json = JSON.parse(JSON.stringify(data));
                    var id_documento = json.resultado;

                    console.log('json: ', json.resultado);
                    console.log('id_documento: ', id_documento);

                    window.location.replace("/documento/editExternoDigital/" + id_documento);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                }
            });
        }
        else {
            alert("Primero complete los datos del Formulario para poder derivar");
        }

        /*
         if (validarFormulario()) {
         $.ajax({
         url: '/documento/ajaxGenerarCiteDeExternoDigital/',
         type: 'POST',
         data: {
         tipo_documento: tipo_documento
         },
         dataType: 'json',
         success: function (data) {
         var json = JSON.parse(JSON.stringify(data));
         var id_documento = json.resultado;

         console.log('json: ', json.resultado);
         console.log('id_documento: ', id_documento);

         window.location.replace("/documento/editExternoDigital/" + id_documento);
         },
         error: function (jqXhr, textStatus, errorThrown) {
         console.log('Error: ' + errorThrown);
         }
         });
         }
         else {
         alert("Primero complete los datos del Formulario para poder derivar");
         }
         */
    }
</script>