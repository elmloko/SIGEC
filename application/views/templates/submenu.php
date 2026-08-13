<ul class="menu-correo">     
    <?php foreach($smenus as $s): ?>        
    <li><?php echo HTML::anchor($s->controlador.'/'.$s->accion,$s->submenu,array('title'=>$s->descripcion));?></li>
    <?php endforeach;?>    
</ul>

