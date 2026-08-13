<div id="colores">    
<?php foreach($colores as $color=>$hex):?>
    <a href="/user/color/<?php echo $color;?>" style="background-color: #<?php echo $hex;?>; color:#<?php echo $hex;?> ; " color="<?php echo $color;?>" ><?php echo $hex;?></a>
<?php endforeach;?>
    
</div>