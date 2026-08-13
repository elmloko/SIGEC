<script>
$(function(){
    $('#id_entidad').change(function(){
        var id=$(this).val();
        location.href='/admin/oficinas/lista/'+id;
    });
     $("#theTable").tablesorter({sortList:[[0,1]], 
                widgets: ['zebra'],
                headers: {             
                    2: { sorter:false}                    
                }
            });
});
</script>
<h2 class="subtitulo"><?php echo $entidad; ?><br/><span>LISTA DE OFICINAS</span></h2>
<div style="float: left; margin-top: 5px;"><a class="button" href="/admin/oficinas/create/<?php echo $id_entidad;?>">+ Nueva Oficina</a></div>
<div style="float: right;"><?php echo Form::select('id_entidad', $options,$id_entidad,array('id'=>'id_entidad'));?></div>
<br/>
<br/>
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
                OPCIONES
            </th>
        </tr>
    </thead>
    <tbody> 
    <?php foreach($oficinas as $o):?>
        <tr>
            <td>
                <?php echo $o->id;?>
            </td>
            <td>
                <a href="/admin/user/lista/<?php echo $o->id;?>"><?php echo $o->oficina;?></a>
            </td>
            <td>
                <a href="/admin/user/lista/<?php echo $o->id;?>"><img src="/media/images/16x16/Write.png" /></a>
            </td>
        </tr>
    <?php endforeach;?>        
    </tbody>
</table>
