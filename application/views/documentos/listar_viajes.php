<script type="text/javascript">
$(function(){
 //add index column with all content.
 $("table#theTable tbody tr:has(td)").each(function(){
   var t = $(this).text().toLowerCase(); //all row text
   $("<td class='indexColumn'></td>")
    .hide().text(t).appendTo(this);
 });//each tr
 $("#FilterTextBox").keyup(function(){
   var s = $(this).val().toLowerCase().split(" ");
   //show all rows.
   $("#theTable tbody tr:hidden").show();
   $.each(s, function(){
       $("#theTable tbody tr:visible .indexColumn:not(:contains('"
          + this + "'))").parent().hide();
   });//each
 });//key up.
 //zebra
 $("#theTable").tablesorter({sortList:[[0,1]], 
                widgets: ['zebra'],
                headers: {             
                    5: { sorter:false}
                }
            });
 //$('#theTable tbody tr:odd').addClass('odd');
 $('input').focus();    
});//document.ready
</script>
<style type="text/css">
    td{margin: 0; padding-top: -5px;padding-bottom: -5px;}
    input[type="text"]{ line-height: 20px; height: 20px; font-size: 14px;}
 </style>
<table width="100%">
    <tr>
        <td align="left">
            <span><a href="/documento/generar/<?php echo $tipo->action;?>" class="button">+ Generar [ <?php echo $tipo->tipo;?>]</a>
            </span>
        </td>
        <td align="right"><p style="font-size: 10px; font-weight: normal; "> FILTRAR: <input type="text" id="FilterTextBox" name="FilterTextBox" size="30" />
    </p></td>
    </tr>
</table>
    <?php if(sizeof($results)>0){?>
<br/>
<table id="theTable" class="tablesorter">
    <thead>
        <tr>
            <th width="150">cite</th>                       
            <th width="285">Destinatario</th>
            <th width="300">Objeto del viaje</th>
            <th width="30">Hoja Ruta</th>
            <th width="50">Fecha Creación</th>
            <th width="80">Acción</th>

        </tr>
    </thead>
    
    <tbody>
    <?php
    foreach ($results as $d): ?>
        <tr>
            <td class="codigo" align="center">
                <a href="/documento/edit/<?php echo $d->id;?>" ><?php echo substr($d->codigo,0,-13).'<br/>'.substr($d->codigo,-13);?></a>               
            </td>
            <td ><?php echo $d->nombre_destinatario;?><br/><b><?php echo $d->cargo_destinatario;?></b></td>
            <td ><?php echo $d->referencia;?></td>
            
            <td align="right" valign="center" >
                <a href="/route/deriv/?hr=<?php echo $d->nur;?>"><?php echo $d->nur;?></a>            
            </td>
            <td align="right" valign="center" ><?php echo Date::fecha_corta($d->fecha_creacion); ?>
             </td> 
            <td style="text-align: right;" >
                <a href="/excel/<?php echo $tipo->action;?>/<?php echo $d->id;?>" title="Enviar a excel para su impresión" class="uiButton" ><img src="/media/images/excel.png"/></a>                                
                <?php if($d->estado==1):?>                
                <a href="/route/trace/?hr=<?php echo $d->nur;?>" title="Ver seguimiento" class="uiButton" ><img src="/media/images/tick.png"/></a>
                <?php else: ?>                
                <a href="/route/deriv/?hr=<?php echo $d->nur;?>" title="Derivar" class="uiButton" ><img src="/media/images/deriva.png" height="16"/></a>          
                <?php endif;?>
                <a href="/documento/edit/<?php echo $d->id;?>" title="Editar documento" class="uiButton" ><img src="/media/images/24/pen.png" height="16"/></a>                                  
            </td>             
        </tr>        
    <?php endforeach; ?>
   </tbody>
   <tfoot>
       <tr>
           <td colspan="5"><?php echo $page_links; ?></td>
       </tr>
   </tfoot>
</table>


<?php } else { ?>
<div class="info">
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
    <strong>Lista Vacia! </strong> No tiene documentos generados de este tipo.</p>
</div>
    <?php }?>
