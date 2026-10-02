<?php
$M = 'Model_Indicadores';
$v = $d['volumen'];
$o = $d['origen'];
$dias = max(1, (int) round((strtotime($f['hasta']) - strtotime($f['desde'])) / 86400) + 1);
$total_tipos = array_sum(array_map(function ($x) { return $x['total']; }, $d['tipos']));
$principal = $d['tipos'] ? $d['tipos'][0] : NULL;
$hojas = $o['externas'] + $o['internas'];
$meses_cortos = array('01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic');
$etiquetas_mes = array_map(function ($m) use ($meses_cortos) {
    return $meses_cortos[substr($m, 5, 2)] . ' ' . substr($m, 2, 2);
}, $d['mensual']['meses']);
// las 5 series con mas documentos; el resto se suma en "Otros"
$series = $d['mensual']['series'];
uasort($series, function ($a, $b) { return array_sum($b) - array_sum($a); });
$visibles = array_slice($series, 0, 5, TRUE);
$resto = array_slice($series, 5, NULL, TRUE);
if ($resto) {
    $otros = array_fill_keys($d['mensual']['meses'], 0);
    foreach ($resto as $s) {
        foreach ($s as $mes => $n) { $otros[$mes] += $n; }
    }
    $visibles['Otros'] = $otros;
}
?>
<div class="col-md-12 ind">
    <?php echo View::factory('reportes/indicadores/_cabecera')->set('f', $f)->set('d', $d)->set('pagina', $pagina); ?>

    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Documentos generados</span>
            <strong><?php echo $M::numero($v['documentos']); ?></strong>
            <span class="ind-kpi-sub"><?php echo number_format($v['documentos'] / $dias, 1, ',', '.'); ?> por día en promedio</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Hojas de ruta nuevas</span>
            <strong><?php echo $M::numero($v['hojas_nuevas']); ?></strong>
            <span class="ind-kpi-sub"><?php echo $v['hojas_nuevas'] > 0 ? number_format($v['documentos'] / $v['hojas_nuevas'], 1, ',', '.') : '—'; ?> documentos por hoja de ruta</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Correspondencia externa</span>
            <strong><?php echo $hojas > 0 ? round(100 * $o['externas'] / $hojas) . '%' : '—'; ?></strong>
            <span class="ind-kpi-sub"><?php echo $M::numero($o['externas']); ?> hojas de ruta ingresaron con documento externo</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Tipo más usado</span>
            <strong class="ind-kpi-texto"><?php echo $principal ? HTML::chars($principal['etiqueta']) : '—'; ?></strong>
            <span class="ind-kpi-sub"><?php echo $principal && $total_tipos ? round(100 * $principal['total'] / $total_tipos) . '% de los documentos' : ''; ?></span>
        </div>
    </div>

    <div class="ind-grid">
        <section class="ind-card ind-span-2">
            <header><h3>Documentos por mes y tipo</h3><p>Últimos 12 meses</p></header>
            <div class="ind-chart ind-chart-alto"><canvas id="g-mensual" aria-label="Documentos por mes"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Por tipo de documento</h3><p>Periodo elegido</p></header>
            <div class="ind-chart"><canvas id="g-tipos" aria-label="Por tipo"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Origen de las hojas de ruta</h3><p>Externas (ingresan por ventanilla) e internas</p></header>
            <div class="ind-chart"><canvas id="g-origen" aria-label="Origen"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Oficinas que más producen</h3><p>Las 15 primeras del periodo</p></header>
            <div class="ind-chart ind-chart-alto"><canvas id="g-oficinas" aria-label="Por oficina"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3>Funcionarios que más producen</h3><p>Los 15 primeros del periodo</p></header>
            <div class="ind-chart ind-chart-alto"><canvas id="g-usuarios" aria-label="Por funcionario"></canvas></div>
        </section>
    </div>
</div>

<?php echo View::factory('reportes/indicadores/_graficos'); ?>
<script>
(function () {
    var G = window.IndGraficos;
    var corto = function (t, n) { t = String(t || ''); return t.length > n ? t.slice(0, n - 1) + '…' : t; };
    var series = <?php echo json_encode(array_map(function ($nombre, $datos) {
        return array('label' => (string) $nombre, 'data' => array_values($datos));
    }, array_keys($visibles), $visibles)); ?>;
    G.apiladas('g-mensual', <?php echo json_encode($etiquetas_mes); ?>, series);
    var tipos = <?php echo json_encode($d['tipos']); ?>;
    G.dona('g-tipos', tipos.map(function (t) { return t.etiqueta; }), tipos.map(function (t) { return +t.total; }));
    G.dona('g-origen', ['Externas', 'Internas'], [<?php echo $o['externas'] . ', ' . $o['internas']; ?>], [G.c.amarillo, G.c.azul]);
    var oficinas = <?php echo json_encode($d['oficinas']); ?>;
    G.barras('g-oficinas', oficinas.map(function (x) { return corto(x.etiqueta, 24); }), oficinas.map(function (x) { return +x.total; }), {horizontal: true, etiqueta: 'Documentos'});
    var usuarios = <?php echo json_encode($d['usuarios']); ?>;
    G.barras('g-usuarios', usuarios.map(function (x) { return corto(x.etiqueta, 26); }), usuarios.map(function (x) { return +x.total; }), {horizontal: true, colores: G.c.amarillo, etiqueta: 'Documentos'});
}());
</script>
