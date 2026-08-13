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
$("#theTable").colResizable({
		liveDrag:true,
		draggingClass:"dragging" 
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
<div class="widget" style="width:100%">
  <div class="title"><img src="/media/images/icons/light/frames.png" alt="" class="title-icon" /><h6>Lista de <?php echo $tipo->plural; ?> </h6></div>
<table id="theTable" class="standard-table" width="100%">
    <thead>
        <tr>
            <th class="sorting-column" width="110"><div>CITE<span></span></div></th>
            <th class="sorting-column" width="285"><div>DESTINATARIO<span></span></div></th>
            <th class="sorting-column" width="300"><div>REFERENCIA<span></span></div></th>
            <th class="sorting-column" width="80"><div>HOJA DE RUTA<span></span></div></th>
            <th class="sorting-column" width="60"><div>FECHA<span></span></div></th>
            <th width="70"><div>ACCIÓN<span></span></div></th>            
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
                <a href="/word/<?php echo $tipo->action;?>/<?php echo $d->id;?>" title="Editar documento en Word" class="icon" ><img src="/media/images/word07.gif"/></a>                
                <?php if($d->estado==1):?>                
                <a href="/route/trace/?hr=<?php echo $d->nur;?>" title="Ver seguimiento" class="icon" ><img src="/media/images/tick.png"/></a>
                <?php else: ?>   
                <?php if($d->nur!=''):?>
                <a href="/route/deriv/?hr=<?php echo $d->nur;?>" title="Derivar" class="icon" ><img src="/media/images/deriva.png" height="16"/></a>                     
                <?php else:?>
                <a href="/document/asignar/<?php echo $d->id;?>" title="Asignar Hoja de Ruta pendiente" class="icon"><img src="/media/images/hojaruta.png" height="16"/></a>            
                <?php endif;?>
                
                <?php endif;?>
                <a href="/documento/edit/<?php echo $d->id;?>" title="Editar documento" class="icon" ><img src="/media/images/24/pen.png" height="16"/></a>                                  
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
</div>

<?php } else { ?>
<div class="info">
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
    <strong>Lista Vacia! </strong> No tiene documentos generados de este tipo.</p>
</div>
    <?php }?>
