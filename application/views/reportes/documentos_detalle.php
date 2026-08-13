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
        <td align="right"><p style="font-size: 10px; font-weight: normal; "> FILTRAR: <input type="text" id="FilterTextBox" name="FilterTextBox" size="30" />
    </p></td>
    </tr>
</table>
    <?php if(sizeof($documentos)>0){?>
<br/>
<table id="theTable" class="tablesorter">
    <thead>
        <tr>
            <th width="150">cite</th>                       
            <th width="285">Destinatario</th>
            <th width="300">Remitente</th>
            <th width="300">Referencia</th>
            <th width="30">Hoja Ruta</th>
            <th width="50">Fecha Creación</th>            

        </tr>
    </thead>
    
    <tbody>
    <?php
    foreach ($documentos as $d): ?>
        <tr>
            <td class="codigo" align="center">
                <b><?php echo substr($d->codigo,0,-13).'<br/>'.substr($d->codigo,-13);?></b>               
            </td>
            <td ><?php echo $d->nombre_destinatario;?><br/><b><?php echo $d->cargo_destinatario;?></b></td>
            <td ><?php echo $d->nombre_remitente;?><br/><b><?php echo $d->cargo_remitente;?></b></td>
            <td ><?php echo $d->referencia;?></td>
            
            <td align="right" valign="center" >
                <a href="/route/trace/?hr=<?php echo $d->nur;?>"><?php echo $d->nur;?></a>            
            </td>
            <td align="right" valign="center" ><?php echo Date::fecha_corta($d->fecha_creacion); ?>
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
