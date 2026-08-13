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
            <th>NOMBRE EMISOR</th>
            <th>NOMBRRE RECEPTOR</th>
            <th>FECHA EMISION</th>
            <th>FECHA RECEPCIóN</th> 
            <th>DIAS INTERMEDIO</th> 
          <th>DIAS RECEPCIóN</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($results as $r):?>
        <tr>
            <td><?php echo $i;?></td>
            <td><a href="/route/trace/?hr=<?php echo $r['nur'];?>"><?php echo $r['nur'];?></a></td>
            <td><?php echo $r['nombre_emisor'];?></td> 
            <td><?php echo $r['nombre_receptor'];?></td>
            <td><?php echo $r['fecha_emision'];?></td>  
            <td><?php echo $r['fecha_recepcion'];?></td>
            <td><?php echo $r['dias_intermedio'];?></td>  
            <td><?php echo $r['dias_recepcion'];?></td>
        </tr>
        <?php $i++; endforeach;?>
    </tbody>
</table>
