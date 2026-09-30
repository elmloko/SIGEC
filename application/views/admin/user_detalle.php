<?php
$h = function ($s) {
    return HTML::chars($s);
};
$foto = file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg') ? '/static/fotos/' . $user->username . '.jpg' : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
$total = 0;
foreach ($tipos as $t) {
    if (isset($permitidos[(int) $t->id])) {
        $total++;
    }
}
// icono segun el tipo de documento
$iconos = array('circular' => 'fa-bullhorn', 'memorandum' => 'fa-file-text-o', 'informe' => 'fa-file-text-o',
    'nota interna' => 'fa-file-o', 'carta' => 'fa-envelope-o', 'doc. externo' => 'fa-inbox',
    'instructivo' => 'fa-list-ol', 'comunicado' => 'fa-bullhorn');
?>
<style>
    body {
        background: #F3F5F8;
    }
    .dp {
        max-width: 860px;
        margin: 0 auto;
        padding: 14px 16px 90px;
        font-family: Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    /* quien es */
    .dp-quien {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        margin-bottom: 14px;
        border-radius: 12px;
        border-top: 3px solid #FECB34;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
    }
    .dp-quien img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #E8EFF8;
    }
    .dp-quien b {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #123E73;
    }
    .dp-quien span {
        display: block;
        font-size: 12.5px;
        color: #6b7686;
    }
    .dp-ayuda {
        margin: 0 2px 12px;
        font-size: 13px;
        color: #5b6574;
    }
    /* barra de acciones */
    .dp-barra {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .dp-cuenta {
        flex: 1 1 auto;
        font-size: 13px;
        color: #6b7686;
    }
    .dp-cuenta b {
        color: #123E73;
    }
    .dp-barra button {
        height: 30px;
        padding: 0 12px;
        border: 1px solid #D5DCE6;
        border-radius: 8px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
    }
    .dp-barra button:hover {
        background: #EEF3FA;
    }
    /* lista de tipos */
    .dp-lista {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 10px;
    }
    .dp-tipo {
        position: relative;
        display: block;
        margin: 0;
        cursor: pointer;
    }
    .dp-tipo input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .dp-tipo > span {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        height: 100%;
        padding: 11px 12px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        background: #fff;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }
    .dp-tipo:hover > span {
        border-color: #B9C8DC;
    }
    .dp-tipo input:checked + span {
        border-color: #1A549A;
        background: #F5F9FF;
        box-shadow: inset 0 0 0 1px #1A549A;
    }
    .dp-tipo input:focus + span {
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .18);
    }
    .dp-marca {
        flex: 0 0 20px;
        width: 20px;
        height: 20px;
        margin-top: 1px;
        border: 2px solid #C4CEDB;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 11px;
        background: #fff;
    }
    .dp-tipo input:checked + span .dp-marca {
        border-color: #1A549A;
        background: #1A549A;
    }
    .dp-marca .fa {
        opacity: 0;
    }
    .dp-tipo input:checked + span .dp-marca .fa {
        opacity: 1;
    }
    .dp-texto {
        flex: 1 1 auto;
        min-width: 0;
    }
    .dp-texto b {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .dp-texto b .fa {
        width: 15px;
        margin-right: 5px;
        color: #8a94a3;
    }
    .dp-tipo input:checked + span .dp-texto b .fa {
        color: #1A549A;
    }
    .dp-texto small {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        color: #8a94a3;
        word-break: break-word;
    }
    .dp-usados {
        display: inline-block;
        margin-top: 5px;
        padding: 1px 8px;
        border-radius: 20px;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 11px;
        font-weight: 600;
    }
    /* pie fijo */
    .dp-pie {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        border-top: 1px solid #E3E8EF;
        background: rgba(255, 255, 255, .97);
    }
    .dp-estado {
        flex: 1 1 200px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .dp-estado.cambios {
        color: #B7791F;
        font-weight: 600;
    }
    .dp-estado.error {
        color: #B42318;
        font-weight: 600;
    }
    .dp-pie .btn {
        margin: 0;
    }
    .dp-pie .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 7px 22px;
        font-weight: 700;
    }
    .dp-aviso {
        display: flex;
        gap: 9px;
        align-items: flex-start;
        margin: 12px 0 0;
        padding: 10px 12px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
    }
</style>

<div class="dp">
    <div class="dp-quien">
        <img src="<?php echo $foto; ?>" alt="">
        <span style="flex:1 1 auto;min-width:0">
            <b><?php echo $h($user->nombre); ?></b>
            <span><?php echo $h($user->cargo); ?><?php echo $oficina ? ' · ' . $h($oficina) : ''; ?></span>
        </span>
    </div>

    <p class="dp-ayuda">Marque los documentos que esta persona podrá <b>generar</b> desde el botón «Nuevo documento». Los que no estén marcados no le aparecerán.</p>

    <div class="dp-barra">
        <span class="dp-cuenta"><b id="dp-n"><?php echo $total; ?></b> de <?php echo count($tipos); ?> seleccionados</span>
        <button type="button" id="dp-todos"><i class="fa fa-check-square-o"></i> Marcar todos</button>
        <button type="button" id="dp-ninguno"><i class="fa fa-square-o"></i> Desmarcar todos</button>
    </div>

    <div class="dp-lista">
        <?php foreach ($tipos as $t):
            $marcado = isset($permitidos[(int) $t->id]);
            $usado = isset($usados[$t->id]) ? (int) $usados[$t->id] : 0;
            $icono = Arr::get($iconos, strtolower(trim($t->tipo)), 'fa-file-o');
            ?>
            <label class="dp-tipo">
                <input type="checkbox" name="tipos[]" value="<?php echo (int) $t->id; ?>" <?php echo $marcado ? 'checked' : ''; ?> data-usado="<?php echo $usado; ?>" data-nombre="<?php echo $h($t->tipo); ?>">
                <span>
                    <span class="dp-marca"><i class="fa fa-check"></i></span>
                    <span class="dp-texto">
                        <b><i class="fa <?php echo $icono; ?>"></i><?php echo $h($t->tipo); ?></b>
                        <?php if (trim($t->descripcion) !== ''): ?>
                            <small><?php echo $h($t->descripcion); ?></small>
                        <?php endif; ?>
                        <?php if ($usado > 0): ?>
                            <span class="dp-usados"><?php echo number_format($usado, 0, ',', '.'); ?> generado<?php echo $usado == 1 ? '' : 's'; ?></span>
                        <?php endif; ?>
                    </span>
                </span>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="dp-aviso" id="dp-nota" style="display:none">
        <i class="fa fa-exclamation-triangle"></i>
        <span id="dp-nota-texto"></span>
    </div>
</div>

<div class="dp-pie">
    <span class="dp-estado" id="dp-estado">Sin cambios</span>
    <a href="javascript:;" class="btn btn-default-bright" id="dp-cerrar">Cerrar</a>
    <a href="javascript:;" class="btn btn-primary" id="aceptar"><i class="fa fa-check"></i> Guardar</a>
</div>

<script src="/static/js/modal-alto.js"></script>
<script>
    $(function () {
        var ID = <?php echo (int) $user->id; ?>;
        var $cajas = $('input[name="tipos[]"]');
        var original = $cajas.filter(':checked').map(function () {
            return this.value;
        }).get().join(',');

        function seleccion() {
            return $cajas.filter(':checked').map(function () {
                return this.value;
            }).get();
        }
        function estado(txt, clase) {
            $('#dp-estado').removeClass('cambios error').addClass(clase || '').text(txt);
        }
        function refrescar() {
            var sel = seleccion();
            $('#dp-n').text(sel.length);
            var cambio = sel.join(',') !== original;
            estado(cambio ? 'Hay cambios sin guardar' : 'Sin cambios', cambio ? 'cambios' : '');
            // aviso si se desmarca un tipo del que ya tiene documentos generados
            var quitados = [];
            $cajas.not(':checked').each(function () {
                if (parseInt($(this).data('usado'), 10) > 0 && original.split(',').indexOf(this.value) > -1) {
                    quitados.push($(this).data('nombre'));
                }
            });
            if (quitados.length) {
                $('#dp-nota-texto').html('Va a quitar <b>' + quitados.join('</b>, <b>') + '</b>, y esta persona ya generó documentos de ese tipo. Los documentos anteriores no se borran: solo dejará de poder crear nuevos.');
                $('#dp-nota').show();
            } else {
                $('#dp-nota').hide();
            }
            // el aviso cambia el alto: se reajusta el modal
            if (window.ajustarModal) {
                window.ajustarModal();
            }
        }
        $cajas.on('change', refrescar);
        $('#dp-todos').on('click', function () {
            $cajas.prop('checked', true);
            refrescar();
        });
        $('#dp-ninguno').on('click', function () {
            $cajas.prop('checked', false);
            refrescar();
        });

        function cerrar() {
            try {
                parent.eModal.close();
            } catch (e) {
                window.close();
            }
        }
        $('#dp-cerrar').on('click', function () {
            if (seleccion().join(',') !== original && !confirm('Hay cambios sin guardar. ¿Cerrar de todos modos?')) {
                return;
            }
            cerrar();
        });

        $('#aceptar').on('click', function () {
            var $b = $(this);
            if ($b.prop('disabled')) {
                return;
            }
            var sel = seleccion();
            if (!sel.length && !confirm('No marcó ningún documento: esta persona no podrá generar ninguno. ¿Continuar?')) {
                return;
            }
            $b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
            estado('Guardando…');
            $.ajax({
                type: 'POST',
                url: '/admin/ajax/addDocumentos',
                dataType: 'json',
                traditional: false,
                data: {tipos: sel, id_user: ID}
            }).done(function (r) {
                if (r && r.ok) {
                    original = sel.join(',');
                    estado('Guardado');
                    cerrar();
                } else {
                    estado((r && r.msg) || 'No se pudo guardar.', 'error');
                    $b.prop('disabled', false).html('<i class="fa fa-check"></i> Guardar');
                }
            }).fail(function () {
                estado('No se pudo guardar: revise su conexión.', 'error');
                $b.prop('disabled', false).html('<i class="fa fa-check"></i> Guardar');
            });
        });

        refrescar();
        if (window.ajustarModal) {
            window.ajustarModal();
            // por si las fuentes o las fotos cambian el alto al terminar de cargar
            $(window).on('load', function () {
                window.ajustarModal();
            });
        }
    });
</script>
