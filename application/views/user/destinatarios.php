<?php
$dir_fotos = DOCROOT . 'static/fotos/';
$foto_de = function ($username, $genero) use ($dir_fotos) {
    $f = $dir_fotos . $username . '.jpg';
    return ($username != '' && file_exists($f)) ? '/static/fotos/' . $username . '.jpg?v=' . filemtime($f) : '/static/fotos/' . ($genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
};
// agrupados por oficina
$por_oficina = array();
$total = 0;
foreach ($destinatarios as $d) {
    $of = trim((string) $d->oficina) !== '' ? mb_strtoupper(trim($d->oficina), 'UTF-8') : 'SIN OFICINA';
    $por_oficina[$of][] = $d;
    $total++;
}
ksort($por_oficina);
?>
<style>
    .ds-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ds-card .btn {
        margin: 0;
    }
    .ds-card .btn .fa {
        margin-right: 5px;
    }
    .ds-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 18px;
        padding: 18px 22px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .ds-cab-icono {
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
    .ds-cab-texto {
        flex: 1 1 320px;
        min-width: 0;
    }
    .ds-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ds-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .ds-cifra {
        padding: 8px 14px;
        border-radius: 12px;
        background: var(--correos-fondo, #F3F5F8);
        text-align: center;
    }
    .ds-cifra b {
        display: block;
        font-size: 20px;
        line-height: 1.1;
        color: var(--correos-azul, #1A549A);
    }
    .ds-cifra span {
        font-size: 11.5px;
        color: #7a8594;
    }
    .ds-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 14px 22px;
        border-bottom: 1px solid #EEF2F7;
    }
    .ds-buscar {
        position: relative;
        flex: 1 1 300px;
    }
    .ds-buscar .fa {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .ds-buscar input {
        width: 100%;
        height: 38px;
        padding: 4px 12px 4px 34px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 19px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 13px;
    }
    .ds-buscar input:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .ds-vista {
        display: inline-flex;
        padding: 3px;
        border-radius: 10px;
        background: var(--correos-fondo, #F3F5F8);
    }
    .ds-vista button {
        padding: 6px 12px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        font-size: 12px;
        font-weight: 600;
        color: #6b7686;
        cursor: pointer;
    }
    .ds-vista button.activo {
        background: #fff;
        color: var(--correos-azul, #1A549A);
        box-shadow: 0 1px 3px rgba(18, 62, 115, .15);
    }
    .ds-cuerpo {
        padding: 6px 22px 20px;
    }
    .ds-grupo-titulo {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 16px 0 8px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ds-grupo-titulo .fa {
        color: #B7791F;
    }
    .ds-grupo-titulo span {
        padding: 0 7px;
        border-radius: 9px;
        background: #EEF2F7;
        color: #6b7686;
        font-size: 11px;
    }
    .ds-grilla {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 10px;
    }
    /* por oficina: los grupos se acomodan en columnas para no dejar huecos */
    #ds-grupos {
        column-width: 330px;
        column-gap: 18px;
    }
    #ds-grupos .ds-grupo {
        break-inside: avoid;
        -webkit-column-break-inside: avoid;
        padding: 16px 0 4px;
    }
    #ds-grupos .ds-grupo-titulo {
        margin-top: 0;
    }
    #ds-grupos .ds-grilla {
        grid-template-columns: minmax(0, 1fr);
        gap: 8px;
    }
    .ds-persona {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border: 1px solid #EEF2F7;
        border-radius: 12px;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .ds-persona:hover {
        border-color: var(--correos-borde, #DCE3EC);
        box-shadow: 0 4px 12px rgba(18, 62, 115, .10);
    }
    .ds-persona img {
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
    }
    .ds-datos {
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.3;
    }
    .ds-datos b {
        display: block;
        font-size: 13.5px;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ds-datos span {
        display: block;
        font-size: 11.5px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ds-oficina {
        display: none !important;
    }
    .ds-plano .ds-oficina {
        display: block !important;
    }
    .ds-quitar {
        flex: 0 0 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #b7c0cc;
        transition: background .15s, color .15s;
    }
    .ds-quitar:hover,
    .ds-quitar:focus {
        background: #FDE8E8;
        color: #D32F2F;
        text-decoration: none;
    }
    .ds-etiqueta {
        flex: 0 0 auto;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .ds-etiqueta.superior {
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
    }
    .ds-etiqueta.dependiente {
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #8a6100;
    }
    .ds-vacio {
        padding: 40px 20px;
        text-align: center;
        font-size: 13px;
        color: #8a94a3;
    }
    .ds-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 34px;
        color: #c5ccd6;
    }
    .ds-auto-cab {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid #EEF2F7;
    }
    .ds-auto-cab h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .ds-auto-cab p {
        margin: 0;
        font-size: 12.5px;
        color: #7a8594;
    }
</style>

<div class="col-lg-12">
    <!-- cabecera -->
    <div class="ds-card">
        <div class="ds-cab">
            <div class="ds-cab-icono"><i class="fa fa-users"></i></div>
            <div class="ds-cab-texto">
                <h2>Mis destinatarios</h2>
                <p>Las personas de esta lista aparecen en la <b>libreta</b> al generar un documento, para completar el destinatario o la vía con un clic.</p>
            </div>
            <div class="ds-cifra"><b><?php echo $total; ?></b><span>en su lista</span></div>
            <?php if (count($automaticos)): ?>
                <div class="ds-cifra"><b><?php echo count($automaticos); ?></b><span>automáticos</span></div>
            <?php endif; ?>
            <a href="#" class="btn btn-primary" id="addDes" rel="<?php echo (int) $user->id; ?>"><i class="fa fa-user-plus"></i> Agregar destinatarios</a>
        </div>

        <?php if ($total): ?>
            <div class="ds-barra">
                <div class="ds-buscar">
                    <i class="fa fa-search"></i>
                    <input type="search" id="ds-buscar" placeholder="Buscar por nombre, cargo u oficina…" autocomplete="off"/>
                </div>
                <div class="ds-vista" role="group" aria-label="Forma de ver la lista">
                    <button type="button" class="activo" data-vista="grupos"><i class="fa fa-sitemap"></i> Por oficina</button>
                    <button type="button" data-vista="plano"><i class="fa fa-sort-alpha-asc"></i> A–Z</button>
                </div>
            </div>
        <?php endif; ?>

        <div class="ds-cuerpo" id="ds-cuerpo">
            <?php if (!$total): ?>
                <div class="ds-vacio"><i class="fa fa-users"></i>
                    Todavía no agregó a nadie.<br/>Pulse <b>Agregar destinatarios</b> para elegir a las personas a las que suele enviar documentos.</div>
            <?php else: ?>
                <!-- vista por oficina -->
                <div id="ds-grupos">
                    <?php foreach ($por_oficina as $oficina => $personas): ?>
                        <div class="ds-grupo">
                            <div class="ds-grupo-titulo"><i class="fa fa-building-o"></i> <?php echo HTML::chars($oficina); ?> <span><?php echo count($personas); ?></span></div>
                            <div class="ds-grilla">
                                <?php foreach ($personas as $d): ?>
                                    <div class="ds-persona">
                                        <img src="<?php echo HTML::chars($foto_de($d->username, $d->genero)); ?>" alt="" loading="lazy"/>
                                        <div class="ds-datos">
                                            <b><?php echo HTML::chars($d->nombre); ?></b>
                                            <span><?php echo HTML::chars($d->cargo); ?></span>
                                            <span class="ds-oficina"><?php echo HTML::chars($d->oficina); ?></span>
                                        </div>
                                        <a href="/user/xdes/?id_des=<?php echo (int) $d->id; ?>&amp;id_user=<?php echo (int) $user->id; ?>&amp;volver=destinatarios"
                                           class="ds-quitar delDes" rel="<?php echo HTML::chars($d->nombre); ?>" title="Quitar de la lista"><i class="fa fa-times"></i></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="ds-vacio" id="ds-sin-resultados" style="display:none"><i class="fa fa-search"></i> Nadie coincide con la búsqueda.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($automaticos)): ?>
        <!-- automaticos -->
        <div class="ds-card">
            <div class="ds-auto-cab">
                <i class="fa fa-magic" style="font-size:18px;color:#B7791F"></i>
                <div>
                    <h3>Siempre disponibles</h3>
                    <p>Su superior y sus dependientes aparecen solos en la libreta; no hace falta agregarlos.</p>
                </div>
            </div>
            <div class="ds-cuerpo" style="padding-top:16px">
                <div class="ds-grilla">
                    <?php foreach ($automaticos as $a): ?>
                        <div class="ds-persona">
                            <img src="<?php echo HTML::chars($foto_de($a['username'], $a['genero'])); ?>" alt="" loading="lazy"/>
                            <div class="ds-datos">
                                <b><?php echo HTML::chars($a['nombre']); ?></b>
                                <span><?php echo HTML::chars($a['cargo']); ?></span>
                                <?php if ($a['oficina'] != ''): ?><span><?php echo HTML::chars($a['oficina']); ?></span><?php endif; ?>
                            </div>
                            <span class="ds-etiqueta <?php echo strtolower($a['relacion']); ?>"><?php echo HTML::chars($a['relacion']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $(function () {
        $('#addDes').on('click', function (e) {
            e.preventDefault();
            eModal.iframe('/content/destinos/' + $(this).attr('rel'), 'Agregar destinatarios');
        });
        $('a.delDes').on('click', function () {
            return confirm('¿Quitar de su lista de destinatarios a:\n' + $(this).attr('rel') + '?');
        });

        // vista por oficina o A-Z (se recuerda en este navegador)
        var $grupos = $('#ds-grupos');
        var plano = null;
        function vistaPlana() {
            if (!plano) {
                var personas = $grupos.find('.ds-persona').clone(true).get().sort(function (a, b) {
                    return $.trim($(a).find('b').text()).localeCompare($.trim($(b).find('b').text()), 'es', {sensitivity: 'base'});
                });
                plano = $('<div class="ds-grilla ds-plano" id="ds-plano" style="margin-top:16px"></div>').append(personas);
                plano.insertAfter($grupos);
            }
            return plano;
        }
        function mostrar(vista) {
            $('.ds-vista button').removeClass('activo').filter('[data-vista="' + vista + '"]').addClass('activo');
            $grupos.toggle(vista === 'grupos');
            if (vista === 'plano') {
                vistaPlana().show();
            } else if (plano) {
                plano.hide();
            }
            try {
                localStorage.setItem('sigec_destinatarios_vista', vista);
            } catch (e) {
            }
            $('#ds-buscar').trigger('input');
        }
        $('.ds-vista button').on('click', function () {
            mostrar($(this).data('vista'));
        });
        try {
            if (localStorage.getItem('sigec_destinatarios_vista') === 'plano') {
                mostrar('plano');
            }
        } catch (e) {
        }

        // busqueda sin tildes y con varias palabras
        function normal(t) {
            t = (t || '').toLowerCase();
            return t.normalize ? t.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : t;
        }
        $('#ds-buscar').on('input keyup search', function () {
            var palabras = normal($(this).val()).split(/\s+/).filter(Boolean);
            var visibles = 0;
            var $contenedor = (plano && plano.is(':visible')) ? plano : $grupos;
            $contenedor.find('.ds-persona').each(function () {
                var texto = normal($(this).find('.ds-datos').text() + ' ' + $(this).closest('.ds-grupo').find('.ds-grupo-titulo').text());
                var ok = palabras.every(function (w) {
                    return texto.indexOf(w) > -1;
                });
                $(this).toggle(ok);
                visibles += ok ? 1 : 0;
            });
            $grupos.find('.ds-grupo').each(function () {
                $(this).toggle($(this).find('.ds-persona:visible').length > 0 || !palabras.length);
            });
            $('#ds-sin-resultados').toggle(visibles === 0);
        });
    });
</script>
