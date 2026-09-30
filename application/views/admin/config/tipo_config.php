<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
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
        margin: 0 0 4px;
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
    .fo-intro {
        margin: 0 0 14px 38px;
        font-size: 12px;
        color: #8a94a3;
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
    .fo-campo input.mono {
        font-family: Consolas, "Courier New", monospace;
        font-size: 13px;
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
        margin: 0 0 14px;
        padding: 11px 13px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        cursor: pointer;
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
    .fo-vista {
        margin-top: 6px;
        padding: 9px 12px;
        border-radius: 9px;
        background: #F7F9FC;
        border: 1px dashed #D5DCE6;
        font-family: Consolas, "Courier New", monospace;
        font-size: 12.5px;
        color: #123E73;
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
    <span class="fo-cab-icono"><i class="fa fa-file-text-o"></i></span>
    <div class="fo-cab-texto">
        <h2><?php echo $nuevo ? 'Nuevo tipo de documento' : $h($datos['tipo']); ?></h2>
        <p><?php echo $nuevo ? 'Define un documento que la gente podrá generar desde «Nuevo documento».' : 'Editando el tipo de documento'; ?></p>
    </div>
    <a href="/admin/tipos" class="fo-btn"><i class="fa fa-arrow-left"></i> Volver al listado</a>
</div>

<?php if (!empty($error)): ?>
    <div class="fo-msg mal"><i class="fa fa-exclamation-triangle"></i> <span><b>No se guardó.</b> <?php echo $error; ?></span></div>
<?php endif; ?>

<form method="post" action="" class="fo-card" id="fo-form" autocomplete="off">
    <div class="fo-seccion">
        <h3><i class="fa fa-tag"></i> Nombre</h3>
        <p class="fo-intro">Es lo que ve la gente en el menú «Nuevo documento».</p>
        <?php if (!$nuevo): ?>
            <div class="fo-datos">
                <span class="fo-dato"><i class="fa fa-file-text-o"></i> <b><?php echo $n($uso['documentos']); ?></b> documentos generados</span>
                <span class="fo-dato"><i class="fa fa-users"></i> <b><?php echo $n($uso['usuarios']); ?></b> personas lo pueden usar</span>
            </div>
        <?php endif; ?>
        <div class="fo-campos">
            <div class="fo-campo">
                <label for="fo-tipo">Nombre (singular)</label>
                <input type="text" name="tipo" id="fo-tipo" value="<?php echo $h($datos['tipo']); ?>" placeholder="Informe" required>
            </div>
            <div class="fo-campo">
                <label for="fo-plural">Nombre (plural)</label>
                <input type="text" name="plural" id="fo-plural" value="<?php echo $h($datos['plural']); ?>" placeholder="Informes" required>
            </div>
            <div class="fo-campo">
                <label for="fo-abrev">Abreviatura</label>
                <input type="text" name="abreviatura" id="fo-abrev" value="<?php echo $h($datos['abreviatura']); ?>" placeholder="INF" style="text-transform:uppercase">
                <div class="fo-ayuda">Encabeza el cite: <b>INF</b>/AGBC/…</div>
            </div>
            <div class="fo-campo">
                <label for="fo-action">Identificador interno</label>
                <input type="text" name="action" id="fo-action" class="mono" value="<?php echo $h($datos['action']); ?>" placeholder="informe" required>
                <div class="fo-ayuda">Va en la dirección web: /documento/generar/<b id="fo-eco"><?php echo $h($datos['action']); ?></b>. Sin espacios ni tildes.</div>
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-sort-numeric-asc"></i> Numeración del cite</h3>
        <p class="fo-intro">Cómo se arma el número con que se identifica cada documento.</p>
        <div class="fo-campos">
            <div class="fo-campo ancho">
                <label for="fo-cite-tipo">Plantilla del cite</label>
                <input type="text" name="cite_tipo" id="fo-cite-tipo" class="mono" value="<?php echo $h($datos['cite_tipo']); ?>">
                <div class="fo-ayuda">Piezas disponibles: <code>{$tip}</code> abreviatura · <code>{$ent}</code> entidad · <code>{$ofi}</code> oficina · <code>{$cor}</code> correlativo · <code>{$anio}</code> año · <code>{$mosca}</code> rúbrica del autor.</div>
            </div>
            <div class="fo-campo ancho">
                <label for="fo-desc">Cómo se explica al usuario</label>
                <input type="text" name="descripcion" id="fo-desc" value="<?php echo $h($datos['descripcion']); ?>" placeholder="INF/ENTIDAD/OFICINA N° Nro/Año">
                <div class="fo-ayuda">Texto de ejemplo que se muestra en el listado de tipos.</div>
            </div>
            <div class="fo-campo ancho">
                <label class="fo-check" style="margin:0">
                    <input type="checkbox" name="cite_propio" value="1" <?php echo (int) $datos['cite_propio'] === 1 ? 'checked' : ''; ?>>
                    <span>
                        <b>Usa un cite propio</b>
                        <small>Para documentos que no siguen el correlativo de la oficina y llevan su propia numeración.</small>
                    </span>
                </label>
            </div>
            <div class="fo-campo ancho">
                <label for="fo-cite">Cite propio</label>
                <input type="text" name="cite" id="fo-cite" class="mono" value="<?php echo $h($datos['cite']); ?>">
            </div>
        </div>
    </div>

    <div class="fo-seccion">
        <h3><i class="fa fa-cogs"></i> Comportamiento</h3>
        <label class="fo-check">
            <input type="checkbox" name="via" value="1" <?php echo (int) $datos['via'] === 1 ? 'checked' : ''; ?>>
            <span>
                <b>Permite indicar «vías»</b>
                <small>Añade el campo VÍA al redactarlo, para los documentos que pasan por un superior antes de llegar al destinatario.</small>
            </span>
        </label>
        <?php if (!$nuevo AND (int) $datos['activo'] === 1 AND (int) $uso['usuarios'] > 0): ?>
            <div class="fo-aviso">
                <i class="fa fa-exclamation-triangle"></i>
                <span>No podrá desactivarlo mientras <b><?php echo $n($uso['usuarios']); ?></b> persona<?php echo (int) $uso['usuarios'] == 1 ? '' : 's'; ?> tenga<?php echo (int) $uso['usuarios'] == 1 ? '' : 'n'; ?> permiso para generarlo.</span>
            </div>
        <?php endif; ?>
        <label class="fo-check">
            <input type="checkbox" name="activo" value="1" <?php echo (int) $datos['activo'] === 1 ? 'checked' : ''; ?>>
            <span>
                <b>Tipo activo</b>
                <small>Si lo desactiva, deja de poder asignarse y de aparecer al generar documentos. Los ya generados no se tocan.</small>
            </span>
        </label>
        <div class="fo-campos">
            <div class="fo-campo">
                <label for="fo-tpl">Plantilla</label>
                <input type="text" name="template" id="fo-tpl" class="mono" value="<?php echo $h($datos['template']); ?>">
                <div class="fo-ayuda">Archivo que arma el documento. No lo cambie sin saber cuál existe.</div>
            </div>
            <div class="fo-campo">
                <label for="fo-tplvia">Plantilla con vía</label>
                <input type="text" name="template_via" id="fo-tplvia" class="mono" value="<?php echo $h($datos['template_via']); ?>">
            </div>
        </div>
    </div>

    <div class="fo-barra">
        <span class="fo-estado" id="fo-estado">Sin cambios</span>
        <a href="/admin/tipos" class="btn btn-default-bright">Cancelar</a>
        <button type="submit" name="submit" value="1" class="btn btn-primary" id="fo-guardar">
            <i class="fa fa-check"></i> <?php echo $nuevo ? 'Crear tipo de documento' : 'Guardar cambios'; ?>
        </button>
    </div>
</form>

<script>
    $(function () {
        $('#fo-action').on('input', function () {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '');
            $('#fo-eco').text(this.value || '…');
        });
        var $form = $('#fo-form'), original = $form.serialize(), enviando = false;
        function revisar() {
            var cambio = $form.serialize() !== original;
            $('#fo-estado').toggleClass('cambios', cambio).text(cambio ? 'Hay cambios sin guardar' : 'Sin cambios');
        }
        $form.on('input change', revisar);
        $form.on('submit', function () {
            enviando = true;
            $('#fo-guardar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
        });
        $(window).on('beforeunload', function () {
            if (!enviando && $form.serialize() !== original) {
                return 'Hay cambios sin guardar.';
            }
        });
        revisar();
    });
</script>
