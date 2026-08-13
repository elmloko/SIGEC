<script type="text/javascript">
    $(document).ready(function () {
        var oficina = $('#oficina').val();
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
                        {name: 'nombre_destinatario', type: 'string'},
                        {name: 'cargo_destinatario', type: 'string'},
                        {name: 'nombre_remitente', type: 'string'},
                        {name: 'cargo_remitente', type: 'string'},
                        {name: 'referencia', type: 'string'},
                        {name: 'institucion_destinatario', type: 'string'},
                        {name: 'institucion_remitente', type: 'string'},
                        {name: 'fecha_creacion', type: 'date', format: 'yyyy-MM-dd H:mm:ss'},
                        {name: 'nombre', type: 'string'},
                        {name: 'cargo', type: 'string'},
                    ],
                    cache: false,
                    url: '/ajax/jqwdocumentos',
                    data: {
                        oficina: <?php echo $oficina; ?>,
                        tipo: <?php echo $tipo; ?>

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
                    //autorowheight: true,
                    pageable: true,
                    virtualmode: true,
                    //pagesize: 50,
                    enabletooltips: true,
                    theme: 'custom',
                    rendergridrows: function (obj)
                    {
                        return obj.data;
                    },
                    columns: [
                        {text: 'HOJA DE RUTA', datafield: 'nur', width: '90'},
                        {text: 'CITE DOCUMENTO', datafield: 'cite_original', width: '200'},
                        {text: 'NOMBRE DESTINATARIO', datafield: 'nombre_destinatario', width: '180'},
                        {text: 'CARGO DESTINATARIO', datafield: 'cargo_destinatario', width: '180'},
                        {text: 'NOMBRE REMITENTE', datafield: 'nombre_remitente', width: '180'},
                        {text: 'CARGO REMITENTE', datafield: 'cargo_remitente', width: '180'},
                        {text: 'REFERENCIA', datafield: 'referencia', width: '250'},
                        {text: 'FECHA', datafield: 'fecha_creacion', width: '120', cellsformat: 'yyyy-MM-dd H:mm:ss', filtertype: 'date'},
                        {text: 'CREADO POR EL USUARIO', datafield: 'nombre', width: '250'},
                    ],
                    ready: function () {
                        var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;
                        $('#jqxgrid').jqxGrid({pagesizeoptions: ['50', rowscount]});
                    }
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
                    //var url = "https://sigec.localhost/route/trace/?hr=" + nur;
                    var url = "https://sigec.oopp.gob.bo/route/trace/?hr=" + nur;
                    window.open(url, '_blank');
                } else
                {
                    alert("el documento no tiene hoja de ruta");
                }

                return false;
            } else {
                alert("Seleccione un proceso por favor");
            }
        });
        // $('a.jqxButton').jqxLinkButton({width: '120', height: '30'});

        $('#excel').click(function () {
            $("#jqxgrid").jqxGrid('exportdata', 'xls', 'reporte', true, null, true, 'https://www.jqwidgets.com/export_server/save-file.php');
        });
    });
</script>
<style>
    .jqx-grid-column-header{z-index: 1 !important;}     
    .jqx-grid-cell{z-index: 1 !important;}
</style>
<div class="both" style="position: relative; margin: 5px; right: 5px; display: block; clear: both; height: 35px; ">
    <a class="btn btn-sm btn-default-bright" href="javascript:void(0)" id="quitarFiltro"
       title="Quitar Filtro(s)">
        <i class="fa fa-filter"></i> Quitar Filtro(s)
    </a>

    <a class="btn btn-sm btn-info" href="javascript:void(0)" id="seguimiento" title="Ver seguimiento del proceso">
        <i class="fa fa-paw"></i> Ver seguimiento
    </a>  
</div>
<div>
    <h2>
        Documentos generados: <strong><?php echo $tipo_doc->plural; ?></strong>
    </h2>
    <p><hr/></p>
</div>

<?php echo Form::hidden('oficina', $oficina, array('id' => $oficina)); ?>
<div style="">
    <a class="btn btn-sm btn-success" href="javascript:;" id="excel"
       title="Generar Reporte Excel">
        <i class="fa fa-file-excel-o"></i> GENERAR REPORTE
    </a>
    <div id="jqxgrid"></div>
</div>