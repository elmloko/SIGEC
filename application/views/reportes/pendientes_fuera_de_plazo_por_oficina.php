<script type="text/javascript">
    $(document).ready(function () {
        var theme = 'custom';

        function actualizar() {
            var visibleRows = $('#jqxgrid').jqxGrid('getrows');
            var count = visibleRows.length;
            //suma de uh
            var uh = 0;
            $.each(visibleRows, function (i, e) {
                uh += e.no_recibido;
            })
            var ep = 0;
            $.each(visibleRows, function (i, e) {
                ep += e.pendiente;
            })
            $('#statusbarjqxgrid').html('Total usuarios: <b>' + count + '</b>, No recibidos: <b>' + uh + '</b>, ' + ', Pendientes: <b>' + ep + '</b>, ');
        }

        function actualizar2() {
            var visibleRows = $('#ordersGrid').jqxGrid('getrows');
            var count = visibleRows.length;
            //suma de uh
            $('#statusbarordersGrid').html('Total Pendientes: <b>' + count + '</b>');
        }

        // prepare the data
        var source =
        {
            datatype: "json",
            datafields: [
                {name: 'id', type: 'int'},
                {name: 'oficina'},
                {name: 'nombre'},
                {name: 'cargo'},
                {name: 'no_recibido', type: 'int'},
                {name: 'pendiente', type: 'int'},
                {name: 'suma', type: 'int'},
            ],
            id: 'id',
            url: '/ajax/jsonTotalFueraDePlazoPorOficina'
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        //agrupacion personalizada
        var toThemeProperty = function (className) {
            return className + " " + className + "-" + theme;
        }
        var groupsrenderer = function (text, group, expanded, data) {
            // if (data.groupcolumn.datafield == 'uh_costo' || data.groupcolumn.datafield == 'uh_aprobado') {
            if (data.subItems.length > 0) {
                var aggregate = this.getcolumnaggregateddata('pendiente', ['sum'], true, data.subItems);
                var total = this.getcolumnaggregateddata('no_recibido', ['sum'], true, data.subItems);
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, data.subItems);
            } else {
                var rows = new Array();
                var getRows = function (group, rows) {
                    if (group.subGroups.length > 0) {
                        for (var i = 0; i < group.subGroups.length; i++) {
                            getRows(group.subGroups[i], rows);
                        }
                    } else {
                        for (var i = 0; i < group.subItems.length; i++) {
                            rows.push(group.subItems[i]);
                        }
                    }
                }
                getRows(data, rows);
                var aggregate = this.getcolumnaggregateddata('pendiente', ['sum'], true, rows);
                var total = this.getcolumnaggregateddata('no_recibido', ['sum'], true, rows);
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, rows);
            }
            return '<div class="' + toThemeProperty('jqx-grid-groups-row') + '" style="position: absolute;"><span>' + text + ' (' + suma.sum + ') , </span>' + '<span class="' + toThemeProperty('jqx-grid-groups-row-details') + '">' + " Pendientes: " + '<b>' + aggregate.sum + '</b>' + ", No recibidos : " + ' <b>' + total.sum + '</b>' + '</span></div>';
        }
        var groupsrenderer2 = function (text, group, expanded, data) {
            // if (data.groupcolumn.datafield == 'uh_costo' || data.groupcolumn.datafield == 'uh_aprobado') {
            if (data.subItems.length > 0) {
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, data.subItems);
            } else {
                var rows = new Array();
                var getRows = function (group, rows) {
                    if (group.subGroups.length > 0) {
                        for (var i = 0; i < group.subGroups.length; i++) {
                            getRows(group.subGroups[i], rows);
                        }
                    } else {
                        for (var i = 0; i < group.subItems.length; i++) {
                            rows.push(group.subItems[i]);
                        }
                    }
                }
                getRows(data, rows);
                var suma = this.getcolumnaggregateddata('suma', ['sum'], true, rows);
            }
            return '<div class="' + toThemeProperty('jqx-grid-groups-row') + '" style="position: absolute;"><span>' + text + ' (' + suma.sum + ') </span></div>';
        }
        var barra = function (statusbar) {
        };
        var barra2 = function (statusbar) {
        };
        // Create jqxGrid
        $("#jqxgrid").jqxGrid(
            {
                width: '100%',
                height: 300,
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
                    // {text: 'ID', datafield: 'id', width: '10%', filtertype: 'textbox', filtercondition: 'contains'},
                    {text: 'OFICINA', datafield: 'oficina', width: '34%', filtertype: 'checkedlist'},
                    // {text: 'NOMBRE', datafield: 'nombre', width: '25%', filtertype: 'textbox', filtercondition: 'contains'},
                    // {text: 'CARGO', datafield: 'cargo', width: '25%', filtertype: 'textbox', filtercondition: 'contains'},
                    {text: 'PENDIENTES', datafield: 'pendiente', width: '8%'},
                    {text: 'NO RECIBIDA', datafield: 'no_recibido', width: '8%', filtertype: 'textbox', filtercondition: 'contains'},
                    {text: 'Suma', datafield: 'suma', width: 0, hidden: true}
                ]
                //groups: ['departamento']
            });
        //mostramos el total de proyectos
        $("#jqxgrid").bind("filter", function (event) {
            actualizar();
        });
        $("#jqxgrid").bind("bindingcomplete", function (event) {
            actualizar();
        });
        $("#jqxgrid").bind("rowddclick", function (event) {
            var args = event.args;
            //var row = args.rowindex;
            var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', row);
            var nombre = dataRecord.nombre;
            var pd = dataRecord.pendiente;
            var rc = dataRecord.no_recibido;
            // grafica(nombre, pd, rc);
        });
        // prepare the data
        //dataAdapter.dataBind();
        $("#jqxgrid").bind('rowclick', function (event) {
            var args = event.args;
            var row = args.rowindex;
            var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', row);
            var id = dataRecord.id;

            var nombre = dataRecord.nombre;
            var pd = dataRecord.pendiente;
            var rc = dataRecord.no_recibido;
            //grafica(nombre, pd, rc);

            $('span#titulo').text(dataRecord.oficina);
            var source2 =
            {
                datatype: "json",
                datafields: [
                    {name: 'id'},
                    {name: 'nur'},
                    {name: 'nombre_receptor'},
                    {name: 'cargo_receptor'},
                    {name: 'de_oficina'},
                    {name: 'referencia'},
                    {name: 'gestion'},
                    {name: 'fecha', type: 'date', format: 'dd/MM/yyyy HH:mm:ss'},
                    {name: 'fecha2', type: 'date', format: 'dd/MM/yyyy HH:mm:ss'},
                    {name: 'dias', type: 'int'},
                    {name: 'dias2', type: 'int'},
                    {name: 'dias3', type: 'int'},
                    {name: 'oficial'},
                    {name: 'accion'},
                    {name: 'estado'},
                    {name: 'suma'},
                ],
                // id: 'id',
                url: '/ajax/jsonFueraDePlazoPorOficinaId/' + id,
            };
            var adapter = new $.jqx.dataAdapter(source2);
            // update data source.
            $("#ordersGrid").jqxGrid({source: adapter});
            //$('#ordersGrid').jqxGrid('pincolumn', 'desembolso_nombre');
        });
        $("#ordersGrid").jqxGrid(
            {
                width: '100%',
                height: 300,
                theme: theme,
                filterable: true,
                sortable: true,
                keyboardnavigation: true,
                altrows: true,
                columnsresize: true,
                columnsreorder: true,
                groupable: true,
                groupsrenderer: groupsrenderer2,
                showfilterrow: true,
                showstatusbar: true,
                // statusbarheight: 50,
                renderstatusbar: barra2,
                //showaggregates: true,
                enabletooltips: true,
                columns: [
                    {text: 'HOJA DE RUTA', datafield: 'nur', width: 100,},
                    {text: 'DE OFICINA', datafield: 'de_oficina', width: 200, filtertype: 'checkedlist'},
                    {text: 'NOMBRE RECEPTOR', datafield: 'nombre_receptor', width: 150,},
                    {text: 'CARGO RECEPTOR', datafield: 'cargo_receptor', width: 150,},
                    {text: 'REFERENCIA PROCESO', datafield: 'referencia', width: 250,},
                    {text: 'GESTION', datafield: 'gestion', width: 70,},
                    {
                        text: 'FECHA EMISION',
                        datafield: 'fecha',
                        width: 130,
                        cellsformat: 'dd/MM/yyyy HH:mm:ss',
                        filtertype: 'date'
                    },
                    {
                        text: 'FECHA RECEPCION',
                        datafield: 'fecha2',
                        width: 130,
                        cellsformat: 'dd/MM/yyyy HH:mm:ss',
                        filtertype: 'date'
                    },
                    {
                        text: 'DIAS E->R',
                        datafield: 'dias2',
                        width: 100,
                        cellsrenderer: function (row, datafield, value) {
                            if (parseInt(value) >= 5) {
                                return '<div><div style="display:inline-block; border-radius: 50%/50%; width: 15px; height: 15px; background: black;"></div>' + value + ' días<div/>';
                                //return '<div style="height:25px;background:url(/media/images/amarillo.png) no-repeat;background-size: 20px;background-position:left; text-align:right;"> ' + value + ' días<div/>';
                            } else if (value > 2 && value < 5) {
                                // return '<div style="height:25px;background:url(/media/images/amarillo.png) no-repeat;background-size: 20px;background-position:left; text-align:right;"> ' + value + ' días<div/>';
                            } else if (value > -1 && value <= 2) {
                                // return '<div style="height:25px;background:url(/media/images/verdes.png) no-repeat;background-size: 20px;background-position:left; text-align:right;">' + value + ' días<div/>';
                            } else {
                                // return '<div style="height:25px;background:url("/media/images/verdes.png") no-repeat;background-size: 20px;background-position:left; text-align:right;">' + value + ' días<div/>';
                            }
                        }
                    },
                    /*
                    {
                        text: 'DIAS R->F',
                        datafield: 'dias3',
                        width: 100,
                        cellsrenderer: function (row, datafield, value) {
                            if (value >= 5) {
                                return '<div style="height:25px;background: url(/media/images/rojo.png)no-repeat; background-size: 20px; background-position:left; text-align:right;"> Hace ' + value + ' dias<div/>';
                            } else if (value > 2 && value < 5) {
                                return '<div style="height:25px;background:url(/media/images/amarillo.png) no-repeat;background-size: 20px;background-position:left; text-align:right;"> Hace' + value + ' días<div/>';
                            } else if (value > -1 && value <= 2) {
                                return '<div style="height:25px;background:url(/media/images/verdes.png) no-repeat;background-size: 20px;background-position:left; text-align:right;">Hace ' + value + ' días<div/>';
                            } else {
                                return '<div style="height:25px;background:url("/media/images/verdes.png") no-repeat;background-size: 20px;background-position:left; text-align:right;"> Hace ' + value + ' días<div/>';
                            }

                        }
                    },
                    */
                    {text: 'OFICIAL', datafield: 'oficial', width: 100, filtertype: 'checkedlist'},
                    {text: 'ACCION', datafield: 'accion', width: 120, cellsalign: 'right'},
                    {text: 'ESTADO', datafield: 'estado', width: 100, filtertype: 'checkedlist'},
                    {text: 'Suma', datafield: 'suma', width: 0, hidden: true},
                    {
                        text: 'VER',
                        width: 200,
                        cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {

                            return '<a href="../route/trace/?hr=' + rowdata.nur + '"><img src="../media/images/goto.png"></a>';

                        }
                    }
                ]
            });
        // $("#jqxgrid").jqxGrid('selectrow', 0);
        $("#ordersGrid").bind("bindingcomplete", function (event) {
            actualizar2();
        });
        $("#ordersGrid").bind("filter", function (event) {
            actualizar2();
        });
        Number.prototype.formatMoney = function (c, d, t) {
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

        // expand group.
        $("#expand").bind('click', function () {
            var groupnum = parseInt($("#groupnum").val());
            if (!isNaN(groupnum)) {
                $("#jqxgrid").jqxGrid('expandgroup', groupnum);
            }
        });
        // collapse group.
        $("#collapse").bind('click', function () {
            var groupnum = parseInt($("#groupnum").val());
            if (!isNaN(groupnum)) {
                $("#jqxgrid").jqxGrid('collapsegroup', groupnum);
            }
        });
        // expand all groups.
        $("#expandall").bind('click', function () {
            $("#jqxgrid").jqxGrid('expandallgroups');
        });
        // collapse all groups.
        $("#collapseall").bind('click', function () {
            $("#jqxgrid").jqxGrid('collapseallgroups');
        });
        //exportar a excel
        $("#excelExport").click(function () {
            $("#jqxgrid").jqxGrid('exportdata', 'xls', 'jqxGrid');
        });
        $('#excel').bind('click', function () {
            //cantidad de datos
            var rows = $('#jqxgrid').jqxGrid('getrows');
            var ids = "";
            $.each(rows, function (i, e) {
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
            $.each(columns.records, function (i, e) {
                if (e.datafield != null && e.hidden != true) {
                    columnas += e.datafield + ",";
                    tituls += e.text + ",";
                }
            });
            location.href = "/ajax/excel?datos=" + ids + "&columnas=" + columnas + "&titulos=" + tituls + "&grupo=" + group + "&orden=" + orden;
        });
        $('#quitarFiltro').click(function () {
            $("#jqxgrid").jqxGrid('clearfilters');
        });

        $("#print").click(function () {
            var gridContent = $("#jqxgrid").jqxGrid('exportdata', 'html');
            var newWindow = window.open('', '', 'width=800, height=500'),
                document = newWindow.document.open(),
                pageContent =
                    '<!DOCTYPE html>\n' +
                    '<html>\n' +
                    '<head>\n' +
                    '<meta charset="utf-8" />\n' +
                    '<title>Pendientes</title>\n' +
                    '</head>\n' +
                    '<body>\n' + gridContent + '\n</body>\n</html>';
            document.write(pageContent);
            document.close();
            newWindow.print();
        });
        $("#print2").click(function () {

            var gridContent = $("#ordersGrid").jqxGrid('exportdata', 'html');
            var nombre = $('span#titulo').text();
            var newWindow = window.open('', '', 'width=800, height=500'),
                document = newWindow.document.open(),
                pageContent =
                    '<!DOCTYPE html>\n' +
                    '<html>\n' +
                    '<head>\n' +
                    '<meta charset="utf-8" />\n' +
                    '<title>Pendientes ' + nombre + '</title>\n' +
                    '</head>\n' +
                    '<body>\n<h3>' + nombre + '</h3>\n' +
                    '\n' + gridContent + '\n</body>\n</html>';
            document.write(pageContent);
            document.close();
            newWindow.print();
        });
    });
</script>

<style>
    .jqx-grid-statusbar {
        height: 90px !important;
    }

    .jqx-grid-column-header {
        z-index: 1 !important;
    }

    .jqx-grid-cell {
        z-index: 1 !important;
    }

    #statusbarordersGrid1, #statusbarjqxgrid, #statusbarordersGrid {
        padding: 10px !important;
    }

    jqx-grid-column-header-custom {
        font-weight: 600;
    }
</style>
<div class="row">
    <div class="col-lg-8">
        <span class="text-xl"><i class="fa fa-inbox"></i> Lista de Fuera de Plazo por Oficina</span> Para ver en detalle
        haga doble click en el nombre del funcionario
    </div>
    <div class="col-lg-4">
        <div class=" pull-right">
            <a href="javascript:void(0)" id="print" class=" btn btn-sm btn-primary-dark" title="Quitar filtro"><i
                    class="fa fa-print"></i> Imprimir</a>
            <a href="javascript:void(0)" id="quitarFiltro" class=" btn btn-sm btn-default-light"
               title="Quitar filtro"><i class="fa fa-filter"></i> Quitar Filtro</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">

        <div id="jqxgrid" style=" width: 100%">
        </div>

    </div>
    <!--
    <div class="col-lg-2">
        <div id="jqxChart" style="width:100%; height:302px; position: relative; left: 0px; top: 0px;">
        </div>
    </div>
-->
</div>
<div class="row">
    <hr/>
</div>
<div class="row">
    <div class="col-lg-10"><i class="md md-timer"></i>
        <b>Detalle pendientes usuario : </b><span style="display:none;" id="titulo"></span>
    </div>
    <div class="col-lg-2">
        <div class="pull-right">
            <a href="javascript:void(0)" id="print2" class="btn btn-sm btn-primary-dark" title="Quitar filtro"><i
                    class="fa fa-print"></i> Imprimir</a>
        </div>

    </div>

</div>
<div class="row">
    <div class="col-lg-12">
        <div id="ordersGrid">
        </div>
    </div>
</div>

