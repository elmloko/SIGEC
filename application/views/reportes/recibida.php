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
        $("#fecha1,#fecha2").datepicker(pickerOpts, $.datepicker.regional['es']);
    });
</script>
<form action="" method="post">
    <table>
        <tr>
            <td>De: 
                <br/>
                <br/></td>
            <td colspan="3">
                <?php echo Form::select('oficina', $oficinas); ?>
                <br/>
                <?php
                $t = array(3=>"[EXTERNAS E INTERNAS]",1 => "EXTERNAS", 2 => "INTERNAS");
                echo Form::select('tipo', $t);
                ?>
                <br/>
            </td>
        </tr>
        <tr>
            <td>De fecha:</td>
            <td><input type="text" name="fecha1" value="<?php echo date('d/m/Y'); ?>" id="fecha1"/></td>
            <td>A fecha:</td>
            <td><input type="text" name="fecha2" value="<?php echo date('d/m/Y'); ?>" id="fecha2"/></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">
                <hr/><br/>
                <input type="submit" class="button2" name="submit" value="Generar Reporte" title=""/>
            </td>
        </tr>
    </table>    
</form>