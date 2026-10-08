<?php
$h = function ($s) {
    return HTML::chars($s);
};
// quitamos la propia oficina de la lista de padres posibles
$padres = array();
foreach ($options as $k => $v) {
    if ((int) $k !== (int) $oficina->id) {
        $padres[$k] = $v;
    }
}
?>
<style>
    .fo-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .fo-card .btn {
        margin: 0;
    }
    .fo-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 16px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .fo-cab-icono {
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
    .fo-cab-texto {
        flex: 1 1 240px;
        min-width: 0;
    }
    .fo-cab h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 600;
        color: #123E73;
    }
    .fo-cab p {
        margin: 3px 0 0;
        font-size: 12.5px;
        color: #6b7686;
    }
    .fo-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        padding: 0 13px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
    }
    .fo-btn:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    .fo-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
    }
    .fo-msg.mal {
        background: #FDECEC;
        color: #912018;
    }
    .fo-seccion {
        padding: 18px 22px 6px;
        border-bottom: 1px solid #EEF1F5;
    }
    .fo-seccion:last-of-type {
        border-bottom: 0;
    }
    .fo-seccion h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: #123E73;
    }
    .fo-seccion h3 .fa {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 13px;
    }
    .fo-campos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2px 16px;
    }
    .fo-campo {
        margin-bottom: 14px;
    }
    .fo-campo.ancho {
        grid-column: 1 / -1;
    }
    .fo-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5566;
    }
    .fo-campo input[type=text],
    .fo-campo select {
        width: 100%;
        height: 38px;
        padding: 6px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13.5px;
        color: #1f2937;
        outline: none;
    }
    .fo-campo input:focus,
    .fo-campo select:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .fo-campo input[disabled],
    .fo-campo select[disabled] {
        background: #F3F5F8;
        color: #6b7686;
    }
    .fo-ayuda {
        margin-top: 4px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .fo-check {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 11px 13px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        cursor: pointer;
        margin: 0;
        font-weight: 400;
    }
    .fo-check input {
        margin: 2px 0 0;
    }
    .fo-check b {
        display: block;
        font-size: 13px;
        color: #1f2937;
    }
    .fo-check small {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .fo-datos {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 14px;
    }
    .fo-dato {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 20px;
        background: #F1F4F8;
        color: #4a5566;
        font-size: 12px;
        text-decoration: none;
    }
    .fo-dato b {
        color: #123E73;
    }
    a.fo-dato:hover {
        background: #EEF3FA;
        text-decoration: none;
    }
    .fo-aviso {
        display: flex;
        gap: 9px;
        align-items: flex-start;
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
    }
    .fo-barra {
        position: sticky;
        bottom: 0;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-top: 1px solid #EEF1F5;
        border-radius: 0 0 14px 14px;
        background: rgba(255, 255, 255, .97);
    }
    .fo-estado {
        flex: 1 1 200px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .fo-estado.cambios {
        color: #B7791F;
        font-weight: 600;
    }
    .fo-barra .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 8px 20px;
        font-weight: 700;
    }
</style>

<div class="fo-card fo-cab">
    <span class="fo-cab-icono"><i class="fa fa-sitemap"></i></span>
    <div class="fo-cab-texto">
        <h2><?php echo $h($nombre_oficina); ?></h2>
        <p><?php echo $h($sigla); ?><?php echo isset($entidades[$id_entidad]) ? ' · ' . $h($entidades[$id_entidad]) : ''; ?></p>
    </div>
    <div style="display:flex;gap:7px;flex-wrap:wrap">
        <a href="/admin/user/lista/<?php echo (int) $oficina->id; ?>" class="fo-btn"><i class="fa fa-users"></i> Su personal</a>
        <a href="/admin/oficinas/lista/<?php echo (int) $id_entidad; ?>" class="fo-btn"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>
</div>

<?php if (sizeof($error) > 0): ?>
    <div class="fo-msg mal">
        <i class="fa fa-exclamation-triangle"></i>
        <span><b>No se guardó.</b> <?php foreach ($error as $v) {
                echo $v;
            } ?></span>
    </div>
<?php endif; ?>

<form action="" method="post" class="fo-card" id="fo-form" autocomplete="off">
    <div class="fo-seccion">
        <h3><i class="fa fa-info-circle"></i> Datos de la oficina</h3>
        <div class="fo-datos">
            <?php if ((int) $usuarios > 0): ?>
                <a href="/admin/user/lista/<?php echo (int) $oficina->id; ?>" class="fo-dato"><i class="fa fa-users"></i> <b><?php echo (int) $usuarios; ?></b> <?php echo (int) $usuarios == 1 ? 'persona' : 'personas'; ?></a>
            <?php else: ?>
                <span class="fo-dato"><i class="fa fa-users"></i> sin personal</span>
            <?php endif; ?>
            <?php if ((int) $hijas > 0): ?>
                <span class="fo-dato"><i class="fa fa-sitemap"></i> <b><?php echo (int) $hijas; ?></b> oficina<?php echo (int) $hijas == 1 ? '' : 's'; ?> dependiente<?php echo (int) $hijas == 1 ? '' : 's'; ?></span>
            <?php endif; ?>
        </div>
        <div class="fo-campos">
            <div class="fo-campo ancho">
                <label for="fo-oficina">Nombre de la oficina</label>
                <input type="text" name="oficina" id="fo-oficina" value="<?php echo $h($nombre_oficina); ?>" required>
            </div>
            <div class="fo-campo">
                <label for="fo-sigla">Sigla</label>
                <input type="text" name="sigla" id="fo-sigla" value="<?php echo $h($sigla); ?>" style="text-transform:uppercase" required>
                <div class="fo-ayuda">Aparece en el cite de sus documentos. No puede repetirse.</div>
            </div>
            <div class="fo-campo">
                <label for="fo-entidad">Entidad</label>
                <input type="text" id="fo-entidad" value="<?php echo isset($entidades[$id_entidad]) ? $h($entidades[$id_entidad]) : ''; ?>" disabled>
                <div class="fo-ayuda">La entidad no se cambia desde aquí.</div>
            </div>
            <div class="fo-campo ancho">
                <label for="fo-padre">Depende de</label>
                <?php echo Form::select('padre', $padres, $id_padre_oficina, array('id' => 'fo-padre')); ?>
                <div class="fo-ayuda">Define el organigrama. «Oficina Inicial» si no depende de ninguna.</div>
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-toggle-on"></i> Estado</h3>
        <?php if ((int) $estado === 1 AND (int) $usuarios > 0): ?>
            <div class="fo-aviso">
                <i class="fa fa-exclamation-triangle"></i>
                <span>No podrá desactivarla mientras tenga <b><?php echo (int) $usuarios; ?></b> usuario<?php echo (int) $usuarios == 1 ? '' : 's'; ?> activo<?php echo (int) $usuarios == 1 ? '' : 's'; ?>: se quedaría<?php echo (int) $usuarios == 1 ? '' : 'n'; ?> sin bandeja. Traslade o dé de baja a esas personas primero.</span>
            </div>
        <?php endif; ?>
        <label class="fo-check">
            <input type="checkbox" name="estado" value="1" <?php echo (int) $estado === 1 ? 'checked' : ''; ?>>
            <span>
                <b>Oficina activa</b>
                <small>Si la desactiva, deja de ofrecerse al crear usuarios y al elegir destinatarios. No se borra nada.</small>
            </span>
        </label>
        <div style="height:14px"></div>
    </div>

    <div class="fo-barra">
        <span class="fo-estado" id="fo-estado">Sin cambios</span>
        <a href="/admin/oficinas/lista/<?php echo (int) $id_entidad; ?>" class="btn btn-default-bright">Cancelar</a>
        <button type="submit" name="edit" value="1" class="btn btn-primary" id="fo-guardar"><i class="fa fa-check"></i> Guardar cambios</button>
    </div>
</form>

<script>
    $(function () {
        $('#fo-padre').select2({width: '100%'});
        var $form = $('#fo-form'), original = $form.serialize(), enviando = false;
        function revisar() {
            var cambio = $form.serialize() !== original;
            $('#fo-estado').toggleClass('cambios', cambio).text(cambio ? 'Hay cambios sin guardar' : 'Sin cambios');
        }
        $form.on('input change', revisar);
        $form.on('submit', function () {
            enviando = true;
            // se deshabilita despues de armar el POST: un boton deshabilitado no viaja
            setTimeout(function () {
                $('#fo-guardar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
            }, 0);
        });
        $(window).on('beforeunload', function () {
            if (!enviando && $form.serialize() !== original) {
                return 'Hay cambios sin guardar.';
            }
        });
        revisar();
    });
</script>
