<script type="text/javascript">
$(function(){
 //add index column with all content.
 $("table#theTable tbody tr:has(td)").each(function(){
   var t = $(this).text().toLowerCase(); //all row text
   $("<td class='indexColumn'></td>")
    .hide().text(t).appendTo(this);
 });
 $("#FilterTextBox").keyup(function(){
   var s = $(this).val().toLowerCase().split(" "); 
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
 $('input').focus();        
});
</script>
<style type="text/css">
    td{margin: 0; padding-top: -5px;padding-bottom: -5px;}
    input[type="text"]{ line-height: 20px; height: 20px; font-size: 14px;}   
</style>

<?php if(sizeof($results)>0){?>
<br/>
<table style=" width: 100%;">
    <tr>
        <td align="right"><span style="margin: 5px auto; font-size: 10px; font-weight: normal; ">FILTRAR/BUSCAR: <input type="text" id="FilterTextBox" name="FilterTextBox" size="40" /></span></td>
    </tr>
</table>
<br/>
<table id="theTable" class="tablesorter">
    <thead>
        <tr>
            <th width="150">Archivo</th>            
            <th width="70">Tama&ntilde;o</th>                         
            <th width="160">documento</th>
            <th width="285">Referencia</th>
            <th width="80">Hoja ruta</th>
            <th width="80">Fecha</th>
        </tr>
    </thead>
    
    <tbody>
    <?php foreach ($results as $d): ?>
        <tr>
            <td class="codigo" align="center">
                <a href="/download/?file=<?php echo $d['id'];?>" ><?php echo substr($d['nombre_archivo'],13);?></a>               
            </td>            
            <td ><?php echo number_format($d['tamanio']/1048576,2).' MB';?></td>
            <td><a href="/document/edit/<?php echo $d['id_documento'];?>" ><?php echo $d['codigo'];?></a>               </td>
            <td ><?php echo $d['referencia'];?></td>                        
            <td style="text-align: right;" >
                <a href="/route/trace/?hr=<?php echo $d['nur'];?>"><?php echo $d['nur'];?></a>            
            </td>
            <td><?php echo date::fecha_corta($d['fecha']);?>                               
              </td> 
                                    
        </tr>        
    <?php endforeach; ?>
   </tbody>
   <tfoot>
       <tr>
           <td colspan="5"><?php // echo $page_links; ?></td>
       </tr>
   </tfoot>
</table>


<?php } else { ?>
<div class="info">
<p><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
    <strong>Sin archivos! </strong> Usted no tiene ningun archivo digital subido al sistema.</p>
</div>
<?php }?>
