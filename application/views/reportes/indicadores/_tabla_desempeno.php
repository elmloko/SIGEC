<?php
$M = 'Model_Indicadores';
$num = function ($n) use ($M) {
    return '<td class="num" data-valor="' . (float) $n . '">' . $M::numero($n) . '</td>';
};
?>
<?php if (!$filas): ?>
    <p class="ind-vacio">No hay movimientos en este periodo.</p>
<?php else: ?>
<div class="ind-tabla-wrap">
    <table class="ind-tabla" id="<?php echo $id; ?>">
        <thead>
            <tr>
                <th data-tipo="texto"><?php echo $por_usuario ? 'Funcionario' : 'Oficina'; ?></th>
                <th class="num" title="Derivaciones recibidas en el periodo">Recibidos</th>
                <th class="num" title="Derivadas, archivadas o agrupadas">Atendidos</th>
                <th class="num" title="Atendidos / recibidos">% atención</th>
                <th class="num" title="Derivadas en menos del plazo">En plazo</th>
                <th class="num" title="Promedio desde que se deriva hasta que se recibe">T. recepción</th>
                <th class="num" title="Promedio desde que se recibe hasta que se vuelve a derivar">T. atención</th>
                <th class="num" title="Hoy: recibidos sin atender">Pendientes</th>
                <th class="num" title="Hoy: aún no recibidos">Sin recibir</th>
                <th class="num" title="Hoy: abiertos con el plazo vencido">Vencidos</th>
                <th class="num" title="Días hábiles del abierto más antiguo">Más antiguo</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filas as $fila):
            $atencion = $fila['recibidos'] > 0 ? round(100 * $fila['atendidos'] / $fila['recibidos'], 1) : NULL;
        ?>
            <tr>
                <td data-valor="<?php echo HTML::chars($fila['nombre']); ?>">
                    <?php if ($por_usuario): ?>
                        <a class="ind-nombre" href="<?php echo URL::site('reports/persona') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'usuario' => $fila['id'])); ?>" title="Ver el tablero de esta persona"><?php echo HTML::chars($fila['nombre']); ?></a>
                        <span class="ind-detalle"><?php echo HTML::chars($fila['cargo']); ?><?php echo $fila['ultimo_ingreso'] ? ' · último ingreso ' . date('d/m/Y', strtotime($fila['ultimo_ingreso'])) : ''; ?></span>
                    <?php else: ?>
                        <a class="ind-nombre" href="<?php echo URL::site('reports/desempeno') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'oficina' => $fila['id'])); ?>" title="Ver funcionarios de esta oficina"><?php echo HTML::chars($fila['nombre']); ?></a>
                        <span class="ind-detalle"><?php echo HTML::chars($fila['sigla']); ?></span>
                    <?php endif; ?>
                </td>
                <?php echo $num($fila['recibidos']), $num($fila['atendidos']); ?>
                <td class="num" data-valor="<?php echo $atencion === NULL ? -1 : $atencion; ?>"><?php echo $atencion === NULL ? '—' : number_format($atencion, 0) . '%'; ?></td>
                <td class="num" data-valor="<?php echo $fila['cumplimiento'] === NULL ? -1 : $fila['cumplimiento']; ?>">
                    <span class="ind-pastilla ind-<?php echo $M::semaforo($fila['cumplimiento']); ?>"><?php echo $fila['cumplimiento'] === NULL ? '—' : number_format($fila['cumplimiento'], 0) . '%'; ?></span>
                </td>
                <td class="num" data-valor="<?php echo $fila['min_recepcion'] === NULL ? -1 : $fila['min_recepcion']; ?>"><?php echo $M::duracion($fila['min_recepcion']); ?></td>
                <td class="num" data-valor="<?php echo $fila['min_atencion'] === NULL ? -1 : $fila['min_atencion']; ?>"><?php echo $M::duracion($fila['min_atencion']); ?></td>
                <?php echo $num($fila['pendientes']), $num($fila['no_recibidos']); ?>
                <td class="num" data-valor="<?php echo $fila['vencidos']; ?>"><?php echo $fila['vencidos'] > 0 ? '<b class="ind-rojo">' . $M::numero($fila['vencidos']) . '</b>' : '0'; ?></td>
                <td class="num" data-valor="<?php echo $fila['mas_antiguo']; ?>"><?php echo $fila['mas_antiguo'] > 0 ? $M::numero($fila['mas_antiguo']) . ' d' : '—'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
