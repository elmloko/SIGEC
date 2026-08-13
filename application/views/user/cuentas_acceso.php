<style>
    .item-list-selected {
        background-color: #E0E0E0;
    }
</style>

<script type="text/javascript">
    function imageExist(url) {
        var img = new Image();
        img.src = url;
        return img.height != 0;
    }
</script>

<!-- [INICIO] Con Usuario Seleccionado del MAESTRO: Cargamos Detalle -->
<script type="text/javascript">

    function onclickUsuarioMaestro(boton) {
        var id_btn_usuario_maestro = boton.id;
        var id_usuario_maestro = id_btn_usuario_maestro.replace("btn-usuario-maestro-", "");
        //console.log('id_usuario_maestro: ', id_usuario_maestro);

        // Marcar el Item seleccionado de la Lista
        $(".btn-usuario-maestro").removeClass("item-list-selected");
        $("#" + id_btn_usuario_maestro).addClass("item-list-selected");

        // Ajax
        $.ajax({
            url: '/ajax/listaCuentasQueAdministra',
            data: {
                id_user_maestro: id_usuario_maestro,
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                // var json = JSON.parse(JSON.stringify(data));

                $("#lista-usuarios-detalle").empty();
                $.each(data.cuentas, function (key, value) {

                    var row = JSON.parse(JSON.stringify(value));

                    var foto_perfil = '';
                    var url_foto = '../static/fotos/' + row.username + '.jpg';
                    //console.log('url_foto: ', url_foto);
                    if (imageExist(url_foto)) {
                        foto_perfil += `<img class="img-circle border-white border-xl img-responsive "
                                 width="110"
                                 src="/static/fotos/` + row.username + `.jpg"
                                 alt=""/>`;
                    }
                    else {
                        foto_perfil += `<img class="img-circle border-white border-xl img-responsive "
                                 width="110"
                                 src="/static/fotos/` + row.genero + `.jpg"
                                 alt=""/>`;
                    }

                    var html = `<li id="li-cuenta-acceso-` + row.id + `" class="tile">
                                    <a class="tile-content ink-reaction">
                                        <div class="tile-icon">
                                            ` + foto_perfil + `
                                        </div>
                                        <div class="tile-text text-sm">
                                            ` + row.nombre + `
                                            <small>` + row.cargo + `</small>
                                        </div>
                                    </a>
                                    <a id="btn-eliminar-cuenta-asociada-` + row.id + `"
                                       class="btn btn-xs text-xs btn-danger pull-right btn-eliminar-cuenta-asociada"
                                       onclick="onclickEliminarCuentaAsociada(this)">
                                        <i class="md md-close"></i>
                                    </a>
                                </li>`;
                    $("#lista-usuarios-detalle").append(html);
                });

                $("#id-maestro-usuario-seleccionado").val(id_usuario_maestro);
                $("#btn-actualizar-detalle").css("display", "inline-block");
                $("#btn-nueva-cuenta-para-administrar").css("display", "inline-block");

                return true;
            },
            error: function (xhr, status) {
                console.log("status: " + status);
                console.log("xhr: " + xhr);

                return false;
            }
        });
    }
</script>
<!-- [   FIN] Con Usuario Seleccionado del MAESTRO: Cargamos Detalle -->

<!-- [INICIO] Con Usuario Seleccionado del MAESTRO: Cargamos Detalle -->
<script type="text/javascript">
    function onclickEliminarCuentaAsociada(boton) {
        var id_btn_eliminar_cuenta_asociada = boton.id;
        var id_cuenta_acceso = id_btn_eliminar_cuenta_asociada.replace("btn-eliminar-cuenta-asociada-", "");

        //console.log('id_cuenta_acceso: ', id_cuenta_acceso);

        $.ajax({
            url: '/ajax/deleteAsociacionCuentasDeAcceso',
            data: {
                id_cuenta_acceso: id_cuenta_acceso
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var json = JSON.parse(JSON.stringify(data));
                var resultado = json.resultado;
                var mensaje = json.mensaje;

                if (resultado == 1) {
                    $.notify(mensaje, "success");

                    setTimeout(function () {
                        // Actualizar Lista de cuentas asociadas - DETALLE
                        $("#li-cuenta-acceso-" + id_cuenta_acceso).remove();
                        //location.reload();

                        //$('#div-lista-usuarios-detalle').load(document.URL +  '#div-lista-usuarios-detalle');
                        //location.reload();
                    }, 1000);

                }

                if (resultado == -1) {
                    $.notify(mensaje, "error");
                    setTimeout(function () {
                        //location.reload();
                    }, 1000);
                }

                return true;
            },
            error: function (xhr, status) {
                console.log("status: " + status);
                console.log("xhr: " + xhr);
                console.log(typeof xhr);
                console.log(xhr.errors);

                return false;
            }
        });
    }
</script>

<!-- [INICIO] Agregar nueva cuenta a ser Administrada en el DETALLE del usuario seleccionado-->
<script type="text/javascript">
    function onclickAgregarNuevaCuentaPorAdministrar(boton) {
        var id_usuario_maestro = $("#id-maestro-usuario-seleccionado").val();
        //console.log('id_usuario_maestro: ', id_usuario_maestro);

        eModal.iframe('/content/listadoCuentasPorAdministrar/' + id_usuario_maestro, 'Agregar cuenta(s)');
    }
</script>
<!-- [   FIN] Agregar nueva cuenta a ser Administrada en el DETALLE del usuario seleccionado-->


<!-- [INICIO] Actualizar el DETALLE de Usuario Seleccionado -->
<script type="text/javascript">

    function onclickActualizarDetalleUsuarioSeleccionado(boton) {

        var id_usuario_maestro = $("#id-maestro-usuario-seleccionado").val();
        //console.log('id_usuario_maestro: ', id_usuario_maestro);

        // Marcar el Item seleccionado de la Lista
        $(".btn-usuario-maestro").removeClass("item-list-selected");
        $("#btn-usuario-maestro-" + id_usuario_maestro).addClass("item-list-selected");

        // Ajax
        $.ajax({
            url: '/ajax/listaCuentasQueAdministra',
            data: {
                id_user_maestro: id_usuario_maestro,
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                // var json = JSON.parse(JSON.stringify(data));

                $("#lista-usuarios-detalle").empty();
                $.each(data.cuentas, function (key, value) {

                    var row = JSON.parse(JSON.stringify(value));

                    var foto_perfil = '';
                    var url_foto = '../static/fotos/' + row.username + '.jpg';
                    //console.log('url_foto: ', url_foto);
                    if (imageExist(url_foto)) {
                        foto_perfil += `<img class="img-circle border-white border-xl img-responsive "
                                 width="110"
                                 src="/static/fotos/` + row.username + `.jpg"
                                 alt=""/>`;
                    }
                    else {
                        foto_perfil += `<img class="img-circle border-white border-xl img-responsive "
                                 width="110"
                                 src="/static/fotos/` + row.genero + `.jpg"
                                 alt=""/>`;
                    }

                    var html = `<li id="li-cuenta-acceso-` + row.id + `" class="tile">
                                    <a class="tile-content ink-reaction">
                                        <div class="tile-icon">
                                            ` + foto_perfil + `
                                        </div>
                                        <div class="tile-text text-sm">
                                            ` + row.nombre + `
                                            <small>` + row.cargo + `</small>
                                        </div>
                                    </a>
                                    <a id="btn-eliminar-cuenta-asociada-` + row.id + `"
                                       class="btn btn-xs text-xs btn-danger pull-right btn-eliminar-cuenta-asociada"
                                       onclick="onclickEliminarCuentaAsociada(this)">
                                        <i class="md md-close"></i>
                                    </a>
                                </li>`;
                    $("#lista-usuarios-detalle").append(html);
                });

                $("#id-maestro-usuario-seleccionado").val(id_usuario_maestro);
                $("#btn-actualizar-detalle").css("display", "inline-block");
                $("#btn-nueva-cuenta-para-administrar").css("display", "inline-block");

                return true;
            },
            error: function (xhr, status) {
                console.log("status: " + status);
                console.log("xhr: " + xhr);

                return false;
            }
        });
    }
</script>
<!-- [   FIN] Actualizar el DETALLE de Cuentas Asociadas -->

<script>
    $(function () {
        $('#addDestinatario').click(function () {
            var id_user = $(this).attr('rel');

            eModal.iframe('/user/listadoCuentasPorAdministrar/' + id_user, 'Agregar Destinatario')
        });
        $('a.delDestinatario').click(function () {
            var nombre = $(this).attr('rel');
            if (confirm("Esta usted seguro de quitar de la lista a: \n" + nombre)) {
                return true;
            } else {
                return false;
            }
        });


    });
</script>

<!-- [INICIO] OnKeyUp: Filtro de Busqueda de Usuarios en el MAESTRO-->
<script language="javascript" type="text/javascript">

    function filterPorUsuario(element) {
        var value = $(element).val().toLowerCase();
        $("#lista-usuarios-maestro > li").each(function () {
            if ($(this).text().toLowerCase().indexOf(value) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

</script>
<!-- [   FIN] OnKeyUp: Filtro de Busqueda de Usuarios en el MAESTRO -->

<!-- BEGIN PROFILE HEADER -->
<section class="full-bleed">
    <div class="section-body style-default-dark force-padding text-shadow ">
        <div class="img-backdrop" style="background-image: url('/static/fondo.jpg')"></div>
        <div class="overlay overlay-shade-top stick-top-left height-3"></div>
        <div class="row">
            <div class="col-md-3 col-xs-5">
                <?php if (file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg')): ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110"
                         src="/static/fotos/<?php echo $user->username ?>.jpg?t=<?php echo time(); ?>" alt=""/>
                    <?php
                else:
                    ?>
                    <img class="img-circle border-white border-xl img-responsive " width="110"
                         src="/static/fotos/<?php echo $user->genero . '.jpg' ?>?t=<?php echo time(); ?>" alt=""/>
                <?php endif; ?>

                <h3><?php echo $user->nombre; ?><br/>
                    <small><?php echo $user->cargo ?></small>
                </h3>
            </div><!--end .col -->
            <div class="col-md-9 col-xs-7">
                <div class="width-3 text-center pull-right">
                    <strong class="text-xl"><?php echo $user->logins ?></strong><br/>
                    <span class="text-light opacity-75">Veces ingresadas</span>
                </div>

            </div><!--end .col -->
        </div><!--end .row -->
        <div class="overlay overlay-shade-bottom stick-bottom-left force-padding text-right">
            <div class="pull-right">
                <strong class="text-xl"><?php echo $user->email ?></strong><br/>
                <span class="text-light opacity-75">Ultimo Ingreso: <?php echo Date::fuzzy_span($user->last_login); ?></span>
            </div>
        </div>
    </div><!--end .section-body -->
</section>
<!-- END PROFILE HEADER  -->

<section>
    <div class="section-body no-margin">
        <div class="row">
            <div class="row">
                <!-- [INICIO] MASTER - Listado de Usuarios -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-underline">
                        <!-- Header -->
                        <div class="card-head">
                            <header class="opacity-75">
                                <small>LISTA DE USUARIOS</small>
                            </header>
                        </div>
                        <!-- Body -->
                        <input type="text" id="input-search-usuario" onkeyup="filterPorUsuario(this)"
                               placeholder="Buscar por usuario..." style="width: 100%;">
                        <div class="card-body no-padding height-8 scroll text-medium">
                            <ul id="lista-usuarios-maestro" class="list">
                                <?php foreach ($lista_usuarios_maestro as $usuario): ?>
                                    <li class="tile">
                                        <a id="btn-usuario-maestro-<?php echo $usuario['id']; ?>"
                                           class="tile-content ink-reaction btn-usuario-maestro"
                                           onclick="onclickUsuarioMaestro(this)">
                                            <div class="tile-icon">
                                                <?php if (file_exists(DOCROOT . 'static/fotos/' . $usuario['username'] . '.jpg')): ?>
                                                    <img class="img-circle border-white border-xl img-responsive "
                                                         width="110"
                                                         src="/static/fotos/<?php echo $usuario['username'] ?>.jpg"
                                                         alt=""/>
                                                    <?php
                                                else:
                                                    ?>
                                                    <img class="img-circle border-white border-xl img-responsive "
                                                         width="110"
                                                         src="/static/fotos/<?php echo $usuario['genero'] . '.jpg' ?>"
                                                         alt=""/>
                                                <?php endif; ?>
                                            </div>
                                            <div class="tile-text text-sm">
                                                <?php echo $usuario['nombre'] ?>
                                                <small><?php echo $usuario['cargo'] ?></small>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- [   FIN] MASTER - Listado de Usuarios -->

                <!-- [INICIO] DETALLE - Cuentas para Administrar -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-underline">
                        <!-- Header -->
                        <div class="card-head">
                            <header class="opacity-75">
                                <small>CUENTAS QUE ADMINISTRA</small>
                            </header>
                            <div class="tools" style="display: inline-block; position:relative; right:0;">
                                <input id="id-maestro-usuario-seleccionado" type="hidden"/>

                                <a id="btn-actualizar-detalle"
                                   class="btn btn-icon-toggle"
                                   style="display: none;"
                                   title="Actualizar listado"
                                   onclick="onclickActualizarDetalleUsuarioSeleccionado(this)">
                                    <i class="fa fa-refresh"></i>
                                </a>

                                <a id="btn-nueva-cuenta-para-administrar"
                                   class="btn btn-icon-toggle"
                                   style="display: none;"
                                   title="Adicionar cuenta para administrar"
                                   onclick="onclickAgregarNuevaCuentaPorAdministrar(this)">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Body -->
                        <div id="div-lista-usuarios-detalle" class="card-body no-padding height-8 scroll text-medium">
                            <ul id="lista-usuarios-detalle" class="list">
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- [   FIN] DETALLE - Cuentas para Administrar -->
            </div>
        </div>
</section>


<div id="modal-regular" class="modal" aria-hidden="true" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">Subir Fotograf&iacute;a</h4>
            </div>
            <div id="subir_foto">
                <form action="/user/subirfoto" class="dropzone" id="myAwesomeDropzone">
                    <input type="hidden" name="username" id="ci" value="<?php echo $user->username; ?>"/>
                    <input type="hidden" name="idp" id="idp" value="<?php echo $user->id; ?>"/>

                </form>
            </div>
        </div>
    </div>
</div>
<div id="modal-crop" class="modal" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-hidden="true" data-dismiss="modal" type="button">×</button>
                <h3 class="modal-title">Recortar foto</h3>
            </div>
            <div id="recortar_foto" class="article ">
                <input type="hidden" id="ancho" name="ancho"/>
                <input type="hidden" id="alto" name="alto"/>
                <input type="hidden" id="imgancho" name="imgancho"/>
                <input type="hidden" id="imgalto" name="imgalto"/>
                <!--Image that we Will insert -->
                <img class='imagem_artigo' src="/<?php echo $foto; ?>?t<?php echo time(); ?>" id="cropbox" width="100%"
                     height="100%"/>
                <!--Form to crop-->
                <form id="coords"
                      class="coords" name="fscrop"
                      method="post"
                      action="/user/profile">
                    <div class="inline-labels">
                        <input type="hidden" id="ci" name="username" value="<?php echo $user->username ?>"/>
                        <label>X1 <input type="hidden" size="4" id="x1" name="x1"/></label>
                        <label>Y1 <input type="hidden" size="4" id="y1" name="y1"/></label>
                        <label>X2 <input type="hidden" size="4" id="x2" name="x2"/></label>
                        <label>Y2 <input type="hidden" size="4" id="y2" name="y2"/></label>
                        <label>W <input type="hidden" size="4" id="w" name="w"/></label>
                        <label>H <input type="hidden" size="4" id="h" name="h"/></label>
                    </div>
                    <input type="submit" name="scrop" value="Aceptar" class="btn btn-danger"/>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- -->