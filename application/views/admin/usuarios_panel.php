<?php
$dir_fotos = DOCROOT . 'static/fotos/';
$datos = array();
$oficinas = array();
$niveles = array();
$alta = 0;
foreach ($usuarios as $u) {
    $foto = $dir_fotos . $u['username'] . '.jpg';
    $datos[] = array(
        'id' => (int) $u['id'],
        'u' => $u['username'],
        'n' => trim($u['nombre']),
        'c' => trim($u['cargo']),
        'e' => $u['email'],
        'm' => $u['mosca'],
        'o' => trim($u['oficina']),
        'en' => $u['entidad'],
        'nv' => $u['nivel'],
        'l' => (int) $u['logins'],
        'ul' => (int) $u['last_login'],
        'h' => (int) $u['habilitado'],
        'f' => file_exists($foto) ? '/static/fotos/' . $u['username'] . '.jpg' : '/static/fotos/' . ($u['genero'] == 'mujer' ? 'mujer' : 'hombre') . '.jpg',
    );
    $oficinas[trim($u['oficina'])] = 1;
    $niveles[$u['nivel']] = 1;
    $alta += (int) $u['habilitado'] === 1 ? 1 : 0;
}
ksort($oficinas);
ksort($niveles);
$baja = count($datos) - $alta;
?>
<style>
    .us-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .us-card .btn {
        margin: 0;
    }
    .us-card .btn .fa {
        margin-right: 5px;
    }
    .us-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 18px 22px;
    }
    .us-cab-icono {
        flex: 0 0 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-size: 20px;
    }
    .us-cab-texto {
        flex: 1 1 260px;
    }
    .us-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .us-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    /* pestanas */
    .us-tabs {
        display: flex;
        gap: 4px;
        padding: 0 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .us-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 0;
        border-radius: 10px 10px 0 0;
        background: transparent;
        font-size: 13.5px;
        font-weight: 600;
        color: #6b7686;
        cursor: pointer;
    }
    .us-tab b {
        padding: 1px 8px;
        border-radius: 10px;
        background: #EEF2F7;
        font-size: 11.5px;
        color: #4a5568;
    }
    .us-tab.activo {
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .us-tab.activo b {
        background: #fff;
        color: var(--correos-azul, #1A549A);
    }
    /* filtros */
    .us-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 14px 22px;
        border-bottom: 1px solid #EEF2F7;
    }
    .us-buscar {
        position: relative;
        flex: 1 1 280px;
    }
    .us-buscar .fa {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .us-buscar input,
    .us-filtros select {
        height: 38px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 19px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 13px;
        color: #2d3748;
    }
    .us-buscar input {
        width: 100%;
        padding: 4px 12px 4px 34px;
    }
    .us-filtros select {
        max-width: 240px;
        padding: 4px 12px;
    }
    .us-buscar input:focus,
    .us-filtros select:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        background: #fff;
    }
    .us-info {
        margin-left: auto;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .us-info b {
        color: var(--correos-azul, #1A549A);
    }
    /* tabla */
    .us-tabla {
        width: 100%;
        border-collapse: collapse;
    }
    .us-tabla th {
        padding: 10px 14px;
        border-bottom: 1px solid #EEF2F7;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #8a94a3;
        white-space: nowrap;
    }
    .us-tabla th.ord {
        cursor: pointer;
    }
    .us-tabla th.ord:hover,
    .us-tabla th.ord.activo {
        color: var(--correos-azul, #1A549A);
    }
    .us-tabla td {
        padding: 10px 14px;
        border-bottom: 1px solid #F1F4F8;
        vertical-align: middle;
        font-size: 13px;
        color: #4a5568;
    }
    .us-tabla tbody tr:hover {
        background: #FAFBFD;
    }
    .us-tabla td:first-child,
    .us-tabla th:first-child {
        padding-left: 22px;
    }
    .us-persona {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 240px;
    }
    .us-persona img {
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .us-persona a {
        display: block;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .us-persona span {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .us-cargo {
        display: block;
        font-size: 12.5px;
        color: #2d3748;
    }
    .us-oficina {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .us-rol {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        background: #EEF2F7;
        color: #4a5568;
        white-space: nowrap;
    }
    .us-rol.admin {
        background: #FDE8E8;
        color: #B42318;
    }
    .us-rol.ventanilla {
        background: #FFF7DD;
        color: #8a6100;
    }
    .us-rol.jefe {
        background: #EAF1F9;
        color: #1A549A;
    }
    .us-ingreso b {
        display: block;
        font-size: 12.5px;
        color: #2d3748;
    }
    .us-ingreso span {
        font-size: 11.5px;
        color: #8a94a3;
    }
    .us-ingreso .nunca {
        color: #B7791F;
    }
    .us-acciones {
        text-align: right;
        white-space: nowrap;
    }
    .us-acciones .btn-editar {
        padding: 5px 10px;
    }
    .us-menu-btn {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #6b7686;
    }
    .us-menu-btn:hover,
    .open > .us-menu-btn {
        background: var(--correos-fondo, #F3F5F8);
        color: var(--correos-azul, #1A549A);
    }
    .us-acciones .dropdown-menu {
        min-width: 230px;
        padding: 6px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(18, 62, 115, .18);
    }
    .us-acciones .dropdown-menu > li > a {
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        color: #2d3748;
    }
    .us-acciones .dropdown-menu > li > a .fa {
        width: 18px;
        margin-right: 6px;
        color: var(--correos-azul, #1A549A);
    }
    .us-acciones .dropdown-menu > li > a:hover {
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .us-acciones .dropdown-menu > li > a.peligro,
    .us-acciones .dropdown-menu > li > a.peligro .fa {
        color: #D32F2F;
    }
    .us-acciones .dropdown-menu > li > a.peligro:hover {
        background: #FDE8E8;
    }
    .us-acciones .dropdown-menu > li > a.exito,
    .us-acciones .dropdown-menu > li > a.exito .fa {
        color: #227547;
    }
    .us-vacio {
        padding: 40px 20px;
        text-align: center;
        color: #9aa4b2;
    }
    .us-mas {
        padding: 14px;
        text-align: center;
        border-top: 1px solid #EEF2F7;
    }
    mark {
        padding: 0;
        background: #FFF0B3;
        color: inherit;
    }
    /* aviso flotante */
    #us-aviso {
        position: fixed;
        z-index: 3000;
        right: 24px;
        bottom: 24px;
        display: none;
        max-width: 360px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #123E73;
        color: #fff;
        font-size: 13px;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .25);
    }
    #us-aviso.error {
        background: #B42318;
    }
    @media (max-width: 991px) {
        .us-col-ingreso,
        .us-col-logins {
            display: none;
        }
    }
</style>

<div class="col-lg-12">
    <div class="us-card">
        <div class="us-cab">
            <div class="us-cab-icono"><i class="fa fa-users"></i></div>
            <div class="us-cab-texto">
                <h2>Usuarios</h2>
                <p><?php echo $alta; ?> activos · <?php echo $baja; ?> dados de baja</p>
            </div>
            <a href="#" class="btn btn-default-bright" id="passDefecto" title="Ver y cambiar la contraseña con la que se crean o restablecen las cuentas"><i class="fa fa-key"></i> Contraseña por defecto</a>
            <a href="/admin/user/add" class="btn btn-primary"><i class="fa fa-user-plus"></i> Nuevo usuario</a>
        </div>

        <div class="us-tabs" role="tablist">
            <button type="button" class="us-tab activo" data-estado="1"><i class="fa fa-user"></i> Activos <b id="us-n1"><?php echo $alta; ?></b></button>
            <button type="button" class="us-tab" data-estado="0"><i class="fa fa-user-times"></i> De baja <b id="us-n0"><?php echo $baja; ?></b></button>
        </div>

        <div class="us-filtros">
            <div class="us-buscar">
                <i class="fa fa-search"></i>
                <input type="search" id="us-q" placeholder="Buscar por nombre, usuario, cargo, oficina o correo…" autocomplete="off"/>
            </div>
            <select id="us-oficina" title="Oficina">
                <option value="">Todas las oficinas</option>
                <?php foreach (array_keys($oficinas) as $o): ?>
                    <option value="<?php echo HTML::chars($o); ?>"><?php echo HTML::chars($o); ?></option>
                <?php endforeach; ?>
            </select>
            <select id="us-nivel" title="Rol">
                <option value="">Todos los roles</option>
                <?php foreach (array_keys($niveles) as $nv): ?>
                    <option value="<?php echo HTML::chars($nv); ?>"><?php echo HTML::chars($nv); ?></option>
                <?php endforeach; ?>
            </select>
            <span class="us-info"><b id="us-visibles">0</b> usuarios</span>
        </div>

        <table class="us-tabla">
            <thead>
            <tr>
                <th class="ord activo" data-orden="n">Usuario <i class="fa fa-sort-asc"></i></th>
                <th class="ord" data-orden="c">Cargo y oficina</th>
                <th>Rol</th>
                <th class="ord us-col-ingreso" data-orden="ul">Último ingreso</th>
                <th class="ord us-col-logins" data-orden="l">Ingresos</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="us-filas"></tbody>
        </table>
        <div class="us-vacio" id="us-vacio" style="display:none"><i class="fa fa-search" style="font-size:26px;display:block;margin-bottom:6px"></i> Nadie coincide con los filtros.</div>
        <div class="us-mas" id="us-mas" style="display:none"><button type="button" class="btn btn-default-bright" id="us-ver-mas"><i class="fa fa-angle-down"></i> Mostrar más</button></div>
    </div>
</div>
<div id="us-aviso"></div>

<script>
    $(function () {
        var USUARIOS = <?php echo json_encode($datos); ?>;
        var YO = <?php echo (int) $yo; ?>;
        var estado = location.hash === '#baja' ? 0 : 1;
        var orden = {campo: 'n', asc: true};
        var limite = 50, mostrados = 50;

        function esc(t) {
            return $('<div>').text(t == null ? '' : String(t)).html();
        }
        function normal(t) {
            t = (t || '').toLowerCase();
            return t.normalize ? t.normalize('NFD').replace(/[̀-ͯ]/g, '') : t;
        }
        // resalta la primera palabra buscada que aparece
        function resaltar(texto, palabras) {
            var html = esc(texto), plano = normal(texto);
            palabras.some(function (w) {
                var i = plano.indexOf(w);
                if (i === -1) return false;
                html = esc(texto.substr(0, i)) + '<mark>' + esc(texto.substr(i, w.length)) + '</mark>' + esc(texto.substr(i + w.length));
                return true;
            });
            return html;
        }
        function plural(n, uno, varios) {
            return 'hace ' + n + ' ' + (n === 1 ? uno : varios);
        }
        function hace(ts) {
            if (!ts) return '<span class="nunca">Nunca ingresó</span>';
            var d = new Date(ts * 1000), s = (Date.now() - d.getTime()) / 1000, txt;
            if (s < 3600) txt = 'hace ' + Math.max(1, Math.round(s / 60)) + ' min';
            else if (s < 86400) txt = 'hace ' + Math.round(s / 3600) + ' h';
            else if (s < 86400 * 60) txt = plural(Math.round(s / 86400), 'día', 'días');
            else if (s < 86400 * 730) txt = plural(Math.round(s / 86400 / 30), 'mes', 'meses');
            else txt = plural(Math.round(s / 86400 / 365), 'año', 'años');
            var dd = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
            return '<b>' + txt + '</b><span>' + dd + '</span>';
        }
        function claseRol(n) {
            n = normal(n);
            if (n.indexOf('admin') > -1) return 'admin';
            if (n.indexOf('ventanilla') > -1) return 'ventanilla';
            if (n.indexOf('jefe') > -1) return 'jefe';
            return '';
        }
        function aviso(txt, error) {
            $('#us-aviso').toggleClass('error', !!error).text(txt).stop(true, true).fadeIn(150).delay(3200).fadeOut(300);
        }

        function filtrados() {
            var palabras = normal($('#us-q').val()).split(/\s+/).filter(Boolean);
            var of = $('#us-oficina').val(), nv = $('#us-nivel').val();
            var r = USUARIOS.filter(function (u) {
                if (u.h !== estado) return false;
                if (of && u.o !== of) return false;
                if (nv && u.nv !== nv) return false;
                if (!palabras.length) return true;
                var t = normal([u.n, u.u, u.c, u.o, u.e, u.m].join(' '));
                return palabras.every(function (w) {
                    return t.indexOf(w) > -1;
                });
            });
            r.sort(function (a, b) {
                var x = a[orden.campo], y = b[orden.campo], c;
                c = (typeof x === 'number') ? x - y : String(x).localeCompare(String(y), 'es', {sensitivity: 'base'});
                return orden.asc ? c : -c;
            });
            return {lista: r, palabras: palabras};
        }

        function fila(u, palabras) {
            var menu = '<li><a href="/user/profile/' + u.id + '"><i class="fa fa-user"></i> Ver perfil</a></li>' +
                '<li><a href="#" data-accion="documentos"><i class="fa fa-file-text-o"></i> Documentos permitidos</a></li>' +
                '<li><a href="#" data-accion="estadisticas"><i class="fa fa-bar-chart"></i> Estadísticas</a></li>' +
                '<li><a href="#" data-accion="plazos"><i class="fa fa-bell"></i> Asignar plazos</a></li>' +
                '<li class="divider"></li>' +
                '<li><a href="#" data-accion="reset"><i class="fa fa-repeat"></i> Restablecer contraseña</a></li>' +
                (u.h === 1
                    ? (u.id === YO ? '' : '<li><a href="#" data-accion="baja" class="peligro"><i class="fa fa-user-times"></i> Dar de baja</a></li>')
                    : '<li><a href="#" data-accion="alta" class="exito"><i class="fa fa-user-plus"></i> Dar de alta</a></li>');
            return '<tr data-id="' + u.id + '">' +
                '<td><div class="us-persona"><img src="' + esc(u.f) + '" alt="" loading="lazy"/><div>' +
                '<a href="/admin/user/edit/' + u.id + '" title="Editar">' + resaltar(u.n, palabras) + '</a>' +
                '<span>' + resaltar(u.u, palabras) + (u.e ? ' · ' + resaltar(u.e, palabras) : '') + '</span></div></div></td>' +
                '<td><span class="us-cargo">' + resaltar(u.c || '—', palabras) + '</span><span class="us-oficina">' + resaltar(u.o, palabras) + (u.en ? ' · ' + esc(u.en) : '') + '</span></td>' +
                '<td><span class="us-rol ' + claseRol(u.nv) + '">' + esc(u.nv) + '</span></td>' +
                '<td class="us-ingreso us-col-ingreso">' + hace(u.ul) + '</td>' +
                '<td class="us-col-logins">' + Number(u.l).toLocaleString('es-BO') + '</td>' +
                '<td class="us-acciones"><a href="/admin/user/edit/' + u.id + '" class="btn btn-xs btn-default-bright btn-editar"><i class="fa fa-pencil"></i> Editar</a> ' +
                '<span class="dropdown"><button type="button" class="us-menu-btn" data-toggle="dropdown" title="Más acciones"><i class="fa fa-ellipsis-v"></i></button>' +
                '<ul class="dropdown-menu dropdown-menu-right">' + menu + '</ul></span></td></tr>';
        }

        function pintar() {
            var r = filtrados();
            var html = '';
            r.lista.slice(0, mostrados).forEach(function (u) {
                html += fila(u, r.palabras);
            });
            $('#us-filas').html(html);
            $('#us-visibles').text(r.lista.length);
            $('#us-vacio').toggle(r.lista.length === 0);
            $('#us-mas').toggle(r.lista.length > mostrados);
            $('#us-n1').text(USUARIOS.filter(function (u) { return u.h === 1; }).length);
            $('#us-n0').text(USUARIOS.filter(function (u) { return u.h === 0; }).length);
        }
        function reiniciar() {
            mostrados = limite;
            pintar();
        }

        // pestañas (se recuerda en la direccion: #alta / #baja)
        function marcarPestana() {
            $('.us-tab').removeClass('activo').filter('[data-estado="' + estado + '"]').addClass('activo');
        }
        $('.us-tab').on('click', function () {
            estado = parseInt($(this).data('estado'), 10);
            if (history.replaceState) {
                history.replaceState(null, '', estado ? '#alta' : '#baja');
            }
            marcarPestana();
            reiniciar();
        });

        var temporizador;
        $('#us-q').on('input search', function () {
            clearTimeout(temporizador);
            temporizador = setTimeout(reiniciar, 150);
        });
        $('#us-oficina, #us-nivel').on('change', reiniciar);
        $('#us-ver-mas').on('click', function () {
            mostrados += limite;
            pintar();
        });

        // ordenar por columna
        $('.us-tabla th.ord').on('click', function () {
            var campo = $(this).data('orden');
            orden = {campo: campo, asc: orden.campo === campo ? !orden.asc : (campo === 'n' || campo === 'c')};
            $('.us-tabla th.ord').removeClass('activo').find('.fa').remove();
            $(this).addClass('activo').append(' <i class="fa fa-sort-' + (orden.asc ? 'asc' : 'desc') + '"></i>');
            pintar();
        });

        // acciones de cada usuario
        function usuario(id) {
            return USUARIOS.filter(function (u) { return u.id === id; })[0];
        }
        $('#us-filas').on('click', 'a[data-accion]', function (e) {
            e.preventDefault();
            var u = usuario(parseInt($(this).closest('tr').data('id'), 10));
            var accion = $(this).data('accion');
            if (!u) return;
            if (accion === 'documentos') {
                eModal.iframe('/admin/content/userDetalle/' + u.id, 'Documentos permitidos: ' + u.u);
            } else if (accion === 'estadisticas') {
                eModal.iframe('/admin/content/userStats/' + u.id, 'Estadísticas de: ' + u.u);
            } else if (accion === 'plazos') {
                eModal.iframe('/admin/otorgar/index/' + u.id, 'Usuarios con privilegios: ' + u.u);
            } else if (accion === 'reset') {
                if (!confirm('¿Restablecer la contraseña de "' + u.n + '" a la contraseña por defecto?')) return;
                $.post('/admin/ajax/resetPass', {id: u.id}, function (r) {
                    aviso(r > 0 ? 'Contraseña de ' + u.n + ' restablecida.' : 'No se pudo restablecer la contraseña.', !(r > 0));
                }).fail(function () {
                    aviso('No se pudo restablecer la contraseña.', true);
                });
            } else if (accion === 'baja' || accion === 'alta') {
                var baja = accion === 'baja';
                if (!confirm(baja ? '¿Dar de baja a "' + u.n + '"? Ya no podrá ingresar al sistema.' : '¿Dar de alta a "' + u.n + '"? Podrá volver a ingresar al sistema.')) return;
                $.post('/admin/ajax/' + accion, {id: u.id}, function () {
                    u.h = baja ? 0 : 1;
                    pintar();
                    aviso(u.n + (baja ? ' fue dado de baja.' : ' fue dado de alta.'));
                }).fail(function () {
                    aviso('No se pudo completar la acción.', true);
                });
            }
        });
        $('#passDefecto').on('click', function (e) {
            e.preventDefault();
            eModal.iframe('/admin/content/passDefecto', 'Contraseña por defecto');
        });

        marcarPestana();
        pintar();
        $('#us-q').focus();
        <?php if (isset($aviso) && $aviso !== ''): ?>
        aviso(<?php echo json_encode($aviso); ?>);
        <?php endif; ?>
    });
</script>
