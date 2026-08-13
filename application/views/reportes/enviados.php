<script type="text/javascript">
    $(function() {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '&#x3c;Ant',
            nextText: 'Sig&#x3e;',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mi&eacute;', 'Juv', 'Vie', 'S&aacute;b'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'S&aacute;'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
        var pickerOpts = {
            changeMonth: true,
            changeYear: true,
            yearRange: "-10:+1",
            dateFormat: "dd/mm/yy"
        };
        $('select').select2();
        $("#fecha1,#fecha2").datepicker(pickerOpts, $.datepicker.regional['es']);
        $('#hoy').click(function() {
            var f = new Date();
            var mes = (f.getMonth() + 1);
            if (mes < 10)
                mes = '0' + mes;
            var dia = f.getDate();
            if (dia < 10)
                dia = '0' + dia;
            var fecha = dia + "/" + mes + "/" + f.getFullYear();
            $("#fecha1,#fecha2").val(fecha);
        });
    });
</script>
<style>
    fieldset.panel{padding: 10px;}
</style>
<fieldset class="panel">
    <form action="" method="post">
        <table>
            <tr>
                <td>A: 
                    <br/>
                    <br/></td>
                <td colspan="4">
                    <?php echo Form::select('oficina', $oficinas, null, array('style' => 'width:450px;')); ?>
                    <br/>
                    <br/>
                </td>
            </tr>
            <tr>
                <td>De fecha:</td>
                <td><input type="text" name="fecha1" value="01/04/2013" id="fecha1"/></td>
                <td>A fecha:</td>
                <td><input type="text" name="fecha2" value="<?php echo date('d/m/Y'); ?>" id="fecha2"/></td>
                <td><input id="hoy" value="+ Hoy" type="button" class="button"/></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="4">
                    <hr/><br/>
                    <input type="submit" name="submit" class="button2" value="Generar Reporte" title=""/>
                </td>
            </tr>
        </table>    
    </form>
</fieldset>
<?php if (sizeof($enviados) > 0): ?>
    <div class="">
        <br/>
        <a href="/enviados.php?" class="button">Imprimir reporte en PDF</a>
    </div>
    <fieldset>
        <table id="theTable">
            <thead>
                <tr>
                    <th width="10%">HOJA DE RUTA</th>
                    <th width="20%">DERIVADO POR</th>
                    <th width="10%">FECHA DERIVACION</th>
                    <th width="20%">DERIVADO A</th>
                    <th width="10%">FECHA RECEPCION</th>                
                    <th width="30%">PROVEIDO</th>                
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enviados as $e): ?>
                    <tr>
                        <td><?php echo $e['nur'] ?></td>
                        <td><?php echo $e['nombre_emisor'] ?><br/><?php echo $e['cargo_emisor'] ?></td>
                        <td><?php echo $e['fecha_emision'] ?></td>
                        <td><?php echo $e['nombre_receptor'] ?><br/><?php echo $e['cargo_receptor'] ?><br/><?php echo $e['a_oficina']?></td>
                        <td><?php echo $e['fecha_recepcion'] ?></td>
                        <td><?php echo $e['proveido'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
<?php endif; ?>