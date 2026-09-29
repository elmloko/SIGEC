<?php
// iconos por tipo de documento (por palabra clave del nombre)
$iconos_tipo = array(
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
$icono_de = function ($nombre) use ($iconos_tipo) {
    $n = mb_strtolower($nombre, 'UTF-8');
    foreach ($iconos_tipo as $clave => $icono) {
        if (strpos($n, $clave) !== FALSE) {
            return $icono;
        }
    }
    return 'fa-file-o';
};
$total_documentos = 0;
foreach ($mistipos as $t) {
    $total_documentos += (int) $t['cantidad'];
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        var user = $('#user').val();
        var source =
                {
                    datatype: "json",
                    datafields: [
                        {name: 'cite_original', type: 'string'},
                        {name: 'nur', type: 'string'},
                        {name: 'tipo', type: 'string'},
                        {name: 'nombre_destinatario', type: 'string'},
                        {name: 'cargo_destinatario', type: 'string'},
                        {name: 'referencia', type: 'string'},
                        {name: 'institucion_destinatario', type: 'string'},
                        {name: 'institucion_remitente', type: 'string'},
                        {name: 'fecha_creacion', type: 'date', format: 'yyyy-MM-dd H:mm:ss'},
                        {name: 'nombre', type: 'string'},
                        {name: 'cargo', type: 'string'},
                        {name: 'estado', type: 'int'},
                        {name: 'link', type: 'string'},
                        {name: 'id', type: 'int'},
                        {name: 'edit', type: 'string'},
                    ],
                    cache: false,
                    url: '/ajaxd/documentosjson/' + user,
                    data: {
                        user: user
                    },
                    filter: function () {
                        $("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                    },
                    sort: function () {
                        $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                    },
                    root: 'Rows',
                    beforeprocessing: function (data) {
                        if (data != null) {
                            source.totalrecords = data[0].TotalRows;
                        }
                    }
                };
        var dataadapter = new $.jqx.dataAdapter(source, {
            loadError: function (xhr, status, error) {
                alert(error);
            }
        });

        function escapar(texto) {
            return $('<div/>').text(texto == null ? '' : texto).html();
        }
        function dos(n) {
            return (n < 10 ? '0' : '') + n;
        }

        // destinatario: nombre y, debajo, su cargo
        var destinatarioRenderer = function (row, column, value, defaulthtml, columnproperties, rowdata) {
            var d = rowdata || $('#jqxgrid').jqxGrid('getrowdata', row) || {};
            var html = '<div class="doc-celda"><span class="doc-dest-nombre">' + escapar(value) + '</span>';
            if (d.cargo_destinatario) {
                html += '<span class="doc-dest-cargo">' + escapar(d.cargo_destinatario) + '</span>';
            }
            return html + '</div>';
        };
        // fecha legible (el filtro sigue usando el formato original de la columna)
        var fechaRenderer = function (row, column, value) {
            if (!(value instanceof Date)) {
                return '<div class="doc-celda">' + escapar(value) + '</div>';
            }
            return '<div class="doc-celda"><span class="doc-fecha">' + dos(value.getDate()) + '/' + dos(value.getMonth() + 1) + '/' + value.getFullYear()
                    + '</span><span class="doc-hora">' + dos(value.getHours()) + ':' + dos(value.getMinutes()) + '</span></div>';
        };
        var tipoRenderer = function (row, column, value) {
            return '<div class="doc-celda"><span class="doc-tipo">' + escapar(value) + '</span></div>';
        };
        // celdas que ya vienen con HTML del servidor (hoja de ruta, estado, acciones)
        var htmlRenderer = function (row, column, value) {
            return '<div class="doc-celda doc-celda-acciones">' + (value || '') + '</div>';
        };
        var textoRenderer = function (row, column, value) {
            return '<div class="doc-celda">' + escapar(value) + '</div>';
        };

        // alto de la tabla segun la pantalla
        function altoGrilla() {
            var top = $('#jqxgrid').offset().top;
            return Math.max(420, $(window).height() - top - 90);
        }

        $("#jqxgrid").jqxGrid({
            source: dataadapter,
            width: '100%',
            height: altoGrilla(),
            filterable: true,
            altrows: true,
            showfilterrow: true,
            sortable: true,
            autorowheight: true,
            pageable: true,
            virtualmode: true,
            pagesize: 20,
            pagesizeoptions: ['20', '50', '100'],
            enabletooltips: true,
            columnsresize: true,
            theme: 'custom',
            localization: {
                pagergotopagestring: 'Ir a la página:',
                pagershowrowsstring: 'Mostrar filas:',
                pagerrangestring: ' de ',
                emptydatastring: 'No se encontraron documentos',
                filterselectstring: 'Todos'
            },
            rendergridrows: function (obj) {
                return obj.data;
            },
            columns: [
                {text: 'Hoja de ruta', datafield: 'nur', width: '11%', cellsrenderer: htmlRenderer},
                {text: 'Tipo', datafield: 'tipo', width: '8%', filtertype: 'checkedlist', cellsrenderer: tipoRenderer,
                    filteritems: <?php echo json_encode(array_values($tipoDoc)); ?>},
                {text: 'Cite', datafield: 'cite_original', width: '14%', cellsrenderer: textoRenderer},
                {text: 'Destinatario', datafield: 'nombre_destinatario', width: '21%', cellsrenderer: destinatarioRenderer},
                {text: 'Referencia', datafield: 'referencia', width: '24%', cellsrenderer: textoRenderer},
                {text: 'Fecha', datafield: 'fecha_creacion', width: '9%', cellsformat: 'yyyy-MM-dd H:mm:ss', filtertype: 'date', cellsrenderer: fechaRenderer},
                {text: 'Estado', datafield: 'link', width: '7%', filterable: false, sortable: false, cellsrenderer: htmlRenderer},
                {text: 'Acciones', datafield: 'edit', width: '6%', filterable: false, sortable: false, cellsrenderer: htmlRenderer}
            ]
        });

        $(window).on('resize', function () {
            $('#jqxgrid').jqxGrid({height: altoGrilla()});
        });

        // tarjetas de tipo: filtran la tabla; un segundo clic quita el filtro
        function filtrarPorTipo(tipo) {
            $('.doc-tipo-card').removeClass('activa');
            $('#jqxgrid').jqxGrid('removefilter', 'tipo', false);
            if (tipo) {
                var grupo = new $.jqx.filter();
                grupo.addfilter(1, grupo.createfilter('stringfilter', tipo, 'EQUAL'));
                $('#jqxgrid').jqxGrid('addfilter', 'tipo', grupo);
                $('.doc-tipo-card[data-tipo="' + tipo.replace(/"/g, '\\"') + '"]').addClass('activa');
            }
            $('#jqxgrid').jqxGrid('applyfilters');
        }
        $('.doc-tipo-card').on('click', function (e) {
            if ($(e.target).closest('.doc-tipo-nuevo').length) {
                return; // el enlace "+ Nuevo" navega normalmente
            }
            var tipo = $(this).attr('data-tipo');
            filtrarPorTipo($(this).hasClass('activa') ? '' : tipo);
        }).on('keydown', function (e) {
            if (e.which === 13 || e.which === 32) {
                e.preventDefault();
                $(this).trigger('click');
            }
        });

        $('#quitarFiltro').click(function () {
            $('.doc-tipo-card').removeClass('activa');
            $("#jqxgrid").jqxGrid('clearfilters');
        });

        // justificacion del rechazo: cada icono trae su propio texto
        $(document).on('click', '.doc-ver-rechazo', function (e) {
            e.preventDefault();
            $('#doc-rechazo-nur').text($(this).attr('data-nur') || '');
            $('#doc-rechazo-texto').text($(this).attr('data-observacion') || 'Sin justificación registrada.');
            $('#doc-rechazo-modal').appendTo('body').modal('show');
        });
    });
</script>
<style>
    /* ===== encabezado ===== */
    .doc-encabezado {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .doc-encabezado h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 500;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .doc-encabezado h2 small {
        display: block;
        margin-top: 2px;
        font-size: 13px;
        color: #6b7686;
    }
    .doc-encabezado .doc-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .doc-encabezado .doc-botones .btn {
        margin: 0;
    }
    .doc-encabezado .dropdown-menu {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* ===== tarjetas por tipo ===== */
    .doc-tipos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }
    .doc-tipo-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: #fff;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-left: 4px solid var(--correos-azul, #1A549A);
        border-radius: 8px;
        cursor: pointer;
        transition: box-shadow .15s, border-color .15s, transform .15s;
        user-select: none;
    }
    .doc-tipo-card:hover,
    .doc-tipo-card:focus {
        outline: none;
        box-shadow: 0 3px 10px rgba(18, 62, 115, .12);
        transform: translateY(-1px);
    }
    .doc-tipo-card.activa {
        border-color: var(--correos-amarillo, #FECB34);
        border-left-color: var(--correos-amarillo, #FECB34);
        background: var(--correos-amarillo-suave, #FFF7DD);
    }
    .doc-tipo-icono {
        flex: 0 0 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: var(--correos-azul, #1A549A);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .doc-tipo-card.activa .doc-tipo-icono {
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
    }
    .doc-tipo-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .doc-tipo-nombre {
        display: block;
        font-size: 13px;
        color: #4a5568;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .doc-tipo-cantidad {
        display: block;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.1;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .doc-tipo-nuevo {
        position: absolute;
        top: 6px;
        right: 8px;
        font-size: 11px;
        color: var(--correos-azul, #1A549A);
        opacity: 0;
        transition: opacity .15s;
    }
    .doc-tipo-card:hover .doc-tipo-nuevo,
    .doc-tipo-card:focus .doc-tipo-nuevo {
        opacity: 1;
    }
    @media (hover: none) {
        .doc-tipo-nuevo {
            opacity: 1;
        }
    }

    /* ===== tabla ===== */
    .doc-tabla {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .10);
        padding: 4px;
    }
    #jqxgrid {
        border: 0;
    }
    .jqx-grid-column-header {
        z-index: 1 !important;
    }
    .jqx-grid-column-header span {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .3px;
    }
    .jqx-grid-cell {
        z-index: 1 !important;
    }
    .jqx-grid-cell-custom {
        font-size: 12px !important;
    }
    .doc-celda {
        padding: 8px 6px;
        line-height: 1.35;
        white-space: normal;
        word-break: break-word;
    }
    .doc-dest-nombre {
        display: block;
        font-weight: 600;
        color: #2d3748;
    }
    .doc-dest-cargo {
        display: block;
        font-size: 11px;
        color: #7a8594;
    }
    .doc-fecha {
        display: block;
        font-weight: 600;
        color: #2d3748;
    }
    .doc-hora {
        display: block;
        font-size: 11px;
        color: #7a8594;
    }
    .doc-tipo {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
        background: var(--correos-azul-suave, #EAF1F9);
    }
    .doc-celda-acciones a {
        display: inline-block;
        margin: 0 3px 2px 0;
        line-height: 1;
    }
    .doc-celda-acciones a:hover {
        opacity: .75;
    }

    /* ===== guia de iconos ===== */
    .doc-leyenda {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        margin-top: 10px;
        font-size: 12px;
        color: #6b7686;
    }
    .doc-leyenda span .md {
        font-size: 18px;
        vertical-align: middle;
        margin-right: 4px;
    }
</style>

<input type="hidden" value="<?php echo $user->id ?>" id="user"/>

<div class="doc-encabezado">
    <h2>Mis documentos
        <small><?php echo number_format($total_documentos, 0, ',', '.'); ?> documentos generados</small>
    </h2>
    <div class="doc-botones">
        <a class="btn btn-sm btn-default-bright" href="javascript:;" id="quitarFiltro"><i class="fa fa-filter"></i> Quitar filtros</a>
        <div class="btn-group">
            <button data-toggle="dropdown" class="btn ink-reaction btn-sm btn-accent dropdown-toggle" type="button" aria-expanded="false">
                <i class="fa fa-plus"></i> Generar documento <i class="fa fa-caret-down"></i>
            </button>
            <ul role="menu" class="dropdown-menu dropdown-menu-right">
                <?php foreach ($mistipos as $t): ?>
                    <li><a href="/documento/generar/<?php echo HTML::chars($t['accion']); ?>">
                            <i class="fa <?php echo $icono_de($t['tipo']); ?> fa-fw"></i> <?php echo HTML::chars($t['tipo']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<div class="doc-tipos">
    <?php foreach ($mistipos as $t): ?>
        <div class="doc-tipo-card" tabindex="0" role="button" data-tipo="<?php echo HTML::chars($t['tipo']); ?>"
             title="Ver solo <?php echo HTML::chars($t['plural']); ?>">
            <div class="doc-tipo-icono"><i class="fa <?php echo $icono_de($t['tipo']); ?>"></i></div>
            <div class="doc-tipo-texto">
                <span class="doc-tipo-cantidad"><?php echo number_format((int) $t['cantidad'], 0, ',', '.'); ?></span>
                <span class="doc-tipo-nombre"><?php echo HTML::chars($t['plural']); ?></span>
            </div>
            <a class="doc-tipo-nuevo" href="/documento/generar/<?php echo HTML::chars($t['accion']); ?>"
               title="Generar <?php echo HTML::chars($t['tipo']); ?>"><i class="fa fa-plus"></i> Nuevo</a>
        </div>
    <?php endforeach; ?>
</div>

<div class="doc-tabla">
    <div id="jqxgrid"></div>
</div>

<div class="modal fade" id="doc-rechazo-modal" tabindex="-1" role="dialog" aria-labelledby="doc-rechazo-titulo">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="doc-rechazo-titulo">
                    <i class="fa fa-exclamation-triangle" style="color:#d32f2f"></i> Justificación del rechazo
                    <small id="doc-rechazo-nur"></small>
                </h4>
            </div>
            <div class="modal-body">
                <p id="doc-rechazo-texto" style="white-space: pre-line; margin: 0;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="doc-leyenda">
    <span><i class="md md-play-circle-outline text-warning"></i>Derivar hoja de ruta</span>
    <span><i class="md md-class text-accent-dark"></i>Asignar hoja de ruta</span>
    <span><i class="md md-verified-user text-success"></i>Ver seguimiento</span>
    <span><i class="md md-print text-primary"></i>Imprimir hoja de ruta</span>
    <span><i class="md md-mode-edit text-primary-dark"></i>Editar documento</span>
    <span><i class="fa fa-file-word-o text-primary-dark"></i>Plantilla Word</span>
    <span><i class="fa fa-exclamation-triangle" style="color:#d32f2f"></i> Ver justificación de rechazo</span>
</div>
