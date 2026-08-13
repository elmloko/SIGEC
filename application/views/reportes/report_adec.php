<script type="text/javascript">
    $(function(){
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '&#x3c;Ant',
            nextText: 'Sig&#x3e;',
            currentText: 'Hoy',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
            dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
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
        dateFormat:"yy-mm-dd"
        };
      $("#fecha1,#fecha2").datepicker(pickerOpts,$.datepicker.regional['es']);        
    });
</script>

<form action="" method="post">
    <table>
        <tr>
            <td>Usuario/todos</td>
            <td colspan="3">
               <?php echo Form::select('u',$u);?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">
                <hr/><br/>
                <input type="submit" name="submit" class="button2" value="Generar Reporte" />
            </td>
        </tr>
    </table>
    
</form>