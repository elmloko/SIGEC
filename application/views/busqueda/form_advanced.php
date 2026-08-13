<script>
    $(function () {
        $('select').select2();
        //$('#demo-date').datepicker({autoclose: true, todayHighlight: true});
        //$('#demo-date-month').datepicker({autoclose: true, todayHighlight: true, minViewMode: 1});
        //$('#demo-date-format').datepicker({autoclose: true, todayHighlight: true, format: "yyyy/mm/dd"});
        $('#demo-date-range').datepicker({todayHighlight: true, format: "dd-mm-yyyy"});
        //$('#demo-date-inline').datepicker({todayHighlight: true});
        
        
     
    });

</script>

<div class="row">
    <div class="col-md-12">
        <div class="card card-underline">
            <div class="card-head">
                <header>Busqueda Avanzada</header>
            </div>
            <div class="card-body">
                <form class="form " action="" id="form-search" method="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('nur', Arr::get($_GET, 'nur', ''), array('class' => 'form-control')); ?>
                                <label for='nur'>Hoja de Ruta</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('cite_original', Arr::get($_GET, 'cite_original', ''), array('class' => 'form-control')); ?>
                                <label for='cite_original'>Cite Documento</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::select('tipo', $tipos, Arr::get($_GET, 'tipo', 0), array('class' => 'form-control')) ?>

                                <label for='cite_original'>Tipo Documento</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('referencia', Arr::get($_GET, 'referencia', ''), array('class' => 'form-control')); ?>
                                <label for='nur'>Referencia</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('destinatario', Arr::get($_GET, 'destinatario', ''), array('class' => 'form-control')); ?>
                                <label for='cite_original'>Destinatario</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('remitente', Arr::get($_GET, 'remitente', ''), array('class' => 'form-control')); ?>
                                <label for='cite_original'>Remitente</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <label>Rango de Fechas</label>
                            <div class="input-daterange input-group" id="demo-date-range">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <div class="input-group-content">
                                    <input type="text" class="form-control" name="start" placeholder="dd-mm-aaaa" value="<?php echo Arr::get($_GET, 'start', '') ?>" />

                                </div>
                                <span class="input-group-addon">A</span>
                                <div class="input-group-content">
                                    <input type="text" class="form-control" name="end" placeholder="dd-mm-aaaa" value="<?php echo Arr::get($_GET, 'end', '') ?>" />

                                    <div class="form-control-line"></div>
                                </div>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::input('entidad', Arr::get($_GET, 'entidad', ''), array('class' => 'form-control')); ?>
                                <label for='entidad'>Entidad</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo Form::submit('buscar', 'Buscar', array('class' => 'btn btn-primary-dark')) ?>                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>    
    </div>    
</div>

<?php if (!empty($mensajes)): ?>
    <?php foreach ($mensajes as $m): ?>
        <div class="alert alert-info"><?php echo $m; ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="#" id="boton-exportar-excel" class="btn btn-primary-dark"
   onClick="fnExcelReport('#tblExport','#boton-exportar-excel');">Exportar</a>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-head">
                <header>Resultados: <?php echo $count?></header>
            </div> 
            <div class="card">
                <div class="table-responsive">
                   <table id="" class="table table-bordered no-padding table-striped text-sm ">
                            <thead>
                                <tr>
                                    <th width="150">Entidad</th>
                                    <th width="150">Hoja de ruta</th>            
                                    <th width="160">Documento</th>            
                                    <th width="120">Tipo Doc</th>            
                                    <th width="285">Destinatario</th>
                                    <th width="285">Remitente</th>
                                    <th width="300">Referencia</th>    
                                    <th width="100">Fecha</th>    
                                    <th>Acción</th>    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $mes = 0;
                                $dia = 0;
                                $meses = array(1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic');
                                //    $meses=array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
                                // $dias=array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>'Domingo');
                                $name='';
                                foreach ($result as $d):
                                    ?>
                                    <tr>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['institucion_remitente']); ?></b></td>
                                        <td class="codigo" align="center">
                                            <a href="/route/trace/?hr=<?php echo $d['nur']; ?>"><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nur']); ?>
                                            </a> 
                                        </td>
                                        <td class="codigo" align="center">
                                            <a href="/document/detail/<?php echo $d['id']; ?>" ><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cite_original']); ?></a>               
                                        </td>
                                        <td align="center">
                                            <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['tipo']); ?></b>
                                        </td>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_destinatario']); ?></b>
                                            <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_destinatario']); ?></td>
                                        <td ><b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_remitente']); ?></b>
                                            <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_remitente']); ?></td>
                                        <td ><?php $ref=$_GET['referencia']; echo str_replace($ref, "<span class='text-success'>$ref</span>", $d['referencia']); ?></td>           
                                        <td ><?php echo $d['fecha_creacion']; ?></td>           
                                        <td ><?php if ($d['estado'] == 0 && $d['original'] == 1): ?>
                                                No Derivado          
                                            <?php else: ?>
                                                <a href="/route/trace/?hr=<?php echo $d['nur']; ?>" title="Ver seguimiento" class="text-success text-xl" ><i class="md md- md-verified-user "></i></a>           
                                            <?php endif; ?>                                        
                                        </td>
                                    </tr>        
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9"><?php echo $page_links; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- [INICIO] EXPORTAR A EXCEL LA TABLA -->
<table id="tblExport" style="display: none;" class="table table-bordered no-padding table-striped text-sm ">
    <thead>
    <tr>
        <th width="150">Entidad</th>
        <th width="150">Hoja de ruta</th>
        <th width="160">Documento</th>
        <th width="120">Tipo Doc</th>
        <th width="285">Destinatario</th>
        <th width="285">Remitente</th>
        <th width="300">Referencia</th>
        <th width="100">Fecha</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $mes = 0;
    $dia = 0;
    $meses = array(1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic');
    //    $meses=array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
    // $dias=array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>'Domingo');
    $name = '';
    foreach ($result_all as $d):
        ?>
        <tr>
            <td>
                <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['institucion_remitente']); ?></b>
            </td>
            <td class="codigo" align="center">
                <?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nur']); ?>
            </td>
            <td class="codigo" align="center">
                <?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cite_original']); ?>
            </td>
            <td align="center">
                <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['tipo']); ?></b>
            </td>
            <td>
                <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_destinatario']); ?></b>
                <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_destinatario']); ?>
            </td>
            <td>
                <b><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['nombre_remitente']); ?></b>
                <br/><?php echo str_replace($name, "<span class='resultado'>$name</span>", $d['cargo_remitente']); ?>
            </td>
            <td><?php $ref = $_GET['referencia'];
                echo str_replace($ref, "<span class='text-success'>$ref</span>", $d['referencia']); ?>
            </td>
            <td><?php echo $d['fecha_creacion']; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="9"><?php echo $page_links; ?></td>
    </tr>
    </tfoot>
</table>

<script>
    function fnExcelReport(tableID, buttonID) {
        var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        tab_text = tab_text + '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
        tab_text = tab_text + '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';

        tab_text = tab_text + '<x:Name>Test Sheet</x:Name>';

        tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
        tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';

        tab_text = tab_text + "<table border='1px'>";
        tab_text = tab_text + $(tableID).html();
        tab_text = tab_text + '</table></body></html>';

        var data_type = 'data:application/vnd.ms-excel';

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
            if (window.navigator.msSaveBlob) {
                var blob = new Blob([tab_text], {
                    type: "application/csv;charset=utf-8;"
                });
                navigator.msSaveBlob(blob, 'reporte.xls');
            }
        } else {
            $(buttonID).attr('href', data_type + ', ' + encodeURIComponent(tab_text));
            $(buttonID).attr('download', 'reporte.xls');
        }
    }
</script>
<!-- [FIN] EXPORTAR A EXCEL LA TABLA -->
