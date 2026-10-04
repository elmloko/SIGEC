<script type="text/javascript">
    $(function(){
        $('table.classy tbody tr:odd').addClass('odd'); 
        $("#imprime").click(function(){
            window.print();
            return false;
        });
    });
</script>

<p style="float: right;"><a href="javascript:void(0)" id="imprime" class="uiButton noprint"><img src="/media/images/printer.png" align="absmiddle" alt=""/> Imprimir</a></p><br/>

<table id="theTable">
    <thead>
        <tr>
            <th>#</th>
            <th>NUR</th>
            <th>DOCUMENTO</th>
            <th>FECHA EMISION</th>
            <th>FECHA RECEPCION</th>
            <th>DERIVADO A</th> 
            <th>PROVEIDO</th>
                       
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($results as $r):?>
        <tr>
            <td><?php echo $i;?></td>
            <td><a href="/route/trace/?hr=<?php echo HTML::chars($r['nur']);?>"><?php echo HTML::chars($r['nur']);?></a></td>
            <td><?php echo HTML::chars($r['codigo']);?></td>
            <td><?php echo $r['fecha_emision'];?></td>
            <td><?php echo $r['fecha_recepcion'];?></td>            
            <td><?php echo HTML::chars($r['nombre_receptor']);?></br><b><?php echo HTML::chars($r['cargo_receptor']);?></b></td>            
            <td><?php echo HTML::chars($r['proveido']);?></td>
        </tr>
        <?php $i++; endforeach;?>
        
    </tbody>
</table>
