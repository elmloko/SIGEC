<?php if (!$filas): ?>
    <p class="ind-vacio">Sin movimientos en este periodo.</p>
<?php else: ?>
<ol class="ind-ranking">
    <?php foreach ($filas as $x): ?>
        <li>
            <div class="ind-ranking-texto">
                <?php if ($x['id'] && $enlace): ?>
                    <a class="ind-nombre" href="<?php echo $enlace($x['id']); ?>" title="Ver el tablero de esta persona"><?php echo HTML::chars($x['nombre']); ?></a>
                <?php else: ?>
                    <span class="ind-nombre"><?php echo HTML::chars($x['nombre']); ?></span>
                <?php endif; ?>
                <span class="ind-detalle"><?php echo HTML::chars($x['oficina']); ?></span>
            </div>
            <div class="ind-ranking-barra" aria-hidden="true"><span class="ind-fondo-<?php echo $color; ?>" style="width: <?php echo max(2, round(100 * $x['total'] / max(1, $maximo))); ?>%"></span></div>
            <b class="ind-ranking-valor"><?php echo Model_Indicadores::numero($x['total']); ?></b>
        </li>
    <?php endforeach; ?>
</ol>
<?php endif; ?>
