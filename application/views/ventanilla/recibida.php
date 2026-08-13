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
                        {name: 'cite_original', type: 'string'},
                        {name: 'nur', type: 'string'},
                        {name: 'destinatario', type: 'string'},
                        {name: 'referencia', type: 'string'},
                        {name: 'institucion_destinatario', type: 'string'},
                        {name: 'institucion_remitente', type: 'string'},
                        {name: 'fecha_creacion', type: 'date', format: 'yyyy-MM-dd H:mm:ss'},
                        {name: 'remitente', type: 'string'},
                        {name: 'estado', type: 'int'},
                        {name: 'link', type: 'string'},
                        {name: 'id', type: 'int'},
                        {name: 'edit', type: 'string'},
                    ],
                    cache: false,
                    url: '/ajaxd/ventanillajson/' + user,
                    data: {
                        user: user

                    },
                    //test
                    excel: function ()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'excel');
                    }
                    ,
                    filter: function ()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                    }
                    ,
                    sort: function ()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                    }
                    ,
                    root: 'Rows',
                    beforeprocessing: function (data)
                    {
                        if (data != null)
                        {
                            source.totalrecords = data[0].TotalRows;
                        }
                    }
                }
        ;
        var dataadapter = new $.jqx.dataAdapter(source, {
            loadError: function (xhr, status, error)
            {
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
            }
            else
                return  'boton2';
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
                    rendergridrows: function (obj)
                    {
                        return obj.data;
                    },
                    columns: [
                        {text: 'Hoja Ruta', datafield: 'nur', width: '12%', },
                        //{text: 'HOJA DE RUTA', datafield: 'nur', width: '110'},                        
                        {text: 'CITE DOCUMENTO', datafield: 'cite_original', width: '13%'},
                        {text: 'DESTINATARIO', datafield: 'destinatario', width: '17%'},
                        {text: 'REMITENTE', datafield: 'remitente', width: '17%'},
                        {text: 'REFERENCIA', datafield: 'referencia', width: '25%'},
                        {text: 'FECHA', datafield: 'fecha_creacion', width: '8%', cellsformat: 'yyyy-MM-dd H:mm:ss', filtertype: 'date'},
                        //{text: 'OPCIONES', datafield: 'id', width: 50, cellsrenderer: linkrenderer},
                        {text: '<img scr="asd">', datafield: 'link', filtertype: 'none', width: '4%', cellsrenderer: linkrenderer},
                        {text: '<img scr="asd">', datafield: 'edit', filtertype: 'none', width: '4%', cellsrenderer: editrenderer},
                        //{text: 'CREADO POR EL USUARIO', datafield: 'nombre', width: '250'},
                    ]
                });
        $('#quitarFiltro').click(function () {
            // alert('quitar filtro');
            $("#jqxgrid").jqxGrid('clearfilters');
        });
        $('#seguimiento').click(function () {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var nur = dataRecord.nur;
                if (nur != null)
                {
                    var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                    window.open(url, '_blank');
                }
                else
                {
                    alert("el documento no tiene hoja de ruta");
                }

                return false;
            }
            else {
                alert("Seleccione un proceso por favor");
            }
        });
        // $('a.jqxButton').jqxLinkButton({width: '120', height: '30'});


    });
</script>
<style>
    .jqx-grid-statusbar{height: 90px!important;}
    jqx-grid-column-header span{font-weight: bold;}
    .btn-custom{ background-color: #FFFFFF;
                 background-image: -moz-linear-gradient(center top , #FFFFFF, #F9F9F9 48%, #E2E2E2 52%, #E7E7E7);
                 border-radius: 3px;
                 border-style: solid;
                 border-width: 1px;
                 padding: 2px;
                 border-color: #D1D1D1;
                 color: #111;
    }
    .btn-custom:hover{background-color: #C5DBF5; color: #111; border: 1px solid #C5DBF5; }
    .jqx-grid-column-header{z-index: 1 !important;}     
    .jqx-grid-cell{z-index: 1 !important;}
    bodydiv {
        background:white;
        font-family: tahoma,arial,verdana,sans-serif;
        font-size: 10px !important;
        width: 100%;
        margin: 0 auto;
        background: none repeat scroll 0 0 #FAFBFC;
        border: 1px solid #AAAAAA;
        box-shadow: 0 1px 8px rgba(0, 0, 0, 0.25);
        padding: 3px;
    }
    .jqx-grid-cell-custom{
        font-size: 12px !important;
    }


</style>

<h2>Lista de Correspondencia recibida   </h2>

<div class="row">
    <div class="col-md-12">
        <input type="hidden" value="<?php echo $user->id ?>" id="user" />

        <div class=" right" style=" float: left ">
            <span class="text-xl text-warning"><i class="md md-play-circle-outline"></i> </span>Derivar hoja de ruta            
            | <span class="text-xl text-success"><i class="md md-verified-user"></i> </span>Ver Seguimiento
            | <span class="text-xl text-primary-dark"><i class="md md-edit"></i> </span>Editar documento

        </div>  
        <div class=" pull-right ">
            <a class="btn btn-sm btn-default-bright" href="javascript:;" id="quitarFiltro"><i class="fa fa-filter"></i> Quitar Filtro</a>
            <a class="btn btn-sm btn-primary-dark" href="/ventanilla" id="quitarFiltro"><i class="fa fa-plus-circle"></i> Recepcionar Documento</a>

        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">

        <div id="jqxgrid">

        </div>  
    </div>
</div>


