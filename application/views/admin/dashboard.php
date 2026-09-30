<?php
// estado -> icono y color (los mismos del seguimiento)
$estilo_estado = array(
    1 => array('fa-inbox', '#B26B00', '#FFF3DC'),
    2 => array('fa-clock-o', '#1A549A', '#EAF1F9'),
    4 => array('fa-paper-plane', '#227547', '#E6F4EC'),
    6 => array('fa-files-o', '#5B3C99', '#F1ECFA'),
    10 => array('fa-archive', '#4A5568', '#EEF2F7'),
    11 => array('fa-ban', '#B42318', '#FDE8E8'),
);
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$total_hr = 0;
foreach ($estados as $e) {
    $total_hr += (int) $e['cantidad'];
}
// ultimos 12 meses (con los meses sin documentos en 0)
$meses_nombre = array('', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
$serie = array();
$etiquetas = array();
for ($i = 11; $i >= 0; $i--) {
    $clave = date('Y-m', strtotime(date('Y-m-01') . " -$i month"));
    $etiquetas[] = $meses_nombre[(int) substr($clave, 5, 2)] . ' ' . substr($clave, 2, 2);
    $serie[] = isset($por_mes[$clave]) ? (int) $por_mes[$clave] : 0;
}
?>
<style>
    .pan-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .pan-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 18px;
        margin-bottom: 18px;
        padding: 18px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .pan-cab-icono {
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
    .pan-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pan-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .pan-cab-texto {
        flex: 1 1 280px;
    }
    .pan-accesos {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pan-accesos a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pan-accesos a:hover {
        text-decoration: none;
        border-color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .pan-accesos a .fa {
        color: var(--correos-azul, #1A549A);
    }
    .pan-seccion {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 4px 2px 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #6b7686;
    }
    .pan-seccion span {
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
        color: #9aa4b2;
    }
    /* tarjetas */
    .pan-grilla {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }
    .pan-kpi {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        color: inherit;
        overflow: hidden;
        transition: transform .15s, box-shadow .15s;
    }
    a.pan-kpi:hover {
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(18, 62, 115, .15);
    }
    .pan-kpi-icono {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }
    .pan-kpi b {
        display: block;
        font-size: 24px;
        line-height: 1.1;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pan-kpi > span:not(.pan-kpi-icono):not(.pan-barra) span {
        display: block;
        font-size: 12.5px;
        color: #6b7686;
    }
    .pan-kpi small {
        display: block;
        font-size: 11px;
        color: #9aa4b2;
    }
    .pan-barra {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 4px;
        background: #EEF2F7;
    }
    .pan-barra i {
        display: block;
        height: 100%;
    }
    /* hoy */
    .pan-hoy {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1px;
        border-radius: 14px;
        overflow: hidden;
        background: #EEF2F7;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 20px;
    }
    .pan-hoy div {
        padding: 14px 18px;
        background: #fff;
    }
    .pan-hoy b {
        display: block;
        font-size: 22px;
        color: var(--correos-azul, #1A549A);
    }
    .pan-hoy span {
        font-size: 12px;
        color: #6b7686;
    }
    .pan-hoy .fa {
        margin-right: 4px;
        color: #B7791F;
    }
    /* paneles */
    .pan-paneles {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }
    .pan-panel-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .pan-panel-cab h3 {
        margin: 0;
        flex: 1 1 auto;
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pan-panel-cab h3 .fa {
        margin-right: 6px;
        color: var(--correos-azul, #1A549A);
    }
    .pan-panel-cab small {
        font-weight: 500;
        color: #8a94a3;
    }
    .pan-buscar {
        position: relative;
        flex: 0 1 260px;
    }
    .pan-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .pan-buscar input {
        width: 100%;
        height: 34px;
        padding: 4px 10px 4px 30px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 17px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 12.5px;
    }
    .pan-grafico {
        height: 250px;
        padding: 10px 12px 6px;
    }
    /* bitacora */
    .pan-bitacora {
        list-style: none;
        margin: 0;
        padding: 6px 0;
        min-height: 200px;
    }
    .pan-bitacora li {
        display: flex;
        gap: 12px;
        padding: 10px 18px;
        border-bottom: 1px solid #F1F4F8;
    }
    .pan-bitacora li:last-child {
        border-bottom: 0;
    }
    .pan-bit-hora {
        flex: 0 0 74px;
        font-size: 11.5px;
        line-height: 1.35;
        color: #8a94a3;
    }
    .pan-bit-hora b {
        display: block;
        font-size: 13px;
        color: #2d3748;
    }
    .pan-bit-texto {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 12.5px;
        color: #4a5568;
        word-break: break-word;
    }
    .pan-bit-texto .pan-quien {
        display: block;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pan-bit-texto b {
        color: #2d3748;
    }
    .pan-bit-tipo {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #EEF2F7;
        color: #6b7686;
    }
    .pan-bit-tipo.ingreso {
        background: #E6F4EC;
        color: #2E9E5B;
    }
    .pan-bit-tipo.salida {
        background: #F3F5F8;
        color: #9aa4b2;
    }
    .pan-bit-tipo.deriva {
        background: #EAF1F9;
        color: #1A549A;
    }
    .pan-bit-tipo.cancela,
    .pan-bit-tipo.elimina {
        background: #FDE8E8;
        color: #D32F2F;
    }
    .pan-bit-tipo.genera {
        background: #FFF7DD;
        color: #B7791F;
    }
    .pan-bit-ip {
        flex: 0 0 auto;
        font-size: 11px;
        color: #b7c0cc;
    }
    .pan-paginas {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 18px;
        border-top: 1px solid #EEF2F7;
        font-size: 12px;
        color: #8a94a3;
    }
    .pan-paginas .pagination {
        margin: 0;
    }
    /* conectados */
    .pan-conectados {
        list-style: none;
        margin: 0;
        padding: 6px 0;
        max-height: 520px;
        overflow-y: auto;
    }
    .pan-conectados li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 18px;
    }
    .pan-conectados li:hover {
        background: var(--correos-fondo, #F3F5F8);
    }
    .pan-avatar {
        position: relative;
        flex: 0 0 38px;
    }
    .pan-avatar img {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
    }
    .pan-avatar i {
        position: absolute;
        right: 0;
        bottom: 1px;
        width: 11px;
        height: 11px;
        border: 2px solid #fff;
        border-radius: 50%;
        background: #2E9E5B;
    }
    .pan-avatar i.ausente {
        background: #F59E0B;
    }
    .pan-con-texto {
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.3;
    }
    .pan-con-texto a {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pan-con-texto span {
        display: block;
        font-size: 11.5px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pan-con-hace {
        flex: 0 0 auto;
        font-size: 11px;
        color: #9aa4b2;
    }
    .pan-vivo {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #2E9E5B;
    }
    .pan-vivo:before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2E9E5B;
        animation: pan-latido 2s infinite;
    }
    @keyframes pan-latido {
        0%, 100% { opacity: 1; }
        50% { opacity: .3; }
    }
    .pan-vacio {
        padding: 30px 18px;
        text-align: center;
        font-size: 13px;
        color: #9aa4b2;
    }
    @media (max-width: 1199px) {
        .pan-paneles {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 767px) {
        .pan-hoy {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="col-lg-12">
    <!-- cabecera -->
    <div class="pan-card pan-cab">
        <div class="pan-cab-icono"><i class="fa fa-cogs"></i></div>
        <div class="pan-cab-texto">
            <h2>Panel de administración</h2>
            <p>Estado general del sistema de correspondencia.</p>
        </div>
        <div class="pan-accesos">
            <a href="/admin/user"><i class="fa fa-users"></i> Usuarios</a>
            <a href="/admin/hojasruta/lista"><i class="fa fa-tags"></i> Documentos</a>
            <a href="/admin/oficinas/lista"><i class="fa fa-building-o"></i> Oficinas</a>
            <a href="/admin/tipos"><i class="fa fa-file-text-o"></i> Tipos de documento</a>
        </div>
    </div>

    <!-- hoy -->
    <div class="pan-seccion"><i class="fa fa-calendar"></i> Hoy</div>
    <div class="pan-hoy">
        <div><b><?php echo $n($hoy['documentos']); ?></b><span><i class="fa fa-file-text-o"></i> documentos generados</span></div>
        <div><b><?php echo $n($hoy['derivaciones']); ?></b><span><i class="fa fa-paper-plane"></i> derivaciones</span></div>
        <div><b><?php echo $n($hoy['recepciones']); ?></b><span><i class="fa fa-inbox"></i> recepciones</span></div>
        <div><b><?php echo $n($hoy['usuarios']); ?></b><span><i class="fa fa-user"></i> usuarios con actividad</span></div>
    </div>

    <!-- hojas de ruta por estado -->
    <div class="pan-seccion"><i class="fa fa-tags"></i> Derivaciones por estado <span>· <?php echo $n($total_hr); ?> en total</span></div>
    <div class="pan-grilla">
        <?php foreach ($estados as $id => $v):
            $st = isset($estilo_estado[$id]) ? $estilo_estado[$id] : array('fa-file-text-o', '#1A549A', '#EAF1F9');
            $pct = $total_hr ? round(100 * $v['cantidad'] / $total_hr, 1) : 0;
            ?>
            <a href="<?php echo $v['accion']; ?>" class="pan-kpi" title="<?php echo HTML::chars($v['descripcion']); ?>">
                <span class="pan-kpi-icono" style="background:<?php echo $st[2]; ?>;color:<?php echo $st[1]; ?>"><i class="fa <?php echo $st[0]; ?>"></i></span>
                <span>
                    <b><?php echo $n($v['cantidad']); ?></b>
                    <span><?php echo HTML::chars($v['titulo']); ?></span>
                    <small><?php echo $pct; ?>% del total</small>
                </span>
                <span class="pan-barra"><i style="width:<?php echo $pct; ?>%;background:<?php echo $st[1]; ?>"></i></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- sistema -->
    <div class="pan-seccion"><i class="fa fa-server"></i> Sistema</div>
    <div class="pan-grilla">
        <a href="/admin/user" class="pan-kpi">
            <span class="pan-kpi-icono" style="background:#EAF1F9;color:#1A549A"><i class="fa fa-users"></i></span>
            <span><b><?php echo $n($usuarios_activos); ?></b><span>Usuarios activos</span><small><?php echo $n($usuarios - $usuarios_activos); ?> dados de baja · <?php echo $n($usuarios); ?> en total</small></span>
        </a>
        <a href="/admin/hojasruta/lista" class="pan-kpi">
            <span class="pan-kpi-icono" style="background:#FFF7DD;color:#B7791F"><i class="fa fa-file-word-o"></i></span>
            <span><b><?php echo $n($documentos); ?></b><span>Documentos generados</span><small>originales y respuestas</small></span>
        </a>
        <a href="/admin/hojasruta/lista" class="pan-kpi">
            <span class="pan-kpi-icono" style="background:#E6F4EC;color:#227547"><i class="fa fa-tag"></i></span>
            <span><b><?php echo $n($hojasruta); ?></b><span>Hojas de ruta</span><small>documentos con número de hoja de ruta</small></span>
        </a>
        <a href="/admin/entidades" class="pan-kpi">
            <span class="pan-kpi-icono" style="background:#F1ECFA;color:#5B3C99"><i class="fa fa-building-o"></i></span>
            <span><b><?php echo $n($entidades); ?></b><span>Entidades</span><small>administradas</small></span>
        </a>
    </div>

    <div class="pan-paneles">
        <div>
            <!-- grafico -->
            <div class="pan-card">
                <div class="pan-panel-cab">
                    <h3><i class="fa fa-bar-chart"></i>Documentos generados por mes <small>· últimos 12 meses</small></h3>
                </div>
                <div class="pan-grafico" id="pan-grafico"></div>
            </div>

            <!-- bitacora -->
            <div class="pan-card">
                <div class="pan-panel-cab">
                    <h3><i class="fa fa-history"></i>Actividad reciente <small>(<span id="bitacora-total">0</span> registros)</small></h3>
                    <div class="pan-buscar">
                        <i class="fa fa-search"></i>
                        <input type="search" id="pan-bit-q" placeholder="Filtrar por usuario o acción…" autocomplete="off"/>
                    </div>
                </div>
                <ul class="pan-bitacora" id="bitacora"></ul>
                <div class="pan-paginas">
                    <span id="pan-bit-info"></span>
                    <ul class="pagination pagination-sm" id="bitacora-pagination"></ul>
                </div>
            </div>
        </div>

        <!-- conectados -->
        <div class="pan-card">
            <div class="pan-panel-cab">
                <h3><i class="fa fa-signal"></i>Conectados ahora <small>(<span id="cantidad">0</span>)</small></h3>
                <span class="pan-vivo">en vivo</span>
            </div>
            <ul class="pan-conectados" id="usuarios"></ul>
        </div>
    </div>
</div>

<script>
    $(function () {
        function escapar(t) {
            return $('<div>').text(t == null ? '' : String(t)).html();
        }
        // la bitacora guarda <b>...</b> para resaltar; se escapa todo y solo se vuelven a permitir esas etiquetas
        function textoBitacora(t) {
            return escapar(t).replace(/&lt;(\/?)b&gt;/gi, '<$1b>').replace(/&amp;ntilde;/g, 'ñ').replace(/&amp;([a-z]+|#\d+);/gi, '&$1;');
        }
        function tipoAccion(t) {
            t = (t || '').toLowerCase();
            if (t.indexOf('ingres') > -1) return ['ingreso', 'fa-sign-in'];
            if (t.indexOf('salio') > -1 || t.indexOf('salió') > -1) return ['salida', 'fa-sign-out'];
            if (t.indexOf('cancela') > -1) return ['cancela', 'fa-undo'];
            if (t.indexOf('elimin') > -1) return ['elimina', 'fa-trash-o'];
            if (t.indexOf('deriva') > -1) return ['deriva', 'fa-paper-plane'];
            if (t.indexOf('gener') > -1) return ['genera', 'fa-file-text-o'];
            if (t.indexOf('busqueda') > -1 || t.indexOf('búsqueda') > -1) return ['', 'fa-search'];
            return ['', 'fa-circle-o'];
        }

        // grafico de documentos por mes
        if (window.Highcharts) {
            new Highcharts.Chart({
                chart: {renderTo: 'pan-grafico', type: 'column', backgroundColor: 'transparent', style: {fontFamily: 'inherit'}},
                title: {text: ''},
                credits: {enabled: false},
                legend: {enabled: false},
                exporting: {enabled: false},
                xAxis: {categories: <?php echo json_encode($etiquetas); ?>, lineColor: '#DCE3EC', tickLength: 0, labels: {style: {color: '#6b7686'}}},
                yAxis: {title: {text: ''}, gridLineColor: '#EEF2F7', labels: {style: {color: '#9aa4b2'}}},
                tooltip: {formatter: function () {
                    return '<b>' + this.x + '</b><br/>' + Highcharts.numberFormat(this.y, 0, ',', '.') + ' documentos';
                }},
                plotOptions: {column: {borderRadius: 4, borderWidth: 0, color: '#1A549A', pointPadding: .08, groupPadding: .08}},
                series: [{name: 'Documentos', data: <?php echo json_encode($serie); ?>}]
            });
        }

        // usuarios conectados (se actualiza cada 15 s)
        function conectados() {
            $.getJSON('/admin/ajax/usuariosconectados', function (data) {
                var $ul = $('#usuarios').empty();
                $('#cantidad').text(data.length);
                if (!data.length) {
                    $ul.append('<li class="pan-vacio">Nadie conectado en los últimos 10 minutos.</li>');
                }
                $.each(data, function (i, u) {
                    var min = parseFloat(u.minutos) || 0;
                    var hace = min < 1 ? 'hace instantes' : 'hace ' + Math.round(min) + ' min';
                    $ul.append('<li><span class="pan-avatar"><img src="' + escapar(u.foto) + '" alt=""/><i class="' + (min >= 5 ? 'ausente' : '') + '"></i></span>' +
                        '<span class="pan-con-texto"><a href="/user/profile/' + parseInt(u.id, 10) + '">' + escapar(u.nombre) + '</a><span>' + escapar(u.cargo) + '</span></span>' +
                        '<span class="pan-con-hace">' + hace + '</span></li>');
                });
            }).always(function () {
                setTimeout(conectados, 15000);
            });
        }
        conectados();

        // bitacora paginada y con filtro
        var pagina = 1, filtro = '', temporizador = null;
        function cargarBitacora(p) {
            pagina = p || 1;
            $.getJSON('/admin/ajax/bitacora', {page: pagina, limit: 12, q: filtro}, function (resp) {
                var $ul = $('#bitacora').empty();
                if (!resp.data.length) {
                    $ul.append('<li class="pan-vacio">No hay actividad' + (filtro ? ' que coincida con "' + escapar(filtro) + '"' : '') + '.</li>');
                }
                $.each(resp.data, function (i, e) {
                    var f = (e.fecha || '').split(' ');
                    var t = tipoAccion(e.accion_realizada);
                    $ul.append('<li><span class="pan-bit-tipo ' + t[0] + '"><i class="fa ' + t[1] + '"></i></span>' +
                        '<span class="pan-bit-hora"><b>' + escapar((f[1] || '').substr(0, 5)) + '</b>' + escapar(f[0] || '') + '</span>' +
                        '<span class="pan-bit-texto"><span class="pan-quien">' + escapar(e.usuario || 'Sistema') + '</span>' + textoBitacora(e.accion_realizada) + '</span>' +
                        '<span class="pan-bit-ip" title="Dirección IP">' + escapar(e.ip) + '</span></li>');
                });
                $('#bitacora-total').text(filtro && resp.total > 5000 ? '5.000+' : Number(resp.total).toLocaleString('es-BO'));
                $('#pan-bit-info').text(resp.total ? 'Página ' + resp.page + ' de ' + Math.max(1, resp.pages) : '');
                paginador(resp.page, resp.pages);
            });
        }
        function paginador(page, pages) {
            var $p = $('#bitacora-pagination').empty();
            if (pages <= 1) {
                return;
            }
            function item(txt, destino, desactivado, activo) {
                var $li = $('<li>').toggleClass('disabled', !!desactivado).toggleClass('active', !!activo);
                var $a = $('<a href="#">').text(txt);
                if (!desactivado && !activo) {
                    $a.on('click', function (ev) {
                        ev.preventDefault();
                        cargarBitacora(destino);
                    });
                }
                $p.append($li.append($a));
            }
            item('«', page - 1, page <= 1);
            var ini = Math.max(1, page - 2), fin = Math.min(pages, ini + 4);
            ini = Math.max(1, fin - 4);
            for (var i = ini; i <= fin; i++) {
                item(i, i, false, i === page);
            }
            item('»', page + 1, page >= pages);
        }
        $('#pan-bit-q').on('input search', function () {
            var v = $.trim($(this).val());
            clearTimeout(temporizador);
            temporizador = setTimeout(function () {
                filtro = v;
                cargarBitacora(1);
            }, 400);
        });
        cargarBitacora(1);
    });
</script>
