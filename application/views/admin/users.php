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
                    6: { sorter:false}                    
                }
            });
 $('input').focus();
});//document.ready
</script>
<p style="margin: 5px auto; font-size: 10px; font-weight: normal; "> FILTRAR/BUSCAR: <input type="text" id="FilterTextBox" name="FilterTextBox" size="40" />
</p>
<table id="theTable" class="tablesorter">
    <thead>
        <tr>
            <th>
                USERNMAE
            </th>
            <th>
                NOMBRE
            </th>
            <th>
                CARGO
            </th>
            <th>
                OFICINA
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($users as $k=>$v ): ?>
        <tr>
            <td>
                <a href="/admin/user/detalle/<?php echo $v['id_user']?>"><?php echo $v['username']?></a>   
                
            </td>
            <td>
             <a href="/admin/user/detalle/<?php echo $v['id_user']?>"><?php echo $v['nombre']?></a>   
            </td>
            <td>
             <?php echo $v['cargo']?>   
            </td>
            <td>
             <?php echo $v['oficina']?>   
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
<?php
echo '<pre>';
//var_dump($users);
echo '</pre>';

?>
