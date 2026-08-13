<script type="text/javascript">
    $(function() {
        $('table.classy tbody tr:odd').addClass('odd');
        $("#imprime").click(function() {
            window.print();
            return false;
        });
    });
</script>
<h2><?php echo strtoupper($titulo);?> <br/>
    <p><?php echo strtoupper($descripcion)?></p>
</h2>

<p style="float: right;"><a href="javascript:void(0)" id="imprime" class="uiButton noprint"><img src="/media/images/printer.png" align="absmiddle" alt=""/> Imprimir</a></p><br/>
<?php if ($tipo_rep == 1||$tipo_rep==3) { ?>
    <table id="theTable">
        <thead>
            <tr>
                <th>#</th>
                <th>HOJA DE RUTA</th>
                <th>CITE</th>
                <th>FECHA INGRESO</th>            
                <th>INSTITUCION REMITENTE</th>
                <th>REMITENTE</th>            
                <th>REFERENCIA</th>
                <th>DESTINATARIO</th>
                <th>FECHA DERIVACION</th>
                <th>ESTADO ACTUAL</th>
                <!-- <th>DERIVADO POR</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($results as $r): ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><a href="/route/trace/?hr=<?php echo $r['nur']; ?>"><?php echo $r['nur']; ?></a></td>
                    <td><?php echo $r['cite_original']; ?></td>
                    <td><?php echo $r['fecha_emision']; ?></td>
                    <td><?php echo $r['institucion_remitente']; ?></td>            
                    <td><?php echo $r['nombre_remitente']; ?><br/><b><?php echo $r['cargo_remitente']; ?></b></td>
                    <td><?php echo $r['referencia']; ?></td>            
                    <td><?php echo $r['nombre_destinatario']; ?><br/><b><?php echo $r['cargo_destinatario']; ?></b></td>
                    <td><?php echo $r['fecha_emision']; ?></td>            
                    <td><?php echo $r['estado']; ?></td>
                    <!-- <td><?php echo $r['nombre_emisor']; ?></td> -->
                </tr>
        <?php $i++;
    endforeach; ?>        
        </tbody>
    </table>
<?php
}
if ($tipo_rep == 2) {
    ?>
    <table id="theTable">
        <thead>
            <tr>
                <th>#</th>
                <th>HOJA DE RUTA</th>
                <th>CITE</th>
                <th>FECHA CREACI&Oacute;N</th>                            
                <th>REMITENTE</th>            
                <th>REFERENCIA</th>
                <th>DESTINATARIO</th>
                <th>FECHA DERIVACION</th>
                <th>ESTADO ACTUAL</th>
                <!-- <th>DERIVADO POR</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($results as $r): ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><a href="/route/trace/?hr=<?php echo $r['nur']; ?>"><?php echo $r['nur']; ?></a></td>
                    <td><?php echo $r['cite_original']; ?></td>
                    <td><?php echo $r['fecha_emision']; ?></td>                    
                    <td><?php echo $r['nombre_remitente']; ?><br/><b><?php echo $r['cargo_remitente']; ?></b></td>
                    <td><?php echo $r['referencia']; ?></td>            
                    <td><?php echo $r['nombre_destinatario']; ?><br/><b><?php echo $r['cargo_destinatario']; ?></b></td>
                    <td><?php echo $r['fecha_emision']; ?></td>            
                    <td><?php echo $r['estado']; ?></td>
                    <!-- <td><?php echo $r['nombre_emisor']; ?></td> -->
                </tr>
        <?php $i++;
    endforeach; ?>        
        </tbody>
    </table>
    <?php
}
?>
