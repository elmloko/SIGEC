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
                        {name: 'id', type: 'int'},
                        {name: 'username', type: 'string'},
                        {name: 'nombre', type: 'string'},
                        {name: 'cargo', type: 'string'},
                        {name: 'accion_realizada', type: 'string'},
                        {name: 'ip_usuario', type: 'string'},
                        {name: 'fecha_hora', type: 'date', format: 'yyyy-MM-dd H:mm:ss'},
                        {name: 'habilitado', type: 'string'},                        
                    ],
                    cache: false,
                    url: '/ajax/vitacora',
                    data: {                       
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
                        {text: 'USERNAME', datafield: 'username', width: '10%'},
                        {text: 'NOMBRE', datafield: 'nombre', width: '15%'},
                        {text: 'CARGO', datafield: 'cargo', width: '15%'},
                        {text: 'ACCION REALIZADA', datafield: 'accion_realizada', width: '40%'},                        
                        {text: 'IP', datafield: 'ip_usuario', width: '5%'},
                        {text: 'FECHA', datafield: 'fecha_hora', width: '10%', cellsformat: 'yyyy-MM-dd H:mm:ss', filtertype: 'date'},
                        {text: 'USUARIO ACTIVO', datafield: 'habilitado', width: '5%'},
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
                {
                    //var url = "http://sigec.localhost/route/trace/?hr=" + nur;
                    var url = "https://sigec.oopp.gob.bo/route/trace/?hr=" + nur;
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
    .jqx-grid-column-header{z-index: 1 !important;}     
    .jqx-grid-cell{z-index: 1 !important;}
</style>
<div class="both" style="position: relative; margin: 5px; right: 5px; display: block; clear: both; height: 35px; ">
    <a href="javascript:void(0)" id="quitarFiltro" class="button" title="Quitar filtro"><img src="/media/images/filter_dark.png"/> Quitar Filtro(s)</a>
    <a href="javascript:void(0)" id="seguimiento" class="button2" title="Ver seguimiento del proceso"> Ver seguimiento</a>  
</div>
<div>
    <h2>
        Vitacora: <strong> Sistema de Correspondencia</strong>
    </h2>
    <p><hr/></p>
</div>

<div style="">
    <div id="jqxgrid"></div>
</div>