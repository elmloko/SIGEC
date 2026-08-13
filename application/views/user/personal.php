<script type="text/javascript">
    $(function(){
        $("#theTable").tablesorter({sortList:[[0,0]],
                widgets: ['zebra']
        }); 
    });
</script>
<table id="theTable" class="tablesorter">
    <thead>        
        <tr>

            <th>Nombre</th>
            <th>Cargo</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($usuarios as $u):?>
            <tr>

                <td><?php echo $u->nombre?></td>
                <td><?php echo $u->cargo?></td>
            </tr>    
        <?php $i++; endforeach;?>
        
    </tbody>
</table>
<div class="info" style="text-align:center;">
    <p><span style="float: left; margin-right: .3em;" class=""></span> 
      &larr;<a onclick="javascript:history.back(); return false;" href="#" style="font-weight: bold; text-decoration: underline;  " > Regresar<a/></p>    
</div>