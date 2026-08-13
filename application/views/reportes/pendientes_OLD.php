<script type="text/javascript">
    $(document).ready(function() {
        var theme = 'custom';
        var monto_aev = 0;
        function actualizar()
        {
            var visibleRows = $('#jqxgrid').jqxGrid('getrows');
            var count = visibleRows.length;
            //suma de uh
            var uh = 0;
            $.each(visibleRows, function(i, e) {
                uh += e.no_recibido;
            })
            var ep = 0;
            $.each(visibleRows, function(i, e) {
                ep += e.pendiente;
            })

            $('#statusbarjqxgrid').html('Total: <b>' + count + '</b> oficinas, No recibidos: <b>' + uh + '</b>, ' + ', Pendientes: <b>' + ep + '</b>, ');
        }

        // prepare the data
        var source =
                {
                    datatype: "json",
                    datafields: [
                        {name: 'id'},
                        {name: 'oficina'},
                        {name: 'no_recibido', type: 'int'},
                        {name: 'pendiente', type: 'int'},
                    ],
                    id: 'id',
                    url: '/ajax/jsonpendientes',
                    updaterow: function(rowid, rowdata, commit) {
                        // synchronize with the server - send update command
                        var data = "update=true&FirstName=" + rowdata.FirstName + "&LastName=" + rowdata.LastName + "&Title=" + rowdata.Title;
                        data = data + "&Address=" + rowdata.Address + "&City=" + rowdata.City + "&Country=" + rowdata.Country + "&Notes=''";
                        data = data + "&EmployeeID=" + rowdata.EmployeeID;

                        $.ajax({
                            dataType: 'json',
                            url: 'data.php',
                            data: data,
                            success: function(data, status, xhr) {
                                // update command is executed.
                                commit(true);
                            }
                        });
                    }
                };
        var dataAdapter = new $.jqx.dataAdapter(source);
        //agrupacion personalizada
        var toThemeProperty = function(className) {
            return className + " " + className + "-" + theme;
        }
        var groupsrenderer = function(text, group, expanded, data) {
            // if (data.groupcolumn.datafield == 'uh_costo' || data.groupcolumn.datafield == 'uh_aprobado') {
            if (data.subItems.length > 0) {
                var aggregate = this.getcolumnaggregateddata('uh_ejecucion', ['sum'], true, data.subItems);
                var total = this.getcolumnaggregateddata('monto_fin_aev', ['sum'], true, data.subItems);
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, data.subItems);
            }
            else {
                var rows = new Array();
                var getRows = function(group, rows) {
                    if (group.subGroups.length > 0) {
                        for (var i = 0; i < group.subGroups.length; i++) {
                            getRows(group.subGroups[i], rows);
                        }
                    }
                    else {
                        for (var i = 0; i < group.subItems.length; i++) {
                            rows.push(group.subItems[i]);
                        }
                    }
                }
                getRows(data, rows);
                var aggregate = this.getcolumnaggregateddata('uh_ejecucion', ['sum'], true, rows);
                var total = this.getcolumnaggregateddata('monto_fin_aev', ['sum'], true, rows);
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, rows);
            }
            return '<div class="' + toThemeProperty('jqx-grid-groups-row') + '" style="position: absolute;"><span>' + text + ' (' + suma.sum + ') , </span>' + '<span class="' + toThemeProperty('jqx-grid-groups-row-details') + '">' + " UH: " + '<b>' + aggregate.sum + '</b>' + ", Costo Total : " + ' <b>' + total.sum + '</b>' + '</span></div>';
            // }
            //  else {
            //     return '<div class="' + toThemeProperty('jqx-grid-groups-row') + '" style="position: absolute;"><span>' + text + '</span>';
            //  }
        }
        var barra = function(statusbar) {
        };
        // Create jqxGrid
        $("#jqxgrid").jqxGrid(
                {
                    width: '100%',
                    height: 260,
                    source: dataAdapter,
                    filterable: true,
                    sortable: true,
                    groupable: true,
                    altrows: true,
                    groupsrenderer: groupsrenderer,
                    showstatusbar: true,
                    columnsresize: true,
                    columnsreorder: true,
                    keyboardnavigation: true,
                    theme: theme,
                    showfilterrow: true,
                    renderstatusbar: barra,
                    enabletooltips: true,
                    //update: false,
                    columns: [
//                        {text: 'CUCE SICOES', datafield: 'cuce_proyecto', width: '5%', filtertype: 'textbox', filtercondition: 'contains'},  
                        {text: 'ID', datafield: 'id', width: '10%', filtertype: 'textbox', filtercondition: 'contains'},
                        {text: 'OFICINA', datafield: 'oficina', width: '70%', filtertype: 'textbox', filtercondition: 'contains'},
                        {text: 'NO RECIBIDA', datafield: 'no_recibido', width: '10%', filtertype: 'textbox', filtercondition: 'contains'},
                        {text: 'PENDIENTES', datafield: 'pendiente', width: '10%'},
                        {text: 'Suma', datafield: 'suma', width: 1, hidden: true}
                    ],
                    //groups: ['departamento']
                });
        //mostramos el total de proyectos
        $("#jqxgrid").bind("filter", function(event) {
            actualizar();
        });
        $("#jqxgrid").bind("bindingcomplete", function(event) {
            actualizar();

        });
        // prepare the data          
        //dataAdapter.dataBind();
        $("#jqxgrid").bind('rowdoubleclick', function(event) {
            var args = event.args;
            var row = args.rowindex;
            var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', row);
            var id = dataRecord.id;
            //var monto_obra = ;
            //var monto_sup = ;
            monto_aev = parseFloat(dataRecord.monto_fin_aev).toFixed(2);
            monto_aev = formatNumber(monto_aev);
            $('span#titulo').text(dataRecord.proyecto_nombre);
            var source2 =
                    {
                        datatype: "json",
                        datafields: [
                            {name: 'id'},
                            {name: 'nombre'},
                            {name: 'cargo'},
                            {name: 'pendiente', type: 'int'},
                            {name: 'no_recibido', type: 'int'},
                            {name: 'ultimo_ingreso', type: 'date'},
                        ],
                        id: 'id',
                        url: '/ajax/jsonpusers/' + id,
                    };
            var adapter = new $.jqx.dataAdapter(source2);
            // update data source.
            $("#ordersGrid").jqxGrid({source: adapter});
            //$('#ordersGrid').jqxGrid('pincolumn', 'desembolso_nombre');

            var source3 =
                    {
                        datatype: "json",
                        datafields: [
                            {name: 'id'},
                            {name: 'desembolso_nombre'},
                            {name: 'fecha_desembolso'},
                            {name: 'monto_desembolso', type: 'int'},
                            {name: 'cite_informe_desembolso'},
                            {name: 'fecha_informe_sol'},
                            {name: 'hoja_ruta'},
                        ],
                        id: 'id',
                        url: '/ajax/desembolsos2/' + id,
                    };
            var adapter2 = new $.jqx.dataAdapter(source3);
            // update data source.
            $("#ordersGrid2").jqxGrid({source: adapter2});
            //$('#ordersGrid').jqxGrid('pincolumn', 'desembolso_nombre');
        });
        $("#ordersGrid").jqxGrid(
                {
                    width: '100%',
                    height: 240,
                    theme: theme,
                    filterable: true,
                    sortable: true,
                    keyboardnavigation: true,
                   // altrows: true,
                    groupsrenderer: groupsrenderer,
                    showfilterrow: true,
                    showstatusbar: true,
                    statusbarheight: 50,
                    showaggregates: true,
                    altrows: true,
                            columns: [
                                {text: 'NOMBRE', datafield: 'nombre', width: '30%', },
                                {text: 'CARGO.', datafield: 'cargo', width: '35%', },
                                {text: 'No recibido', datafield: 'no_recibido', width: '10%', aggregates:
                                            [{'<b>Total </b>':
                                                            function(aggregatedValue, currentValue, column, record) {
                                                                return aggregatedValue + currentValue;
                                                            }
                                                }]},
                                {text: 'Pendientes', datafield: 'pendiente', width: '10%', aggregates:
                                            [{'<b>Total </b>':
                                                            function(aggregatedValue, currentValue, column, record) {
                                                                return aggregatedValue + currentValue;
                                                            }
                                                }]},
                                {text: 'Ultimmo Ingreso', datafield: 'ultimo_ingreso', width: '15%', cellsalign: 'right'},
                            ]
                });
        // $("#jqxgrid").jqxGrid('selectrow', 0);       
        $("#ordersGrid").bind("bindingcomplete", function(event) {
            var visibleRows = $('#ordersGrid').jqxGrid('getrows');
            var monto = 0;
            $.each(visibleRows, function(i, e) {
                monto += e.monto_desembolso;
            })
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var monto_contruccion = dataRecord.monto_fin_aev;
                var resto = monto_contruccion - monto;
                resto = parseFloat(resto).toFixed(2);
            }
            // $('#statusbarordersGrid').html('Monto Contrato: <b>Bs' + formatNumber(monto_contruccion)+'</b> / Desembolsado: <b> Bs' + formatNumber(monto) + '</b> /  Monto adeudado <b>Bs' + formatNumber(resto));
            // $('#statusbarordersGrid').html('Monto Contrato: <b>Bs' + formatNumber(monto_contruccion));

        });
        $("#ordersGrid2").jqxGrid(
                {
                    width: '100%',
                    height: 250,
                    theme: theme,
                    keyboardnavigation: true,
                    showstatusbar: true,
                   // altrows: true,
                    columns: [
                        {text: 'Planilla', datafield: 'desembolso_nombre', width: '33%'},
                        {text: 'Fecha', datafield: 'fecha_desembolso', width: '33%', cellsalign: 'right'},
                        {text: 'Monto desembolsado', datafield: 'monto_desembolso', cellsformat: 'c2', width: '33%', cellsalign: 'right'},
                    ]
                });
        $("#ordersGrid2").bind("bindingcomplete", function(event) {
            var visibleRows2 = $('#ordersGrid2').jqxGrid('getrows');
            var monto2 = 0;
            $.each(visibleRows2, function(i, e) {
                monto2 += e.monto_desembolso;
            })
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var monto_supervision = dataRecord.monto_contrato_sup;
                var resto = monto_supervision - monto2;
                resto = parseFloat(resto).toFixed(2);
            }
            $('#statusbarordersGrid2').html('Monto Contrato: <b>Bs' + formatNumber(monto_supervision) + '</b> / Desembolsado: <b> Bs' + formatNumber(monto2) + '</b> /  Monto adeudado <b>Bs' + formatNumber(resto));


        });
        Number.prototype.formatMoney = function(c, d, t) {
            var n = this,
                    c = isNaN(c = Math.abs(c)) ? 2 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };
        function formatNumber(num, prefix) {
            prefix = prefix || '';
            num += '';
            var splitStr = num.split(".");
            var splitLeft = splitStr[0];
            var splitRight = splitStr.length > 1 ? "." + splitStr[1] : '';
            var regx = /(\d+)(\d{3})/;
            while (regx.test(splitLeft)) {
                splitLeft = splitLeft.replace(regx, "$1" + "," + "$2");
            }
            return prefix + splitLeft + splitRight;
        }
        $("#expand").jqxButton({theme: theme});
        $("#collapse").jqxButton({theme: theme});
        $("#expandall").jqxButton({theme: theme});
        $("#collapseall").jqxButton({theme: theme});

        // expand group.
        $("#expand").bind('click', function() {
            var groupnum = parseInt($("#groupnum").val());
            if (!isNaN(groupnum)) {
                $("#jqxgrid").jqxGrid('expandgroup', groupnum);
            }
        });
        // collapse group.
        $("#collapse").bind('click', function() {
            var groupnum = parseInt($("#groupnum").val());
            if (!isNaN(groupnum)) {
                $("#jqxgrid").jqxGrid('collapsegroup', groupnum);
            }
        });

        // expand all groups.
        $("#expandall").bind('click', function() {
            $("#jqxgrid").jqxGrid('expandallgroups');
        });

        // collapse all groups.
        $("#collapseall").bind('click', function() {
            $("#jqxgrid").jqxGrid('collapseallgroups');
        });

        // trigger expand and collapse events.
        $("#jqxgrid").bind('groupexpand', function(event) {
            var args = event.args;
            $("#expandedgroup").html("Group: " + args.group + ", Level: " + args.level);
        });

        $("#jqxgrid").bind('groupcollapse', function(event) {
            var args = event.args;
            $("#collapsedgroup").html("Group: " + args.group + ", Level: " + args.level);
        });
        //exportar a excel
        $("#excelExport").click(function() {
            $("#jqxgrid").jqxGrid('exportdata', 'xls', 'jqxGrid');
        });

        $('#print').click(function() {
            var group = $('#jqxgrid').jqxGrid('groups');
            console.log(group);
            var orden = $('#jqxgrid').jqxGrid('sortcolumn');
            console.log(orden);
            var dir = false;
            if (orden) {
                dir = $('#jqxgrid').jqxGrid('sortdirection');
                console.log(dir.ascending);
            }
            var columnas = [];
            var titulos = [];
            var columns = $('#jqxgrid').jqxGrid('columns');
            $.each(columns.records, function(i, e) {
                if (e.datafield != null && e.hidden != true)
                {
                    columnas.push(e.datafield);
                    titulos.push(e.text);
                }
            });
            console.log(columnas);
            //generamos el reporte
            var filtros = [];
            var filtro = $('#jqxgrid').jqxGrid('getfilterinformation');
            $.each(filtro, function(i, e) {
                filtros.push(e.filtercolumn);
                alert(e.filter.operator);
            });
            var datainformation = $('#jqxgrid').jqxGrid('updatebounddata');
            dataAdapter.dataBind();
            console.log(dataAdapter._options);


            //  location.href="/excel/reporte/?columnas[]="+columnas+'&grupo='+group+'&orden='+orden+'&dir='+dir+'&titulos='+titulos;
        });

        $('#modificar').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var left = screen.availWidth;
                var top = screen.availHeight;
                left = (left - 800) / 2;
                top = (top - 500) / 2;
                var res = window.showModalDialog("/content/editarProyecto/" + dataRecord.id, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
                if (res != null)
                {
                    $("#myGrid").addClass('loading');
                    $.ajax({
                        type: "POST",
                        data: {estado: res, id: grid.getDataItem(selectedIndexes).id},
                        url: "/ajax/cambiarEstado",
                        success: function(data)
                        {
                            $('#jqxgrid').jqxGrid('updatebounddata');
                        }
                    });
                }
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        $('#afisico').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                location.href = "/proyecto/afisico/" + dataRecord.id;
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        $('#editar').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                location.href = "/proyecto/edit/" + dataRecord.id;
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        $('#etapa_old').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var left = screen.availWidth;
                var top = screen.availHeight;
                left = (left - 800) / 2;
                top = (top - 500) / 2;
                var res = window.showModalDialog("/content/estados/" + dataRecord.id, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
                if (res != null)
                {
                    $("#myGrid").addClass('loading');
                    $.ajax({
                        type: "POST",
                        data: {estado: res, id: dataRecord.id},
                        url: "/ajax/cambiarEstado",
                        success: function(data)
                        {
                            $('#jqxgrid').jqxGrid('updatebounddata');
                        }
                    });
                }
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        $('#etapa').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                $('#jqxwindow').jqxWindow({title: dataRecord.estado});
                $('#jqxwindow').jqxWindow('open');
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });

        $('#desembolsosss').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var left = screen.availWidth;
                var top = screen.availHeight;
                left = (left - 800) / 2;
                top = (top - 500) / 2;
                var res = window.showModalDialog("/content/desembolzar/" + dataRecord.id, "", "center:0;dialogWidth:750px;dialogHeight:450px;scroll=yes;resizable=yes;status=yes;" + "dialogLeft:" + left + "px;dialogTop:" + top + "px");
                if (res != null)
                {
                    $("#myGrid").addClass('loading');
                    $.ajax({
                        type: "POST",
                        data: {estado: res, id: dataRecord.id},
                        url: "/ajax/cambiarEstado",
                        success: function(data)
                        {
                            $('#jqxgrid').jqxGrid('updatebounddata');
                        }
                    });
                }
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        //detalle 
        $('#jqxgridd').bind('rowdoubleclick', function(event)
        {
            var args = event.args;
            var row = args.rowindex;
            var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', row);
            // $('#detalleProyecto').html('<img src="/media/jqwidgets/styles/images/ajax-loader.gif" />'); 
            $('#jqxwindow').jqxWindow({title: dataRecord.proyecto_nombre});
            var monto_construccion = 0;
            var monto_supervision = 0;
            //$('#jqxwindow').jqxWindow('open');
            $.ajax({
                type: "POST",
                data: {id: dataRecord.id},
                url: "/ajax/detalleLicitacion",
                success: function(data)
                {
                    //  $('#detalleProyecto').html(data); 

                }
            });

        });
        //
        $("#jqxwindow").jqxWindow(
                {height: 300,
                    width: 400,
                    autoOpen: false,
                    isModal: true
                }
        );
        $('#excel').bind('click', function() {
            //cantidad de datos
            var rows = $('#jqxgrid').jqxGrid('getrows');
            var ids = "";
            $.each(rows, function(i, e) {
                ids += e.id + ",";
            });
            var group = $('#jqxgrid').jqxGrid('groups');

            var orden = $('#jqxgrid').jqxGrid('sortcolumn');
            var dir = false;
            if (orden) {
                dir = $('#jqxgrid').jqxGrid('sortdirection');
            }
            var columnas = "";
            var tituls = "";
            var columns = $('#jqxgrid').jqxGrid('columns');
            $.each(columns.records, function(i, e) {
                if (e.datafield != null && e.hidden != true)
                {
                    columnas += e.datafield + ",";
                    tituls += e.text + ",";
                }
            });

            location.href = "/ajax/excel?datos=" + ids + "&columnas=" + columnas + "&titulos=" + tituls + "&grupo=" + group + "&orden=" + orden;

        });
        $('#quitarFiltro').click(function() {
            $("#jqxgrid").jqxGrid('clearfilters');
        });
        $('#sicoes').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                var cuce = dataRecord.cuce_proyecto.split('-');
                if (cuce[3] != null)
                    var url = "http://www.sicoes.gob.bo/contrat/procesos.php?form[txtCUCE4]=" + cuce[3] + "&form[rdVigentes]=%";
                else
                    var url = "http://www.sicoes.gob.bo/contrat/procesos.php?form[txtEntidad]=AGENCIA%20ESTATAL%20DE%20VIVIENDA&form[txtCUCE4]=%";
                window.open(url, '_blank');
                return false;
            }
            else {
                alert("Seleccione un proyecto por favor");
            }
        });
        $('#pdf').bind('click', function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {

                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                location.href = "/reporte_pdf.php/?id=" + dataRecord.id;

            }
            else
            {
                alert("seleccione un proyecto por favor");
            }
        });
        $('#desembolso').click(function() {
            var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            if (rowindex > -1)
            {
                var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', rowindex);
                location.href = "/monitoreo/desembolsos/" + dataRecord.id;
                //window.open("/monitoreo/desembolsos/" + dataRecord.id, '_blank');
                return false;
            }
            else
            {
                alert("Seleccione un proyecto por favor");
            }
        });
    });
</script>

<style>
    .jqx-grid-statusbar{height: 90px!important;}
    .jqx-grid-column-header{z-index: 1 !important;}     
    .jqx-grid-cell{z-index: 1 !important;}
    #statusbarordersGrid1,#statusbarjqxgrid,#statusbarordersGrid2{padding: 10px !important;}
</style>
</style>

<div class="row-fluid">

    <div style="text-align: right; width: 100%; display: block; height: 20px; padding: 5px; ">                      
        <a href="javascript:void(0)" id="pdf" class="btn btn-mini" title="Imprimir reporte"><img src="/media/images/pdf.png"/> Imprimir</a>        
        <a href="javascript:void(0)" id="quitarFiltro" class="btn btn-mini" title="Quitar filtro"><img src="/media/jqwidgets/resources/filter_dark.png"/> Quitar Filtro</a>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div id="jqxgrid" style="">
        </div>
    </div>
</div>
<h5><br/><b>Desembolsos : </b><span id="titulo"></span></h5>
<div class="row-fluid">    
    <div class="span7">
        <div id="ordersGrid">
        </div>
    </div>
    <div class="span5">
        <div id="ordersGrid2">
        </div>
    </div>    
</div>
