<?php
/*
 * Boton "Nuevo documento" del menu lateral: despliega los tipos de documento que el usuario puede generar
 * (tabla usertipo) y cada uno lleva directo a /documento/generar/{tipo}.
 */
$usuario_nd = Auth::instance()->get_user();
$tipos_nd = DB::query(Database::SELECT, 'SELECT t.tipo, t.action FROM usertipo u INNER JOIN tipos t ON t.id = u.id_tipo
        WHERE u.id_user = :u AND t.id <> 6 ORDER BY t.id')
        ->param(':u', (int) $usuario_nd->id)
        ->execute()->as_array();
if (!count($tipos_nd)) {
    return;
}
// iconos por palabra clave del nombre (los mismos que en Mis documentos)
$iconos_nd = array(
    'informe' => 'fa-file-text-o',
    'memo' => 'fa-clipboard',
    'circular' => 'fa-bullhorn',
    'carta' => 'fa-envelope-o',
    'instructivo' => 'fa-list-ol',
    'intructivo' => 'fa-list-ol',
    'comunicado' => 'fa-comment-o',
    'nota' => 'fa-pencil-square-o',
    'resoluci' => 'fa-gavel',
    'certificado' => 'fa-certificate',
);
$icono_nd = function ($nombre) use ($iconos_nd) {
    $n = mb_strtolower($nombre, 'UTF-8');
    foreach ($iconos_nd as $clave => $icono) {
        if (strpos($n, $clave) !== FALSE) {
            return $icono;
        }
    }
    return 'fa-file-o';
};
?>
<div class="mn-nuevo" id="mn-nuevo">
    <a href="/document" class="mn-cta" id="mn-nuevo-btn" aria-expanded="false" aria-controls="mn-nuevo-lista" title="Generar un documento nuevo">
        <i class="fa fa-plus"></i> Nuevo documento <span class="mn-cta-flecha fa fa-angle-down"></span>
    </a>
    <ul class="mn-nuevo-lista" id="mn-nuevo-lista">
        <?php foreach ($tipos_nd as $t): ?>
            <li>
                <a href="/documento/generar/<?php echo HTML::chars($t['action']); ?>">
                    <i class="fa <?php echo $icono_nd($t['tipo']); ?> fa-fw"></i> <?php echo HTML::chars($t['tipo']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<script>
    (function () {
        var caja = document.getElementById('mn-nuevo');
        var btn = document.getElementById('mn-nuevo-btn');
        if (!caja || !btn) {
            return;
        }
        function abrir(si) {
            caja.className = 'mn-nuevo' + (si ? ' abierto' : '');
            btn.setAttribute('aria-expanded', si ? 'true' : 'false');
        }
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            abrir(caja.className.indexOf('abierto') === -1);
        });
        // se cierra al hacer clic fuera o con Esc
        document.addEventListener('click', function (e) {
            if (!caja.contains(e.target)) {
                abrir(false);
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                abrir(false);
            }
        });
    })();
</script>
