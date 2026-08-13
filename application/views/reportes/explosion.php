<script type="text/javascript">
    $(document).ready(function() {
        // prepare the data
var url='';
        var theme = 'metro';
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
                    ],
                    cache: false,
                    url: '/ajax/explosion',
                    //test
                    excel: function()
                    {
                        // update the grid and send a request to the server.
                         url=$("#jqxgrid").jqxGrid('updatebounddata', 'excel');
                    },
                    filter: function()
                    {
                        // update the grid and send a request to the server.
                        url=$("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                    },
                    sort: function()
                    {
                        // update the grid and send a request to the server.
                        url=$("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                    },
                    root: 'Rows',
                    beforeprocessing: function(data)
                    {
                        if (data != null)
                        {
                            source.totalrecords = data[0].TotalRows;
                        }
                    }
                };
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
                    rendergridrows: function(obj)
                    {
                        return obj.data;
                    },
                    columns: [
                        {text: 'HOJA DE RUTA', datafield: 'nur', width: '5%'},
                        {text: 'CITE DOCUMENTO', datafield: 'cite_original', width: '10%'},
                        {text: 'NOMBRE DESTINATARIO', datafield: 'nombre_destinatario', width: '10%'},
                        {text: 'CARGO DESTINATARIO', datafield: 'cargo_destinatario', width: '10%'},
                        {text: 'INSTITUCION DESTINATARIO', datafield: 'institucion_destinatario', width: '10%'},
                        {text: 'NOMBRE REMITENTE', datafield: 'nombre_remitente', width: '10%'},
                        {text: 'CARGO REMITENTE', datafield: 'cargo_remitente', width: '10%'},
                        {text: 'INSTITUCION REMITENTE', datafield: 'institucion_remitente', width: '10%'},
                        {text: 'REFERENCIA', datafield: 'referencia', width: '15%'},
                        {text: 'FECHA', datafield: 'fecha_creacion', width: '10%', cellsformat: 'yyyy-MM-dd H:m:s', filtertype: 'date'}
                    ]
                });

        $('#quitarFiltro').click(function() {
            // alert('quitar filtro');
            $("#jqxgrid").jqxGrid('clearfilters');
        });
        $('#excel').click(function() {
            alert(url);
            //$("#jqxgrid").jqxGrid('updatebounddata', 'excel');
            //console.log(dataAdapter);  
        });
        // $('a.jqxButton').jqxLinkButton({width: '120', height: '30'});
    });
</script>
</head>
<div class="both" style="position: relative; margin: 5px; right: 5px; display: block; clear: both; height: 35px; ">
    <a href="javascript:void(0)" id="quitarFiltro" class="button" title="Quitar filtro"><img src="/media/jqwidgets/resources/filter_dark.png"/> Quitar Filtro(s)</a>
    <a href="javascript:void(0)" id="excel" class="button" title="Exportar Proyectos a documento Excel"><img src="/media/images/excel.png"/> Exportar a Excel</a>  
</div>
<div style="">
    <div id="jqxgrid"></div>
</div>