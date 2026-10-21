<?php
/*
 * Filas VIA del formulario de documento (generar y editar), hasta Vias::MAXIMO.
 * Campos: via / cargovia, via2 / cargovia2, via3 / cargovia3. Las filas vacias (salvo la primera)
 * quedan ocultas hasta que se pulsa "Agregar otro VIA".
 * Recibe $vias: lista de array('nombre' => .., 'cargo' => ..) ya guardados (puede estar vacia).
 */
$vias = isset($vias) ? array_values($vias) : array();
?>
<?php for ($i = 1; $i <= Vias::MAXIMO; $i++): ?>
    <?php
    $sufijo = $i == 1 ? '' : $i;
    $v = isset($vias[$i - 1]) ? $vias[$i - 1] : array('nombre' => '', 'cargo' => '');
    $oculta = $i > 1 && $v['nombre'] == '';
    ?>
    <div class="gd-fila gd-fila-via"<?php echo $oculta ? ' style="display:none"' : ''; ?>>
        <div class="gd-etiqueta">VÍA:<?php if ($i == 1): ?><small>opcional</small><?php endif; ?></div>
        <div>
            <div class="gd-par">
                <?php echo Form::input('via' . $sufijo, $v['nombre'], array('id' => 'via' . $sufijo, 'class' => 'gd-via', 'autocomplete' => 'off', 'placeholder' => 'Nombre')); ?>
                <?php echo Form::input('cargovia' . $sufijo, $v['cargo'], array('id' => 'cargovia' . $sufijo, 'class' => 'gd-cargovia', 'autocomplete' => 'off', 'placeholder' => 'Cargo')); ?>
            </div>
            <div class="gd-ayuda">
                <span>
                    <?php if ($i == 1): ?>Déjelo vacío si el documento va directo<?php endif; ?>
                    <?php if ($i < Vias::MAXIMO): ?><a href="#" class="gd-otro-via"><i class="fa fa-plus"></i> Agregar otro VÍA</a><?php endif; ?>
                </span>
                <a href="#" class="gd-limpiar"><i class="fa fa-times"></i> limpiar</a>
            </div>
        </div>
    </div>
<?php endfor; ?>
