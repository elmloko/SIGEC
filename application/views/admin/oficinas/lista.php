<?php
$h = function ($s) {
    return HTML::chars($s);
};
$activas = 0;
foreach ($oficinas as $o) {
    $activas += (int) $o['estado'] === 1 ? 1 : 0;
}
$total = count($oficinas);
?>
<style>
    .ad-lista {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ad-l-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px 14px 0 0;
    }
    .ad-l-icono {
        flex: 0 0 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #1A549A;
        color: #fff;
        font-size: 19px;
    }
    .ad-l-texto {
        flex: 1 1 240px;
        min-width: 0;
    }
    .ad-l-texto h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .ad-l-texto p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .ad-l-nuevo {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 36px;
        padding: 0 16px;
        border-radius: 9px;
        background: #1A549A;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .ad-l-nuevo:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    .ad-l-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 0 22px 14px;
        padding: 11px 14px;
        border-radius: 11px;
        font-size: 13px;
    }
    .ad-l-msg.ok {
        background: #E6F6EC;
        color: #1E7B45;
    }
    .ad-l-msg.mal {
        background: #FDECEC;
        color: #912018;
    }
    /* filtros */
    .ad-filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 0 22px 14px;
    }
    .ad-buscar {
        position: relative;
        flex: 1 1 240px;
    }
    .ad-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        margin-top: -7px;
        color: #9aa4b2;
        font-size: 13px;
    }
    .ad-buscar input {
        width: 100%;
        height: 36px;
        padding: 6px 11px 6px 31px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13.5px;
        outline: none;
    }
    .ad-buscar input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .ad-filtros select {
        height: 36px;
        min-width: 190px;
        padding: 6px 10px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13px;
        color: #334155;
    }
    .ad-pestanas {
        display: flex;
        gap: 4px;
    }
    .ad-pes {
        height: 36px;
        padding: 0 14px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #4a5566;
    }
    .ad-pes.activa {
        background: #1A549A;
        border-color: #1A549A;
        color: #fff;
    }
    /* tabla */
    .ad-tabla-caja {
        padding: 0 22px 22px;
    }
    table.ad-tabla {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        overflow: hidden;
    }
    .ad-tabla th {
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
    .ad-tabla td {
        padding: 10px 12px;
        border-bottom: 1px solid #F1F4F8;
        font-size: 12.5px;
        color: #334155;
        vertical-align: middle;
        word-wrap: break-word;
    }
    .ad-tabla tr:last-child td {
        border-bottom: 0;
    }
    .ad-tabla tbody tr:hover td {
        background: #F7FAFD;
    }
    .ad-sigla {
        display: inline-block;
        max-width: 100%;
        box-sizing: border-box;
        padding: 3px 8px;
        border-radius: 6px;
        background: #EEF3FA;
        color: #1A549A;
        font-family: Consolas, "Courier New", monospace;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
        /* las siglas largas se parten por la barra, no a mitad de palabra */
        word-break: normal;
        overflow-wrap: break-word;
    }
    .ad-ofi b {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }
    .ad-ofi small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .ad-etq {
        display: inline-block;
        padding: 1px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .ad-etq.si {
        background: #E6F4EC;
        color: #1E7B45;
    }
    .ad-etq.no {
        background: #FDECEC;
        color: #B42318;
    }
    .ad-num {
        font-weight: 600;
        color: #123E73;
    }
    .ad-num.cero {
        color: #9aa4b2;
        font-weight: 400;
    }
    .ad-acciones {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }
    .ad-acciones form {
        margin: 0;
    }
    .ad-acc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 29px;
        padding: 0 11px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
        cursor: pointer;
    }
    .ad-acc:hover {
        background: #EEF3FA;
        border-color: #B9C8DC;
        color: #123E73;
        text-decoration: none;
    }
    .ad-acc.apagar {
        border-color: #E8B4B0;
        color: #B42318;
    }
    .ad-acc.apagar:hover {
        background: #FDECEC;
        border-color: #D98A84;
    }
    .ad-acc.prender {
        border-color: #A7D9BC;
        color: #1E7B45;
    }
    .ad-acc.prender:hover {
        background: #E6F6EC;
        border-color: #6FBF95;
    }
    .ad-vacio {
        padding: 34px 16px;
        text-align: center;
        color: #8a94a3;
        font-size: 13.5px;
    }
    mark {
        padding: 0;
        background: #FFF3C4;
    }
</style>

<div class="ad-lista">
    <div class="ad-l-cab">
        <span class="ad-l-icono"><i class="fa fa-sitemap"></i></span>
        <div class="ad-l-texto">
            <h2>Oficinas<?php echo $filtrada ? ' de ' . $h($entidad) : ''; ?></h2>
            <p><?php echo $activas; ?> activas de <?php echo $total; ?>. Cada persona pertenece a una oficina, y de ahí salen las siglas del cite.</p>
        </div>
        <?php if ($filtrada): ?>
            <a href="/admin/oficinas/create/<?php echo (int) $id_entidad; ?>" class="ad-l-nuevo"><i class="fa fa-plus"></i> Nueva oficina</a>
        <?php else: ?>
            <span class="ad-l-nuevo" style="background:#E3E8EF;color:#6b7686;cursor:default"
                  title="Elija primero una entidad: la oficina se crea dentro de una."><i class="fa fa-plus"></i> Nueva oficina</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="ad-l-msg ok"><i class="fa fa-check-circle"></i> <span><?php echo $h($mensaje); ?></span></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="ad-l-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><?php echo $h($error); ?></span></div>
    <?php endif; ?>

    <div class="ad-filtros">
        <span class="ad-buscar">
            <i class="fa fa-search"></i>
            <input type="search" id="of-q" placeholder="Buscar por oficina, sigla o entidad…" autocomplete="off">
        </span>
        <?php echo Form::select('id_entidad', array('' => 'Todas las entidades') + $options, $id_entidad, array('id' => 'id_entidad')); ?>
        <span class="ad-pestanas">
            <button type="button" class="ad-pes activa" data-estado="todas">Todas</button>
            <button type="button" class="ad-pes" data-estado="1">Activas</button>
            <button type="button" class="ad-pes" data-estado="0">Inactivas</button>
        </span>
    </div>

    <div class="ad-tabla-caja">
        <table class="ad-tabla">
            <colgroup>
                <col style="width:165px"><col><col style="width:20%"><col style="width:100px"><col style="width:92px"><col style="width:190px">
            </colgroup>
            <thead>
            <tr>
                <th>Sigla</th>
                <th>Oficina</th>
                <th>Entidad</th>
                <th>Personal</th>
                <th>Estado</th>
                <th style="text-align:right">Opciones</th>
            </tr>
            </thead>
            <tbody id="of-filas">
            <?php foreach ($oficinas as $o):
                $activa = (int) $o['estado'] === 1;
                ?>
                <tr data-estado="<?php echo $activa ? 1 : 0; ?>"
                    data-buscar="<?php echo $h(strtolower($o['oficina'] . ' ' . $o['sigla'] . ' ' . $o['entidad'])); ?>">
                    <td><span class="ad-sigla" title="<?php echo $h($o['sigla']); ?>"><?php
                            // se permite el salto despues de cada barra para que no se corte a mitad de sigla
                            echo str_replace('/', '/<wbr>', $h($o['sigla']));
                            ?></span></td>
                    <td class="ad-ofi">
                        <b><?php echo $h($o['oficina']); ?></b>
                        <?php if (!empty($o['nombre_padre'])): ?>
                            <small>depende de <?php echo $h($o['nombre_padre']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><a href="/admin/oficinas/lista/<?php echo (int) $o['id_entidad']; ?>" style="color:#1A549A"><?php echo $h($o['entidad']); ?></a></td>
                    <td>
                        <?php if ((int) $o['usuarios'] > 0): ?>
                            <a href="/admin/user/lista/<?php echo (int) $o['id']; ?>" class="ad-num"><?php echo (int) $o['usuarios']; ?> <?php echo (int) $o['usuarios'] == 1 ? 'persona' : 'personas'; ?></a>
                        <?php else: ?>
                            <span class="ad-num cero">sin personal</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="ad-etq <?php echo $activa ? 'si' : 'no'; ?>"><?php echo $activa ? 'Activa' : 'Inactiva'; ?></span></td>
                    <td>
                        <div class="ad-acciones">
                            <a href="/admin/oficinas/edit/<?php echo (int) $o['id']; ?>" class="ad-acc"><i class="fa fa-pencil"></i> Editar</a>
                            <form method="post" action="/admin/oficinas/remove/<?php echo (int) $o['id']; ?>">
                                <button type="submit" class="ad-acc <?php echo $activa ? 'apagar' : 'prender'; ?> btn-estado-ofi"
                                        data-nombre="<?php echo $h($o['oficina']); ?>" data-activa="<?php echo $activa ? 1 : 0; ?>"
                                        data-usuarios="<?php echo (int) $o['usuarios']; ?>">
                                    <i class="fa <?php echo $activa ? 'fa-ban' : 'fa-check-circle'; ?>"></i> <?php echo $activa ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="ad-vacio" id="of-vacio" style="display:none"><i class="fa fa-search" style="display:block;font-size:24px;margin-bottom:6px"></i> Ninguna oficina coincide con la búsqueda.</div>
    </div>
</div>

<script>
    $(function () {
        $('#id_entidad').on('change', function () {
            var id = $(this).val();
            location.href = id ? '/admin/oficinas/lista/' + id : '/admin/oficinas/lista';
        });

        var estado = 'todas';
        function normal(t) {
            return (t || '').toString().toLowerCase()
                    .replace(/[áàä]/g, 'a').replace(/[éèë]/g, 'e').replace(/[íìï]/g, 'i')
                    .replace(/[óòö]/g, 'o').replace(/[úùü]/g, 'u').replace(/ñ/g, 'n');
        }
        function filtrar() {
            var palabras = normal($('#of-q').val()).split(/\s+/).filter(Boolean);
            var visibles = 0;
            $('#of-filas tr').each(function () {
                var $f = $(this);
                var ok = (estado === 'todas' || String($f.data('estado')) === estado);
                if (ok && palabras.length) {
                    var texto = normal($f.data('buscar'));
                    ok = palabras.every(function (w) {
                        return texto.indexOf(w) > -1;
                    });
                }
                $f.toggle(ok);
                visibles += ok ? 1 : 0;
            });
            $('#of-vacio').toggle(visibles === 0);
        }
        var t = null;
        $('#of-q').on('input search', function () {
            clearTimeout(t);
            t = setTimeout(filtrar, 150);
        });
        $('.ad-pes').on('click', function () {
            $('.ad-pes').removeClass('activa');
            $(this).addClass('activa');
            estado = String($(this).data('estado'));
            filtrar();
        });

        $('.btn-estado-ofi').on('click', function (e) {
            e.preventDefault();
            var $b = $(this), nombre = String($b.data('nombre'));
            if (parseInt($b.data('activa'), 10) === 1) {
                var usuarios = parseInt($b.data('usuarios'), 10) || 0;
                if (usuarios > 0) {
                    alert('No se puede desactivar "' + nombre + '": todavía tiene ' + usuarios + ' usuario(s) activo(s).\n\nTrasládelos a otra oficina o deles de baja primero.');
                    return;
                }
                if (!confirm('¿Desactivar la oficina "' + nombre + '"?\n\nDejará de aparecer al crear usuarios y al elegir destinatarios. No se borra nada y puede volver a activarla.')) {
                    return;
                }
            } else if (!confirm('¿Activar la oficina "' + nombre + '"?')) {
                return;
            }
            $b.closest('form')[0].submit();
        });
    });
</script>
