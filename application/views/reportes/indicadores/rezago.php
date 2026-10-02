<?php
$M = 'Model_Indicadores';
$r = $d['rezago'];
$detalle = $d['detalle'];
$ranking = $d['oficinas'];
usort($ranking, function ($a, $b) { return $b['vencidos'] - $a['vencidos']; });
$ranking = array_values(array_filter(array_slice($ranking, 0, 12), function ($x) { return $x['vencidos'] > 0; }));
$por_usuario = $f['oficina'] > 0;
?>
<div class="col-md-12 ind">
    <?php echo View::factory('reportes/indicadores/_cabecera')->set('f', $f)->set('d', $d)->set('pagina', $pagina); ?>

    <p class="ind-aviso">
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        Esta página muestra lo que está <b>sin atender hoy</b>, sin importar el periodo elegido. Un trámite vence al cumplir <?php echo $plazo; ?> días hábiles desde que se derivó.
    </p>

    <div class="ind-kpis">
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Vencidos</span>
            <strong class="ind-rojo"><?php echo $M::numero($r['vencidos']); ?></strong>
            <span class="ind-kpi-sub">de <?php echo $M::numero($r['total']); ?> abiertos (<?php echo $r['total'] > 0 ? round(100 * $r['vencidos'] / $r['total']) : 0; ?>%)</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Sin recibir</span>
            <strong><?php echo $M::numero($r['no_recibidos']); ?></strong>
            <span class="ind-kpi-sub">el destinatario aún no los marcó como recibidos</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Recibidos sin atender</span>
            <strong><?php echo $M::numero($r['pendientes']); ?></strong>
            <span class="ind-kpi-sub">recibidos, pero sin derivar ni archivar</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Por archivar</span>
            <strong class="ind-ambar"><?php echo $M::numero($r['por_archivar']); ?></strong>
            <span class="ind-kpi-sub">la instrucción era "Archivar"; se pueden cerrar de inmediato</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Copias abiertas</span>
            <strong><?php echo $M::numero($r['copias']); ?></strong>
            <span class="ind-kpi-sub">copias informativas que siguen en bandeja</span>
        </div>
        <div class="ind-kpi">
            <span class="ind-kpi-titulo">Más de 120 días</span>
            <strong class="ind-rojo"><?php echo $M::numero($r['b5']); ?></strong>
            <span class="ind-kpi-sub">el más antiguo lleva <?php echo $M::numero($r['maximo']); ?> días hábiles</span>
        </div>
    </div>

    <div class="ind-grid">
        <section class="ind-card">
            <header><h3>Antigüedad de lo abierto</h3><p>Días hábiles que llevan sin atenderse</p></header>
            <div class="ind-chart"><canvas id="g-antiguedad" aria-label="Antigüedad"></canvas></div>
        </section>
        <section class="ind-card">
            <header><h3><?php echo $por_usuario ? 'Funcionarios' : 'Oficinas'; ?> con más vencidos</h3><p>Trámites con el plazo vencido, hoy</p></header>
            <?php if ($ranking): ?>
                <div class="ind-chart"><canvas id="g-ranking" aria-label="Ranking de vencidos"></canvas></div>
            <?php else: ?>
                <p class="ind-vacio">No hay trámites vencidos.</p>
            <?php endif; ?>
        </section>
    </div>

    <section class="ind-card">
        <header class="ind-card-acciones">
            <div>
                <h3>Trámites vencidos, del más antiguo al más reciente</h3>
                <p><?php echo count($detalle) >= 1000 ? 'Se muestran los 1.000 más antiguos. Elija una oficina para acotar la lista.' : $M::numero(count($detalle)) . (count($detalle) === 1 ? ' trámite.' : ' trámites.'); ?> Haga clic en la hoja de ruta para ver su seguimiento.</p>
            </div>
            <?php if ($detalle): ?>
            <div class="ind-herramientas">
                <input type="search" class="ind-buscar" placeholder="Buscar hoja de ruta, persona, oficina…" data-buscar="tabla-rezago" aria-label="Buscar en la tabla">
                <button type="button" class="ind-btn" data-exportar="tabla-rezago" data-nombre="vencidos_<?php echo date('Y-m-d'); ?>"><i class="fa fa-download" aria-hidden="true"></i> Excel (CSV)</button>
            </div>
            <?php endif; ?>
        </header>
        <?php if (!$detalle): ?>
            <p class="ind-vacio">No hay trámites vencidos.</p>
        <?php else: ?>
        <div class="ind-tabla-wrap ind-tabla-alta">
            <table class="ind-tabla" id="tabla-rezago">
                <thead>
                    <tr>
                        <th data-tipo="texto">Hoja de ruta</th>
                        <th data-tipo="texto">Referencia</th>
                        <th data-tipo="texto">Está con</th>
                        <th data-tipo="texto">Enviado por</th>
                        <th class="num">Derivado el</th>
                        <th data-tipo="texto">Instrucción</th>
                        <th data-tipo="texto">Estado</th>
                        <th class="num">Días háb.</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($detalle as $x): ?>
                    <tr>
                        <td data-valor="<?php echo HTML::chars($x['nur']); ?>">
                            <a class="ind-nombre" href="/route/trace/?hr=<?php echo urlencode($x['nur']); ?>"><?php echo HTML::chars($x['nur']); ?></a>
                            <?php if ($x['prioridad']): ?><span class="ind-pastilla ind-rojo">Urgente</span><?php endif; ?>
                            <?php if (!$x['oficial']): ?><span class="ind-pastilla ind-gris">Copia</span><?php endif; ?>
                        </td>
                        <td data-valor="<?php echo HTML::chars($x['referencia']); ?>" class="ind-referencia"><?php echo HTML::chars($x['referencia']); ?></td>
                        <td data-valor="<?php echo HTML::chars($x['nombre_receptor']); ?>">
                            <span class="ind-nombre"><?php echo HTML::chars($x['nombre_receptor']); ?></span>
                            <span class="ind-detalle"><?php echo HTML::chars($x['a_oficina']); ?></span>
                        </td>
                        <td data-valor="<?php echo HTML::chars($x['nombre_emisor']); ?>">
                            <?php echo HTML::chars($x['nombre_emisor']); ?>
                            <span class="ind-detalle"><?php echo HTML::chars($x['de_oficina']); ?></span>
                        </td>
                        <td class="num" data-valor="<?php echo strtotime($x['fecha_emision']); ?>"><?php echo date('d/m/Y', strtotime($x['fecha_emision'])); ?></td>
                        <td data-valor="<?php echo HTML::chars($x['accion']); ?>"><?php echo HTML::chars($x['accion']); ?></td>
                        <td data-valor="<?php echo $x['estado']; ?>"><?php echo $x['estado'] == Model_Indicadores::NO_RECIBIDO ? 'Sin recibir' : 'Recibido'; ?></td>
                        <td class="num" data-valor="<?php echo $x['dias']; ?>"><b class="<?php echo $x['dias'] > 30 ? 'ind-rojo' : 'ind-ambar'; ?>"><?php echo $M::numero($x['dias']); ?></b></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php echo View::factory('reportes/indicadores/_graficos'); ?>
<script>
(function () {
    var G = window.IndGraficos;
    G.barras('g-antiguedad', ['0–2', '3–<?php echo $plazo - 1; ?>', '<?php echo $plazo; ?>–10', '11–30', '31–120', 'más de 120'],
        <?php echo json_encode(array($r['b0'], $r['b1'], $r['b2'], $r['b3'], $r['b4'], $r['b5'])); ?>,
        {colores: [G.c.verde, G.c.verde, G.c.ambar, G.c.rojo, G.c.rojo, G.c.rojoOscuro], etiqueta: 'Abiertos'});
    var ranking = <?php echo json_encode(array_map(function ($x) use ($por_usuario) {
        return array('n' => $por_usuario ? $x['nombre'] : ($x['sigla'] ?: $x['nombre']), 'v' => $x['vencidos']);
    }, $ranking)); ?>;
    if (ranking.length) {
        G.barras('g-ranking', ranking.map(function (x) { return x.n.length > 26 ? x.n.slice(0, 25) + '…' : x.n; }), ranking.map(function (x) { return x.v; }),
            {horizontal: true, colores: G.c.rojo, etiqueta: 'Vencidos'});
    }
}());
</script>
