<?php
$h = function ($s) {
    return HTML::chars($s);
};
$grupos = array();
foreach ($permisos as $clave => $p) {
    $grupos[$p[2]][$clave] = $p;
}
?>
<style>
    .rp-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .rp-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 16px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .rp-cab-icono {
        flex: 0 0 44px;
        height: 44px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #1A549A;
        color: #fff;
        font-size: 18px;
    }
    .rp-cab-texto {
        flex: 1 1 240px;
        min-width: 0;
    }
    .rp-cab h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 600;
        color: #123E73;
    }
    .rp-cab p {
        margin: 3px 0 0;
        font-size: 12.5px;
        color: #6b7686;
    }
    .rp-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        background: #E6F4EC;
        color: #1F6B40;
    }
    .rp-msg.mal { background: #FDECEC; color: #A23131; }
    .rp-msg.aviso { background: #FFF7DD; color: #7A5A00; }
    .rp-msg .fa { margin-top: 2px; }
    .rp-seccion {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 4px 12px;
        padding: 16px 22px 10px;
    }
    .rp-seccion h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #123E73;
    }
    .rp-seccion span {
        font-size: 12.5px;
        color: #6b7686;
    }
    .rp-tabla-caja {
        overflow-x: auto;
        padding: 0 10px 12px;
        -webkit-overflow-scrolling: touch;
    }
    table.rp-tabla {
        width: 100%;
        min-width: 640px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }
    .rp-tabla th,
    .rp-tabla td {
        padding: 10px 12px;
        border-bottom: 1px solid #EEF1F5;
        vertical-align: middle;
    }
    .rp-tabla thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #F6F8FB;
        color: #123E73;
        font-size: 12px;
        text-align: center;
        border-bottom: 2px solid #E1E7EF;
        white-space: nowrap;
    }
    .rp-tabla thead th:first-child {
        text-align: left;
        border-radius: 10px 0 0 0;
    }
    .rp-tabla thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-rol small {
        display: block;
        margin-top: 2px;
        font-weight: 500;
        color: #7a8594;
        font-size: 11px;
    }
    .rp-rol.propio { box-shadow: inset 0 -3px 0 #FECB34; }
    .rp-tabla td.rp-celda {
        text-align: center;
        width: 120px;
        transition: background .15s;
    }
    .rp-tabla td.rp-celda.cambiado { background: #FFF7DD; }
    .rp-grupo td {
        padding: 14px 12px 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #8a94a3;
        border-bottom: none;
    }
    .rp-nombre b {
        display: block;
        color: #1f2d3d;
        font-weight: 600;
    }
    .rp-nombre span {
        display: block;
        margin-top: 2px;
        color: #6b7686;
        font-size: 12px;
        line-height: 1.4;
        max-width: 520px;
    }
    .rp-menu-icono {
        display: inline-flex;
        width: 26px;
        height: 26px;
        margin-right: 8px;
        border-radius: 7px;
        align-items: center;
        justify-content: center;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 12px;
        vertical-align: middle;
    }
    .rp-menu-fila { display: flex; align-items: flex-start; }
    .rp-nombre .rp-etiqueta {
        display: inline-block;
        margin-left: 6px;
        padding: 1px 7px;
        border-radius: 10px;
        background: #ECEFF3;
        color: #5b6573;
        font-size: 10.5px;
        font-weight: 600;
        vertical-align: middle;
    }
    .rp-nombre .rp-etiqueta.lock { background: #EEF3FA; color: #1A549A; }
    .rp-fijo { color: #9aa3af; font-size: 15px; }
    .rp-fijo.si { color: #227547; }
    /* interruptor */
    .rp-sw {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
        margin: 0;
        cursor: pointer;
        vertical-align: middle;
    }
    .rp-sw input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        cursor: pointer;
        z-index: 1;
    }
    .rp-sw i {
        position: absolute;
        inset: 0;
        border-radius: 22px;
        background: #CBD3DE;
        transition: background .15s;
    }
    .rp-sw i:after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .25);
        transition: transform .15s;
    }
    .rp-sw input:checked + i { background: #1A549A; }
    .rp-sw input:checked + i:after { transform: translateX(18px); }
    .rp-sw input:focus-visible + i { outline: 2px solid #FECB34; outline-offset: 2px; }
    .rp-sw input:disabled { cursor: not-allowed; }
    .rp-sw input:disabled + i { opacity: .5; }
    /* barra de guardado */
    .rp-barra {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 12px 18px;
        margin-bottom: 18px;
        border-radius: 14px;
        background: #123E73;
        color: #fff;
        box-shadow: 0 -2px 12px rgba(18, 62, 115, .25);
    }
    .rp-barra span { flex: 1 1 220px; font-size: 13px; }
    .rp-barra .btn { margin: 0; }
    .rp-barra .btn-descartar { background: transparent; border: 1px solid rgba(255, 255, 255, .5); color: #fff; }
    .rp-barra .btn-guardar { background: #FECB34; border-color: #FECB34; color: #123E73; font-weight: 700; }
    .rp-barra .btn-guardar:disabled { opacity: .55; }
    @media (max-width: 600px) {
        .rp-cab, .rp-seccion { padding-left: 16px; padding-right: 16px; }
        .rp-tabla-caja { padding: 0 4px 10px; }
    }
</style>

<div class="rp-card">
    <div class="rp-cab">
        <div class="rp-cab-icono"><i class="fa fa-shield"></i></div>
        <div class="rp-cab-texto">
            <h2>Roles y permisos</h2>
            <p>Qué puede ver, descargar y usar cada rol. Quien interviene en una hoja de ruta siempre la ve y descarga; aquí se decide qué más puede hacer cada rol.</p>
        </div>
    </div>
</div>

<?php if ($mensaje): ?>
    <div class="rp-msg"><i class="fa fa-check-circle"></i> <span><?php echo $h($mensaje); ?></span></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="rp-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><b>No se guardó.</b> <?php echo $error; ?></span></div>
<?php endif; ?>
<?php if (!$tabla_lista): ?>
    <div class="rp-msg aviso"><i class="fa fa-database"></i>
        <span><b>Solo lectura:</b> falta crear la tabla <b>nivel_permisos</b> en la base de datos. Mientras tanto el sistema funciona con los valores que se ven aquí, pero no se pueden cambiar.</span></div>
<?php endif; ?>

<form method="post" action="/admin/roles" id="rp-form" autocomplete="off">

    <div class="rp-card">
        <div class="rp-seccion">
            <h3><i class="fa fa-key"></i> Permisos</h3>
            <span>Lo que cada rol puede hacer además de lo suyo.</span>
        </div>
        <div class="rp-tabla-caja">
            <table class="rp-tabla">
                <thead>
                    <tr>
                        <th scope="col">Permiso</th>
                        <?php foreach ($niveles as $n): ?>
                            <th scope="col" class="rp-rol <?php echo (int) $n['id'] === $nivel_propio ? 'propio' : ''; ?>" title="<?php echo $h($n['descripcion']); ?>">
                                <?php echo $h($n['nivel']); ?>
                                <small><?php echo (int) $n['activos']; ?> activo<?php echo (int) $n['activos'] === 1 ? '' : 's'; ?><?php echo (int) $n['id'] === $nivel_propio ? ' · usted' : ''; ?></small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grupos as $grupo => $lista): ?>
                        <tr class="rp-grupo"><td colspan="<?php echo count($niveles) + 1; ?>"><?php echo $h($grupo); ?></td></tr>
                        <?php foreach ($lista as $clave => $p): ?>
                            <tr>
                                <td class="rp-nombre"><b><?php echo $h($p[0]); ?></b><span><?php echo $h($p[1]); ?></span></td>
                                <?php foreach ($niveles as $n): $on = !empty($valores[$n['id']][$clave]); ?>
                                    <td class="rp-celda">
                                        <label class="rp-sw" title="<?php echo $h($p[0] . ' · ' . $n['nivel']); ?>">
                                            <input type="checkbox" name="permiso[<?php echo (int) $n['id']; ?>][<?php echo $h($clave); ?>]" value="1"
                                                   data-inicial="<?php echo $on ? 1 : 0; ?>" <?php echo $on ? 'checked' : ''; ?> <?php echo $tabla_lista ? '' : 'disabled'; ?>
                                                   aria-label="<?php echo $h($p[0] . ' para ' . $n['nivel']); ?>">
                                            <i></i>
                                        </label>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rp-card">
        <div class="rp-seccion">
            <h3><i class="fa fa-bars"></i> Pestañas del menú</h3>
            <span>Qué pestañas aparecen en el menú lateral de cada rol. Quitar una pestaña no bloquea sus páginas por enlace.</span>
        </div>
        <div class="rp-tabla-caja">
            <table class="rp-tabla">
                <thead>
                    <tr>
                        <th scope="col">Pestaña</th>
                        <?php foreach ($niveles as $n): ?>
                            <th scope="col" class="rp-rol <?php echo (int) $n['id'] === $nivel_propio ? 'propio' : ''; ?>"><?php echo $h($n['nivel']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td class="rp-nombre">
                                <div class="rp-menu-fila">
                                    <span class="rp-menu-icono"><i class="<?php echo $h(preg_match('/^(fa|md) [a-z0-9-]+$/', trim((string) $m['logo'])) ? trim($m['logo']) : 'fa fa-circle-o'); ?>"></i></span>
                                    <div>
                                        <b><?php echo $h($m['menu']); ?>
                                            <?php if ($m['reportes']): ?><span class="rp-etiqueta lock"><i class="fa fa-lock"></i> según el permiso «Reportes del sistema»</span><?php endif; ?>
                                            <?php if ((int) $m['opciones'] === 0): ?><span class="rp-etiqueta">sin opciones: no se muestra</span><?php endif; ?>
                                        </b>
                                        <span><?php echo $h(trim((string) $m['controlador']) !== '' ? '/' . trim($m['controlador'], '/') : 'sin enlace'); ?><?php echo $m['lista'] ? ' · ' . $h($m['lista']) : ''; ?></span>
                                    </div>
                                </div>
                            </td>
                            <?php foreach ($niveles as $n): ?>
                                <td class="rp-celda">
                                    <?php if ($m['reportes']): $si = !empty($valores[$n['id']]['ver_reportes']); ?>
                                        <i class="fa <?php echo $si ? 'fa-check-circle' : 'fa-minus-circle'; ?> rp-fijo <?php echo $si ? 'si' : ''; ?>"
                                           data-reportes="<?php echo (int) $n['id']; ?>" title="Se cambia con el permiso «Reportes del sistema»"></i>
                                    <?php else: $on = !empty($asignados[$n['id']][$m['id']]); ?>
                                        <label class="rp-sw" title="<?php echo $h($m['menu'] . ' · ' . $n['nivel']); ?>">
                                            <input type="checkbox" name="menu[<?php echo (int) $n['id']; ?>][<?php echo (int) $m['id']; ?>]" value="1"
                                                   data-inicial="<?php echo $on ? 1 : 0; ?>" <?php echo $on ? 'checked' : ''; ?> <?php echo $tabla_lista ? '' : 'disabled'; ?>
                                                   aria-label="<?php echo $h('Pestaña ' . $m['menu'] . ' para ' . $n['nivel']); ?>">
                                            <i></i>
                                        </label>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($tabla_lista): ?>
        <div class="rp-barra">
            <span id="rp-estado"><i class="fa fa-info-circle"></i> Sin cambios.</span>
            <button type="button" class="btn btn-descartar" id="rp-descartar" disabled><i class="fa fa-undo"></i> Descartar</button>
            <button type="submit" class="btn btn-guardar" id="rp-guardar" disabled><i class="fa fa-save"></i> Guardar cambios</button>
        </div>
    <?php endif; ?>
</form>

<script>
    (function () {
        var form = document.getElementById('rp-form');
        if (!form) { return; }
        var casillas = form.querySelectorAll('input[type=checkbox][data-inicial]');
        var estado = document.getElementById('rp-estado');
        var guardar = document.getElementById('rp-guardar');
        var descartar = document.getElementById('rp-descartar');
        var enviando = false;

        function cambios() {
            var n = 0;
            for (var i = 0; i < casillas.length; i++) {
                var c = casillas[i];
                var distinto = (c.checked ? '1' : '0') !== c.getAttribute('data-inicial');
                c.closest('td').classList.toggle('cambiado', distinto);
                if (distinto) { n++; }
            }
            return n;
        }
        // la fila de Reportes en "Pestañas" refleja el permiso mientras se edita
        function reflejarReportes() {
            var marcas = form.querySelectorAll('[data-reportes]');
            for (var i = 0; i < marcas.length; i++) {
                var nivel = marcas[i].getAttribute('data-reportes');
                var p = form.querySelector('input[name="permiso[' + nivel + '][ver_reportes]"]');
                var si = p && p.checked;
                marcas[i].className = 'fa ' + (si ? 'fa-check-circle' : 'fa-minus-circle') + ' rp-fijo' + (si ? ' si' : '');
            }
        }
        function actualizar() {
            var n = cambios();
            reflejarReportes();
            if (!estado) { return; }
            estado.innerHTML = n
                ? '<i class="fa fa-pencil"></i> <b>' + n + '</b> cambio' + (n === 1 ? '' : 's') + ' sin guardar (resaltados en amarillo).'
                : '<i class="fa fa-info-circle"></i> Sin cambios.';
            guardar.disabled = n === 0;
            descartar.disabled = n === 0;
        }
        form.addEventListener('change', actualizar);
        if (descartar) {
            descartar.addEventListener('click', function () {
                for (var i = 0; i < casillas.length; i++) {
                    casillas[i].checked = casillas[i].getAttribute('data-inicial') === '1';
                }
                actualizar();
            });
        }
        form.addEventListener('submit', function (e) {
            var propio = <?php echo (int) $nivel_propio; ?>;
            var rep = form.querySelector('input[name="permiso[' + propio + '][ver_reportes]"]');
            if (rep && !rep.checked && rep.getAttribute('data-inicial') === '1'
                && !confirm('Está quitando los Reportes a su propio rol. ¿Continuar?')) {
                e.preventDefault();
                return;
            }
            enviando = true;
            guardar.disabled = true;
            guardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando…';
        });
        window.addEventListener('beforeunload', function (e) {
            if (!enviando && cambios() > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        actualizar();
    })();
</script>
