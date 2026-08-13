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
//$('.descripcion2').redactor({lang : 'es'});
//incluir destinatario
$('a.destino').click(function(){
    var nombre=$(this).attr('nombre');   
    var cargo=$(this).attr('cargo');   
    var via=$(this).attr('via');   
    var cargo_via=$(this).attr('cargo_via');   
    $('#destinatario').val(nombre);
    $('#cargo_des').val(cargo);
    $('#via').val(via);
    $('#cargovia').val(cargo_via);
    $('#referencia').focus();
    return false;
    });
$('#btnword').click(function(){
        $('#word').val(1);
        return true
    });
$('#crear').click(function(){
        $('#word').val(0);
        return true
    });
});
</script>

<div class="formulario">
    <form action="/documento/generar/<?php echo $documento->action;?>" method="post" id="frmCreate">
<table width="100%">        
<tr>
<td  valign="top" style=" padding-left: 1px;" rowspan="2">
<input type="hidden" name="proceso" value="1"/>    
<input type="hidden" name="titulo" />   
<input type="hidden" name="tipo_viaje" />   
<p>
<?php
echo Form::label('destinatario', 'Nombre del destinatario:',array('class'=>'form'));
echo Form::input('destinatario',Arr::get($doc, 'nombre_destinatario',''),array('id'=>'destinatario','size'=>48,'class'=>'required'));
?>
</p>
<p>
<?php
echo Form::label('destinatario', 'Cargo Destinatario:',array('class'=>'form'));
echo Form::input('cargo_des',Arr::get($doc, 'cargo_destinatario',''),array('id'=>'cargo_des','size'=>48,'class'=>'required'));
?>
</p> 
<input type="hidden" name="institucion_des" />   
<input type="hidden" name="via" />   
<input type="hidden" name="cargovia" /> 
<input type="hidden" name="adjuntos" /> 
<b>1.- DATOS DE FUNCIONARIO</b>
<p>
<?php
   echo Form::label('remitente', 'Remitente:',array('class'=>'form'));
   echo Form::input('remitente',Arr::get($doc, 'nombre_remitente',$user->nombre),array('id'=>'remitente','size'=>55,'class'=>'required'));            
?> Cedula Identidad:  
<input type="text" name="cedula_identidad" value="<?php echo $user->cedula_identidad.' '.$user->expedido;?>"/>
<?php
   //echo Form::label('mosca','Mosca:');
   echo Form::hidden('mosca',$user->mosca,array('id'=>'mosca','size'=>5));
?>
<?php
   echo Form::label('cargo', 'Cargo Remitente:',array('class'=>'form'));
   echo Form::input('cargo_rem',Arr::get($doc, 'cargo_remitente',$user->cargo),array('id'=>'cargo_rem','size'=>55,'class'=>'required'));
?>

<?php
            echo Form::label('copias', 'Con copia a:',array('class'=>'form'));
            echo Form::input('copias','',array('id'=>'adjuntos','size'=>55,'class'=>'required'));
            ?>
</p>
<b>2.- EVENTO</b>
<p>
<?php
    echo Form::label('motivo', 'EVENTO, ACTIVIDAD CENTRAL O MOTIVO DEL VIAJE REALIZADO',array('class'=>'form'));
    echo Form::textarea('referencia',Arr::get($doc,'referencia',''),array('id'=>'referencia','rows'=>2,'class'=>'required'));
?>
</p>
<p>
<?php
    echo Form::label('destinatario', 'Principales actividades realizadas ( Realice un detalle cronologico por dia de ser necesario adjuntar informe adjunto):',array('class'=>'form'));
    echo Form::textarea('descripcion','',array('id'=>'descripcion','rows'=>4,'class'=>'required'));
?>
</p>   
<p>
<?php
    echo Form::label('destinatario', 'Informe Técnico de Justificacion de Convalidación de viaje (Llenar si corresponde. Señalar las razones y motivos por los que no efectuó la solicitud de resolucion en forma previa a la realización del viaje y en el plazo establecido).',array('class'=>'form'));
    echo Form::textarea('no_descripcion','',array('id'=>'no_descripcion','rows'=>4,'class'=>'required'));
?>
</p>      

</td>
<td valign="top">
    <br/>
    <input type="submit" class="button2" name="submit" value="Generar Documento"/>
    <a href="" class="button" onclick="javascript:history.back(); return false;" >Cancelar</a>
    <hr/>  
    <b>3.- RESUMEN ADMINISTRATIVO</b>
    <label>COMISION A (Lugar) :</label>
   <input type="text" name="lugar" id="lugar" value="<?php echo Arr::get($solicitud,'lugar',''); ?>" style="width: 400px; " />
    <label>Duración:</label>        
FECHA SALIDA :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fecha_salida" id="fecha1" size="9                                                                                      " value="<?php echo date('Y-m-d',strtotime(Arr::get($solicitud, 'fecha_salida','')))?>" />
HORA SALIDA: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo Form::select('hora_salida',$horas,date('H:i',strtotime(Arr::get($solicitud, 'fecha_salida')))); ?>
<br/>
FECHA RETORNO :<input type="text" name="fecha_retorno" id="fecha2" size="9" value="<?php echo date('Y-m-d',strtotime(Arr::get($solicitud, 'fecha_retorno','')))?>" />
HORA RETORNO: <?php echo Form::select('hora_retorno',$horas,date('H:i',strtotime(Arr::get($solicitud, 'fecha_retorno')))); ?>
<label>Medio de Transporte:</label>
<p><span class="lb">IDA:</span>
    <?php echo Form::select('transporte_ida', $transporte, Arr::get($solicitud, 'medio_transporte',''));?>
</p> 
<p>
<p><span class="lb">RETORNO</span>        
    <?php echo Form::select('transporte_retorno', $transporte, Arr::get($solicitud, 'medio_transporte',''));?>
</p>
<p><span class="lb">La Unidad Financiera le entrego Viaticos ?</span>
    <select name="viatico"><option value="1">SI</option><option value="0">NO</option></select>    
</p>
<p><br/><b>4.- ADJUNTOS</b></p>
<p class="adjunto">
<span class="lb">Resolucion Sup/Min/Adm N°:</span>
<input type="text" name="resolucion" />
</p>
<p class="adjunto">
<span class="lb">Pases a bordo:</span>
<input type="text" name="pases" />
</p>
<p class="adjunto">
<span class="lb">Formulario 110 RCV-IVA factura por :</span>
<input type="text" name="form110" />
</p>
<p class="adjunto">
<span class="lb">Boleta de Deposito a la Cta. Cte Bs.:</span>
<input type="text" name="cuenta_corriente" />
</p>
<p class="adjunto">
<span class="lb">Boleta de Deposito a la CTU:</span>
<input type="text" name="cuenta_utc" />
</p>
<p class="adjunto">
<span class="lb">Compte de asignacion de fondos(copia):</span>
<input type="text" name="fondos_copia" />
</p>
<p class="adjunto">
<span class="lb">Pasajes aereos:</span>
<input type="text" name="pasaje_aereo" />
</p>
<p class="adjunto">
<span class="lb">Pasajes Terrestres:</span>
<input type="text" name="pasaje_terrestre" />
</p>
<p class="adjunto">
<span class="lb">Formulario 604 RC-IVA Retención:</span>
<input type="text" name="form604" />
</p>
<p class="adjunto">
<span class="lb">Planilla invitados firmada:</span>
<input type="text" name="planilla_invitados" />
</p>

    


</td>

</tr>
<tr>
<input type="hidden" id="word" value="0" name="word"  />


</tr>

<tr>
<td colspan="2" style="padding-left: 5px;">
<div style="clear:both; display: block;"></div>
</td>
<td></td>
</tr>
</table>
</fieldset>
</form>
</div>
