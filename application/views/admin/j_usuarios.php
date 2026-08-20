<script type="text/javascript">
    $(document).ready(function () {
        var user = $('#user').val();
        // prepare the data
        var url = '';
        //  var theme = 'metro';
        var pagesize = 50;
        var source =
            {
                datatype: "json",
                datafields: [
                    {name: 'username', type: 'string'},
                    {name: 'usuario', type: 'string'},
                    {name: 'oficina', type: 'string'},
                    {name: 'email', type: 'string'},
                    {name: 'entidad', type: 'string'},
                    {name: 'mosca', type: 'string'},
                    {name: 'logins', type: 'string'},
                    {name: 'habilitado', type: 'int'},
                    {name: 'last_login', type: 'date', format: 'yyyy-MM-dd H:mm:ss'},
                    {name: 'id', type: 'int'},
                    {name: 'edit', type: 'string'},
                ],
                cache: false,
                url: '/admin/ajax/usuariosjson/' + user,
                data: {
                    user: user

                },
                //test
                excel: function () {
                    // update the grid and send a request to the server.
                    url = $("#jqxgrid").jqxGrid('updatebounddata', 'excel');
                }
                ,
                filter: function () {
                    // update the grid and send a request to the server.
                    url = $("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                }
                ,
                sort: function () {
                    // update the grid and send a request to the server.
                    url = $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                }
                ,
                root: 'Rows',
                beforeprocessing: function (data) {
                    if (data != null) {
                        source.totalrecords = data[0].TotalRows;
                    }
                }
            }
            ;
        var dataadapter = new $.jqx.dataAdapter(source, {
                loadError: function (xhr, status, error) {
                    alert(error);
                }
            }
        );
        var linkrenderer = function (row, column, value) {
            if (value.indexOf('#') != -1) {
                value = value.substring(0, value.indexOf('#'));
            }
            var format = {target: '"_blank"'};
            //var html = $.jqx.dataFormat.formatlink(value, format);
            var html = value;
            // alert(value);
            return html;
        }
        var editrenderer = function (row, column, value) {
            if (value.indexOf('#') != -1) {
                value = value.substring(0, value.indexOf('#'));
            }
            var format = {target: '"_blank"'};
            //var html = $.jqx.dataFormat.formatlink(value, format);
            var html = value;
            // alert(value);
            return html;
        }
        var hojarenderer = function (row, column, value) {
            if (value.indexOf('#') != -1) {
                value = value.substring(0, value.indexOf('#'));
            }
            var format = {target: '"_blank"'};
            //var html = $.jqx.dataFormat.formatlink(value, format);
            var html = '<strong>' + value + '</strong>';
            // alert(value);
            return html;
        }
        var btnclass = function (row, columnfield, value) {
            if (value == 0) {
                return 'boton1';
            } else
                return 'boton2';
        }
        // initialize jqxGrid
        $("#jqxgrid").jqxGrid(
            {
                source: dataadapter,
                width: '100%',
                filterable: true,
                altrows: true,
                showfilterrow: true,
                sortable: true,
                //  groupable: true,
                //autoheight: true,
                height: '460',
                autorowheight: true,
                pageable: true,
                virtualmode: true,
                pagesize: 50,
                // enabletooltips: true,
                theme: 'custom',
                rendergridrows: function (obj) {
                    return obj.data;
                },
                columns: [
                    {text: 'username', datafield: 'username', width: '8%',},
                    //{text: 'HOJA DE RUTA', datafield: 'nur', width: '110'},                        
                    {text: 'USUARIO', datafield: 'usuario', width: '20%'},
                    {text: 'CORREO', datafield: 'email', width: '13%'},
                    {text: 'MOSCA', datafield: 'mosca', width: '4%'},
                    {text: 'OFICINA', datafield: 'oficina', width: '17%'},
                    {text: 'Entidad', datafield: 'entidad', width: '20%'},
                    {text: 'LOGINS', datafield: 'logins', width: '4%'},
                    {text: 'ACTIVO', datafield: 'habilitado', width: '4%'},
                    {
                        text: 'ULT.INGRESO',
                        datafield: 'last_login',
                        width: '7%',
                        cellsformat: 'yyyy-MM-dd H:mm:ss',
                        filtertype: 'date'
                    },
                    //{text: 'OPCIONES', datafield: 'id', width: 50, cellsrenderer: linkrenderer},
                    {text: 'EDIT', datafield: 'edit', filtertype: 'none', width: '4%', cellsrenderer: editrenderer},
                    //{text: 'CREADO POR EL USUARIO', datafield: 'nombre', width: '250'},
                ]
            });
        $('#quitarFiltro').click(function () {
            // alert('quitar filtro');
            $("#jqxgrid").jqxGrid('clearfilters');
        });
        $('#baja').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var id_user = dataRecord.id;
                if (id_user != null) {

                    eModal.confirm('Esta seguro deshabilitar al usuario :  \"' + dataRecord.username + '\"', 'Deshabilitar usuario en el sistema')
                        .then(
                            function () {
                                $.ajax({
                                    type: "POST",
                                    data: {id: id_user},
                                    url: "/admin/ajax/baja",
                                    // dataType: "html",
                                    success: function (data) {
                                        location.reload(true);
                                    }
                                });
                            }
                            , null);


                    /*
                     var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                     window.open(url, '_blank'); */
                } else {
                    var url = "/admin/ajax/mensaje/2";
                    var title = "Mensaje de alerta";
                    eModal.ajax(url, title);

                }

                return false;
            } else {
                var url = "/admin/ajax/mensaje/1";
                var title = "Mensaje de alerta";
                eModal.ajax(url, title);
                //eModal.alert('Seleccione un usuario por favor', '');
            }
        });
        $('#alta').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var id_user = dataRecord.id;
                if (id_user != null) {

                    eModal.confirm('Esta seguro habilitar al usuario :  \"' + dataRecord.username + '\"', 'Habilitar usuario en el sistema')
                        .then(
                            function () {
                                $.ajax({
                                    type: "POST",
                                    data: {id: id_user},
                                    url: "/admin/ajax/alta",
                                    // dataType: "html",
                                    success: function (data) {
                                        location.reload(true);
                                    }
                                });
                            }
                            , null);


                    /*
                     var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                     window.open(url, '_blank'); */
                } else {
                    var url = "/admin/ajax/mensaje/2";
                    var title = "Mensaje de alerta";
                    eModal.ajax(url, title);

                }

                return false;
            } else {
                var url = "/admin/ajax/mensaje/1";
                var title = "Mensaje de alerta";
                eModal.ajax(url, title);
                //eModal.alert('Seleccione un usuario por favor', '');
            }
        });
        $('#resetPass').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var id_user = dataRecord.id;
                if (id_user != null) {

                    eModal.confirm('Esta seguro de resetear al password por defecto? al usuario:  \"' + dataRecord.username + '\"', 'Resetear Password')
                        .then(
                            function () {
                                $.ajax({
                                    type: "POST",
                                    data: {id: id_user},
                                    url: "/admin/ajax/resetPass",
                                    // dataType: "html",
                                    success: function (data) {
                                        if (data > 0) {
                                            var url = "/admin/ajax/mensaje/3";
                                            var title = "Contraseña restablecida correctamente";
                                            eModal.ajax(url, title);
                                        } else {
                                            var url = "/admin/ajax/mensaje/4";
                                            var title = "Mensaje";
                                            eModal.ajax(url, title);
                                        }

                                    }
                                });
                            }
                            , null);


                    /*
                     var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                     window.open(url, '_blank'); */
                } else {
                    var url = "/admin/ajax/mensaje/2";
                    var title = "Mensaje de alerta";
                    eModal.ajax(url, title);

                }

                return false;
            } else {
                var url = "/admin/ajax/mensaje/1";
                var title = "Mensaje de alerta";
                eModal.ajax(url, title);
                //eModal.alert('Seleccione un usuario por favor', '');
            }
        });
        $('#documentos').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var id_user = dataRecord.id;
                if (id_user != null) {
                    //var id_user = $(this).attr('rel');
                    eModal.iframe('/admin/content/userDetalle/' + id_user, 'Documentos permitidos : ' + dataRecord.username);
                    /*
                     var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                     window.open(url, '_blank'); */
                } else {
                    var url = "/admin/ajax/mensaje/2";
                    var title = "Mensaje de alerta";
                    eModal.ajax(url, title);
                }

                return false;
            } else {
                var options = {
                    url: "/admin/ajax/mensaje/1",
                    title: "Mensaje de alerta",
                    size: eModal.size.sm
                };
                eModal.ajax(options);
                //eModal.alert('Seleccione un usuario por favor', '');
            }
        });


        $('#estadisticas').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var id_user = dataRecord.id;
                if (id_user != null) {
                    eModal.iframe('/admin/content/userStats/' + id_user, 'Estadisticas de : ' + dataRecord.username);
                } else {
                    var url = "/admin/ajax/mensaje/2";
                    var title = "Mensaje de alerta";
                    eModal.ajax(url, title);
                }

                return false;
            } else {
                var options = {
                    url: "/admin/ajax/mensaje/1",
                    title: "Mensaje de alerta",
                    size: eModal.size.sm
                };
                eModal.ajax(options);
            }
        });

        $('#seguimiento').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1) {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var nur = dataRecord.nur;
                if (nur != null) {
                    var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                    window.open(url, '_blank');
                } else {
                    alert("el documento no tiene hoja de ruta");
                }

                return false;
            } else {
                alert("Seleccione un proceso por favor");
            }
        });
        // $('a.jqxButton').jqxLinkButton({width: '120', height: '30'});


    });

    // Onclick del Boton para asignar privilegios de plazos a usuarios
    function asignarPrivilegiosDePlazosAUsuarios() {

        var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
        if (rowindex > -1) {
            var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
            var id_user = dataRecord.id;
            if (id_user != null) {
                //var id_user = $(this).attr('rel');
                eModal.iframe('/admin/otorgar/index/' + id_user, 'Usuarios con Privilegios : ' + dataRecord.username);
            } else {
                var url = "/admin/ajax/mensaje/2";
                var title = "Mensaje de alerta";
                eModal.ajax(url, title);
            }

            return false;
        } else {
            var options = {
                url: "/admin/ajax/mensaje/1",
                title: "Mensaje de alerta",
                size: eModal.size.sm
            };
            eModal.ajax(options);
            //eModal.alert('Seleccione un usuario por favor', '');
        }
    }

</script>
<style>
    .jqx-grid-statusbar {
        height: 90px !important;
    }

    jqx-grid-column-header span {
        font-weight: bold;
    }

    .btn-custom {
        background-color: #FFFFFF;
        background-image: -moz-linear-gradient(center top, #FFFFFF, #F9F9F9 48%, #E2E2E2 52%, #E7E7E7);
        border-radius: 3px;
        border-style: solid;
        border-width: 1px;
        padding: 2px;
        border-color: #D1D1D1;
        color: #111;
    }

    .btn-custom:hover {
        background-color: #C5DBF5;
        color: #111;
        border: 1px solid #C5DBF5;
    }

    .jqx-grid-column-header {
        z-index: 1 !important;
    }

    .jqx-grid-cell {
        z-index: 1 !important;
    }

    bodydiv {
        background: white;
        font-family: tahoma, arial, verdana, sans-serif;
        font-size: 10px !important;
        width: 100%;
        margin: 0 auto;
        background: none repeat scroll 0 0 #FAFBFC;
        border: 1px solid #AAAAAA;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
        padding: 3px;
    }

    .jqx-grid-cell-custom {
        font-size: 12px !important;
    }


</style>


<div class="row">
    <div class="col-md-12">
        <input type="hidden" value="<?php echo $user->id ?>" id="user"/>
        <h2>Lista de Usuarios </h2>
        <div class=" pull-right ">

            <a class="btn btn-sm btn-warning" onclick="asignarPrivilegiosDePlazosAUsuarios()" id="otorgar-plazos"
               title="Asignar usuarios a los que podrá asignar plazos de respuesta">
                <i class="fa fa-bell"></i>
                Asignar Plazos
            </a>

            <a class="btn btn-sm btn-success" href="javascript:;" id="documentos"
               title="Revisar documentos permitidos por el usuario"><i class="fa fa-dot-circle-o"></i> Documentos
                permitidos</a>

            <a class="btn btn-sm btn-primary" href="javascript:;" id="estadisticas"
               title="Ver entrada, pendientes, archivo y documentos generados por el usuario"><i class="fa fa-bar-chart"></i> Estadisticas</a>

            <a class="btn btn-sm btn-default-dark" href="javascript:;" id="resetPass" title="Resetear contraseña"><i
                    class="fa fa-repeat"></i> Resetear password</a>

            <a class="btn btn-sm btn-info" href="javascript:;" id="alta"
               title="Desahbilitar el acceso al sistema a un usuario"><i class="fa fa-thumbs-o-up"></i> Dar de Alta</a>

            <a class="btn btn-sm btn-danger" href="javascript:;" id="baja"
               title="Desahbilitar el acceso al sistema a un usuario"><i class="fa fa-thumbs-o-down"></i> Dar de
                Baja</a>

            <a class="btn btn-sm btn-default-bright" href="javascript:;" id="quitarFiltro"
               title="Quitar filtros de busqueda y ordanamiento en la tabla"><i class="fa fa-filter"></i> Quitar Filtro</a>

            <a class="btn btn-sm btn-primary-dark" href="/admin/user/add" id="nuevo" title="Crear nuevo usuario"><i
                    class="fa fa-user-plus"></i> Nuevo</a>

        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">

        <div id="jqxgrid">

        </div>
    </div>
</div>