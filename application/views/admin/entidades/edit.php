<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$logo = 'static/logos/' . $e->logo;
if (!$e->logo OR !file_exists(DOCROOT . $logo)) {
    $logo = 'static/logos/entidad.jpg';
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
    .fo-cab-logo {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 88px;
        height: 52px;
        padding: 4px;
        border: 1px solid #E3E8EF;
        border-radius: 10px;
        background: #F7F9FC;
    }
    .fo-cab-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
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
    .fo-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
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
    .fo-msg.ok {
        background: #E6F6EC;
        color: #1E7B45;
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
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
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
    .fo-campo input[type=text] {
        width: 100%;
        height: 38px;
        padding: 6px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13.5px;
        color: #1f2937;
        outline: none;
    }
    .fo-campo input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
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
    }
    .fo-dato b {
        color: #123E73;
    }
    .fo-intro {
        margin: 0 0 14px 38px;
        font-size: 12px;
        color: #8a94a3;
    }
    /* logo */
    .fo-logo {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    .fo-logo-caja {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 190px;
        height: 96px;
        padding: 8px;
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        background: #F7F9FC;
    }
    .fo-logo-caja img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .fo-logo-lado {
        flex: 1 1 240px;
    }
    .fo-logo-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 6px;
    }
    .fo-logo-botones label {
        margin: 0;
        cursor: pointer;
    }
    .fo-logo-botones .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        font-weight: 600;
    }
    .fo-logo-botones .btn-primary[disabled] {
        opacity: .45;
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
    <span class="fo-cab-logo"><img src="/<?php echo $h($logo); ?>?t=<?php echo time(); ?>" alt=""></span>
    <div class="fo-cab-texto">
        <h2><?php echo $h($e->sigla); ?></h2>
        <p><?php echo $h($e->entidad); ?></p>
    </div>
    <div class="fo-acciones">
        <a href="/admin/oficinas/lista/<?php echo (int) $e->id; ?>" class="fo-btn"><i class="fa fa-sitemap"></i> Sus oficinas</a>
        <a href="/admin/entidades" class="fo-btn"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="fo-msg ok"><i class="fa fa-check-circle"></i> <span><?php echo $mensaje; ?></span></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="fo-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><b>No se guardó.</b> <?php echo $errors; ?></span></div>
<?php endif; ?>

<form method="post" action="" class="fo-card" id="fo-form" autocomplete="off" enctype="multipart/form-data">
    <div class="fo-seccion">
        <h3><i class="fa fa-building-o"></i> Identificación</h3>
        <div class="fo-datos">
            <span class="fo-dato"><i class="fa fa-sitemap"></i> <b><?php echo $n($uso['oficinas']); ?></b> oficinas</span>
            <span class="fo-dato"><i class="fa fa-users"></i> <b><?php echo $n($uso['usuarios']); ?></b> usuarios activos</span>
            <span class="fo-dato"><i class="fa fa-file-text-o"></i> <b><?php echo $n($uso['documentos']); ?></b> documentos</span>
        </div>
        <div class="fo-campos">
            <div class="fo-campo ancho">
                <label for="fo-entidad">Nombre de la entidad</label>
                <input type="text" name="entidad" id="fo-entidad" value="<?php echo $h($datos['entidad']); ?>" required>
            </div>
            <div class="fo-campo">
                <label for="fo-sigla">Sigla</label>
                <input type="text" name="sigla" id="fo-sigla" value="<?php echo $h($datos['sigla']); ?>" style="text-transform:uppercase" required>
                <div class="fo-ayuda">Encabeza el cite de todos sus documentos.</div>
            </div>
            <div class="fo-campo">
                <label for="fo-sigla2">Sigla abreviada</label>
                <input type="text" name="sigla2" id="fo-sigla2" value="<?php echo $h($datos['sigla2']); ?>" maxlength="3" style="text-transform:uppercase">
                <div class="fo-ayuda">Hasta 3 letras. Se usa en el número de hoja de ruta.</div>
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-picture-o"></i> Logo</h3>
        <p class="fo-intro">Aparece en la cabecera de los documentos que genera la entidad.</p>
        <div class="fo-logo">
            <span class="fo-logo-caja"><img src="/<?php echo $h($logo); ?>?t=<?php echo time(); ?>" alt="" id="fo-vista-logo"></span>
            <div class="fo-logo-lado">
                <div class="fo-logo-botones">
                    <label class="fo-btn" for="fo-archivo"><i class="fa fa-upload"></i> Elegir imagen…</label>
                    <input type="file" name="logo" id="fo-archivo" accept="image/jpeg,image/png,image/gif" style="display:none">
                    <button type="submit" name="subir_logo" value="1" class="btn btn-primary" id="fo-btn-logo" disabled>
                        <i class="fa fa-check"></i> Cambiar logo
                    </button>
                </div>
                <div class="fo-ayuda" id="fo-archivo-nombre">JPG, PNG o GIF, hasta 2 MB. Se reduce solo si es muy grande.</div>
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-map-marker"></i> Datos de contacto <small style="font-weight:400;color:#8a94a3;font-size:12px">— se imprimen en los documentos</small></h3>
        <div class="fo-campos">
            <div class="fo-campo ancho">
                <label for="fo-dir">Dirección</label>
                <input type="text" name="direccion" id="fo-dir" value="<?php echo $h($datos['direccion']); ?>">
            </div>
            <div class="fo-campo">
                <label for="fo-tel">Teléfonos</label>
                <input type="text" name="telefono" id="fo-tel" value="<?php echo $h($datos['telefono']); ?>">
            </div>
            <div class="fo-campo">
                <label for="fo-pie1">Pie de página 1</label>
                <input type="text" name="pie_1" id="fo-pie1" value="<?php echo $h($datos['pie_1']); ?>">
            </div>
            <div class="fo-campo">
                <label for="fo-pie2">Pie de página 2</label>
                <input type="text" name="pie_2" id="fo-pie2" value="<?php echo $h($datos['pie_2']); ?>">
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-toggle-on"></i> Estado</h3>
        <label class="fo-check">
            <input type="checkbox" name="estado" value="1" <?php echo (int) $datos['estado'] === 1 ? 'checked' : ''; ?>>
            <span>
                <b>Entidad activa</b>
                <small>Si la desactiva, deja de ofrecerse al crear oficinas y usuarios. No se borra nada.</small>
            </span>
        </label>
        <div style="height:14px"></div>
    </div>

    <div class="fo-barra">
        <span class="fo-estado" id="fo-estado">Sin cambios</span>
        <a href="/admin/entidades" class="btn btn-default-bright">Cancelar</a>
        <button type="submit" name="guardar" value="1" class="btn btn-primary" id="fo-guardar"><i class="fa fa-check"></i> Guardar cambios</button>
    </div>
</form>

<script>
    $(function () {
        var $form = $('#fo-form'), original = $form.serialize(), enviando = false;
        function revisar() {
            var cambio = $form.serialize() !== original;
            $('#fo-estado').toggleClass('cambios', cambio).text(cambio ? 'Hay cambios sin guardar' : 'Sin cambios');
        }
        $form.on('input change', revisar);

        // logo: vista previa antes de subirlo
        $('#fo-archivo').on('change', function () {
            var f = this.files && this.files[0];
            $('#fo-btn-logo').prop('disabled', !f);
            if (!f) {
                $('#fo-archivo-nombre').text('JPG, PNG o GIF, hasta 2 MB. Se reduce solo si es muy grande.');
                return;
            }
            $('#fo-archivo-nombre').text(f.name + ' · ' + Math.round(f.size / 1024) + ' KB');
            if (window.FileReader) {
                var lector = new FileReader();
                lector.onload = function (ev) {
                    $('#fo-vista-logo').attr('src', ev.target.result);
                };
                lector.readAsDataURL(f);
            }
        });
        $('#fo-btn-logo').on('click', function () {
            enviando = true;
            var $b = $(this);
            setTimeout(function () {
                $b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Subiendo…');
            }, 0);
        });
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
