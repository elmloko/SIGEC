<?php
$M = 'Model_Indicadores';
$filas = $d['filas'];
$por_usuario = $d['por_usuario'];
$r = $d['rezago'];
$t = $d['tiempos'];
$con_vencidos = count(array_filter($filas, function ($x) { return $x['vencidos'] > 0; }));
$medidos = array_filter($filas, function ($x) { return $x['cumplimiento'] !== NULL; });
$mejor = $peor = NULL;
foreach ($medidos as $x) {
    if ($x['recibidos'] < 10) {
        continue; // con muy pocos casos el porcentaje no dice mucho
    }
    if ($mejor === NULL || $x['cumplimiento'] > $mejor['cumplimiento']) { $mejor = $x; }
    if ($peor === NULL || $x['cumplimiento'] < $peor['cumplimiento']) { $peor = $x; }
}
$top = $filas;
usort($top, function ($a, $b) { return $b['recibidos'] - $a['recibidos']; });
$top = array_slice($top, 0, 12);
$quien = $por_usuario ? 'funcionarios' : 'oficinas';
$nombre_corto = function ($x) use ($por_usuario) {
    return $por_usuario ? $x['nombre'] : ($x['sigla'] ?: $x['nombre']);
};
?>
<div class="col-md-12 ind">
    <?php echo View::factory('reportes/indicadores/_cabecera')->set('f', $f)->set('d', $d)->set('pagina', $pagina); ?>

    <?php if ($por_usuario): ?>
        <p class="ind-migas">
            <a href="<?php echo URL::site('reports/desempeno') . '?' . http_build_query(array('desde' => $f['desde'], 'hasta' => $f['hasta'])); ?>">← Todas las oficinas</a>
            <span>Funcionarios de <b><?php echo HTML::chars($f['oficinas'][$f['oficina']]); ?></b></span>
        </p>
    <?php endif; ?>

    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo"><?php echo ucfirst($quien); ?> con actividad</span>
            <strong><?php echo $M::numero(count($filas)); ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($con_vencidos); ?> tienen trámites vencidos</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Vencidos hoy</span>
            <strong class="ind-rojo"><?php echo $M::numero($r['vencidos']); ?></strong>
            <span class="ind-kpi-sub">de <?php echo $M::numero($r['total']); ?> abiertos</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Mejor cumplimiento</span>
            <strong class="ind-verde ind-kpi-texto"><?php echo $mejor ? HTML::chars($nombre_corto($mejor)) : '—'; ?></strong>
            <span class="ind-kpi-sub"><?php echo $mejor ? number_format($mejor['cumplimiento'], 0) . '% en plazo · ' . $M::numero($mejor['recibidos']) . ' recibidos' : 'sin datos suficientes'; ?></span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Menor cumplimiento</span>
            <strong class="ind-rojo ind-kpi-texto"><?php echo $peor ? HTML::chars($nombre_corto($peor)) : '—'; ?></strong>
            <span class="ind-kpi-sub"><?php echo $peor ? number_format($peor['cumplimiento'], 0) . '% en plazo · ' . $M::numero($peor['recibidos']) . ' recibidos' : 'sin datos suficientes'; ?></span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Recepción / atención</span>
            <strong class="ind-kpi-texto"><?php echo $M::duracion($t['recepcion']); ?> · <?php echo $M::duracion($t['atencion']); ?></strong>
            <span class="ind-kpi-sub">tiempos medios del periodo</span>
        </div>
    </div>

    <?php if ($top): ?>
    <section class="ind-card">
        <header><h3>Carga y atención — <?php echo count($top) < count($filas) ? 'los ' . count($top) . ' ' . $quien . ' con más trabajo' : $quien; ?></h3><p>Recibidos en el periodo, cuántos se atendieron y cuántos tienen hoy vencidos</p></header>
        <div class="ind-chart ind-chart-alto"><canvas id="g-carga" aria-label="Carga y atención"></canvas></div>
    </section>
    <?php endif; ?>

    <section class="ind-card">
        <header class="ind-card-acciones">
            <div>
                <h3>Detalle por <?php echo $por_usuario ? 'funcionario' : 'oficina'; ?></h3>
                <p><?php echo $por_usuario ? 'Haga clic en un funcionario para ver su tablero, o en un encabezado para ordenar.' : 'Haga clic en una oficina para ver a sus funcionarios, o en un encabezado para ordenar.'; ?> Plazo: <?php echo $plazo; ?> días hábiles.</p>
            </div>
            <div class="ind-herramientas">
                <input type="search" class="ind-buscar" placeholder="Buscar…" data-buscar="tabla-desempeno" aria-label="Buscar en la tabla">
                <button type="button" class="ind-btn" data-exportar="tabla-desempeno" data-nombre="desempeno_<?php echo $f['desde'] . '_' . $f['hasta']; ?>"><i class="fa fa-download" aria-hidden="true"></i> Excel (CSV)</button>
            </div>
        </header>
        <?php echo View::factory('reportes/indicadores/_tabla_desempeno')->set('filas', $filas)->set('por_usuario', $por_usuario)->set('f', $f)->set('id', 'tabla-desempeno'); ?>
        <p class="ind-leyenda">
            <span class="ind-pastilla ind-verde">≥ 80%</span> en plazo
            <span class="ind-pastilla ind-ambar">60–79%</span>
            <span class="ind-pastilla ind-rojo">&lt; 60%</span>
            · "Recibidos", "atendidos" y los tiempos son del periodo; "pendientes", "sin recibir" y "vencidos" son la situación de hoy.
        </p>
    </section>
</div>

<?php echo View::factory('reportes/indicadores/_graficos'); ?>
<script>
(function () {
    var G = window.IndGraficos;
    var top = <?php echo json_encode(array_map(function ($x) use ($nombre_corto) {
        return array('n' => $nombre_corto($x), 'r' => $x['recibidos'], 'a' => $x['atendidos'], 'v' => $x['vencidos']);
    }, $top)); ?>;
    if (!top.length) { return; }
    G.barraLinea('g-carga', top.map(function (x) { return x.n.length > 22 ? x.n.slice(0, 21) + '…' : x.n; }), [
        {label: 'Recibidos', data: top.map(function (x) { return x.r; }), tipo: 'bar', color: G.c.azulClaro},
        {label: 'Atendidos', data: top.map(function (x) { return x.a; }), tipo: 'bar', color: G.c.azul},
        {label: 'Vencidos hoy', data: top.map(function (x) { return x.v; }), tipo: 'bar', color: G.c.rojo}
    ]);
}());
</script>
