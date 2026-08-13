<script type="text/javascript">
    $(function(){
        $('table#theTable tbody tr:odd').addClass('odd'); 
        $("#imprime").click(function(){
            window.print();
            return false;
        });
    });
</script>
<p style="float: right;"><a href="javascript:void(0)" id="imprime" class="uiButton noprint"><img src="/media/images/printer.png" align="absmiddle" alt=""/> Imprimir</a></p><br/></p><br/>
<h2 ><?php echo $titulo;?></h2>
<table id="theTable">
    <thead>
        <tr>
            <th>#</th>
            <th>HOJA DE RUTA</th>
            <th>CITE</th>
            <th>FECHA EMISION</th>
            <th>EMISOR</th>
            <th>DESTINATARIO</th> 
            <th>REFERENCIA</th> 
            <th>PROVEIDO</th>
            <th>DE OFICINA</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($results as $r):?>
        <tr>
            <td><?php echo $i;?></td>
            <td><a href="/route/trace/?hr=<?php echo $r['nur'];?>"><?php echo $r['nur'];?></a></td>
            <td><?php echo $r['codigo'];?></td> 
            <td><?php echo $r['fecha'];?></td>
            <td><?php echo $r['nombre_emisor'];?><br><?php echo $r['cargo_emisor'];?></td>
            <td><?php echo $r['nombre_destinatario'];?><br><?php echo $r['cargo_destinatario'];?></td>
            <td><?php echo $r['referencia'];?></td>  
  <td><?php echo $r['proveido'];?></td>
<td><?php echo $r['de_oficina'];?></td>  
        </tr>
        <?php $i++; endforeach;?>
        
    </tbody>
</table>
