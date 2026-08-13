<script type="text/javascript">
    $(document).ready(function() {
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
                        {name: 'fecha_creacion', type: 'date', format: 'yyyy-MM-dd H:m:s'},
                        {name: 'estado', type: 'string'},
                        {name: 'nombre_receptor', type: 'string'},
                        {name: 'fecha_recepcion', type: 'string'},
                        {name: 'd1', type: 'string'},
                        {name: 'd2', type: 'string'},
                        {name: 'd3', type: 'string'},
                    ],
                    cache: false,
                    url: '/ajax/explosion',
                    data: {
                        oficina: <?php echo $oficina; ?>
                    },
                    //test
                    excel: function()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'excel');
                    }
                    ,
                    filter: function()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                    }
                    ,
                    sort: function()
                    {
                        // update the grid and send a request to the server.
                        url = $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                    }
                    ,
                    root: 'Rows',
                    beforeprocessing: function(data)
                    {
                        if (data != null)
                        {
                            source.totalrecords = data[0].TotalRows;
                        }
                    }
                }
        ;
        var dataadapter = new $.jqx.dataAdapter(source, {
            loadError: function(xhr, status, error)
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
                    pagesize: 50,
                    enabletooltips: true,
                    theme: 'custom',
                    rendergridrows: function(obj)
                    {
                        return obj.data;
                    },
                    columns: [
                        {text: 'HOJA DE RUTA', datafield: 'nur', width: '79'},
                        {text: 'CITE DOCUMENTO', datafield: 'cite_original', width: '180'},
                        {text: 'NOMBRE DESTINATARIO', datafield: 'nombre_destinatario', width: '150'},
                        {text: 'CARGO DESTINATARIO', datafield: 'cargo_destinatario', width: '150'},
                        {text: 'NOMBRE REMITENTE', datafield: 'nombre_remitente', width: '150'},
                        {text: 'CARGO REMITENTE', datafield: 'cargo_remitente', width: '150'},
                        {text: 'REFERENCIA', datafield: 'referencia', width: '200'},
                        {text: 'FECHA', datafield: 'fecha_creacion', width: '120', cellsformat: 'yyyy-MM-dd H:m:s', filtertype: 'date'},
                        {text: 'DERIVADO A', datafield: 'nombre_receptor', width: '150'},
                        {text: 'FECHA RECEPCION', datafield: 'fecha_recepcion', width: '150'},
                        {text: 'DIAS 1', datafield: 'd1', width: '50'},
                        {text: 'DIAS 2', datafield: 'd2', width: '50'},
                        {text: 'DIAS 3', datafield: 'd3', width: '50'},
                        {text: 'ESTADO', datafield: 'estado', width: '120'}
                    ]
                });

        $('#quitarFiltro').click(function() {
            // alert('quitar filtro');
            $("#jqxgrid").jqxGrid('clearfilters');
        });
        $('#seguimiento').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var nur = dataRecord.nur;
                if (nur != null)
                    //var url = "https://sigec.localhost/route/trace/?hr=" + nur;
                    var url = "https://sigec.oopp.gob.bo/route/trace/?hr=" + nur;
                else
                    alert("el documento no tiene hoja de ruta");
                window.open(url, '_blank');
                return false;
            }
            else {
                alert("Seleccione un proceso por favor");
            }
        });
        // $('a.jqxButton').jqxLinkButton({width: '120', height: '30'});
    });
</script>
<style type="text/css">
    .jqx-grid-column-header{z-index: 1 !important;}
    .jqx-grid-cell{z-index: 1 !important;}
</style>
<div class="both" style="position: relative; margin: 5px; right: 5px; display: block; clear: both; height: 35px; ">
    <a href="javascript:void(0)" id="quitarFiltro" class="button" title="Quitar filtro"><img src="/media/jqwidgets/resources/filter_dark.png"/> Quitar Filtro(s)</a>
    <a href="javascript:void(0)" id="seguimiento" class="button2" title="Ver seguimiento del proceso"> Ver seguimiento</a>  
</div>
<div>
    <h2>
        <b>Dias 1 :</b> Dias transcurridos desde la recepecion de la hoja de ruta
    </h2>
    <h2>
        <b>Dias 2 :</b> Dias transcurridos desde en ultimo envio de la hoja de ruta
    </h2>
    <h2>
        <b>Dias 3 :</b> Dias transcurridos a la fecha desde la creacion de la hoja de ruta
    </h2>
    <p><hr/></p>
</div>

<?php echo Form::hidden('oficina', $oficina, array('id' => $oficina)); ?>

<div style="">
    <div id="jqxgrid"></div>
</div>