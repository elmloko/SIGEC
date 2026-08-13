<script type="text/javascript">
    function visible(text) {
        //Fade in Background
        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({'filter': 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies
        $('#loading').css({'filter': 'alpha(opacity=80)'}).fadeIn().append('<span>').html('<img src="/media/images/load-indicator.gif" align="absmiddle" alt="" /> ' + text);
    }
    function ocultar() {
        $('#fade , #loading').fadeOut(function () {
            $('#fade, a.close').remove();  //fade them both out
        });
    }

    //adicionar un destinatariov
    function ajaxs(oficial) {
        //$('#theTable tbody tr').remove();
        var hijo = $('#hijo').val();
        var destinatario = $('#destino').val();
        var accion = $('#accion').val();
        var accion_texto = $('#accion option:selected').text();
        var proveido = $('#proveido').val();
        var user = $('#user').val();
        var adjunto = $('#adjunto').val();
        var id_seg = $('#id_seg').val();
        var estado = $('#estado').val();
        var document = $('#document').val();
        var adjunto = $('#adjunto').val();
        var tipo = $('#oficial').val();
        var fecha = $('#fecha').val();
        var urgente = $('#checkbox_urgente')[0].checked;
        var usuario_puede_poner_plazo = $('#usuario_puede_poner_plazo').val();
        // alert('usuario_puede_poner_plazo: ' + usuario_puede_poner_plazo);

        var error_derivar = false;
        var valor_numerico_urgente = 0;


        if (urgente == true) {
            valor_numerico_urgente = 1;

        }
        else {
            valor_numerico_urgente = 0;
        }


        if (user != 255) {

            if (usuario_puede_poner_plazo == 1) {

                var fecha = $('#fecha').val();
                // var fecha_plazo_formateada = fecha.split('/')[0] + "/" + fecha.split('/')[1] + "/" + fecha.split('/')[2];
                var fecha_plazo_formateada = fecha.split('/')[1] + "/" + fecha.split('/')[0] + "/" + fecha.split('/')[2];

                var dia_fecha_plazo = fecha.split('/')[0];
                var fecha_actual = new Date();
                var fecha_actual_formateada = (fecha_actual.getMonth() + 1) + "/" + fecha_actual.getDate() + "/" + fecha_actual.getFullYear();
                var dia_fecha_actual = fecha_actual.getDate();

                // console.log('fecha -> ' + fecha_plazo_formateada);
                // console.log('fecha_actual -> ' + fecha_actual_formateada);

                // console.log('fecha -> ' + new Date(fecha_plazo_formateada));
                // console.log('fecha_actual -> ' + new Date(fecha_actual_formateada));

                // alert($('#fecha').val());
                if ($('#fecha').val().length <= 0) {
                    alert("Usted debe ingresar una 'Fecha de Respuesta'");
                    // fecha = " ";
                    error_derivar = true;
                }
                else {
                    // if ((Date.parse(fecha_plazo_formateada) < Date.parse(fecha_actual_formateada)) && (dia_fecha_plazo != dia_fecha_actual)) {
                    if ((Date.parse(fecha_plazo_formateada) < Date.parse(fecha_actual_formateada))) {
                        alert("Usted debe ingresar una 'Fecha de Respuesta' mayor a la actual");
                        error_derivar = true;
                    }
                }

                if ($('#proveido').val().length <= 0) {
                    alert("Usted debe ingresar un 'Proveido'");
                    error_derivar = true;
                }

                if ($('#fecha').val().length <= 0) {
                    // alert("Usted debe ingresar una 'Fecha de Respuesta'");
                    // error_derivar = true;
                }

                if ($("#destino option:selected").text().length <= 0) {
                    alert("Usted debe ingresar un Destinatario");
                    error_derivar = true;
                }
            }
            else {
                if ($('#proveido').val().length <= 0) {
                    alert("Usted debe ingresar un 'Proveido'");
                    error_derivar = true;
                }

                if ($("#destino option:selected").text().length <= 0) {
                    alert("Usted debe ingresar un Destinatario");
                    error_derivar = true;
                }
            }
        }
        else {
            error_derivar = false;
        }


        if (!error_derivar) {

            if (adjunto == null) {
                adjunto = 0;
            }

            var nur = $('#nur').val();
            visible('Derivando...');

            /*
            var parametros = {
                tipo: tipo,
                oficial: oficial,
                fecha: fecha,
                destino: destinatario,
                adjunto: adjunto,
                document: document,
                nur: nur,
                accion: accion,
                proveido: proveido,
                hijo: hijo,
                user: user,
                adjunto: adjunto,
                id_seg: id_seg,
                estado: estado,
                urgente: valor_numerico_urgente                
            };
            console.log('parametros: ', parametros);
            */

            $.ajax({
                type: "POST",
                data: {
                    tipo: tipo,
                    oficial: oficial,
                    fecha: fecha,
                    destino: destinatario,
                    adjunto: adjunto,
                    document: document,
                    nur: nur,
                    accion: accion,
                    proveido: proveido,
                    hijo: hijo,
                    user: user,
                    adjunto: adjunto,
                    id_seg: id_seg,
                    estado: estado,
                    urgente: valor_numerico_urgente
                },
                url: "/ajax/derivar",
                dataType: "json",
                success: function (item) {
                    //console.log('item: ', item);
                    ocultar();
                    if (item.id) {
                        var adjunto = '';
                        $.each(item.adjunto, function (k, v) {
                            adjunto = adjunto + v + "<br/>";
                        });
                        if (item.oficial == "0") {
                            $('#theTable tbody').append('<tr class="oficial0"><td rowspan="2" ><a href="javascript:;" onclick="activar($(this));" class="btn btn-sm btn-danger" title="Cancelar derivación"  id="' + item.id + '" destino="' + item.id_destino + '" oficial="' + item.oficial + '" ><i class="fa fa-trash-o"></i></a></td><td rowspan="2"><b class="label style-default">COPIA</label></td><td>' + item.receptor_nombre + '<br/><b>' + item.receptor_cargo + '</b></td><td>' + accion_texto + '<br/></td><td><b>' + adjunto + '</b></td></tr><tr class="oficial0"><td colspan="3"><b>Proveido: </b>' + item.proveido + '</td></tr>');
                        }
                        else {
                            $('#theTable tbody').append('<tr class="oficial1"><td rowspan="2" ><a href="javascript:;" onclick="activar($(this));" class="btn btn-sm btn-danger" title="Cancelar derivación"  id="' + item.id + '" destino="' + item.id_destino + '" oficial="' + item.oficial + '" ><i class="fa fa-trash-o"></i></a></td><td rowspan="2"><label class="label style-primary-dark">OFICIAL</label></td><td>' + item.receptor_nombre + '<br/><b>' + item.receptor_cargo + '</b></td><td>' + accion_texto + '<br/></td><td><b>' + adjunto + '</b></td></tr><tr class="oficial1"><td colspan="3" ><b>Proveido: </b>' + item.proveido + '</td></tr>');
                        }
                    }
                    else {
                        ocultar();
                        alert(item.error);
                    }
                    //alert(item.url_web_service_chat);
                },
                error: function () {
                    ocultar();
                }
            });
        }
    }
    function activar(link) {
        var $this = link;
        var id = $this.attr('id');
        //alert(id)
        var destino = $this.attr('destino');
        var oficial = $this.attr('oficial');
        var document = $('#document').val();
        visible('Quitando destinatario...');
        $.ajax({
            type: "POST",
            data: {id: id, destino: destino, oficial: oficial, document: document},
            url: "/ajax/eliminar",
            dataType: "json",
            success: function (item) {
                ocultar();
            },
            error: function () {
                ocultar();
            }
        });
        $this.parent('td').parent('tr').next().remove();
        $this.parent('td').parent('tr').remove();
        return false;
        console.log(link.attr('title'));
    }
    $(function () {
        $('body').append(
            $('<div>').attr('id', 'loading').addClass('loading').css({
                position: 'absolute',
                display: 'none',
                top: '48%',
                left: '48%',
                background: '#ffffff'
            })
        );
        $('#fecha-resp').datepicker({autoclose: true, todayHighlight: true});
        $('#imprimir').click(function () {
            visible();
            ocultar();
            return false;
        });
        //  $('#frmDerivar').validate();
        $('#dOficial').bind('click', function () {
            ajaxs(1);
            return false;
        });
        $('#dCopia').bind('click', function () {
            ajaxs(0);
            return false;
        });
        /*      $("#adjunto").fcbkcomplete({
         json_url: "/ajax/documentos",
         addontab: true,
         maxitems: 5,
         height: 5,
         cache: true
         });
         */
        /*    $('table#theTable :checkbox').on("click", function () {
         alert("sadad");
         var $this = $(this);
         var id = $this.attr('id');
         var destino = $this.attr('destino');
         var oficial = $this.attr('oficial');
         var document = $('#document').val();
         visible('Quitando destinatario...');
         $.ajax({
         type: "POST",
         data: {id: id, destino: destino, oficial: oficial, document: document},
         url: "/ajax/eliminar",
         dataType: "json",
         success: function (item)
         {
         ocultar();
         },
         error: function () {
         ocultar();
         }
         });
         $this.parent('td').parent('tr').next().remove();
         $this.parent('td').parent('tr').remove();
         return false;
         //var $this=$(this).find('input:checkbox');
         //  var id=$this.attr('id');
         // alert('hola');
         }); */

//eliminar
        $('#eliminar').click(function () {
            var len = 0;
            var destinatarios = [];
            var valor;
            $('#theTable tbody tr').each(function (index, domEle) {
                var checked = $(this).find('input:checkbox').attr('checked');
                if (checked) {
                    valor = $(this).find('input:checkbox').attr('id');
                    destinatarios[len] = valor;
                    len++;
                }
            });
            if (len > 0) {
                alert(destinatarios)
            }
            else {
                alert("Seleccione un destinatario por favor.");
            }
        });
        $('#fecha').datepicker({autoclose: true, todayHighlight: true, format: "dd/mm/yyyy"});
        $('select').select2();
    });

</script>

<style type="text/css">
    table tr td {
        padding: 2px;
    }

    input[type="checkbox"] {
        cursor: pointer;
        border: 2px solid #DC5526;
    }

    div.loading {
        border: 5px solid #666;
        background-color: #fff;
        padding: 10px;
    }
</style>

<div style="width: 100%;">

    <div id="derivacion">
        <!-- mostrar errores -->
        <?php if (sizeof($errors) > 0): ?>
            <div class="error">
                <p><span style="float: left; margin-right: .3em;" class=""></span>
                    <?php foreach ($errors as $k => $v): ?>
                    <strong><?= $k ?>: </strong> <?php echo $v; ?></p>
                <?php endforeach; ?>
            </div>
            <br/>
        <?php endif; ?>
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-underline">
                    <div class=" card-head">
                        <header><i class="fa fa-tags"></i> Derivar : <?php echo $documento->nur; ?></header>
                        <div class="tools">

                            <span class=" opacity-50">Cite original: </span>
                            <span class="text-medium text-primary-dark"><?php echo $documento->cite_original; ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="/route/derivando/?nur=<?php echo $documento->nur; ?>" method="post" class="form"
                              id="frmDerivar">
                            <input type="hidden" value="<?php echo $hijo; ?>" name="hijo" id="hijo"/>
                            <input type="hidden" value="<?php echo $documento->nur; ?>" name="nur" id="nur"/>
                            <input type="hidden" value="<?php echo $id_seguimiento; ?>" name="id_seg" id="id_seg"/>
                            <input type="hidden" value="<?php echo $oficial; ?>" name="oficial" id="oficial"/>
                            <input type="hidden" value="<?php echo $documento->estado; ?>" name="estado" id="estado"/>
                            <input type="hidden" value="<?php echo $user->id; ?>" name="user" id="user"/>
                            <input type="hidden" value="<?php echo $documento->id; ?>" name="document" id="document"/>

                            <div class="row">
                                <div class="col-lg-2 col-md-2">
                                    <span class=" opacity-50">Referencia: </span>
                                </div>
                                <div class="col-lg-10 col-md-10">
                                    <span
                                        class="text-medium text-primary-dark"><?php echo $documento->referencia; ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 col-md-2">
                                    <span class=" opacity-50">Destinatario: </span>
                                </div>
                                <div class="col-lg-10 col-md-10">
                                    <span class="text-medium">
                                        <?php echo $documento->nombre_destinatario; ?>
                                        | <?php echo $documento->cargo_destinatario; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 col-md-2">
                                    <span class=" opacity-50">Remitente: </span>
                                </div>
                                <div class="col-lg-10 col-md-10">
                                    <span class="text-medium">
                                        <strong><?php echo $documento->nombre_remitente; ?>
                                            | </strong> <?php echo $documento->cargo_remitente; ?>
                                    </span>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-lg-9 col-md-9">
                                    <div class="form-group">
                                        <?php echo Form::select('destino', $destinatarios, Arr::get($_POST, 'destino', NULL), array('id' => 'destino', 'class' => 'form-control required',)); ?>
                                        <?php echo Form::label('derivar', 'Derivar a :'); ?>
                                    </div>
                                </div>


                                <!-- MANTENER LA PERSONA QUE DERIVA, DESDE LA VENTANA ANTERIOR -->
                                <?php
                                $nombre_usuario_destinatario = $documento->nombre_destinatario;
                                $posicion_usuario_destinatario = 0;

                                foreach ($destinatarios as $id_usuario => $usuario) {

                                    if (strpos($usuario, $nombre_usuario_destinatario) !== FALSE) {
                                        // echo $posicion_usuario_destinatario . ' --- ' . $id_usuario . ' --- ' . $nombre_usuario_destinatario . '<br>';
                                        break;
                                    } else {
                                        $posicion_usuario_destinatario++;
                                    }
                                }
                                ?>
                                <input type="hidden" value="<?php echo $posicion_usuario_destinatario; ?>"
                                       name="posicion_usuario_destinatario" id="posicion_usuario_destinatario"/>
                                <script>
                                    var posicion = $('#posicion_usuario_destinatario').val();
                                    // console.log('posicion ' + posicion);
                                    document.getElementById('destino').selectedIndex = posicion;
                                </script>

                                <div class="col-lg-3 col-md-3">

                                    <label>

                                        <?php if (($user->id == '255') != TRUE) { ?>
                                            <input type="checkbox" value="1" id="checkbox_urgente"/><span>Urgente</span>
                                        <?php } else { ?>
                                            <input style="display: none;" type="checkbox" value="1"
                                                   id="checkbox_urgente"/><span style="display: none;">Urgente</span>
                                        <?php } ?>

                                    </label>
                                </div>
                            </div>
                            <div class="row">

                                <?php if (($user->id == '255') != TRUE) { ?>
                                    <div class="col-lg-6 col-md-6">

                                        <div class="form-group">
                                            <?php echo Form::textarea('proveido', Arr::get($_POST, 'proveido', ''), array('COLS' => 12, 'rows' => 2, 'class' => 'required form-control', 'id' => 'proveido')); ?>
                                            <?php echo Form::label('proveido', 'Proveido'); ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div style="display: none;" class="col-lg-6 col-md-6">

                                        <div class="form-group">
                                            <?php echo Form::textarea('proveido', Arr::get($_POST, 'proveido', ''), array('COLS' => 12, 'rows' => 2, 'class' => 'required form-control', 'id' => 'proveido')); ?>
                                            <?php echo Form::label('proveido', 'Proveido'); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="col-lg-3 col-md-3">
                                    <div class="form-group">
                                        <?php echo Form::select('accion', $acciones, Arr::get($_POST, 'accion', NULL), array('class' => 'required', 'id' => 'accion')); ?>
                                        <?php echo Form::label('accion', 'Accion'); ?>
                                    </div>
                                </div>

                                <!-- VALIDAMOS QUE SOLO EL USUARIO VENTANILLA TENGA ACCION 'Para su conocimiento' -->
                                <?php if (($user->id == '255') == TRUE) { ?>
                                    <script>
                                        // index: 4 -> 'Para su conocimiento'
                                        document.getElementById('accion').selectedIndex = 4;
                                        $('#accion').attr('disabled', 'disabled');
                                    </script>
                                <?php } ?>

                                <!-- ========================================================= -->

                                <!-- VERIFICAR SI EL USUARIO LOGUEADO PUEDE COLOCAR 'FECHA DE PLAZO' -->
                                <?php
                                $usuarioPuedePonerPlazo = 0;
                                $id_usuario_logueado = $user->id;

                                foreach (array_keys($destinatarios) as $posicion => $id_usuario_destinatario) {

                                    // echo ($posicion . ', ' . $id_usuario_destinatario) . "<br>";

                                    if ($posicion == $posicion_usuario_destinatario) {

                                        // Verificamos que el Usuario pueda asignar plazo
                                        $query = "  SELECT
                                                        IF(COUNT(*) > 0, 'SI', 'NO') AS respuesta
                                                    FROM
                                                        usuarios_habilitados_plazos
                                                    WHERE
                                                        (id_usuario_padre = '$id_usuario_logueado')
                                                            AND (id_usuario_hijo = '$id_usuario_destinatario');";

                                        $resultSet = db::query(Database::SELECT, $query, FALSE)
                                            ->execute()
                                            ->as_array();

                                        // Verificamos si ya se asigno un plazo a la Hoja de Ruta
                                        $hoja_de_ruta = $documento->nur;

                                        $query_verificar_asignacion = " SELECT
                                                                            IF(COUNT(*) = 0, 'SI', 'NO') AS puede_mostrar_calendario
                                                                        FROM
                                                                            alertas a,
                                                                            (SELECT
                                                                                s.*
                                                                            FROM
                                                                                seguimiento s
                                                                            WHERE
                                                                                nur = '$hoja_de_ruta') AS s
                                                                        WHERE
                                                                            (a.id_seguimiento = s.id)";

                                        $resultSet_verificacion = db::query(Database::SELECT, $query_verificar_asignacion, FALSE)
                                            ->execute()
                                            ->as_array();

                                        if (($resultSet[0]['respuesta'] == 'SI') == TRUE && ($resultSet_verificacion[0]['puede_mostrar_calendario'] == 'SI') == TRUE) {
                                            $usuarioPuedePonerPlazo = TRUE;
                                        }
                                    }
                                }
                                ?>

                                <input type="hidden" value="<?php echo $usuarioPuedePonerPlazo; ?>"
                                       name="usuario_puede_poner_plazo" id="usuario_puede_poner_plazo"/>

                                <!-- ========================================================= -->
                                <!-- Verificaciones para ocultar o no el 'calendario' -->
                                <?php if (($user->id == '255') != TRUE && ($usuarioPuedePonerPlazo == TRUE)) { ?>
                                    <div id="contenedor_calendario" class="col-lg-3 col-md-3">
                                        <div class="form-group control-width-normal">
                                            <div id="fecha-resp" class="input-group date">
                                                <div class="input-group-content">
                                                    <input id="fecha" type="text" name="fecha" class="form-control">
                                                    <label for="fecha">Fecha max de respuesta </label>
                                                </div>
                                                <span class="input-group-addon"><i style="display: none;"
                                                                                   class="fa fa-calendar"></i></span>

                                            </div>
                                            <p class="help-block"
                                               title="Fecha maxima de respuesta en caso de solicitar un informe o nota interna">
                                                (*) Campo Obligatorio</p>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div id="contenedor_calendario" style="display: none;" class="col-lg-3 col-md-3">
                                        <div class="form-group control-width-normal">
                                            <div id="fecha-resp" class="input-group date">
                                                <div class="input-group-content">
                                                    <input id="fecha" type="text" name="fecha" class="form-control">
                                                    <label for="fecha">Fecha max de respuesta </label>
                                                </div>
                                                    <span class="input-group-addon"><i
                                                            class="fa fa-calendar"></i></span>

                                            </div>
                                            <p class="help-block"
                                               title="Fecha maxima de respuesta en caso de solicitar un informe o nota interna">
                                                (*) Campo Obligatorio</p>
                                        </div>
                                    </div>

                                <?php } ?>
                            </div>

                            <table style=" width:100%; ">
                                <tr>

                                    <td colspan="2">
                                        <?php if ($oficial != 0): ?>
                                            <a href="#" id="dOficial" class="btn btn-sm btn-primary-dark"><i
                                                    class="md md-play-circle-outline"></i> Derivar Oficial</a>
                                        <?php endif; ?>
                                        <a href="#" id="dCopia" class="btn btn-sm btn-default"><i
                                                class="md md-play-circle-outline"></i> Derivar Copia</a>

                                    </td>
                                    <td align="right">
                                        <?php if ($documento->estado == 0): ?>
                                            <a href="/print/hr/?code=<?php echo $documento->nur; ?>&p=1" target="_blank"
                                               class="btn btn-sm btn-accent-dark"><i class="fa fa-print"></i> Imprimir
                                                Hoja de Ruta</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php echo Form::hidden('tipo', '', array('id' => 'tipo')); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <table id="theTable" class="table table-condensed ">
                <thead>
                <tr>
                    <th>Cancelar</th>
                    <th>Tipo</th>
                    <th>Derivado a</th>
                    <th>Acción</th>
                    <!-- <th>Fecha max respuesta</th> -->
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Verificamos que el destinatario seleccionado pueda asignar plazos
    $('#destino').on('change', function () {

        // obtenemos el 'id del usuario destinatario seleccionado'
        var id_usuario_logueado = $("#user").val();
        var id_usuario_destinatario = $("#destino").val();
        var hoja_de_ruta = $('#nur').val();

        // var parametros = 'id_usuario_logueado=' + id_usuario_logueado + '&' + 'id_usuario_destinatario=' + id_usuario_destinatario + '&' + 'hoja_de_ruta=' + hoja_de_ruta;
        // console.log(parametros);

        $.ajax({
            type: "GET",
            data: {
                id_usuario_logueado: id_usuario_logueado,
                id_usuario_destinatario: id_usuario_destinatario,
                hoja_de_ruta: hoja_de_ruta
            },
            url: "/ajax/jsonVerificarIdUsuarioPuedaAsignarPlazos/",

            success: function (response) {
                var json = $.parseJSON(response);
                var respuesta = json[0].respuesta;

                // console.log('jsonVerificarIdUsuarioPuedaAsignarPlazos -> ', json);

                if (respuesta === 'SI') {
                    // alert(respuesta);
                    $('#usuario_puede_poner_plazo').val(1);
                    $('#contenedor_calendario').css('display', 'block');
                }
                else {
                    $('#usuario_puede_poner_plazo').val(0);
                    $('#contenedor_calendario').css('display', 'none');
                }
            }
        });

    });
</script>


