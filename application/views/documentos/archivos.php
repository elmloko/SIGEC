<?php
$meses = array(1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$lista = array();
$total_bytes = 0;
foreach ($results as $r) {
    $nombre = substr($r['nombre_archivo'], 13);
    $ts = $r['fecha'] ? strtotime($r['fecha']) : 0;
    $total_bytes += (int) $r['tamanio'];
    $lista[] = array(
        'id' => (int) $r['id'],
        'id_documento' => (int) $r['id_documento'],
        'nombre' => $nombre,
        'es_pdf' => stripos((string) $r['extension'], 'pdf') !== FALSE || preg_match('/\.pdf$/i', $nombre),
        'bytes' => (int) $r['tamanio'],
        'tamanio' => $r['tamanio'] >= 1048576 ? number_format($r['tamanio'] / 1048576, 2) . ' MB' : number_format($r['tamanio'] / 1024, 0) . ' KB',
        'ts' => $ts,
        'fecha' => $ts ? date('d/m/Y H:i', $ts) : '',
        'mes' => $ts ? $meses[(int) date('n', $ts)] . ' ' . date('Y', $ts) : 'Sin fecha',
        'cite' => $r['cite_original'] != '' ? $r['cite_original'] : $r['codigo'],
        'tipo' => $r['tipo'],
        'referencia' => $r['referencia'],
        'nur' => $r['nur'],
    );
}
$total_texto = $total_bytes >= 1048576 ? number_format($total_bytes / 1048576, 1) . ' MB' : number_format($total_bytes / 1024, 0) . ' KB';
?>
<style>
    .arc-barra {
        position: sticky;
        top: 64px;
        z-index: 20;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 14px 18px;
        margin: -8px 0 16px;
        background: #fff;
        border-radius: 10px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .arc-barra h3 {
        flex: 1 1 auto;
        margin: 0;
        font-size: 20px;
        font-weight: 500;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .arc-barra h3 .fa {
        color: var(--correos-azul, #1A549A);
        margin-right: 6px;
    }
    .arc-barra h3 small {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: #7a8594;
    }
    .arc-buscar {
        position: relative;
        flex: 0 1 300px;
        min-width: 180px;
    }
    .arc-buscar .fa {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .arc-buscar input,
    .arc-orden {
        height: 34px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 17px;
        background: var(--correos-fondo, #F3F5F8);
        box-shadow: none;
    }
    .arc-buscar input {
        width: 100%;
        padding: 4px 10px 4px 32px;
    }
    .arc-orden {
        padding: 4px 12px;
        cursor: pointer;
    }
    .arc-buscar input:focus,
    .arc-orden:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        background: #fff;
    }

    .arc-mes {
        margin: 18px 0 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        color: #6b7686;
    }
    .arc-mes span {
        font-weight: normal;
        color: #9aa4b2;
    }
    .arc-mes:first-child {
        margin-top: 0;
    }
    .arc-item {
        display: grid;
        grid-template-columns: 44px minmax(0, 2fr) minmax(0, 2fr) auto;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        margin-bottom: 8px;
        background: #fff;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 10px;
        transition: box-shadow .15s, border-color .15s;
    }
    .arc-item:hover {
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 4px 12px rgba(18, 62, 115, .10);
    }
    .arc-icono {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        background: #FDE8E8;
        color: #D32F2F;
    }
    .arc-icono.arc-otro {
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .arc-nombre {
        display: block;
        font-weight: 600;
        color: #2d3748;
        word-break: break-word;
        line-height: 1.3;
    }
    .arc-meta {
        display: block;
        margin-top: 3px;
        font-size: 12px;
        color: #8a94a3;
    }
    .arc-doc {
        min-width: 0;
        font-size: 13px;
    }
    .arc-doc a.arc-cite {
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .arc-tipo {
        display: inline-block;
        padding: 1px 7px;
        margin-right: 4px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .arc-ref {
        display: block;
        margin-top: 3px;
        color: #4a5568;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .arc-hr {
        display: inline-block;
        margin-top: 4px;
        font-size: 12px;
        font-weight: 600;
        color: var(--correos-azul, #1A549A);
    }
    .arc-acciones {
        display: flex;
        gap: 6px;
        white-space: nowrap;
    }
    .arc-acciones .btn {
        margin: 0;
    }
    .arc-vacio,
    .arc-sin-resultados {
        padding: 40px 20px;
        text-align: center;
        color: #7a8594;
        background: #fff;
        border-radius: 10px;
    }
    .arc-vacio .fa {
        display: block;
        margin-bottom: 10px;
        font-size: 40px;
        color: var(--correos-azul, #1A549A);
    }
    .arc-sin-resultados {
        display: none;
    }
    @media (max-width: 991px) {
        .arc-item {
            grid-template-columns: 44px minmax(0, 1fr);
        }
        .arc-doc,
        .arc-acciones {
            grid-column: 2;
        }
    }
    @media (max-width: 767px) {
        .arc-barra {
            position: static;
        }
        .arc-buscar {
            flex: 1 1 100%;
        }
    }
</style>

<div class="arc-barra">
    <h3><i class="fa fa-paperclip"></i> Archivos digitales
        <small><?php echo count($lista); ?> archivo<?php echo count($lista) == 1 ? '' : 's'; ?> subido<?php echo count($lista) == 1 ? '' : 's'; ?> &middot; <?php echo $total_texto; ?> en total</small>
    </h3>
    <?php if ($lista): ?>
        <div class="arc-buscar">
            <i class="fa fa-search"></i>
            <input type="text" id="arc-buscar" placeholder="Buscar por nombre, cite, referencia u hoja de ruta..."/>
        </div>
        <select id="arc-orden" class="arc-orden" title="Ordenar">
            <option value="fecha-desc">Más recientes primero</option>
            <option value="fecha-asc">Más antiguos primero</option>
            <option value="nombre-asc">Nombre (A-Z)</option>
            <option value="tamanio-desc">Más pesados primero</option>
        </select>
    <?php endif; ?>
</div>

<?php if (!$lista): ?>
    <div class="arc-vacio">
        <i class="fa fa-cloud-upload"></i>
        <h4>Aún no subió archivos digitales</h4>
        Los archivos que suba a sus documentos aparecerán aquí.
    </div>
<?php else: ?>
    <div id="arc-lista">
        <?php foreach ($lista as $a): ?>
            <div class="arc-item" data-ts="<?php echo $a['ts']; ?>" data-bytes="<?php echo $a['bytes']; ?>"
                 data-mes="<?php echo HTML::chars($a['mes']); ?>" data-nombre="<?php echo HTML::chars(mb_strtolower($a['nombre'], 'UTF-8')); ?>"
                 data-texto="<?php echo HTML::chars(mb_strtolower($a['nombre'] . ' ' . $a['cite'] . ' ' . $a['referencia'] . ' ' . $a['nur'] . ' ' . $a['tipo'], 'UTF-8')); ?>">
                <div class="arc-icono <?php echo $a['es_pdf'] ? '' : 'arc-otro'; ?>"><i class="fa <?php echo $a['es_pdf'] ? 'fa-file-pdf-o' : 'fa-file-o'; ?>"></i></div>
                <div>
                    <span class="arc-nombre"><?php echo HTML::chars($a['nombre']); ?></span>
                    <span class="arc-meta"><?php echo $a['tamanio']; ?> &middot; subido el <?php echo $a['fecha']; ?></span>
                </div>
                <div class="arc-doc">
                    <span class="arc-tipo"><?php echo HTML::chars($a['tipo']); ?></span>
                    <a class="arc-cite" href="/document/detalle/<?php echo $a['id_documento']; ?>" title="Ver documento"><?php echo HTML::chars($a['cite']); ?></a>
                    <span class="arc-ref" title="<?php echo HTML::chars($a['referencia']); ?>"><?php echo HTML::chars($a['referencia']); ?></span>
                    <?php if (trim($a['nur']) != ''): ?>
                        <a class="arc-hr" href="/route/trace/?hr=<?php echo urlencode($a['nur']); ?>" title="Ver seguimiento"><i class="md md-label"></i> <?php echo HTML::chars($a['nur']); ?></a>
                    <?php endif; ?>
                </div>
                <div class="arc-acciones">
                    <?php if ($a['es_pdf']): ?>
                        <a href="#" class="btn btn-sm btn-primary visor-pdf" title="Ver sin descargar"
                           data-url="/download/?file=<?php echo $a['id']; ?>&amp;ver=1" data-descargar="/download/?file=<?php echo $a['id']; ?>"
                           data-nombre="<?php echo HTML::chars($a['nombre']); ?>"><i class="fa fa-eye"></i> Ver</a>
                    <?php endif; ?>
                    <a href="/download/?file=<?php echo $a['id']; ?>" class="btn btn-sm btn-default-bright" title="Descargar"><i class="fa fa-download"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="arc-sin-resultados">Ningún archivo coincide con la búsqueda.</div>
<?php endif; ?>

<script type="text/javascript">
    $(function () {
        var $lista = $('#arc-lista');
        if (!$lista.length) {
            return;
        }
        var $items = $lista.children('.arc-item');

        // ordena, filtra y, si el orden es por fecha, agrupa por mes con su cantidad
        function pintar() {
            var orden = $('#arc-orden').val();
            var texto = $.trim($('#arc-buscar').val().toLowerCase());
            var palabras = texto ? texto.split(/\s+/) : [];
            var ordenados = $items.get().sort(function (a, b) {
                var $a = $(a), $b = $(b);
                switch (orden) {
                    case 'fecha-asc':
                        return $a.data('ts') - $b.data('ts');
                    case 'nombre-asc':
                        return String($a.attr('data-nombre')).localeCompare(String($b.attr('data-nombre')));
                    case 'tamanio-desc':
                        return $b.data('bytes') - $a.data('bytes');
                    default:
                        return $b.data('ts') - $a.data('ts');
                }
            });
            $lista.children('.arc-mes').remove();
            var visibles = 0, mesActual = null, $cabecera = null, enMes = 0;
            var porFecha = orden.indexOf('fecha') === 0;
            $.each(ordenados, function (i, el) {
                var $el = $(el);
                var coincide = $.grep(palabras, function (p) {
                    return String($el.attr('data-texto')).indexOf(p) === -1;
                }).length === 0;
                $el.toggle(coincide);
                $lista.append($el);
                if (!coincide) {
                    return;
                }
                visibles++;
                if (porFecha) {
                    if ($el.attr('data-mes') !== mesActual) {
                        if ($cabecera) {
                            $cabecera.find('span').text(' · ' + enMes);
                        }
                        mesActual = $el.attr('data-mes');
                        enMes = 0;
                        $cabecera = $('<div class="arc-mes"></div>').text(mesActual).append('<span></span>');
                        $el.before($cabecera);
                    }
                    enMes++;
                }
            });
            if ($cabecera) {
                $cabecera.find('span').text(' · ' + enMes);
            }
            $('.arc-sin-resultados').toggle(visibles === 0);
        }

        $('#arc-buscar').on('keyup search', pintar).focus();
        $('#arc-orden').on('change', pintar);
        pintar();
    });
</script>

<?php echo View::factory('documentos/visor_pdf'); ?>
