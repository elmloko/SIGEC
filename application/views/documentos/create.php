<?php
// iconos por tipo de documento
$icono_tipo = 'fa-file-text-o';
foreach (array('informe' => 'fa-file-text-o', 'memo' => 'fa-clipboard', 'circular' => 'fa-bullhorn', 'carta' => 'fa-envelope-o',
    'instructivo' => 'fa-list-ol', 'intructivo' => 'fa-list-ol', 'comunicado' => 'fa-comment-o', 'nota' => 'fa-pencil-square-o') as $clave => $icono) {
    if (strpos(mb_strtolower($tipo->tipo, 'UTF-8'), $clave) !== FALSE) {
        $icono_tipo = $icono;
        break;
    }
}
$con_via = $tipo->via != 0;
$es_carta = $documento->tipo == 'Carta';
$iniciales = function ($nombre) {
    $partes = preg_split('/\s+/u', trim((string) $nombre), -1, PREG_SPLIT_NO_EMPTY);
    $ini = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $ini .= mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return $ini !== '' ? $ini : '?';
};
?>
<link rel="stylesheet" href="/static/css/documento-form.css?v=2"/>
<script type="text/javascript" src="/static/js/documento-form.js?v=2"></script>
<script type="text/javascript">
    var stat = 0;
    $(function () {
        $('#destinatario').focus();
        $('#noHojaRuta').click(function () {
            $('#hojaruta').val(0);
            return true;
        });
        $('#cite_sup').click(function () {
            $('#cite_superior').val(1);
            $('#hojaruta').val(0);
            stat = 1;
            return true;
        });
        $('#otrovia').click(function () {
            $('.via2').toggle();
            return false;
        });

        // evita generar dos documentos por doble clic
        $('#frmCreate').on('submit', function (e) {
            if (e.isDefaultPrevented() || ($.fn.valid && !$(this).valid())) {
                return;
            }
            if (!$('#proceso').val()) {
                alert('Elija un tipo de proceso por favor.');
                $('#proceso').focus();
                return false;
            }
            var $botones = $('.gd-acciones .btn');
            setTimeout(function () {
                $botones.prop('disabled', true);
            }, 0);
            $('#gd-generando').show();
        });
    });

    function isValidForms() {
        if ($('#proceso').val() > 0) {
            if (stat) {
                stat = 0;
                return confirm("Esta seguro que desea generar un numero cite de su area superior.");
            }
            return true;
        }
        alert("Escoja un tipo de proceso por favor.");
        return false;
    }
    function activar(c) {
        document.getElementById('cite_sup').style.display = c.checked ? 'inline-block' : 'none';
    }
</script>

<div class="col-lg-8">
    <form class="form form-validate gd-card" action="/documento/generar/<?php echo HTML::chars($documento->action); ?>" method="post" id="frmCreate">
        <!-- tipo de documento y proceso -->
        <div class="gd-cab">
            <div class="gd-cab-icono"><i class="fa <?php echo $icono_tipo; ?>"></i></div>
            <div class="gd-cab-titulo">
                <h2>Generar <?php echo HTML::chars(mb_strtolower($tipo->tipo, 'UTF-8')); ?></h2>
                <small>Fecha: <?php echo date('d/m/Y'); ?> &middot; el cite se asigna al generar</small>
            </div>
            <div class="gd-cab-proceso">
                <label for="proceso">Proceso <span class="gd-req">*</span></label>
                <?php
                // por defecto el proceso "Solicitud"
                $proceso_defecto = '';
                foreach ($options as $id_p => $nombre_p) {
                    if (mb_strtolower(trim($nombre_p), 'UTF-8') == 'solicitud') {
                        $proceso_defecto = $id_p;
                    }
                }
                echo Form::select('proceso', $options, $proceso_defecto, array('id' => 'proceso', 'class' => 'required', 'title' => 'Elija un tipo de proceso por favor'));
                ?>
            </div>
        </div>

        <!-- encabezado del documento, en el mismo orden que el impreso -->
        <div class="gd-hoja">
            <!-- A: -->
            <div class="gd-fila">
                <div class="gd-etiqueta">A:</div>
                <div>
                    <div class="gd-par<?php echo $es_carta ? ' gd-par-titulo' : ''; ?>">
                        <?php if ($es_carta): ?>
                            <select name="titulo" id="titulo" title="Título">
                                <option value="">Título</option>
                                <option>Señor</option>
                                <option>Señora</option>
                                <option>Señores</option>
                            </select>
                        <?php endif; ?>
                        <?php echo Form::input('destinatario', '', array('id' => 'destinatario', 'autocomplete' => 'off', 'placeholder' => 'Nombre del destinatario')); ?>
                        <?php echo Form::input('cargo_des', '', array('id' => 'cargo_des', 'autocomplete' => 'off', 'placeholder' => 'Cargo')); ?>
                        <?php if (!$con_via): ?>
                            <input type="text" name="institucion_des" id="institucion_des" class="gd-completo" autocomplete="off" placeholder="Institución"/>
                        <?php endif; ?>
                    </div>
                    <div class="gd-ayuda"><span>Escríbalo o elíjalo de la libreta de la derecha</span><a href="#" class="gd-limpiar"><i class="fa fa-times"></i> limpiar</a></div>
                </div>
            </div>
            <?php if (!$es_carta): ?>
                <input type="hidden" name="titulo"/>
            <?php endif; ?>

            <?php if ($con_via): ?>
                <input type="hidden" name="institucion_des"/>
                <!-- VIA: -->
                <div class="gd-fila">
                    <div class="gd-etiqueta">VÍA:<small>opcional</small></div>
                    <div>
                        <div class="gd-par">
                            <?php echo Form::input('via', '', array('id' => 'via', 'autocomplete' => 'off', 'placeholder' => 'Nombre')); ?>
                            <?php echo Form::input('cargovia', '', array('id' => 'cargovia', 'autocomplete' => 'off', 'placeholder' => 'Cargo')); ?>
                        </div>
                        <div class="gd-ayuda"><span>Déjelo vacío si el documento va directo</span><a href="#" class="gd-limpiar"><i class="fa fa-times"></i> limpiar</a></div>
                    </div>
                </div>
                <div class="gd-fila via2">
                    <div class="gd-etiqueta">VÍA:</div>
                    <div class="gd-par">
                        <?php echo Form::input('via2', '', array('id' => 'via2', 'autocomplete' => 'off', 'placeholder' => 'Nombre')); ?>
                        <?php echo Form::input('cargovia2', '', array('id' => 'cargovia2', 'class' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Cargo')); ?>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="via"/>
                <input type="hidden" name="cargovia"/>
            <?php endif; ?>

            <!-- DE: -->
            <div class="gd-fila">
                <div class="gd-etiqueta">DE:</div>
                <div class="gd-remitente">
                    <div class="gd-avatar"><?php echo HTML::chars($iniciales($user->nombre)); ?></div>
                    <div>
                        <b><?php echo HTML::chars($user->nombre); ?></b>
                        <span><?php echo HTML::chars($user->cargo); ?></span>
                    </div>
                    <?php if ($user->mosca != ''): ?><span class="gd-mosca" title="Mosca">Mosca: <?php echo HTML::chars($user->mosca); ?></span><?php endif; ?>
                </div>
            </div>
            <?php echo Form::hidden('remitente', $user->nombre, array('id' => 'remitente')); ?>
            <?php echo Form::hidden('cargo_rem', $user->cargo, array('id' => 'cargo_rem')); ?>
            <?php echo Form::hidden('mosca', $user->mosca, array('id' => 'mosca')); ?>

            <!-- REF: -->
            <div class="gd-fila">
                <div class="gd-etiqueta">REF.: <span class="gd-req">*</span></div>
                <div>
                    <textarea name="referencia" id="referencia" class="required" title="Escriba la referencia del documento"
                              placeholder="Asunto del documento, por ejemplo: Solicitud de mantenimiento de equipos"></textarea>
                    <div class="gd-ayuda"><span>Asunto que se verá en la bandeja y en la hoja de ruta</span><span><span id="gd-ref-contador">0</span> caracteres</span></div>
                </div>
            </div>
        </div>

        <!-- anexos -->
        <div class="gd-anexos">
            <div>
                <label for="adjuntos">Adjunto</label>
                <?php echo Form::input('adjuntos', 'Folder', array('id' => 'adjuntos', 'title' => 'Ejemplo: Lo citado')); ?>
            </div>
            <div>
                <label for="hojas">Hojas <span class="gd-req">*</span></label>
                <?php echo Form::input('hojas', '1', array('id' => 'hojas', 'class' => 'required', 'title' => 'La casilla está vacía o ingrese número > 0', 'type' => 'number', 'min' => '1')); ?>
            </div>
            <div>
                <label for="copias">Con copia a</label>
                <?php echo Form::input('copias', '', array('id' => 'copias', 'autocomplete' => 'off', 'placeholder' => 'Opcional')); ?>
            </div>
        </div>

        <input type="hidden" id="hojaruta" value="1" name="hojaruta"/>
        <input type="hidden" id="cite_superior" value="0" name="cite_superior"/>
        <?php echo Form::hidden('descripcion', '', array('id' => 'descripcion')); ?>

        <div class="gd-acciones">
            <span class="gd-izq">
                <?php if ($user->cite_despacho): ?>
                    <label><input type="checkbox" name="cite_despacho" value="1" onclick="activar(this);"> Usar cite de Dirección / Despacho</label>
                <?php else: ?>
                    <span class="gd-req">*</span> Obligatorio
                <?php endif; ?>
            </span>
            <span id="gd-generando"><i class="fa fa-circle-o-notch fa-spin"></i> Generando documento...</span>
            <?php if ($user->cite_despacho): ?>
                <input style="display:none;" type="submit" class="btn btn-warning" name="submit" id="cite_sup" value="Generar con cite Dir./Despacho"/>
            <?php endif; ?>
            <input type="submit" id="noHojaRuta" class="btn btn-default-bright" name="submit" value="Generar sin hoja de ruta"
                   title="Genera el documento sin hoja de ruta, para asignársela después"/>
            <input type="submit" class="btn btn-primary" name="submit" value="Generar con hoja de ruta"
                   title="Genera el documento y le asigna una hoja de ruta nueva"/>
        </div>
    </form>
</div>

<div class="col-lg-4">
    <div class="gd-card gd-libreta">
        <div class="gd-libreta-cab">
            <h3><i class="fa fa-users"></i> Libreta de destinatarios</h3>
            <p>Clic en una persona para llenar: <b id="gd-objetivo-texto">A (destinatario)</b></p>
            <div class="gd-libreta-buscar">
                <i class="fa fa-search"></i>
                <input type="search" id="gd-buscar" placeholder="Buscar por nombre o cargo..." autocomplete="off"/>
            </div>
        </div>
        <div id="vias">
            <ul>
                <?php foreach ($destinatarios as $v): ?>
                    <li class="<?php echo HTML::chars($v['genero']); ?>">
                        <a href="#" class="destino1 destinatario" nombre="<?php echo HTML::chars($v['nombre']); ?>" cargo="<?php echo HTML::chars($v['cargo']); ?>"
                           title="<?php echo HTML::chars($v['cargo']); ?>" via="" cargo_via="">
                            <span class="gd-avatar"><?php echo HTML::chars($iniciales($v['nombre'])); ?></span>
                            <span class="gd-persona"><b><?php echo HTML::chars($v['nombre']); ?></b><small><?php echo HTML::chars($v['cargo']); ?></small></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div id="gd-sin-resultados">Nadie coincide con la búsqueda.</div>
        </div>
        <div class="gd-libreta-pie">
            <?php echo Form::input('addDest', '+ Agregar persona a la libreta', array('class' => 'btn btn-sm btn-default-bright btn-block', 'type' => 'button', 'id' => 'addDest', 'rel' => $user->id)); ?>
            <p><i class="fa fa-lightbulb-o"></i> Haga clic en la fila <b>A</b> o <b>VÍA</b> y luego elija a la persona. Después de generar podrá editar el contenido y subir el archivo.</p>
        </div>
    </div>
</div>
