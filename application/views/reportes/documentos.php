<?php foreach($documentos as $d):?>
<div class="docs doc">     
        <span class="colorcito" style="font-size: 16px;"><?php echo $d['tipo']?></span>
        <br/>
        <br/>
        <b>Creados: <?php echo $d['cantidad']?></b>        
        <br/>
        
        <b>Correlativo: <?php echo $d['correlativo']?></b>        
        <br/>        
        <a href="/reports/document/<?php echo $d['id']?>" class="button" > + Detalle</a>
</div>
<?php endforeach;?>