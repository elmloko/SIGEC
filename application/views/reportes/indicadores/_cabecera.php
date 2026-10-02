<?php
// modo "propio": tablero personal del usuario logueado (sin pestañas de administrador ni selector)
$propio = isset($modo) && $modo === 'propio';
$paginas = array(
    'tablero' => array('Tablero general', 'fa-tachometer'),
    'desempeno' => array('Desempeño', 'fa-users'),
    'persona' => array('Por persona', 'fa-user'),
    'rezago' => array('Rezago y vencidos', 'fa-hourglass-half'),
    'produccion' => array('Producción documental', 'fa-file-text-o'),
);
$qs = function ($cambios = array()) use ($f) {
    $p = array_merge(array('desde' => $f['desde'], 'hasta' => $f['hasta'], 'oficina' => $f['oficina'], 'usuario' => $f['usuario']), $cambios);
    foreach (array('oficina', 'usuario') as $k) {
        if (empty($p[$k])) {
            unset($p[$k]);
        }
    }
    return '?' . http_build_query($p);
};
// texto del encabezado del PDF: periodo y a quien corresponde
$alcance = 'Todas las oficinas';
if ($pagina === 'persona') {
    $alcance = '';
    foreach ($f['usuarios'] as $lista) {
        if (isset($lista[$f['usuario']])) {
            $alcance = $lista[$f['usuario']];
        }
    }
} elseif ($f['oficina']) {
    $alcance = $f['oficinas'][$f['oficina']];
}
$pdf_subtitulo = 'Periodo ' . date('d/m/Y', strtotime($f['desde'])) . ' al ' . date('d/m/Y', strtotime($f['hasta'])) . ($alcance !== '' ? ' - ' . $alcance : '');
$pdf_archivo = ($propio ? 'mis_indicadores' : 'indicadores_' . $pagina) . ($alcance !== '' && $alcance !== 'Todas las oficinas' ? '_' . $alcance : '') . '_' . $f['desde'] . '_' . $f['hasta'];
$atajos = array(
    'Este mes' => array(date('Y-m-01'), date('Y-m-d')),
    'Últimos 30 días' => array(date('Y-m-d', strtotime('-29 days')), date('Y-m-d')),
    'Este año' => array(date('Y-01-01'), date('Y-m-d')),
    'Año anterior' => array((date('Y') - 1) . '-01-01', (date('Y') - 1) . '-12-31'),
);
?>
<div class="ind-pdf-meta" hidden data-titulo="<?php echo HTML::chars($propio ? 'Mis indicadores' : $paginas[$pagina][0]); ?>" data-subtitulo="<?php echo HTML::chars($pdf_subtitulo); ?>" data-archivo="<?php echo HTML::chars($pdf_archivo); ?>"></div>
<script src="/media/js/indicadores-pdf.js?v=20261002" defer></script>
<?php if (!$propio): ?>
<nav class="ind-tabs" aria-label="Reportes">
    <?php foreach ($paginas as $clave => $p): ?>
        <a href="<?php echo URL::site('reports/' . $clave) . $qs(); ?>" class="<?php echo $pagina === $clave ? 'activo' : ''; ?>"<?php echo $pagina === $clave ? ' aria-current="page"' : ''; ?>>
            <i class="fa <?php echo $p[1]; ?>" aria-hidden="true"></i> <?php echo $p[0]; ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<form class="ind-filtros" method="get" action="">
    <label>
        <span>Desde</span>
        <input type="date" name="desde" value="<?php echo HTML::chars($f['desde']); ?>" max="<?php echo date('Y-m-d'); ?>">
    </label>
    <label>
        <span>Hasta</span>
        <input type="date" name="hasta" value="<?php echo HTML::chars($f['hasta']); ?>" max="<?php echo date('Y-m-d'); ?>">
    </label>
    <?php if ($propio): ?>
    <?php elseif ($pagina === 'persona'): ?>
    <label class="ind-filtro-oficina">
        <span>Funcionario</span>
        <select name="usuario" required>
            <option value="">Elija un funcionario…</option>
            <?php foreach ($f['usuarios'] as $oficina => $lista): ?>
                <optgroup label="<?php echo HTML::chars($oficina); ?>">
                    <?php foreach ($lista as $id => $nombre): ?>
                        <option value="<?php echo $id; ?>"<?php echo $f['usuario'] == $id ? ' selected' : ''; ?>><?php echo HTML::chars($nombre); ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
    </label>
    <?php else: ?>
    <label class="ind-filtro-oficina">
        <span>Oficina</span>
        <select name="oficina">
            <option value="0">Todas las oficinas</option>
            <?php foreach ($f['oficinas'] as $id => $nombre): ?>
                <option value="<?php echo $id; ?>"<?php echo $f['oficina'] == $id ? ' selected' : ''; ?>><?php echo HTML::chars($nombre); ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php endif; ?>
    <button type="submit" class="ind-btn ind-btn-primario"><i class="fa fa-filter" aria-hidden="true"></i> Aplicar</button>
    <button type="button" class="ind-btn" data-pdf title="Descargar esta página en PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF</button>
    <div class="ind-atajos">
        <?php foreach ($atajos as $texto => $r): ?>
            <a href="<?php echo $qs(array('desde' => $r[0], 'hasta' => $r[1])); ?>" class="<?php echo $f['desde'] === $r[0] && $f['hasta'] === $r[1] ? 'activo' : ''; ?>"><?php echo $texto; ?></a>
        <?php endforeach; ?>
    </div>
</form>

<p class="ind-nota">
    Periodo <b><?php echo date('d/m/Y', strtotime($f['desde'])); ?></b> al <b><?php echo date('d/m/Y', strtotime($f['hasta'])); ?></b>
    <?php if ($f['oficina'] && $pagina !== 'persona'): ?> ·<b><?php echo HTML::chars($f['oficinas'][$f['oficina']]); ?></b><?php endif; ?>
    · Datos calculados el <?php echo date('d/m/Y H:i', strtotime($d['generado'])); ?>
    <a href="<?php echo $qs(array('refrescar' => 1)); ?>" title="Volver a calcular ahora"><i class="fa fa-refresh" aria-hidden="true"></i> Actualizar</a>
</p>
