<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$total = 0;
foreach ($carpetas as $c) {
    $total += (int) $c['cc'];
}
$js_carpetas = array();
foreach ($carpetas as $c) {
    $js_carpetas[] = array('id' => (int) $c['id'], 'n' => trim($c['carpeta']), 'cc' => (int) $c['cc']);
}
$js_destinos = array();
foreach ($destinos as $d) {
    $js_destinos[] = array('id' => (int) $d['id'], 'n' => trim($d['carpeta']));
}
?>
<style>
    .ar-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ar-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 16px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .ar-cab-icono {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #4A5568;
        color: #fff;
        font-size: 19px;
    }
    .ar-cab-texto {
        flex: 1 1 260px;
    }
    .ar-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .ar-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    /* dos paneles */
    .ar-dos {
        display: grid;
        grid-template-columns: 250px minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }
    @media (max-width: 1000px) {
        .ar-dos {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    .ar-buscar {
        position: relative;
    }
    .ar-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        margin-top: -7px;
        color: #9aa4b2;
        font-size: 13px;
    }
    .ar-buscar input {
        width: 100%;
        height: 36px;
        padding: 6px 11px 6px 31px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13px;
        outline: none;
    }
    .ar-buscar input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    /* carpetas */
    .ar-carpetas-cab {
        padding: 14px 14px 10px;
        border-bottom: 1px solid #EEF1F5;
    }
    .ar-carpetas-cab b {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .ar-carpetas {
        max-height: 560px;
        overflow: auto;
        margin: 0;
        padding: 6px;
        list-style: none;
    }
    .ar-carpeta {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 9px;
        cursor: pointer;
        color: #334155;
    }
    .ar-carpeta:hover {
        background: #F3F6FA;
    }
    .ar-carpeta.activa {
        background: #EEF3FA;
        box-shadow: inset 3px 0 0 #1A549A;
    }
    .ar-carpeta .fa {
        flex: 0 0 18px;
        color: #E0A82E;
        font-size: 16px;
    }
    .ar-carpeta.activa .fa {
        color: #1A549A;
    }
    .ar-carpeta-nombre {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ar-carpeta-cc {
        flex: 0 0 auto;
        min-width: 26px;
        padding: 1px 7px;
        border-radius: 20px;
        background: #F1F4F8;
        color: #4a5566;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }
    .ar-carpeta.activa .ar-carpeta-cc {
        background: #1A549A;
        color: #fff;
    }
    mark {
        padding: 0;
        background: #FFF3C4;
    }
    /* contenido */
    .ar-cont-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 12px;
        padding: 14px 18px;
        border-bottom: 3px solid #FECB34;
    }
    .ar-cont-cab h3 {
        flex: 1 1 200px;
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #123E73;
        min-width: 0;
    }
    .ar-cont-cab h3 .fa {
        margin-right: 7px;
        color: #E0A82E;
    }
    .ar-cont-cab h3 small {
        font-size: 12px;
        font-weight: 400;
        color: #8a94a3;
    }
    .ar-cont-cab .ar-buscar {
        flex: 0 1 260px;
    }
    /* barra de seleccion */
    .ar-sel {
        display: none;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #EEF3FA;
        border-bottom: 1px solid #D5E2F2;
        font-size: 13px;
        color: #123E73;
    }
    .ar-sel.ver {
        display: flex;
    }
    .ar-sel b {
        margin-right: auto;
    }
    .ar-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        padding: 0 13px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
        cursor: pointer;
    }
    .ar-btn:hover {
        background: #F7FAFD;
        color: #123E73;
        text-decoration: none;
    }
    .ar-btn.primario {
        background: #1A549A;
        border-color: #1A549A;
        color: #fff;
    }
    .ar-btn.primario:hover {
        background: #123E73;
        color: #fff;
    }
    /* tabla */
    table.ar-tabla {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .ar-tabla th {
        padding: 9px 12px;
        border-bottom: 1px solid #E3E8EF;
        background: #F7F9FC;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: #6b7686;
        text-align: left;
    }
    .ar-tabla td {
        padding: 10px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 12.5px;
        color: #334155;
        vertical-align: top;
        word-wrap: break-word;
    }
    .ar-tabla tbody tr:hover td {
        background: #F7FAFD;
    }
    .ar-tabla tr.marcada td {
        background: #F2F7FD;
    }
    .ar-tabla input[type=checkbox] {
        width: 16px;
        height: 16px;
        margin: 2px 0 0;
        cursor: pointer;
    }
    .ar-nur {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        background: #EEF3FA;
        color: #1A549A;
        font-family: Consolas, "Courier New", monospace;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none;
    }
    .ar-nur:hover {
        background: #1A549A;
        color: #fff;
        text-decoration: none;
    }
    .ar-ref b {
        display: block;
        font-weight: 600;
        color: #1f2937;
    }
    .ar-ref small {
        display: block;
        margin-top: 1px;
        font-size: 11px;
        color: #8a94a3;
    }
    /* la observacion se recorta a dos lineas; un clic la despliega */
    .ar-obs {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-top: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        background: #F7F9FC;
        font-size: 11.5px;
        color: #4a5566;
        cursor: pointer;
    }
    .ar-obs.abierta {
        display: block;
    }
    .ar-quien b {
        display: block;
        font-weight: 600;
        color: #1f2937;
    }
    .ar-quien small,
    .ar-fecha small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .ar-acc {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .ar-acc a {
        display: flex;
        width: 100%;
        box-sizing: border-box;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 27px;
        padding: 0 9px;
        border: 1px solid #D5DCE6;
        border-radius: 7px;
        background: #fff;
        font-size: 11.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
        white-space: nowrap;
    }
    .ar-acc a:hover {
        background: #EEF3FA;
        text-decoration: none;
    }
    .ar-acc a.sacar {
        color: #92400E;
        border-color: #F0D7A8;
    }
    .ar-acc a.sacar:hover {
        background: #FFF9EC;
    }
    .ar-vacio {
        padding: 50px 16px;
        text-align: center;
        font-size: 13.5px;
        color: #8a94a3;
    }
    .ar-vacio .fa {
        display: block;
        margin-bottom: 10px;
        font-size: 36px;
        color: #C4CEDB;
    }
    /* ventana de mover */
    .ar-fondo {
        position: fixed;
        inset: 0;
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .45);
    }
    .ar-fondo.ver {
        display: flex;
    }
    .ar-dialogo {
        width: 100%;
        max-width: 460px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        overflow: hidden;
    }
    .ar-dialogo-cab {
        padding: 16px 20px;
        border-bottom: 3px solid #FECB34;
    }
    .ar-dialogo-cab h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #123E73;
    }
    .ar-dialogo-cab p {
        margin: 3px 0 0;
        font-size: 12.5px;
        color: #6b7686;
    }
    .ar-dialogo-cuerpo {
        padding: 14px 20px;
    }
    .ar-destinos {
        max-height: 260px;
        overflow: auto;
        margin: 10px 0 0;
        padding: 0;
        list-style: none;
        border: 1px solid #E3E8EF;
        border-radius: 10px;
    }
    .ar-destinos li label {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        padding: 9px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 13px;
        font-weight: 400;
        cursor: pointer;
    }
    .ar-destinos li:last-child label {
        border-bottom: 0;
    }
    .ar-destinos li label:hover {
        background: #F7FAFD;
    }
    .ar-destinos .fa {
        color: #E0A82E;
    }
    .ar-destinos .actual {
        color: #9aa4b2;
        font-size: 11px;
    }
    .ar-nueva {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }
    .ar-nueva input {
        flex: 1 1 auto;
        height: 36px;
        padding: 6px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13px;
        outline: none;
    }
    .ar-nueva input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .ar-dialogo-pie {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 20px;
        border-top: 1px solid #EEF1F5;
        background: #FAFBFC;
    }
    .ar-msg {
        font-size: 12px;
        color: #B42318;
        margin-top: 8px;
        display: none;
    }
    #ar-aviso {
        display: none;
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 2000;
        padding: 12px 18px;
        border-radius: 10px;
        background: #1E7B45;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .18);
    }
</style>

<div class="ar-card ar-cab">
    <span class="ar-cab-icono"><i class="fa fa-archive"></i></span>
    <div class="ar-cab-texto">
        <h2>Archivo</h2>
        <p><?php echo $n($total); ?> documento<?php echo $total == 1 ? '' : 's'; ?> archivado<?php echo $total == 1 ? '' : 's'; ?> en <?php echo count($carpetas); ?> carpeta<?php echo count($carpetas) == 1 ? '' : 's'; ?>. Elija una carpeta para ver su contenido.</p>
    </div>
</div>

<?php if (!$carpetas): ?>
    <div class="ar-card ar-vacio">
        <i class="fa fa-archive"></i>
        No tiene correspondencia archivada.
    </div>
<?php else: ?>
    <div class="ar-dos">
        <div class="ar-card">
            <div class="ar-carpetas-cab">
                <b>Carpetas</b>
                <div class="ar-buscar">
                    <i class="fa fa-search"></i>
                    <input type="search" id="ar-q-carpeta" placeholder="Buscar carpeta…" autocomplete="off">
                </div>
            </div>
            <ul class="ar-carpetas" id="ar-carpetas"></ul>
        </div>

        <div class="ar-card" id="ar-contenido">
            <div class="ar-cont-cab">
                <h3 id="ar-titulo"><i class="fa fa-folder-open"></i>Elija una carpeta</h3>
                <div class="ar-buscar" id="ar-buscar-doc" style="display:none">
                    <i class="fa fa-search"></i>
                    <input type="search" id="ar-q-doc" placeholder="Buscar en esta carpeta…" autocomplete="off">
                </div>
            </div>
            <div class="ar-sel" id="ar-sel">
                <b id="ar-sel-txt">0 seleccionados</b>
                <button type="button" class="ar-btn primario" id="ar-mover"><i class="fa fa-share"></i> Mover a otra carpeta</button>
                <button type="button" class="ar-btn" id="ar-quitar-sel"><i class="fa fa-times"></i> Quitar selección</button>
            </div>
            <div id="ar-cuerpo">
                <div class="ar-vacio"><i class="fa fa-folder-o"></i> Elija una carpeta de la izquierda para ver los documentos que contiene.</div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ventana para mover -->
<div class="ar-fondo" id="ar-fondo">
    <div class="ar-dialogo">
        <div class="ar-dialogo-cab">
            <h4><i class="fa fa-share"></i> Mover a otra carpeta</h4>
            <p id="ar-dialogo-txt"></p>
        </div>
        <div class="ar-dialogo-cuerpo">
            <div class="ar-buscar">
                <i class="fa fa-search"></i>
                <input type="search" id="ar-q-destino" placeholder="Buscar carpeta de destino…" autocomplete="off">
            </div>
            <ul class="ar-destinos" id="ar-destinos"></ul>
            <div class="ar-nueva">
                <input type="text" id="ar-nueva" placeholder="…o escriba el nombre de una carpeta nueva" maxlength="100">
            </div>
            <div class="ar-msg" id="ar-msg"></div>
        </div>
        <div class="ar-dialogo-pie">
            <button type="button" class="ar-btn" id="ar-cancelar">Cancelar</button>
            <button type="button" class="ar-btn primario" id="ar-confirmar"><i class="fa fa-check"></i> Mover</button>
        </div>
    </div>
</div>
<div id="ar-aviso"></div>

<script>
    $(function () {
        var CARPETAS = <?php echo json_encode($js_carpetas); ?>;
        var DESTINOS = <?php echo json_encode($js_destinos); ?>;
        var ABRIR = <?php echo (int) $abrir; ?>;
        var actual = null, filas = [];

        function normal(t) {
            return (t || '').toString().toLowerCase()
                    .replace(/[áàä]/g, 'a').replace(/[éèë]/g, 'e').replace(/[íìï]/g, 'i')
                    .replace(/[óòö]/g, 'o').replace(/[úùü]/g, 'u').replace(/ñ/g, 'n');
        }
        function esc(s) {
            return $('<div>').text(s == null ? '' : s).html();
        }
        function resaltar(txt, palabras) {
            var r = esc(txt);
            if (!palabras.length) {
                return r;
            }
            var plano = normal(r), marcas = [];
            palabras.forEach(function (p) {
                var i = plano.indexOf(p);
                while (i > -1) {
                    marcas.push([i, i + p.length]);
                    i = plano.indexOf(p, i + p.length);
                }
            });
            if (!marcas.length) {
                return r;
            }
            marcas.sort(function (a, b) {
                return a[0] - b[0];
            });
            var fin = 0, salida = '';
            marcas.forEach(function (m) {
                if (m[0] < fin) {
                    return;
                }
                salida += r.slice(fin, m[0]) + '<mark>' + r.slice(m[0], m[1]) + '</mark>';
                fin = m[1];
            });
            return salida + r.slice(fin);
        }
        function palabras(v) {
            return normal(v).split(/\s+/).filter(Boolean);
        }
        function aviso(txt) {
            $('#ar-aviso').text(txt).stop(true, true).fadeIn(150).delay(3000).fadeOut(300);
        }

        /* ---- carpetas ---- */
        function pintarCarpetas() {
            var p = palabras($('#ar-q-carpeta').val());
            var html = '';
            CARPETAS.forEach(function (c) {
                if (p.length && !p.every(function (w) {
                    return normal(c.n).indexOf(w) > -1;
                })) {
                    return;
                }
                html += '<li class="ar-carpeta' + (actual && actual.id === c.id ? ' activa' : '') + '" data-id="' + c.id + '">' +
                        '<i class="fa ' + (actual && actual.id === c.id ? 'fa-folder-open' : 'fa-folder') + '"></i>' +
                        '<span class="ar-carpeta-nombre" title="' + esc(c.n) + '">' + resaltar(c.n, p) + '</span>' +
                        '<span class="ar-carpeta-cc">' + c.cc + '</span></li>';
            });
            $('#ar-carpetas').html(html || '<li class="ar-vacio" style="padding:20px">Ninguna carpeta coincide.</li>');
        }
        $('#ar-q-carpeta').on('input search', pintarCarpetas);
        $('#ar-carpetas').on('click', '.ar-carpeta', function () {
            abrir(parseInt($(this).data('id'), 10));
        });

        function carpeta(id) {
            for (var i = 0; i < CARPETAS.length; i++) {
                if (CARPETAS[i].id === id) {
                    return CARPETAS[i];
                }
            }
            return null;
        }

        /* ---- contenido de la carpeta ---- */
        function abrir(id) {
            var c = carpeta(id);
            if (!c) {
                return;
            }
            actual = c;
            pintarCarpetas();
            $('#ar-titulo').html('<i class="fa fa-folder-open"></i>' + esc(c.n) + ' <small>· cargando…</small>');
            $('#ar-buscar-doc').show();
            $('#ar-q-doc').val('');
            $('#ar-cuerpo').html('<div class="ar-vacio"><i class="fa fa-spinner fa-spin"></i> Cargando documentos…</div>');
            try {
                history.replaceState(null, '', '/bandeja/archivo?c=' + id);
            } catch (e) {
            }
            $.getJSON('/bandeja/carpetajson/' + id).done(function (r) {
                if (!actual || actual.id !== id) {
                    return;
                }
                filas = (r && r.filas) || [];
                c.cc = filas.length;
                pintarCarpetas();
                pintarDocumentos();
            }).fail(function () {
                $('#ar-cuerpo').html('<div class="ar-vacio"><i class="fa fa-exclamation-triangle"></i> No se pudo abrir la carpeta. Recargue la página.</div>');
            });
        }

        function pintarDocumentos() {
            var p = palabras($('#ar-q-doc').val());
            var lista = filas.filter(function (f) {
                if (!p.length) {
                    return true;
                }
                var t = normal([f.nur, f.referencia, f.cite_original, f.codigo, f.nombre_emisor, f.de_oficina, f.observaciones].join(' '));
                return p.every(function (w) {
                    return t.indexOf(w) > -1;
                });
            });
            $('#ar-titulo').html('<i class="fa fa-folder-open"></i>' + esc(actual.n) +
                    ' <small>· ' + filas.length + ' documento' + (filas.length === 1 ? '' : 's') +
                    (p.length ? ', ' + lista.length + ' coinciden' : '') + '</small>');
            if (!filas.length) {
                $('#ar-cuerpo').html('<div class="ar-vacio"><i class="fa fa-folder-o"></i> Esta carpeta ya no tiene documentos.</div>');
                marcarSeleccion();
                return;
            }
            if (!lista.length) {
                $('#ar-cuerpo').html('<div class="ar-vacio"><i class="fa fa-search"></i> Nada coincide en esta carpeta.</div>');
                marcarSeleccion();
                return;
            }
            var html = '<table class="ar-tabla"><colgroup><col style="width:38px"><col style="width:122px"><col><col style="width:19%"><col style="width:96px"><col style="width:122px"></colgroup>' +
                    '<thead><tr><th><input type="checkbox" id="ar-todos" title="Seleccionar todos"></th><th>Hoja de ruta</th><th>Documento</th><th>Lo envió</th><th>Archivado</th><th></th></tr></thead><tbody>';
            lista.forEach(function (f) {
                var ref = $.trim((f.referencia || '').replace(/\./g, ''));
                html += '<tr data-seg="' + f.seg + '">' +
                        '<td><input type="checkbox" class="ar-chk" value="' + f.seg + '"></td>' +
                        '<td><a class="ar-nur" href="/route/trace/?hr=' + encodeURIComponent(f.nur) + '" target="_blank">' + resaltar(f.nur, p) + '</a></td>' +
                        '<td class="ar-ref"><b>' + (ref ? resaltar(f.referencia, p) : '<span style="color:#9aa4b2;font-style:italic;font-weight:400">Sin referencia</span>') + '</b>' +
                        '<small>' + resaltar(f.cite_original || f.codigo || '', p) + '</small>' +
                        (f.observaciones ? '<span class="ar-obs"><i class="fa fa-comment-o"></i> ' + resaltar(f.observaciones, p) + '</span>' : '') + '</td>' +
                        '<td class="ar-quien"><b>' + resaltar(f.nombre_emisor || '', p) + '</b><small>' + resaltar(f.de_oficina || '', p) + '</small></td>' +
                        '<td class="ar-fecha">' + esc((f.archivado || '').split(' ')[0]) + '<small>recibido ' + esc(f.recibido || '') + '</small></td>' +
                        '<td><div class="ar-acc">' +
                        '<a href="/route/trace/?hr=' + encodeURIComponent(f.nur) + '" target="_blank"><i class="fa fa-eye"></i> Ver</a>' +
                        '<a href="#" class="mover-uno" data-seg="' + f.seg + '"><i class="fa fa-share"></i> Mover</a>' +
                        '<a href="/bandeja/unarchive/' + f.seg + '" class="sacar" title="Vuelve a sus pendientes"><i class="fa fa-undo"></i> Desarchivar</a>' +
                        '</div></td></tr>';
            });
            $('#ar-cuerpo').html(html + '</tbody></table>');
            marcarSeleccion();
        }
        var tq = null;
        $('#ar-q-doc').on('input search', function () {
            clearTimeout(tq);
            tq = setTimeout(pintarDocumentos, 150);
        });

        /* ---- seleccion ---- */
        function seleccionados() {
            return $('.ar-chk:checked').map(function () {
                return this.value;
            }).get();
        }
        function marcarSeleccion() {
            var sel = seleccionados();
            $('#ar-sel').toggleClass('ver', sel.length > 0);
            $('#ar-sel-txt').text(sel.length + ' seleccionado' + (sel.length === 1 ? '' : 's'));
            $('.ar-tabla tbody tr').each(function () {
                $(this).toggleClass('marcada', $(this).find('.ar-chk').is(':checked'));
            });
            var total = $('.ar-chk').length;
            $('#ar-todos').prop('checked', total > 0 && sel.length === total);
        }
        $('#ar-cuerpo').on('change', '.ar-chk', marcarSeleccion);
        $('#ar-cuerpo').on('change', '#ar-todos', function () {
            $('.ar-chk').prop('checked', this.checked);
            marcarSeleccion();
        });
        $('#ar-quitar-sel').on('click', function () {
            $('.ar-chk').prop('checked', false);
            marcarSeleccion();
        });
        $('#ar-cuerpo').on('click', '.ar-obs', function () {
            $(this).toggleClass('abierta');
        });
        $('#ar-cuerpo').on('click', 'a.sacar', function (e) {
            if (!confirm('¿Desarchivar este documento?\n\nSale de la carpeta y vuelve a sus pendientes.')) {
                e.preventDefault();
            }
        });

        /* ---- mover ---- */
        var aMover = [];
        function pintarDestinos() {
            var p = palabras($('#ar-q-destino').val());
            var html = '';
            DESTINOS.forEach(function (d) {
                if (p.length && !p.every(function (w) {
                    return normal(d.n).indexOf(w) > -1;
                })) {
                    return;
                }
                var esActual = actual && d.id === actual.id;
                html += '<li><label' + (esActual ? ' style="opacity:.5;cursor:default"' : '') + '>' +
                        '<input type="radio" name="ar-destino" value="' + d.id + '"' + (esActual ? ' disabled' : '') + '>' +
                        '<i class="fa fa-folder"></i> <span>' + resaltar(d.n, p) + '</span>' +
                        (esActual ? ' <span class="actual">(carpeta actual)</span>' : '') + '</label></li>';
            });
            $('#ar-destinos').html(html || '<li style="padding:14px;text-align:center;color:#8a94a3;font-size:12.5px">Ninguna carpeta coincide. Puede crear una nueva abajo.</li>');
        }
        function abrirMover(segs) {
            aMover = segs;
            $('#ar-dialogo-txt').text(segs.length + ' documento' + (segs.length === 1 ? '' : 's') + ' de «' + actual.n + '»');
            $('#ar-q-destino').val('');
            $('#ar-nueva').val('');
            $('#ar-msg').hide();
            pintarDestinos();
            $('#ar-fondo').addClass('ver');
            $('#ar-q-destino').focus();
        }
        function cerrarMover() {
            $('#ar-fondo').removeClass('ver');
        }
        $('#ar-mover').on('click', function () {
            var sel = seleccionados();
            if (sel.length) {
                abrirMover(sel);
            }
        });
        $('#ar-cuerpo').on('click', 'a.mover-uno', function (e) {
            e.preventDefault();
            abrirMover([String($(this).data('seg'))]);
        });
        $('#ar-q-destino').on('input search', pintarDestinos);
        $('#ar-nueva').on('input', function () {
            if ($.trim(this.value) !== '') {
                $('input[name=ar-destino]').prop('checked', false);
            }
        });
        $('#ar-destinos').on('change', 'input', function () {
            $('#ar-nueva').val('');
        });
        $('#ar-cancelar').on('click', cerrarMover);
        $('#ar-fondo').on('click', function (e) {
            if (e.target === this) {
                cerrarMover();
            }
        });
        $(document).on('keydown', function (e) {
            if (e.which === 27) {
                cerrarMover();
            }
        });
        $('#ar-confirmar').on('click', function () {
            var destino = $('input[name=ar-destino]:checked').val();
            var nueva = $.trim($('#ar-nueva').val());
            if (!destino && !nueva) {
                $('#ar-msg').text('Elija una carpeta o escriba el nombre de una nueva.').show();
                return;
            }
            var $b = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Moviendo…');
            $.post('/bandeja/mover', {seg: aMover, destino: destino || 0, nueva: nueva}, null, 'json').done(function (r) {
                if (!r || !r.ok) {
                    $('#ar-msg').text((r && r.msg) || 'No se pudo mover.').show();
                    return;
                }
                // se actualizan los contadores sin recargar
                var dest = carpeta(r.destino);
                if (!dest) {
                    dest = {id: r.destino, n: r.nombre, cc: 0};
                    CARPETAS.push(dest);
                    CARPETAS.sort(function (a, b) {
                        return a.n.localeCompare(b.n);
                    });
                    if (!DESTINOS.some(function (d) {
                        return d.id === r.destino;
                    })) {
                        DESTINOS.push({id: r.destino, n: r.nombre});
                        DESTINOS.sort(function (a, b) {
                            return a.n.localeCompare(b.n);
                        });
                    }
                }
                dest.cc += r.movidos;
                filas = filas.filter(function (f) {
                    return aMover.indexOf(String(f.seg)) === -1;
                });
                actual.cc = filas.length;
                if (actual.cc === 0) {
                    CARPETAS = CARPETAS.filter(function (c) {
                        return c.id !== actual.id;
                    });
                }
                cerrarMover();
                pintarCarpetas();
                pintarDocumentos();
                aviso(r.movidos + ' documento' + (r.movidos === 1 ? '' : 's') + ' movido' + (r.movidos === 1 ? '' : 's') + ' a «' + r.nombre + '».');
            }).fail(function () {
                $('#ar-msg').text('No se pudo mover: revise su conexión.').show();
            }).always(function () {
                $b.prop('disabled', false).html('<i class="fa fa-check"></i> Mover');
            });
        });

        pintarCarpetas();
        if (ABRIR && carpeta(ABRIR)) {
            abrir(ABRIR);
        } else if (CARPETAS.length) {
            abrir(CARPETAS[0].id);
        }
    });
</script>
