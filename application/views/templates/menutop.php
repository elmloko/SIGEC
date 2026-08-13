<?php
$datos_usuario_logueado = Auth::instance()->get_user();
$nombre_usuario = (string)$datos_usuario_logueado->username;
$id_oficina_despacho = (string)$datos_usuario_logueado->id_oficina;

$menu = array();
$id = 0;
foreach ($menus as $m):
    $menu[$m->id][$m->id_submenu]['id'] = $m->id_submenu;
    $menu[$m->id][$m->id_submenu]['submenu'] = $m->submenu;
    $menu[$m->id][$m->id_submenu]['accion'] = $m->accion;
    $menu[$m->id][$m->id_submenu]['descripcion'] = $m->descripcion;
endforeach;
$sm = 0;
?>
<?php foreach ($menus as $m): ?>    
    <?php if ($id != $m->id): ?>
        <li  >  
            <?php if (count($menu[$m->id]) > 1): ?>
                <a href="javascript:;"  class="<?php if($controller==$m->controlador){ echo "active"; }?>" >
                    <div class="gui-icon"><i class="<?php echo $m->logo ?>"></i></div>
                    <span class="title"><?php echo $m->menu; ?></span>
                </a>
                <ul> 
                    <?php
                    ksort($menu[$m->id]);
                    foreach ($menu[$m->id] as $k => $v):
			$item_menu = (string)$menu[$m->id][$k]['submenu'];
                        ?>
                        <li>
                            <?php if (($item_menu === 'Pendientes Despacho') == 1 || ($item_menu === 'Fuera de Plazo') == 1 || ($item_menu === 'Fuera de Plazo (oficina)') == 1) { ?>

                                <?php if (($id_oficina_despacho === "73") == 1) { ?>
                                    <a href="/<?php echo $m->controlador; ?>/<?php echo $menu[$m->id][$k]['accion']; ?>"><span
                                            class="title"><?php echo $item_menu ?></span></a>
                                <?php } ?>

                            <?php } else { ?>
                                <a href="/<?php echo $m->controlador; ?>/<?php echo $menu[$m->id][$k]['accion']; ?>"><span
                                        class="title"><?php echo $menu[$m->id][$k]['submenu']; ?></span></a>
                            <?php } ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <a href="/<?php echo $m->controlador; ?>" class="<?php if($controller==$m->controlador){ echo "active"; }?>" >
                    <div class="gui-icon"><i class="<?php echo $m->logo;?>"></i></div>
                    <span class="title"><?php echo $m->menu; ?></span>
                </a>
            <?php endif; ?>
        </li>
        <?php
        $id = $m->id;
    endif;
    ?>  
<?php endforeach; ?>
