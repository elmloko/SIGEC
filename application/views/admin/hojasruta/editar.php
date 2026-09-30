<?php
$h = function ($s) {
    return HTML::chars($s);
};
$tiene_hr = trim($documento->nur) !== '';
?>
<style>
    .he-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .he-card .btn {
        margin: 0;
    }
    /* cabecera */
    .he-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 16px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .he-cab-icono {
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
    .he-cab-texto {
        flex: 1 1 260px;
        min-width: 0;
    }
    .he-cab h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 600;
        color: #123E73;
    }
    .he-cab p {
        margin: 3px 0 0;
        font-size: 12.5px;
        color: #6b7686;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .he-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }
    .he-btn {
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
    .he-btn:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    /* avisos */
    .he-msg {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 16px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
    }
    .he-msg.ok {
        background: #E6F6EC;
        color: #1E7B45;
    }
    .he-msg.mal {
        background: #FDECEC;
        color: #912018;
    }
    .he-msg b {
        color: inherit;
    }
    .he-msg .fa {
        margin-top: 2px;
    }
    /* secciones del formulario */
    .he-seccion {
        padding: 18px 22px 6px;
        border-bottom: 1px solid #EEF1F5;
    }
    .he-seccion:last-of-type {
        border-bottom: 0;
    }
    .he-seccion h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: #123E73;
    }
    .he-seccion h3 .fa {
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
    .he-campos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2px 16px;
    }
    .he-campo {
        margin-bottom: 14px;
    }
    .he-campo.ancho {
        grid-column: 1 / -1;
    }
    .he-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5566;
    }
    .he-campo input {
        width: 100%;
        height: 38px;
        padding: 6px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13.5px;
        color: #1f2937;
        outline: none;
        box-shadow: none;
    }
    .he-campo input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .he-campo input.mono {
        font-family: Consolas, "Courier New", monospace;
        font-size: 13px;
    }
    .he-ayuda {
        margin-top: 4px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    /* aviso del cambio de numero */
    .he-peligro {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 2px 0 14px;
        padding: 11px 13px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
        grid-column: 1 / -1;
    }
    .he-peligro .fa {
        margin-top: 2px;
    }
    .he-peligro b {
        color: #7A5A00;
    }
    /* archivos digitales */
    .he-archivos {
        display: grid;
        gap: 8px;
        margin-bottom: 14px;
    }
    .he-archivo {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 11px;
        padding: 10px 13px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        background: #fff;
    }
    .he-a-icono {
        flex: 0 0 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF2F7;
        color: #4A5568;
        font-size: 16px;
    }
    .he-a-icono.pdf {
        background: #FDE8E8;
        color: #B42318;
    }
    .he-a-texto {
        flex: 1 1 220px;
        min-width: 0;
    }
    .he-a-texto b {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .he-a-texto small {
        display: block;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .he-a-botones {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .he-btn.quitar {
        border-color: #E8B4B0;
        color: #B42318;
    }
    .he-btn.quitar:hover {
        background: #FDECEC;
        border-color: #D98A84;
        color: #912018;
    }
    .he-vacio {
        margin: 0 0 14px;
        padding: 20px 14px;
        border: 1px dashed #D5DCE6;
        border-radius: 11px;
        text-align: center;
        font-size: 13px;
        color: #8a94a3;
    }
    .he-vacio .fa {
        display: block;
        margin-bottom: 6px;
        font-size: 22px;
        color: #C4CEDB;
    }
    .he-subir {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 9px;
        padding-top: 12px;
        border-top: 1px dashed #E3E8EF;
    }
    .he-subir label {
        margin: 0;
        cursor: pointer;
    }
    .he-elegido {
        flex: 1 1 160px;
        font-size: 12.5px;
        color: #8a94a3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .he-subir .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        font-weight: 600;
    }
    .he-subir .btn-primary[disabled] {
        opacity: .45;
    }
    /* acciones sobre el documento */
    .he-tarjetas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(235px, 1fr));
        gap: 10px;
    }
    .he-tarjeta {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        background: #fff;
        text-align: left;
        text-decoration: none;
        transition: border-color .12s, background .12s, box-shadow .12s;
    }
    .he-tarjeta:hover {
        border-color: #B9C8DC;
        background: #F7FAFD;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(18, 62, 115, .1);
    }
    .he-t-icono {
        flex: 0 0 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    .he-t-icono.ver {
        background: #EEF3FA;
        color: #1A549A;
    }
    .he-t-icono.seg {
        background: #E6F4EC;
        color: #227547;
    }
    .he-t-icono.editar {
        background: #FFF7DD;
        color: #B7791F;
    }
    .he-t-icono.borrar {
        background: #FDE8E8;
        color: #B42318;
    }
    .he-t-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .he-t-texto b {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .he-t-texto small {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        line-height: 1.35;
        color: #8a94a3;
    }
    .he-tarjeta.peligro:hover {
        border-color: #E8B4B0;
        background: #FEF6F6;
    }
    .he-tarjeta.peligro:hover .he-t-texto b {
        color: #B42318;
    }
    /* barra de guardar */
    .he-barra {
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
    .he-estado {
        flex: 1 1 200px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .he-estado.cambios {
        color: #B7791F;
        font-weight: 600;
    }
    .he-barra .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 8px 20px;
        font-weight: 700;
    }
</style>

<div class="he-card he-cab">
    <span class="he-cab-icono"><i class="fa fa-pencil"></i></span>
    <div class="he-cab-texto">
        <h2><?php echo $tiene_hr ? 'Hoja de ruta ' . $h($documento->nur) : 'Documento sin hoja de ruta'; ?></h2>
        <p title="<?php echo $h($documento->referencia); ?>"><?php echo $h($documento->cite_original != '' ? $documento->cite_original : $documento->codigo); ?> · <?php echo $h($documento->referencia); ?></p>
    </div>
    <div class="he-acciones">
        <a href="/document/detalle/<?php echo (int) $documento->id; ?>" class="he-btn" target="_blank"><i class="fa fa-file-text-o"></i> Ver documento</a>
        <?php if ($tiene_hr): ?>
            <a href="/route/trace/?hr=<?php echo urlencode($documento->nur); ?>" class="he-btn" target="_blank"><i class="md md-verified-user"></i> Ver seguimiento</a>
        <?php endif; ?>
        <a href="/admin/hojasruta/lista" class="he-btn"><i class="fa fa-arrow-left"></i> Volver al listado</a>
    </div>
</div>

<?php if (!empty($aviso_error)): ?>
    <div class="he-msg mal">
        <i class="fa fa-exclamation-triangle"></i>
        <span><?php echo $h($aviso_error); ?></span>
    </div>
<?php endif; ?>
<?php if (sizeof($error) > 0): ?>
    <div class="he-msg mal">
        <i class="fa fa-exclamation-triangle"></i>
        <span><b>No se guardó.</b>
            <?php foreach ($error as $v): ?>
                <?php echo $v; ?>
            <?php endforeach; ?>
        </span>
    </div>
<?php endif; ?>
<?php if (sizeof($info) > 0): ?>
    <div class="he-msg ok">
        <i class="fa fa-check-circle"></i>
        <span><?php foreach ($info as $v) {
                echo $v;
            } ?></span>
    </div>
<?php endif; ?>

<form action="" method="post" id="he-form" class="he-card" autocomplete="off">
    <div class="he-seccion">
        <h3><i class="fa fa-tag"></i> Identificación</h3>
        <div class="he-campos">
            <div class="he-campo">
                <label for="he-nur">Número de hoja de ruta</label>
                <input type="text" name="nur" id="he-nur" class="mono" value="<?php echo $h($documento->nur); ?>">
            </div>
            <div class="he-campo">
                <label for="he-codigo">Código del documento</label>
                <input type="text" name="codigo" id="he-codigo" value="<?php echo $h($documento->codigo); ?>">
                <div class="he-ayuda">Identificador interno: no se puede repetir.</div>
            </div>
            <div class="he-campo">
                <label for="he-fecha">Fecha de creación</label>
                <input type="text" name="fecha_creacion" id="he-fecha" class="mono" value="<?php echo $h($documento->fecha_creacion); ?>" placeholder="AAAA-MM-DD HH:MM:SS">
                <div class="he-ayuda">Formato AAAA-MM-DD HH:MM:SS. Ordena el documento en el seguimiento.</div>
            </div>
            <?php if ($tiene_hr): ?>
                <div class="he-peligro" id="he-aviso-nur" style="display:none">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Está cambiando el número de la hoja de ruta. Se renumerará <b>todo el expediente</b>:
                        <b><?php echo (int) $alcance['pasos']; ?></b> paso<?php echo (int) $alcance['pasos'] == 1 ? '' : 's'; ?> del seguimiento,
                        <b><?php echo (int) $alcance['documentos']; ?></b> documento<?php echo (int) $alcance['documentos'] == 1 ? '' : 's'; ?><?php if ((int) $alcance['agrupaciones'] > 0): ?> y <b><?php echo (int) $alcance['agrupaciones']; ?></b> agrupación<?php echo (int) $alcance['agrupaciones'] == 1 ? '' : 'es'; ?><?php endif; ?>.
                        Es un cambio que no se deshace solo.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="he-seccion">
        <h3><i class="fa fa-file-text-o"></i> Contenido</h3>
        <div class="he-campos">
            <div class="he-campo ancho">
                <label for="he-cite">Cite</label>
                <input type="text" name="cite_original" id="he-cite" value="<?php echo $h($documento->cite_original); ?>">
                <div class="he-ayuda">Es lo que la gente busca y lo que se imprime en la hoja de ruta.</div>
            </div>
            <div class="he-campo ancho">
                <label for="he-ref">Referencia</label>
                <input type="text" name="referencia" id="he-ref" value="<?php echo $h($documento->referencia); ?>">
            </div>
        </div>
    </div>

    <div class="he-seccion">
        <h3><i class="fa fa-users"></i> Personas <small style="font-weight:400;color:#8a94a3;font-size:12px">— como figuran impresas en el documento</small></h3>
        <div class="he-campos">
            <div class="he-campo">
                <label for="he-rem">Remitente</label>
                <input type="text" name="nombre_remitente" id="he-rem" value="<?php echo $h($documento->nombre_remitente); ?>">
            </div>
            <div class="he-campo">
                <label for="he-des">Destinatario</label>
                <input type="text" name="nombre_destinatario" id="he-des" value="<?php echo $h($documento->nombre_destinatario); ?>">
            </div>
            <div class="he-campo">
                <label for="he-carrem">Cargo del remitente</label>
                <input type="text" name="cargo_remitente" id="he-carrem" value="<?php echo $h($documento->cargo_remitente); ?>">
            </div>
            <div class="he-campo">
                <label for="he-cardes">Cargo del destinatario</label>
                <input type="text" name="cargo_destinatario" id="he-cardes" value="<?php echo $h($documento->cargo_destinatario); ?>">
            </div>
            <?php if ($autor): ?>
                <div class="he-campo ancho">
                    <div class="he-ayuda" style="margin:0">Estos nombres son los que se imprimen. No cambian quién generó el documento en el sistema
                        (<b><?php echo $h($autor); ?></b>) ni a quién se le derivó.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="he-barra">
        <span class="he-estado" id="he-estado">Sin cambios</span>
        <a href="/admin/hojasruta/lista" class="btn btn-default-bright">Cancelar</a>
        <button type="submit" name="editar" value="1" class="btn btn-primary" id="he-guardar"><i class="fa fa-check"></i> Guardar cambios</button>
    </div>
</form>

<!-- archivos digitales del documento -->
<div class="he-card">
    <div class="he-seccion" style="border-bottom:0;padding-bottom:18px">
        <h3><i class="fa fa-paperclip"></i> Archivos digitales <small style="font-weight:400;color:#8a94a3;font-size:12px">— lo que la gente abre y descarga</small></h3>

        <?php if (count($archivos) == 0): ?>
            <p class="he-vacio"><i class="fa fa-file-o"></i> Este documento no tiene ningún archivo digital.</p>
        <?php else: ?>
            <div class="he-archivos">
                <?php foreach ($archivos as $a):
                    $nombre = substr($a->nombre_archivo, 13);
                    $es_pdf = stripos($a->extension, 'pdf') !== FALSE || preg_match('/\.pdf$/i', $nombre);
                    $kb = (int) $a->tamanio;
                    ?>
                    <div class="he-archivo">
                        <span class="he-a-icono <?php echo $es_pdf ? 'pdf' : ''; ?>"><i class="fa <?php echo $es_pdf ? 'fa-file-pdf-o' : 'fa-file-o'; ?>"></i></span>
                        <span class="he-a-texto">
                            <b title="<?php echo $h($nombre); ?>"><?php echo $h($nombre); ?></b>
                            <small><?php echo $kb >= 1048576 ? number_format($kb / 1048576, 2) . ' MB' : number_format($kb / 1024, 0) . ' KB'; ?><?php if ($a->fecha): ?> · <?php echo date('d/m/Y H:i', strtotime($a->fecha)); ?><?php endif; ?></small>
                        </span>
                        <span class="he-a-botones">
                            <?php if ($es_pdf): ?>
                                <a href="/download/?file=<?php echo (int) $a->id; ?>&amp;ver=1" target="_blank" class="he-btn"><i class="fa fa-eye"></i> Ver</a>
                            <?php endif; ?>
                            <a href="/download/?file=<?php echo (int) $a->id; ?>" class="he-btn"><i class="fa fa-download"></i> Descargar</a>
                            <form method="post" action="" style="display:inline" class="he-form-quitar">
                                <input type="hidden" name="quitar_archivo" value="<?php echo (int) $a->id; ?>"/>
                                <button type="submit" class="he-btn quitar" data-nombre="<?php echo $h($nombre); ?>"><i class="fa fa-trash-o"></i> Quitar</button>
                            </form>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data" class="he-subir" id="he-form-subir">
            <label class="he-btn" for="he-archivo"><i class="fa fa-paperclip"></i> Elegir archivo PDF…</label>
            <input type="file" name="archivo" id="he-archivo" accept="application/pdf" style="display:none"/>
            <span class="he-elegido" id="he-elegido">Ningún archivo elegido</span>
            <button type="submit" name="adjuntar" value="1" class="btn btn-primary" id="he-btn-subir" disabled><i class="fa fa-upload"></i> Adjuntar</button>
        </form>
    </div>
</div>

<!-- acciones sobre el expediente completo, fuera del formulario -->
<div class="he-card">
    <div class="he-seccion" style="border-bottom:0;padding-bottom:18px">
        <h3><i class="fa fa-wrench"></i> Acciones sobre este documento</h3>
        <div class="he-tarjetas">
            <a href="/document/detalle/<?php echo (int) $documento->id; ?>" target="_blank" class="he-tarjeta">
                <span class="he-t-icono ver"><i class="fa fa-eye"></i></span>
                <span class="he-t-texto">
                    <b>Ver el documento</b>
                    <small>Cómo lo ven los usuarios, con sus archivos adjuntos.</small>
                </span>
            </a>
            <?php if ($tiene_hr): ?>
                <a href="/route/trace/?hr=<?php echo urlencode($documento->nur); ?>" target="_blank" class="he-tarjeta">
                    <span class="he-t-icono seg"><i class="md md-verified-user"></i></span>
                    <span class="he-t-texto">
                        <b>Ver el recorrido</b>
                        <small>Por dónde pasó y con quién está ahora. Desde ahí puede deshacer derivaciones.</small>
                    </span>
                </a>
            <?php endif; ?>
            <a href="#" class="he-tarjeta" id="he-ir-datos">
                <span class="he-t-icono editar"><i class="fa fa-pencil"></i></span>
                <span class="he-t-texto">
                    <b>Editar los datos</b>
                    <small>Número, cite, referencia, fecha y personas: el formulario de arriba.</small>
                </span>
            </a>
            <form method="post" action="/admin/hojasruta/eliminar/<?php echo (int) $documento->id; ?>" id="he-form-eliminar" style="margin:0">
                <input type="hidden" name="confirmar" value="1"/>
                <button type="submit" class="he-tarjeta peligro" id="he-eliminar"
                        data-nur="<?php echo $h($tiene_hr ? $documento->nur : $documento->codigo); ?>">
                    <span class="he-t-icono borrar"><i class="fa fa-trash-o"></i></span>
                    <span class="he-t-texto">
                        <b>Eliminar definitivamente</b>
                        <small>Borra el expediente entero y no se puede deshacer.</small>
                    </span>
                </button>
            </form>
        </div>
        <div class="he-peligro" style="margin-top:14px">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Al eliminar se borran <b><?php echo (int) $alcance['documentos']; ?></b> documento<?php echo (int) $alcance['documentos'] == 1 ? '' : 's'; ?>,
                <b><?php echo (int) $alcance['pasos']; ?></b> paso<?php echo (int) $alcance['pasos'] == 1 ? '' : 's'; ?> del seguimiento,
                sus archivos adjuntos<?php if ((int) $alcance['agrupaciones'] > 0): ?>, <b><?php echo (int) $alcance['agrupaciones']; ?></b> agrupación<?php echo (int) $alcance['agrupaciones'] == 1 ? '' : 'es'; ?><?php endif; ?>
                y el número de hoja de ruta. <b>No hay papelera ni forma de recuperarlo.</b> Si solo quiere sacarlo de circulación, anúlelo desde el seguimiento.</span>
        </div>
    </div>
</div>

<script>
    $(function () {
        var $form = $('#he-form');
        var nurOriginal = <?php echo json_encode((string) $documento->nur); ?>;
        var original = $form.serialize(), enviando = false;

        function revisar() {
            var cambio = $form.serialize() !== original;
            $('#he-estado').toggleClass('cambios', cambio).text(cambio ? 'Hay cambios sin guardar' : 'Sin cambios');
            $('#he-aviso-nur').toggle($.trim($('#he-nur').val()) !== nurOriginal);
        }
        $form.on('input change', revisar);

        $form.on('submit', function (e) {
            var nuevo = $.trim($('#he-nur').val());
            if (nuevo !== nurOriginal) {
                if (!confirm('Va a renumerar toda la hoja de ruta:\n\n' + nurOriginal + '  →  ' + nuevo +
                        '\n\nSe actualizan el seguimiento, los documentos y las agrupaciones del expediente. ¿Continuar?')) {
                    e.preventDefault();
                    return;
                }
            }
            enviando = true;
            $('#he-guardar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
        });
        $(window).on('beforeunload', function () {
            if (!enviando && $form.serialize() !== original) {
                return 'Hay cambios sin guardar.';
            }
        });
        revisar();

        // lleva al formulario y deja el cursor en el primer campo
        $('#he-ir-datos').on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: $('#he-form').offset().top - 20}, 250);
            $('#he-nur').focus();
        });

        // archivos digitales
        $('#he-archivo').on('change', function () {
            var f = this.files && this.files[0];
            $('#he-elegido').text(f ? f.name + ' (' + Math.round(f.size / 1024) + ' KB)' : 'Ningún archivo elegido');
            $('#he-btn-subir').prop('disabled', !f);
        });
        $('#he-form-subir').on('submit', function () {
            enviando = true;
            $('#he-btn-subir').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Subiendo…');
        });
        $('.he-btn.quitar').on('click', function (e) {
            e.preventDefault();
            if (!confirm('¿Quitar el archivo "' + $(this).data('nombre') + '"?\n\nDejará de verse y de poder descargarse desde el documento.')) {
                return;
            }
            enviando = true;
            $(this).closest('form')[0].submit();
        });

        // eliminar: dos confirmaciones, la segunda escribiendo el numero
        $('#he-eliminar').on('click', function (e) {
            e.preventDefault();
            var nur = String($(this).data('nur'));
            if (!confirm('¿ELIMINAR DEFINITIVAMENTE ' + nur + '?\n\nSe borrarán para siempre el documento, todo su seguimiento (derivaciones), sus agrupaciones y su correlativo. Esta acción NO se puede deshacer.')) {
                return;
            }
            var escrito = prompt('Para confirmar, escriba el número: ' + nur);
            if (escrito === null) {
                return;
            }
            if ($.trim(escrito).toUpperCase() !== nur.toUpperCase()) {
                alert('El número no coincide. No se eliminó nada.');
                return;
            }
            enviando = true;
            $('#he-form-eliminar')[0].submit();
        });
    });
</script>
