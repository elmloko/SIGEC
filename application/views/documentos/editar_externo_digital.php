<div class="row">
    <div class="col-lg-8">
        <div class="card card-underline ">
            <form class="form form-validate" action="/documento/generarExternoDigital" method="post"
                  id="frmCreate">
                <div id="alerta-success" class="alert alert-success" style="display: none">
                    <p id="texto-alerta-success"></p>
                </div>
                <div id="alerta-danger" class="alert alert-danger" style="display: none">
                    <p id="texto-alerta-danger"></p>
                </div>
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
                        <input id="btn-editar-externo-digital"
                               type="button" class="btn btn-sm btn-warning" value="Editar"
                               onclick="editarExternoDigital()">
                        <input id="btn-derivar-externo-digital"
                               type="button" class="btn btn-sm btn-primary-dark" value="Derivar a otra Entidad"
                               onclick="derivarExternoDigital()">
                    </div>
                </div>
                <!-- [Fin] Card Head -->
                <div class="card-body no-padding">
                    <div class="col-lg-6 col-md-6">
                        <input type="hidden" id="id-documento" name="id-documento"
                               value="<?php echo $documento->id; ?>">
                        <div class="form-group">
                            <?php
                            echo Form::input('cite-original', $documento->cite_original, array('id' => 'cite-original', 'class' => 'form-control required', 'readonly', 'title' => '(*) Campo requerido'));
                            echo Form::label('cite-original', 'Cite del Documento:', array('class' => 'form'));
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo Form::input('destinatario', $documento->nombre_destinatario, array('id' => 'destinatario', 'class' => 'form-control required', 'readonly', 'title' => '(*) Campo requerido'));
                            echo Form::label('destinatario', 'Nombre del destinatario:', array('class' => 'form'));
                            ?>
                        </div>
                        <div class="form-group">
                            <?php
                            echo Form::input('cargo-destinatario', $documento->cargo_destinatario, array('id' => 'cargo-destinatario', 'size' => 48, 'class' => 'form-control required', 'readonly', 'title' => '(*) Campo requerido'));
                            echo Form::label('cargo-destinatario', 'Cargo Destinatario:', array('class' => 'form'));
                            ?>
                        </div>
                        <div class="form-group">
                        <textarea name="referencia" id="referencia" class="form-control required"
                                  title="(*) Campo requerido"><?php echo $documento->referencia; ?></textarea>
                            <label for="referencia">Referencia</label>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <?php
                            echo Form::input('entidad-remitente', $documento->id_entidad_remitente, array('id' => 'entidad-remitente', 'size' => 35, 'class' => 'form-control required', 'readonly', 'title' => '(*) Campo requerido'));
                            echo Form::label('entidad-remitente', 'Entidad Remitente:', array('class' => 'form'));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php
                            echo Form::input('nro-hojas', $documento->hojas, array('id' => 'nro-hojas', 'class' => 'form-control required', 'title' => 'Ejemplo: 50', 'title' => '(*) Campo requerido'));
                            echo Form::label('nro-hojas', 'Cantidad Hojas Adjuntas:', array('class' => 'form'));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php echo Form::select('prioridades', $options_prioridades, $documento->prioridad_digital, array('id' => 'prioridades', 'class' => 'required form-control')); ?>
                            <?php
                            echo Form::label('prioridades', 'Prioridad:', array('class' => 'form'));
                            ?>
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

    <div class="col-lg-4">
        <div class="row">
            <div class="card card-underline">
                <div class="card-head">
                    <header>Archivos Digitales</header>
                </div>
                <div class="card-body">
                    <label class="form" for="file-nota">Seleccione la Nota</label>
                    <input type="file" class="form file" id="file-nota" name="archivo" class="formControl form-control"
                           accept="application/pdf" onchange="encodeFileNotaToBase64(this)" required/>

                    <label class="form file">Seleccione un Adjunto</label>
                    <input class="form" type="file" class="file" id="file-adjunto" name="archivo"
                           class="formControl form-control"
                           accept="application/pdf" onchange="encodeFileAdjuntoToBase64(this)" required/>
                    <!-- FileNota Base 64 -->
                    <input type="hidden" id="file-nota-base64" name="file-nota-base64" value="">
                    <input type="hidden" id="file-adjunto-base64" name="file-nota-base64" value="">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function validarFormulario() {

        var isFormularioValido = true;

        var nombreDestinatario = $('#destinatario').val();
        var cargoDestinatario = $('#cargo-destinatario').val();
        var referencia = $('#referencia').val();
        var entidadRemitente = $('#entidad-remitente').val();
        var nroDeHojas = $('#nro-hojas').val();
        var citeOriginal = $('#cite-original').val();

        var fileNotaBase64 = $('#file-nota-base64').val();
        var fileAdjuntoBase64 = $('#file-adjunto-base64').val();

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
        if (!citeOriginal) {
            alert("Usted debe ingresar el Cite");
            isFormularioValido = false;
        }
        if (!fileNotaBase64) {
            alert("Usted debe subir el archivo digital de la Nota");
            isFormularioValido = false;
        }
        if (!fileAdjuntoBase64) {
            alert("Usted debe subir el archivo digital del Adjunto");
            isFormularioValido = false;
        }
        return isFormularioValido;
    }

    /**
     * Actualizamos el 'input hidden' con el valor en base64 del 'Archivo subido', cada que se sube un nuevo archivo
     * */
    function encodeFileNotaToBase64(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function () {
                $('#file-nota-base64').val(reader.result);
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        }
    }

    /**
     * Actualizamos el 'input hidden' con el valor en base64 del 'Archivo subido', cada que se sube un nuevo archivo
     * */
    function encodeFileAdjuntoToBase64(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function () {
                $('#file-adjunto-base64').val(reader.result);
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        }
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

    function editarExternoDigital() {

        // Ajax
        var id_documento = $('#id-documento').val();
        var nombre_destinatario = $('#destinatario').val();
        var cargo_destinatario = $('#cargo-destinatario').val();
        var referencia = $('#referencia').val();
        var entidad_remitente = $('#entidad-remitente').val();
        var nro_de_hojas = $('#nro-hojas').val();
        var prioridad = $('#prioridades option:selected').val();

        console.log("id_documento: " + id_documento);
        console.log("nombreDestinatario: " + nombre_destinatario);
        console.log("cargoDestinatario: " + cargo_destinatario);
        console.log("referencia: " + referencia);
        console.log("nroDeHojas: " + nro_de_hojas);
        console.log("entidad_remitente: " + entidad_remitente);
        console.log("prioridad: " + prioridad);

        $.ajax({
            url: '/documento/ajaxEditarExternoDigital/',
            type: 'POST',
            data: {
                id_documento: id_documento,
                nombre_destinatario: nombre_destinatario,
                cargo_destinatario: cargo_destinatario,
                referencia: referencia,
                nro_de_hojas: nro_de_hojas,
                entidad_remitente: entidad_remitente,
                prioridad: prioridad
            },
            // sets timeout to 3 seconds
            timeout: 3000,
            dataType: 'json',
            success: function (data) {
                var json = JSON.parse(JSON.stringify(data));

                var resultado = json.resultado;
                var mensaje = json.mensaje;
                var mensajebd = json.mensajebd;

                $("#alerta-success").css("display", "block");
                var texto_alerta = $('#texto-alerta-success');
                texto_alerta.text(mensaje);

                $("#alerta-danger").css("display", "none");

                console.log('json: ', json);
                console.log('resultado: ', resultado);
                console.log('mensaje: ', mensaje);
                console.log('mensajebd: ', mensajebd);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log('Error: ' + errorThrown);
                $("#alerta-success").css("display", "none");

                $("#alerta-danger").css("display", "block");
                var texto_alerta_danger = $('#texto-alerta-danger');
                texto_alerta_danger.text('El documento no fue editado correctamente');
            }
        });
    }

    /**
     * Onclick Button derivarExternoDigital
     * */
    function derivarExternoDigital() {
        //alert('hola');
        if (validarFormulario()) {
            // Ajax
            var nombreDestinatario = $('#destinatario').val();
            var cargoDestinatario = $('#cargo-destinatario').val();
            var referencia = $('#referencia').val();
            var entidadRemitente = $('#entidad-remitente').val();
            var nroDeHojas = $('#nro-hojas').val();
            var citeOriginal = $('#cite-original').val();
            var prioridad = $('#prioridades option:selected').val();

            var fileNotaBase64 = $('#file-nota-base64').val();
            fileNotaBase64 = fileNotaBase64.replace('data:application/pdf;base64,', '');
            var fileAdjuntoBase64 = $('#file-adjunto-base64').val();
            fileAdjuntoBase64 = fileAdjuntoBase64.replace('data:application/pdf;base64,', '');

            console.log("nombreDestinatario: " + nombreDestinatario);
            console.log("cargoDestinatario: " + cargoDestinatario);
            console.log("referencia: " + referencia);
            console.log("entidadRemitente: " + entidadRemitente);
            console.log("nroDeHojas: " + nroDeHojas);
            console.log("citeOriginal: " + citeOriginal);
            console.log("prioridad: " + prioridad);

            console.log("fileNotaBase64: " + fileNotaBase64);
            console.log("fileAdjuntoBase64: " + fileAdjuntoBase64);

            $.ajax({
                url: '/documento/ajaxDerivarExternoDigital/',
                type: 'POST',
                data: {
                    nombreDestinatario: nombreDestinatario,
                    cargoDestinatario: cargoDestinatario,
                    referencia: referencia,
                    entidadRemitente: entidadRemitente,
                    nroDeHojas: nroDeHojas,
                    citeOriginal: citeOriginal,
                    prioridad: prioridad,
                    fileNotaBase64: fileNotaBase64,
                    fileAdjuntoBase64: fileAdjuntoBase64
                },
                dataType: 'json',
                // sets timeout to 3 seconds
                timeout: 3000,
                success: function (data) {
                    console.log('resultJSON: ' + data);

                    $("#alerta-success").css("display", "block");
                    var texto_alerta = $('#texto-alerta-success');
                    texto_alerta.text('El documento fue derivado correctamente');

                    $("#alerta-danger").css("display", "none");

                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log('Error: ' + errorThrown);
                    $("#alerta-success").css("display", "none");

                    $("#alerta-danger").css("display", "block");
                    var texto_alerta_danger = $('#texto-alerta-danger');
                    texto_alerta_danger.text('El documento no fue derivado correctamente');
                }
                /*
                 error: function (xmlhttprequest, textstatus, message) {
                 if (textstatus === "timeout") {
                 alert("got timeout");
                 } else {
                 alert(textstatus);
                 }
                 }
                 */
            });
        }
        else {
            alert("Primero complete los datos del Formulario para poder derivar");
        }
    }
</script>