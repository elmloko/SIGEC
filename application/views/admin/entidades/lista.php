<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$total = 0;
foreach ($entidades as $e) {
    $total++;
}
?>
<style>
    .ad-lista {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ad-lista .btn {
        margin: 0;
    }
    /* cabecera */
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
        flex: 1 1 260px;
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
    /* avisos */
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
    /* tarjetas de entidad */
    .ad-ents {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 12px;
        padding: 0 22px 22px;
    }
    .ad-ent {
        display: flex;
        flex-direction: column;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .ad-ent-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 88px;
        padding: 10px;
        background: #F7F9FC;
        border-bottom: 1px solid #EEF1F5;
    }
    .ad-ent-logo img {
        max-height: 68px;
        max-width: 100%;
        object-fit: contain;
    }
    .ad-ent-cuerpo {
        flex: 1 1 auto;
        padding: 12px 14px;
    }
    .ad-ent-sigla {
        display: inline-block;
        padding: 2px 9px;
        margin-bottom: 6px;
        border-radius: 6px;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .03em;
    }
    .ad-ent-baja {
        background: #FDECEC;
        color: #B42318;
    }
    .ad-ent-cuerpo h3 {
        margin: 0 0 8px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
        color: #1f2937;
    }
    .ad-ent-datos {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .ad-dato {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 9px;
        border-radius: 20px;
        background: #F1F4F8;
        color: #4a5566;
        font-size: 11.5px;
        text-decoration: none;
    }
    .ad-dato b {
        color: #123E73;
    }
    a.ad-dato:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .ad-ent-pie {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 14px;
        border-top: 1px solid #EEF1F5;
        background: #FCFDFE;
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
    .ad-acc.borrar {
        margin-left: auto;
        border-color: #E8B4B0;
        color: #B42318;
    }
    .ad-acc.borrar:hover {
        background: #FDECEC;
        border-color: #D98A84;
    }
    .ad-acc[disabled] {
        opacity: .5;
        cursor: not-allowed;
    }
    .ad-ent-pie form {
        margin: 0 0 0 auto;
    }
</style>

<div class="ad-lista">
    <div class="ad-l-cab">
        <span class="ad-l-icono"><i class="fa fa-building-o"></i></span>
        <div class="ad-l-texto">
            <h2>Entidades</h2>
            <p><?php echo $total; ?> entidad<?php echo $total == 1 ? '' : 'es'; ?> registrada<?php echo $total == 1 ? '' : 's'; ?>. De cada una cuelgan sus oficinas, sus usuarios y sus documentos.</p>
        </div>
        <a href="/admin/entidades/nuevo" class="ad-l-nuevo"><i class="fa fa-plus"></i> Nueva entidad</a>
    </div>

    <?php if (!empty($mensaje)): ?>
        <div class="ad-l-msg ok"><i class="fa fa-check-circle"></i> <span><?php echo $h($mensaje); ?></span></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="ad-l-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><?php echo $h($error); ?></span></div>
    <?php endif; ?>

    <div class="ad-ents">
        <?php foreach ($entidades as $e):
            $u = isset($uso[$e->id]) ? $uso[$e->id] : array('oficinas' => 0, 'usuarios' => 0, 'activos' => 0, 'documentos' => 0);
            $borrable = ((int) $u['oficinas'] + (int) $u['usuarios'] + (int) $u['documentos']) === 0;
            $logo = 'static/logos/' . $e->logo;
            if (!$e->logo OR !file_exists(DOCROOT . $logo)) {
                $logo = 'static/logos/entidad.jpg';
            }
            ?>
            <div class="ad-ent">
                <div class="ad-ent-logo">
                    <img src="/<?php echo $h($logo); ?>?t=<?php echo time(); ?>" alt="<?php echo $h($e->sigla); ?>">
                </div>
                <div class="ad-ent-cuerpo">
                    <span class="ad-ent-sigla"><?php echo $h($e->sigla); ?></span>
                    <?php if ((int) $e->estado !== 1): ?>
                        <span class="ad-ent-sigla ad-ent-baja">Inactiva</span>
                    <?php endif; ?>
                    <h3><?php echo $h($e->entidad); ?></h3>
                    <div class="ad-ent-datos">
                        <a href="/admin/oficinas/lista/<?php echo (int) $e->id; ?>" class="ad-dato"><i class="fa fa-sitemap"></i> <b><?php echo $n($u['oficinas']); ?></b> oficinas</a>
                        <span class="ad-dato"><i class="fa fa-users"></i> <b><?php echo $n($u['activos']); ?></b> usuarios activos</span>
                        <span class="ad-dato"><i class="fa fa-file-text-o"></i> <b><?php echo $n($u['documentos']); ?></b> documentos</span>
                    </div>
                </div>
                <div class="ad-ent-pie">
                    <a href="/admin/entidades/edit/<?php echo (int) $e->id; ?>" class="ad-acc"><i class="fa fa-pencil"></i> Editar</a>
                    <?php if ($borrable): ?>
                        <form method="post" action="/admin/entidades/eliminar/<?php echo (int) $e->id; ?>">
                            <button type="submit" class="ad-acc borrar btn-borrar-ent" data-sigla="<?php echo $h($e->sigla); ?>"><i class="fa fa-trash-o"></i> Eliminar</button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="ad-acc borrar" disabled
                                title="No se puede eliminar: tiene oficinas, usuarios o documentos asociados."><i class="fa fa-trash-o"></i> Eliminar</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    $(function () {
        $('.btn-borrar-ent').on('click', function (e) {
            e.preventDefault();
            var sigla = String($(this).data('sigla'));
            if (!confirm('¿Eliminar la entidad ' + sigla + '?\n\nNo tiene oficinas, usuarios ni documentos, pero la acción no se puede deshacer.')) {
                return;
            }
            $(this).closest('form')[0].submit();
        });
    });
</script>
