<script>
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
                    6: { sorter:false}                    
                }
            });
 $('input').focus();
});
</script>

<p style="margin: 5px auto; font-size: 10px; font-weight: normal; "> FILTRAR/BUSCAR: <input type="text" id="FilterTextBox" name="FilterTextBox" size="40" />
</p>
<table id="theTable" class="tablesorter">
    <thead>
        <tr>
            <th>
                ID
            </th>
            <th>
                OFICINA
            </th>
            <th>
                SIGLA
            </th>
            <th>
                ENTIDAD
            </th>
            <th>
                OPCIONES
            </th>
        </tr>
    </thead>
    <tbody> 
    <?php foreach($oficinas as $o):?>
        <tr>
            <td>
                <?php echo $o['id'];?>
            </td>
            <td>
                <a href="/admin/user/lista/<?php echo $o['id'];?>"><?php echo $o['oficina'];?></a>
            </td>
            <td>
                <a href="/admin/user/lista/<?php echo $o['id'];?>"><?php echo $o['sigla'];?></a>
            </td>
            <td>
                <a href="/admin/oficinas/lista/<?php echo $o['id_entidad'];?>" title="<?php echo $o['entidad'];?>"><?php echo $o['sigla_entidad'];?></a>
            </td>
            <td>
                <a href="/admin/user/lista/<?php echo $o['id'];?>"><img src="/media/images/16x16/Write.png" /></a>
            </td>
        </tr>
    <?php endforeach;?>        
    </tbody>
</table>
