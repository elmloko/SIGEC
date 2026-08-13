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
$('#descripcion').redactor({lang : 'es',css: 'docstyle.css'});
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
    <br/>
    <br/>
    <br/>
<label>Proceso: </label><?php echo Form::select('proceso', $options, NULL);?><br/>    
<?php if($documento->tipo=='Carta'):?>
<p>
<label>Titulo:</label>
<select name="titulo">
    <option></option>
    <option>Señor</option>
    <option>Señora</option>
    <option>Señores</option>    
</select>
</p>
<?php else:?>
<input type="hidden" name="titulo" />   
<?php endif;?>

<p>
<?php
echo Form::label('destinatario', 'Nombre del destinatario:',array('class'=>'form'));
echo Form::input('destinatario','',array('id'=>'destinatario','size'=>48,'class'=>'required'));
?>
</p>
<p>
<?php
echo Form::label('destinatario', 'Cargo Destinatario:',array('class'=>'form'));
echo Form::input('cargo_des','',array('id'=>'cargo_des','size'=>48,'class'=>'required'));
?>
</p>   
<?php if($tipo->via==0):?>
<p>
<label>Institución Destinatario</label>
    <input type="text" size="40" name="institucion_des" />    
    <input type="hidden" name="via" />   
    <input type="hidden" name="cargovia" />   
</p>
<p>
<?php else:?>
<input type="hidden" size="40" name="institucion_des" />   
<?php
echo Form::label('via', 'Via:',array('class'=>'form'));
echo Form::input('via','',array('id'=>'via','size'=>48,'class'=>'required'));
?>
<?php
echo Form::label('cargovia', 'Cargo Via:',array('class'=>'form'));
echo Form::input('cargovia','',array('id'=>'cargovia','size'=>48,'class'=>'required'));
?>
<?php endif;?>
</p>

<p>
<?php
   echo Form::label('remitente', 'Remitente:',array('class'=>'form'));
   echo Form::input('remitente',$user->nombre,array('id'=>'remitente','size'=>35,'class'=>'required'));            
?>            
<?php
   //echo Form::label('mosca','Mosca:');
   echo Form::input('mosca',$user->mosca,array('id'=>'mosca','size'=>5));
?>
<?php
   echo Form::label('cargo', 'Cargo Remitente:',array('class'=>'form'));
   echo Form::input('cargo_rem',$user->cargo,array('id'=>'cargo_rem','size'=>48,'class'=>'required'));
?>
<?php
   echo Form::label('adjuntos', 'Adjunto:',array('class'=>'form'));
   echo Form::input('adjuntos','',array('id'=>'adjuntos','size'=>48,'class'=>'required','title'=>'Ejemplo: Lo citado'));
?>
<?php
            echo Form::label('copias', 'Con copia a:',array('class'=>'form'));
            echo Form::input('copias','',array('id'=>'adjuntos','size'=>48,'class'=>'required'));
            ?>
</p>
</td>
<td valign="top">
    <br/>
    <input type="submit" class="button2" name="submit" value="Generar Documento"/>
    <a href="" class="button" onclick="javascript:history.back(); return false;" >Cancelar</a>
    <hr/>        
    <b>1.- DATOS DE LA SOLICITUD</b>
<p>
    <label><br/> </label>
    &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="pasaje" value="1"/><span style="color:#111; font-weight: bold; font-style: italic;"> Pasaje</span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="checkbox" name="viatico" value="1"/><span style="color:#111; font-weight: bold; font-style: italic;"> Viatico</span>    
</p>
<br/>
<b>2.- DATOS DE VIAJE</b>
<p>
<labe> Pais, Ciudad, y/o Localidad:</label>
<br/>
<input type="text" name="lugar" size="68" />
</p>
<p>
<label>Objeto del Viaje:</label>
<textarea name="referencia" id="referencia" style="width: 350px; " ></textarea>   
</p>
<p>
<label>El viaje es:</label>
<?php 
echo Form::select('tipo_viaje', $t_viaje, NULL, array('class'=>'required'));
?>
</p>

<p>
    <label>Medio de Trasporte:</label>
<?php 
echo Form::select('medio_transporte', $m_transporte, NULL, array('class'=>'required'));
?>
</p>    
<span>Fecha Salida: &nbsp;&nbsp;&nbsp;&nbsp; </span>    
 <input type="text" name="fecha_salida" id="fecha1"  />
 
<span>Hora Salida:&nbsp;&nbsp;&nbsp; </span>    
 <input type="text" name="hora_salida" class="form"  />
<p>
<span>Fecha Retorno: &nbsp;</span> <input type="text" name="fecha_retorno" id="fecha2" />
<span>Hora Retorno: </span>
<input type="text" name="hora_retorno" />
<br/>
<span>Días:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
<input type="text" name="dias"  />

</td>
<td valign="top">
<?php if($documento->via>-10){ ?>   
    <div id="vias">
        <ul>
            <?php foreach($vias  as $v): ?>
            <li class="<?php echo $v['genero']?>"><?php echo HTML::anchor('#',$v['nombre'],array('class'=>'destino','nombre'=>$v['nombre'],'title'=>$v['cargo'],'cargo'=>$v['cargo'],'via'=>$v['via'],'cargo_via'=>$v['cargo_via']));?></li>
            <?php endforeach; ?>
            <!-- Inmediato superior -->    
            <?php foreach($superior  as $v){ ?>
            <li class="<?php echo $v['genero']?>"><?php echo HTML::anchor('#',$v['nombre'],array('class'=>'destino','nombre'=>$v['nombre'],'title'=>$v['cargo'],'cargo'=>$v['cargo'],'via'=>'','cargo_via'=>''));?></li>
            <?php } ?>
            <!-- dependientes -->    
            <?php foreach($dependientes  as $v){ ?>
            <li class="<?php echo $v['genero']?>"><?php echo HTML::anchor('#',$v['nombre'],array('class'=>'destino','nombre'=>$v['nombre'],'title'=>$v['cargo'],'cargo'=>$v['cargo'],'via'=>'','cargo_via'=>''));?></li>
            <?php } ?>
        </ul>
    </div>
<?php }?>
</td>
</tr>
<tr>
    <td colspan="2">

<input type="hidden" id="word" value="0" name="word"  />
<div class="descripcion" style="width: 680px; float: left; ">
<?php
echo Form::hidden('descripcion','');
?>
</div>
</td>

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
