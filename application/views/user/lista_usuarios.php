<?php
// se abre dentro de un iframe (eModal) desde el perfil y desde los formularios de documentos
$dir_fotos = DOCROOT . 'static/fotos/';
$id = isset($id) ? (int) $id : (int) Auth::instance()->get_user()->id;
$lista = array();
foreach ($destinos as $d) {
    $lista[] = $d;
}
$total = count($lista);
?>
<style>
    html, body {
        background: #F3F5F8 !important;
    }
    body {
        margin: 0;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #2d3748;
    }
    .ld-barra {
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 14px;
        padding: 14px 18px;
        background: #fff;
        border-bottom: 3px solid #FECB34;
        box-shadow: 0 2px 8px rgba(18, 62, 115, .08);
    }
    .ld-buscar {
        position: relative;
        flex: 1 1 320px;
    }
    .ld-buscar .fa {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .ld-buscar input {
        width: 100%;
        height: 42px;
        padding: 6px 12px 6px 38px;
        border: 1px solid #DCE3EC;
        border-radius: 21px;
        background: #F3F5F8;
        font-size: 14px;
    }
    .ld-buscar input:focus {
        outline: none;
        border-color: #1A549A;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .ld-info {
        font-size: 12.5px;
        color: #6b7686;
        white-space: nowrap;
    }
    .ld-info b {
        color: #1A549A;
    }
    .ld-limpiar {
        margin-left: 6px;
        color: #8a94a3;
        font-size: 12px;
    }
    .ld-agregar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: #1A549A;
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, opacity .15s;
    }
    .ld-agregar:hover {
        background: #123E73;
    }
    .ld-agregar[disabled] {
        opacity: .45;
        cursor: not-allowed;
    }
    .ld-agregar .ld-n {
        min-width: 22px;
        padding: 1px 7px;
        border-radius: 11px;
        background: #FECB34;
        color: #123E73;
        font-size: 12px;
    }
    .ld-lista {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 10px;
        padding: 16px 18px 24px;
    }
    .ld-persona {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        padding: 12px 44px 12px 12px;
        border: 2px solid transparent;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .10);
        cursor: pointer;
        font-weight: normal;
        transition: border-color .12s, box-shadow .12s, background .12s;
    }
    .ld-persona:hover {
        box-shadow: 0 4px 12px rgba(18, 62, 115, .14);
    }
    .ld-persona input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .ld-persona img {
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
    }
    .ld-datos {
        min-width: 0;
        line-height: 1.3;
    }
    .ld-datos b {
        display: block;
        font-size: 14px;
        color: #123E73;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ld-datos span {
        display: block;
        font-size: 12px;
        color: #6b7686;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ld-datos span .fa {
        width: 13px;
        margin-right: 3px;
        color: #9aa4b2;
    }
    /* casilla visual */
    .ld-marca {
        position: absolute;
        right: 14px;
        top: 50%;
        width: 22px;
        height: 22px;
        margin-top: -11px;
        border: 2px solid #C5CCD6;
        border-radius: 6px;
        background: #fff;
        color: transparent;
        font-size: 12px;
        line-height: 18px;
        text-align: center;
        transition: all .12s;
    }
    .ld-persona.sel {
        border-color: #1A549A;
        background: #EAF1F9;
    }
    .ld-persona.sel .ld-marca {
        border-color: #1A549A;
        background: #1A549A;
        color: #fff;
    }
    .ld-persona input:focus-visible + .ld-marca {
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .3);
    }
    .ld-vacio {
        grid-column: 1 / -1;
        padding: 40px 20px;
        text-align: center;
        color: #8a94a3;
    }
    .ld-vacio .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 32px;
        color: #C5CCD6;
    }
    mark {
        padding: 0;
        background: #FFF0B3;
        color: inherit;
    }
</style>

<input type="hidden" value="<?php echo $id; ?>" id="id_user">

<div class="ld-barra">
    <div class="ld-buscar">
        <i class="fa fa-search"></i>
        <input type="search" id="FilterTextBox" placeholder="Buscar por nombre, cargo u oficina…" autocomplete="off"/>
    </div>
    <span class="ld-info"><span id="ld-visibles"><?php echo $total; ?></span> personas ·
        <b id="ld-seleccionados">0</b> seleccionadas
        <a href="#" class="ld-limpiar" id="ld-limpiar" style="display:none">quitar selección</a></span>
    <button type="button" class="ld-agregar" id="adicionar" disabled>
        <i class="fa fa-user-plus"></i> Agregar <span class="ld-n" id="ld-n">0</span>
    </button>
</div>

<div class="ld-lista" id="ld-lista">
    <?php if (!$total): ?>
        <div class="ld-vacio"><i class="fa fa-users"></i> Ya tiene a todas las personas en su lista de destinatarios.</div>
    <?php endif; ?>
    <?php foreach ($lista as $d):
        $f = $dir_fotos . $d->username . '.jpg';
        $foto = file_exists($f) ? '/static/fotos/' . $d->username . '.jpg?v=' . filemtime($f) : '/static/fotos/' . ($d->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
        ?>
        <label class="ld-persona usuario">
            <input type="checkbox" name="s" class="check" value="<?php echo (int) $d->id; ?>"/>
            <span class="ld-marca"><i class="fa fa-check"></i></span>
            <img src="<?php echo HTML::chars($foto); ?>" alt="" loading="lazy"/>
            <span class="ld-datos">
                <b class="ld-t"><?php echo HTML::chars($d->nombre); ?></b>
                <span><i class="fa fa-briefcase"></i><span class="ld-t" style="display:inline"><?php echo HTML::chars($d->cargo != '' ? $d->cargo : 'Sin cargo'); ?></span></span>
                <span><i class="fa fa-map-marker"></i><span class="ld-t" style="display:inline"><?php echo HTML::chars($d->oficina); ?></span></span>
            </span>
        </label>
    <?php endforeach; ?>
    <div class="ld-vacio" id="ld-sin-resultados" style="display:none"><i class="fa fa-search"></i> Nadie coincide con la búsqueda.</div>
</div>

<script type="text/javascript">
    $(function () {
        var $personas = $('#ld-lista .ld-persona');
        // texto sin tildes para buscar "jose" y encontrar "José"
        function normal(t) {
            t = (t || '').toLowerCase();
            return t.normalize ? t.normalize('NFD').replace(/[̀-ͯ]/g, '') : t;
        }
        $personas.each(function () {
            var $p = $(this);
            $p.data('texto', normal($p.find('.ld-datos').text()));
            $p.find('.ld-t').each(function () {
                $(this).data('original', $(this).text());
            });
        });

        function contar() {
            var n = $personas.filter('.sel').length;
            $('#ld-seleccionados').text(n);
            $('#ld-n').text(n);
            $('#adicionar').prop('disabled', n === 0);
            $('#ld-limpiar').toggle(n > 0);
        }
        $personas.find('input').on('change', function () {
            $(this).closest('.ld-persona').toggleClass('sel', this.checked);
            contar();
        });
        $('#ld-limpiar').on('click', function (e) {
            e.preventDefault();
            $personas.find('input').prop('checked', false).trigger('change');
        });

        // busqueda: todas las palabras deben aparecer (en cualquier orden) y se resaltan
        $('#FilterTextBox').on('input keyup search', function () {
            var palabras = normal($(this).val()).split(/\s+/).filter(function (w) {
                return w !== '';
            });
            var visibles = 0;
            $personas.each(function () {
                var $p = $(this), texto = $p.data('texto');
                var ok = palabras.every(function (w) {
                    return texto.indexOf(w) > -1;
                });
                $p.toggle(ok);
                visibles += ok ? 1 : 0;
                $p.find('.ld-t').each(function () {
                    var original = $(this).data('original');
                    if (!ok || !palabras.length) {
                        $(this).text(original);
                        return;
                    }
                    var esc = function (t) {
                        return $('<div>').text(t).html();
                    };
                    var plano = normal(original), html = esc(original);
                    // se resalta la primera palabra buscada que aparezca en este campo
                    palabras.some(function (w) {
                        var i = plano.indexOf(w);
                        if (i === -1) {
                            return false;
                        }
                        html = esc(original.substr(0, i)) + '<mark>' + esc(original.substr(i, w.length)) + '</mark>' + esc(original.substr(i + w.length));
                        return true;
                    });
                    $(this).html(html);
                });
            });
            $('#ld-visibles').text(visibles);
            $('#ld-sin-resultados').toggle(visibles === 0 && $personas.length > 0);
        }).focus();

        // Enter en la busqueda con un solo resultado lo selecciona
        $('#FilterTextBox').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var $v = $personas.filter(':visible');
                if ($v.length === 1) {
                    $v.find('input').prop('checked', !$v.find('input').prop('checked')).trigger('change');
                }
            }
        });

        $('#adicionar').on('click', function () {
            var ids = $personas.filter('.sel').find('input').map(function () {
                return this.value;
            }).get();
            if (!ids.length) {
                return;
            }
            var $b = $(this).prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> Agregando…');
            $.ajax({
                type: 'POST',
                url: '/ajax/addUser',
                dataType: 'json',
                data: {destinos: ids.join(';'), id: $('#id_user').val()},
                success: function (r) {
                    var p = window.parent;
                    // la pagina que abrio el modal decide: el formulario de documentos agrega a su libreta
                    // sin recargar (para no perder lo escrito); el resto recarga
                    if (p && p !== window && typeof p.destinatariosAgregados === 'function') {
                        p.destinatariosAgregados((r && r.agregados) || []);
                    } else if (p && p !== window) {
                        p.location.reload();
                    }
                },
                error: function () {
                    alert('No se pudieron agregar los destinatarios. Intente nuevamente.');
                    $b.prop('disabled', false).html('<i class="fa fa-user-plus"></i> Agregar <span class="ld-n">' + ids.length + '</span>');
                }
            });
        });
    });
</script>
