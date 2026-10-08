<?php
// Formulario de recepcion: ventanilla registra la documentacion que llega de afuera
// y le da un numero de hoja de ruta para que empiece a circular.
$h = function ($s) {
    return HTML::chars($s);
};
$libreta = array();
foreach ($destinos as $d) {
    $libreta[] = array(
        'n' => trim($d->nombre),
        'c' => trim($d->cargo),
        'e' => trim($d->entidad),
        'g' => $d->genero == 'mujer' ? 'mujer' : 'hombre',
    );
}
$js_inst = array();
foreach ($instituciones as $i) {
    $js_inst[] = array('v' => trim($i['v']), 'n' => (int) $i['n']);
}
$js_rem = array();
foreach ($remitentes as $r) {
    $js_rem[] = array('n' => trim($r['n']), 'c' => trim($r['c']), 'i' => trim($r['i']), 'v' => (int) $r['veces']);
}
$vacio = function ($v) {
    return trim(str_replace('.', '', (string) $v)) === '';
};
?>
<style>
    .rc-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .rc-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        padding: 16px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .rc-cab-icono {
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
    .rc-cab-texto {
        flex: 1 1 260px;
    }
    .rc-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .rc-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .rc-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 36px;
        padding: 0 15px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A549A;
        text-decoration: none;
    }
    .rc-btn:hover {
        background: #EEF3FA;
        color: #123E73;
        text-decoration: none;
    }
    /* distribucion */
    .rc-dos {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 18px;
        align-items: start;
    }
    @media (max-width: 1150px) {
        .rc-dos {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    .rc-seccion {
        padding: 18px 22px 6px;
        border-bottom: 1px solid #EEF1F5;
    }
    .rc-seccion:last-of-type {
        border-bottom: 0;
    }
    .rc-seccion h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        color: #123E73;
    }
    .rc-paso {
        flex: 0 0 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #1A549A;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }
    .rc-intro {
        margin: 0 0 14px 34px;
        font-size: 12px;
        color: #8a94a3;
    }
    .rc-campos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 2px 16px;
    }
    .rc-campo {
        position: relative;
        margin-bottom: 14px;
    }
    .rc-campo.ancho {
        grid-column: 1 / -1;
    }
    .rc-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5566;
    }
    .rc-campo label i.obl {
        color: #B42318;
        font-style: normal;
    }
    .rc-campo input[type=text],
    .rc-campo input[type=number],
    .rc-campo select,
    .rc-campo textarea {
        width: 100%;
        min-height: 38px;
        padding: 7px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13.5px;
        color: #1f2937;
        outline: none;
    }
    .rc-campo textarea {
        min-height: 72px;
        resize: vertical;
    }
    .rc-campo input:focus,
    .rc-campo select:focus,
    .rc-campo textarea:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .rc-campo input.err,
    .rc-campo textarea.err {
        border-color: #D92D20;
        background: #FEF6F6;
    }
    .rc-ayuda {
        margin-top: 4px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .rc-error {
        margin-top: 4px;
        font-size: 12px;
        color: #B42318;
        display: none;
    }
    /* el recorrido: de quien viene / a quien va, enfrentados */
    .rc-ruta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 34px minmax(0, 1fr);
        align-items: stretch;
        gap: 0;
        padding: 18px 22px 10px;
        border-bottom: 1px solid #EEF1F5;
    }
    @media (max-width: 860px) {
        .rc-ruta {
            grid-template-columns: minmax(0, 1fr);
        }
        .rc-flecha {
            transform: rotate(90deg);
            padding: 6px 0;
        }
    }
    .rc-lado {
        border: 1px solid #E3E8EF;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .rc-lado-cab {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid #EEF1F5;
    }
    .rc-lado.de .rc-lado-cab {
        background: #FFF7EC;
    }
    .rc-lado.para .rc-lado-cab {
        background: #EFF6FF;
    }
    .rc-lado-icono {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .rc-lado.de .rc-lado-icono {
        background: #FDE3C0;
        color: #9A5B00;
    }
    .rc-lado.para .rc-lado-icono {
        background: #D5E4F7;
        color: #123E73;
    }
    .rc-lado-cab b {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        color: #1f2937;
    }
    .rc-lado-cab small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .rc-lado-cuerpo {
        padding: 14px 14px 12px;
    }
    .rc-flecha {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #C4CEDB;
        font-size: 17px;
    }
    /* sugerencias */
    .rc-sug {
        position: absolute;
        z-index: 30;
        left: 0;
        right: 0;
        top: 100%;
        margin-top: 2px;
        max-height: 230px;
        overflow: auto;
        border: 1px solid #D5DCE6;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(18, 62, 115, .14);
        display: none;
    }
    .rc-sug div {
        padding: 8px 12px;
        font-size: 12.5px;
        cursor: pointer;
        border-bottom: 1px solid #F1F4F8;
    }
    .rc-sug div:last-child {
        border-bottom: 0;
    }
    .rc-sug div:hover,
    .rc-sug div.sel {
        background: #EEF3FA;
    }
    .rc-sug b {
        display: block;
        color: #1f2937;
    }
    .rc-sug small {
        color: #8a94a3;
        font-size: 11px;
    }
    .rc-sug .veces {
        float: right;
        color: #1A549A;
        font-size: 10.5px;
        font-weight: 700;
    }
    .rc-sug-pie {
        padding: 7px 12px;
        background: #F7F9FC;
        font-size: 11px;
        color: #8a94a3;
    }
    /* aviso de repetido */
    .rc-alerta {
        display: none;
        gap: 9px;
        align-items: flex-start;
        margin: 0 0 14px;
        padding: 10px 13px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
        grid-column: 1 / -1;
    }
    .rc-alerta.ver {
        display: flex;
    }
    .rc-alerta a {
        color: #7A5A00;
        text-decoration: underline;
        font-weight: 600;
    }
    /* panel lateral */
    .rc-tabs {
        display: flex;
        border-bottom: 1px solid #E3E8EF;
    }
    .rc-tab {
        flex: 1 1 0;
        padding: 12px 8px;
        border: 0;
        background: #F7F9FC;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b7686;
        border-bottom: 3px solid transparent;
    }
    .rc-tab.activa {
        background: #fff;
        color: #123E73;
        border-bottom-color: #FECB34;
    }
    .rc-buscar {
        position: relative;
        margin: 12px 14px 8px;
    }
    .rc-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        margin-top: -7px;
        color: #9aa4b2;
        font-size: 13px;
    }
    .rc-buscar input {
        width: 100%;
        height: 34px;
        padding: 6px 11px 6px 31px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        font-size: 13px;
        outline: none;
    }
    .rc-buscar input:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .rc-libreta {
        max-height: 420px;
        overflow: auto;
        margin: 0;
        padding: 0 0 8px;
        list-style: none;
    }
    .rc-libreta li a {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 8px 14px;
        text-decoration: none;
        color: #334155;
    }
    .rc-libreta li a:hover {
        background: #F7FAFD;
        text-decoration: none;
    }
    .rc-libreta .fa {
        flex: 0 0 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 12px;
    }
    .rc-libreta .mujer .fa {
        background: #FCEEF4;
        color: #B4367A;
    }
    .rc-libreta b {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .rc-libreta small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
    }
    .rc-libreta .vacio {
        padding: 20px 14px;
        text-align: center;
        font-size: 12.5px;
        color: #8a94a3;
    }
    /* recibidos hoy */
    .rc-hoy {
        max-height: 460px;
        overflow: auto;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .rc-hoy li {
        display: flex;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid #F1F4F8;
    }
    .rc-hoy li:last-child {
        border-bottom: 0;
    }
    .rc-hora {
        flex: 0 0 auto;
        font-family: Consolas, "Courier New", monospace;
        font-size: 11px;
        color: #8a94a3;
        padding-top: 2px;
    }
    .rc-hoy-txt {
        flex: 1 1 auto;
        min-width: 0;
    }
    .rc-hoy-txt b {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rc-hoy-txt b.sin {
        color: #9aa4b2;
        font-style: italic;
        font-weight: 400;
    }
    .rc-hoy-txt small {
        display: block;
        font-size: 11px;
        color: #8a94a3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rc-hoy-nur {
        font-family: Consolas, "Courier New", monospace;
        color: #1A549A;
        font-weight: 700;
        text-decoration: none;
    }
    .rc-etq {
        display: inline-block;
        padding: 0 7px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }
    .rc-etq.falta {
        background: #FDE8E8;
        color: #B42318;
    }
    .rc-etq.ok {
        background: #E6F4EC;
        color: #1E7B45;
    }
    .rc-panel-vacio {
        padding: 26px 14px;
        text-align: center;
        font-size: 12.5px;
        color: #8a94a3;
    }
    /* barra de guardar */
    .rc-barra {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-top: 1px solid #EEF1F5;
        border-radius: 0 0 14px 14px;
        background: rgba(255, 255, 255, .97);
    }
    .rc-estado {
        flex: 1 1 220px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .rc-estado.mal {
        color: #B42318;
        font-weight: 600;
    }
    .rc-barra .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 9px 22px;
        font-weight: 700;
        margin: 0;
    }
    .rc-limpiar {
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        height: 38px;
        padding: 0 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #6b7686;
    }
    .rc-limpiar:hover {
        background: #F3F6FA;
    }
</style>

<div class="rc-card rc-cab">
    <span class="rc-cab-icono"><i class="fa fa-inbox"></i></span>
    <div class="rc-cab-texto">
        <h2>Recepcionar correspondencia</h2>
        <p>Registre el documento que llega de otra institución. Al guardarlo se le asigna hoja de ruta y queda listo para derivar.</p>
    </div>
    <a href="/ventanilla/pendientes" class="rc-btn"><i class="fa fa-clock-o"></i> Pendientes de derivar</a>
</div>

<form class="form" method="post" enctype="multipart/form-data" id="frmCreate" autocomplete="off">
    <div class="rc-dos">
        <div class="rc-card">
            <!-- el recorrido del documento: de quien viene (izquierda) y a quien va (derecha) -->
            <div class="rc-ruta">
                <div class="rc-lado de">
                    <div class="rc-lado-cab">
                        <span class="rc-lado-icono"><i class="fa fa-sign-in"></i></span>
                        <span>
                            <b>Viene de</b>
                            <small>Fuera de la institución</small>
                        </span>
                    </div>
                    <div class="rc-lado-cuerpo">
                        <div class="rc-campo">
                            <label for="institucionrem">Institución que envía</label>
                            <?php echo Form::input('institucionrem', Arr::get($_POST, 'institucionrem', ''), array('id' => 'institucionrem', 'autocomplete' => 'off', 'placeholder' => 'Escriba y elija de la lista')); ?>
                            <div class="rc-sug" id="sug-inst"></div>
                        </div>
                        <div class="rc-campo">
                            <label for="remitente">Remitente <i class="obl">*</i></label>
                            <?php echo Form::input('remitente', Arr::get($_POST, 'remitente', ''), array('id' => 'remitente', 'autocomplete' => 'off', 'placeholder' => 'Quién firma o entrega')); ?>
                            <div class="rc-sug" id="sug-rem"></div>
                            <div class="rc-error" id="err-remitente">Escriba quién envía el documento.</div>
                        </div>
                        <div class="rc-campo" style="margin-bottom:4px">
                            <label for="cargorem">Cargo</label>
                            <?php echo Form::input('cargorem', Arr::get($_POST, 'cargorem', ''), array('id' => 'cargorem', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="rc-ayuda" id="rc-pista-rem">Al elegir de la lista se completan el cargo y la institución.</div>
                    </div>
                </div>

                <div class="rc-flecha"><i class="fa fa-long-arrow-right"></i></div>

                <div class="rc-lado para">
                    <div class="rc-lado-cab">
                        <span class="rc-lado-icono"><i class="fa fa-sign-out"></i></span>
                        <span>
                            <b>Va dirigido a</b>
                            <small>Dentro de la institución</small>
                        </span>
                    </div>
                    <div class="rc-lado-cuerpo">
                        <div class="rc-campo">
                            <label for="destinatario">Destinatario <i class="obl">*</i></label>
                            <?php echo Form::input('destinatario', Arr::get($_POST, 'destinatario', ''), array('id' => 'destinatario', 'autocomplete' => 'off', 'placeholder' => 'Elija de la libreta →')); ?>
                            <div class="rc-sug" id="sug-des"></div>
                            <div class="rc-error" id="err-destinatario">Indique a quién va dirigido.</div>
                        </div>
                        <div class="rc-campo">
                            <label for="cargodes">Cargo</label>
                            <?php echo Form::input('cargodes', Arr::get($_POST, 'cargodes', ''), array('id' => 'cargodes', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="rc-campo" style="margin-bottom:4px">
                            <label for="instituciondes">Institución destinataria</label>
                            <?php echo Form::input('instituciondes', Arr::get($_POST, 'instituciondes', ''), array('id' => 'instituciondes', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="rc-ayuda" id="rc-pista-des">Un clic en la libreta de la derecha llena los tres campos.</div>
                    </div>
                </div>
            </div>

            <div class="rc-seccion">
                <h3><span class="rc-paso"><i class="fa fa-file-text-o"></i></span> El documento</h3>
                <p class="rc-intro">Lo que dice el papel que le entregan.</p>
                <div class="rc-campos">
                    <div class="rc-alerta" id="rc-repetido">
                        <i class="fa fa-exclamation-triangle" style="margin-top:2px"></i>
                        <span id="rc-repetido-txt"></span>
                    </div>
                    <div class="rc-campo">
                        <label for="cite">Cite original <i class="obl">*</i></label>
                        <?php echo Form::input('cite', Arr::get($_POST, 'cite', ''), array('id' => 'cite', 'autocomplete' => 'off')); ?>
                        <div class="rc-ayuda">El número con que viene. Si no trae, escriba <b>S/C</b>.</div>
                    </div>
                    <div class="rc-campo">
                        <label for="motivo">Motivo</label>
                        <?php echo Form::select('motivo', $motivos, '', array('id' => 'motivo')); ?>
                    </div>
                    <div class="rc-campo ancho">
                        <label for="descripcion">Referencia <i class="obl">*</i></label>
                        <textarea id="descripcion" name="descripcion" maxlength="500" placeholder="De qué trata el documento"><?php echo $h(Arr::get($_POST, 'descripcion', '')); ?></textarea>
                        <div class="rc-error" id="err-descripcion">Escriba de qué trata el documento.</div>
                        <div class="rc-ayuda">Es lo que verá el destinatario en su bandeja y lo que permite encontrarlo después. <span id="rc-cuenta"></span></div>
                    </div>
                    <div class="rc-campo">
                        <label for="adjunto">Adjuntos</label>
                        <?php echo Form::input('adjunto', Arr::get($_POST, 'adjunto', ''), array('id' => 'adjunto', 'placeholder' => 'Ej.: 1 CD, 2 planos')); ?>
                    </div>
                    <div class="rc-campo">
                        <label for="hojas">N.º de hojas</label>
                        <input type="number" name="hojas" id="hojas" min="0" step="1" value="<?php echo $h(Arr::get($_POST, 'hojas', '')); ?>">
                    </div>
                </div>
            </div>

            <div class="rc-barra">
                <span class="rc-estado" id="rc-estado">Los campos con <i style="color:#B42318;font-style:normal">*</i> son obligatorios</span>
                <button type="button" class="rc-limpiar" id="rc-limpiar"><i class="fa fa-eraser"></i> Limpiar</button>
                <button type="submit" name="submit" value="Recepcionar Documento" class="btn btn-primary" id="crear">
                    <i class="fa fa-check"></i> Recepcionar documento
                </button>
            </div>
        </div>

        <div class="rc-card">
            <div class="rc-tabs">
                <button type="button" class="rc-tab activa" data-panel="libreta"><i class="fa fa-users"></i> Destinatarios</button>
                <button type="button" class="rc-tab" data-panel="hoy"><i class="fa fa-clock-o"></i> Recibidos hoy (<?php echo count($hoy); ?>)</button>
            </div>

            <div id="panel-libreta">
                <div class="rc-buscar">
                    <i class="fa fa-search"></i>
                    <input type="search" id="rc-q" placeholder="Buscar persona o cargo…" autocomplete="off">
                </div>
                <ul class="rc-libreta" id="rc-libreta"></ul>
            </div>

            <div id="panel-hoy" style="display:none">
                <?php if (!$hoy): ?>
                    <div class="rc-panel-vacio"><i class="fa fa-inbox" style="display:block;font-size:24px;margin-bottom:7px;color:#C4CEDB"></i> Todavía no recepcionó nada hoy.</div>
                <?php else: ?>
                    <ul class="rc-hoy">
                        <?php foreach ($hoy as $d):
                            $sinref = $vacio($d['referencia']);
                            ?>
                            <li>
                                <span class="rc-hora"><?php echo $h($d['hora']); ?></span>
                                <span class="rc-hoy-txt">
                                    <b class="<?php echo $sinref ? 'sin' : ''; ?>"><?php echo $sinref ? 'Sin referencia' : $h($d['referencia']); ?></b>
                                    <small>
                                        <a href="/route/trace/?hr=<?php echo urlencode($d['nur']); ?>" class="rc-hoy-nur"><?php echo $h($d['nur']); ?></a>
                                        · <?php echo $h($d['institucion_remitente'] != '' ? $d['institucion_remitente'] : $d['nombre_remitente']); ?>
                                        <?php if ((int) $d['estado'] === 0): ?>
                                            <span class="rc-etq falta">sin derivar</span>
                                        <?php else: ?>
                                            <span class="rc-etq ok">derivado</span>
                                        <?php endif; ?>
                                    </small>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <input type="hidden" id="oficina" value="<?php echo (int) $oficina; ?>">
</form>

<script>
    $(function () {
        var LIBRETA = <?php echo json_encode($libreta); ?>;
        var INSTITUCIONES = <?php echo json_encode($js_inst); ?>;
        var REMITENTES = <?php echo json_encode($js_rem); ?>;

        function normal(t) {
            return (t || '').toString().toLowerCase()
                    .replace(/[áàä]/g, 'a').replace(/[éèë]/g, 'e').replace(/[íìï]/g, 'i')
                    .replace(/[óòö]/g, 'o').replace(/[úùü]/g, 'u').replace(/ñ/g, 'n');
        }
        function esc(s) {
            return $('<div>').text(s == null ? '' : s).html();
        }
        function resaltar(txt, palabras) {
            var r = esc(txt);
            if (!palabras.length) {
                return r;
            }
            var plano = normal(r), marcas = [];
            palabras.forEach(function (p) {
                var i = plano.indexOf(p);
                while (i > -1) {
                    marcas.push([i, i + p.length]);
                    i = plano.indexOf(p, i + p.length);
                }
            });
            if (!marcas.length) {
                return r;
            }
            marcas.sort(function (a, b) {
                return a[0] - b[0];
            });
            var fin = 0, salida = '';
            marcas.forEach(function (m) {
                if (m[0] < fin) {
                    return;
                }
                salida += r.slice(fin, m[0]) + '<mark>' + r.slice(m[0], m[1]) + '</mark>';
                fin = m[1];
            });
            return salida + r.slice(fin);
        }

        /* ---- sugerencias de institucion y remitente, sacadas de lo ya recibido ---- */
        function montarSugeridor($campo, $caja, buscar, elegir) {
            var sel = -1;
            function cerrar() {
                $caja.hide().empty();
                sel = -1;
            }
            $campo.on('input focus', function () {
                var texto = $.trim($campo.val());
                if (texto.length < 2) {
                    cerrar();
                    return;
                }
                var palabras = normal(texto).split(/\s+/).filter(Boolean);
                var lista = buscar(palabras).slice(0, 8);
                if (!lista.length) {
                    cerrar();
                    return;
                }
                var html = '';
                lista.forEach(function (x, i) {
                    html += '<div data-i="' + i + '">' + x.html + '</div>';
                });
                html += '<div class="rc-sug-pie">Elija uno para no repetir el mismo nombre escrito de otra forma</div>';
                $caja.html(html).show().data('lista', lista);
                sel = -1;
            }).on('keydown', function (e) {
                if (!$caja.is(':visible')) {
                    return;
                }
                var $op = $caja.find('div[data-i]');
                if (e.which === 40 || e.which === 38) {
                    e.preventDefault();
                    sel = e.which === 40 ? Math.min(sel + 1, $op.length - 1) : Math.max(sel - 1, 0);
                    $op.removeClass('sel').eq(sel).addClass('sel');
                } else if (e.which === 13 && sel > -1) {
                    e.preventDefault();
                    elegir($caja.data('lista')[sel]);
                    cerrar();
                } else if (e.which === 27) {
                    cerrar();
                }
            }).on('blur', function () {
                setTimeout(cerrar, 180);
            });
            $caja.on('mousedown', 'div[data-i]', function () {
                elegir($caja.data('lista')[parseInt($(this).data('i'), 10)]);
                cerrar();
            });
        }

        montarSugeridor($('#institucionrem'), $('#sug-inst'), function (palabras) {
            return INSTITUCIONES.filter(function (x) {
                var t = normal(x.v);
                return palabras.every(function (w) {
                    return t.indexOf(w) > -1;
                });
            }).map(function (x) {
                return {
                    valor: x.v,
                    html: '<span class="veces">' + x.n + '</span><b>' + resaltar(x.v, palabras) + '</b>'
                };
            });
        }, function (x) {
            $('#institucionrem').val(x.valor);
            $('#remitente').focus();
        });

        montarSugeridor($('#remitente'), $('#sug-rem'), function (palabras) {
            var inst = normal($('#institucionrem').val());
            return REMITENTES.filter(function (x) {
                var t = normal(x.n);
                return palabras.every(function (w) {
                    return t.indexOf(w) > -1;
                });
            }).sort(function (a, b) {
                // primero los de la institución ya escrita
                var ai = inst && normal(a.i).indexOf(inst) > -1 ? 1 : 0;
                var bi = inst && normal(b.i).indexOf(inst) > -1 ? 1 : 0;
                return (bi - ai) || (b.v - a.v);
            }).map(function (x) {
                return {
                    n: x.n, c: x.c, i: x.i,
                    html: '<b>' + resaltar(x.n, palabras) + '</b><small>' + esc(x.c ? x.c + ' · ' : '') + esc(x.i) + '</small>'
                };
            });
        }, function (x) {
            $('#remitente').val(x.n).removeClass('err');
            $('#err-remitente').hide();
            if (x.c) {
                $('#cargorem').val(x.c);
            }
            if (x.i && $.trim($('#institucionrem').val()) === '') {
                $('#institucionrem').val(x.i);
            }
            $('#cite').focus();
        });

        // el destinatario tambien se puede teclear: sugiere de la libreta
        montarSugeridor($('#destinatario'), $('#sug-des'), function (palabras) {
            return LIBRETA.filter(function (p) {
                var t = normal(p.n + ' ' + p.c);
                return palabras.every(function (w) {
                    return t.indexOf(w) > -1;
                });
            }).map(function (p) {
                return {
                    n: p.n, c: p.c, e: p.e,
                    html: '<b>' + resaltar(p.n, palabras) + '</b><small>' + esc(p.c) + '</small>'
                };
            });
        }, function (p) {
            $('#destinatario').val(p.n).removeClass('err');
            $('#cargodes').val(p.c);
            $('#instituciondes').val(p.e);
            $('#err-destinatario').hide();
            $('#cite').focus();
        });

        /* ---- libreta de destinatarios ---- */
        function pintarLibreta() {
            var palabras = normal($('#rc-q').val()).split(/\s+/).filter(Boolean);
            var lista = LIBRETA.filter(function (p) {
                if (!palabras.length) {
                    return true;
                }
                var texto = normal(p.n + ' ' + p.c + ' ' + p.e);
                return palabras.every(function (w) {
                    return texto.indexOf(w) > -1;
                });
            });
            var html = '';
            lista.slice(0, 200).forEach(function (p) {
                html += '<li class="' + p.g + '"><a href="#" class="destino" data-i="' + LIBRETA.indexOf(p) + '">' +
                        '<span class="fa fa-user"></span>' +
                        '<span><b>' + resaltar(p.n, palabras) + '</b><small>' + resaltar(p.c, palabras) + '</small></span></a></li>';
            });
            if (!lista.length) {
                html = '<li class="vacio">Nadie coincide con la búsqueda.</li>';
            }
            $('#rc-libreta').html(html);
        }
        var t = null;
        $('#rc-q').on('input search', function () {
            clearTimeout(t);
            t = setTimeout(pintarLibreta, 150);
        });
        $('#rc-libreta').on('click', 'a.destino', function (e) {
            e.preventDefault();
            var p = LIBRETA[parseInt($(this).data('i'), 10)];
            if (!p) {
                return;
            }
            $('#destinatario').val(p.n).removeClass('err');
            $('#cargodes').val(p.c);
            $('#instituciondes').val(p.e);
            $('#err-destinatario').hide();
            $('#rc-estado').removeClass('mal').html('Destinatario: <b>' + esc(p.n) + '</b>');
        });
        pintarLibreta();

        $('.rc-tab').on('click', function () {
            $('.rc-tab').removeClass('activa');
            $(this).addClass('activa');
            var p = $(this).data('panel');
            $('#panel-libreta').toggle(p === 'libreta');
            $('#panel-hoy').toggle(p === 'hoy');
        });

        $('#motivo').select2({width: '100%'});

        /* ---- cite: aviso si ya existe ---- */
        $('#cite').on('blur', function () {
            var cite = $.trim($(this).val());
            if (cite === '') {
                $(this).val('S/C');
                return;
            }
            $.ajax({
                type: 'POST',
                data: {cite: cite, oficina: $('#oficina').val()},
                url: '/ajax/vercite',
                dataType: 'json'
            }).done(function (item) {
                if (item && item.error) {
                    $('#cite').addClass('err');
                    $('#rc-repetido-txt').html(esc(item.error) + ' Revise en <a href="/ventanilla/listar?q=' + encodeURIComponent(cite) + '" target="_blank">lo recepcionado</a> antes de volver a registrarlo.');
                    $('#rc-repetido').addClass('ver');
                } else {
                    $('#cite').removeClass('err');
                    $('#rc-repetido').removeClass('ver');
                }
            });
        });

        /* ---- contador de la referencia ---- */
        $('#descripcion').on('input', function () {
            var n = this.value.length;
            $('#rc-cuenta').text(n > 0 ? n + '/500' : '');
        });

        /* ---- validacion ---- */
        $('#frmCreate').on('submit', function (e) {
            var faltan = [];
            $.each(['remitente', 'descripcion', 'destinatario'], function (i, campo) {
                var $c = $('#' + campo);
                if ($.trim($c.val()) === '') {
                    $c.addClass('err');
                    $('#err-' + campo).show();
                    faltan.push(campo);
                } else {
                    $c.removeClass('err');
                    $('#err-' + campo).hide();
                }
            });
            if (faltan.length) {
                e.preventDefault();
                $('#rc-estado').addClass('mal').text('Faltan datos obligatorios: revise los campos marcados.');
                $('#' + faltan[0]).focus();
                return;
            }
            // se deshabilita despues de armar el POST: un boton deshabilitado no viaja
            setTimeout(function () {
                $('#crear').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Recepcionando…');
            }, 0);
        });
        $('#remitente, #descripcion, #destinatario').on('input', function () {
            if ($.trim($(this).val()) !== '') {
                $(this).removeClass('err');
                $('#err-' + this.id).hide();
            }
        });

        $('#rc-limpiar').on('click', function () {
            if (!confirm('¿Borrar lo escrito y empezar de nuevo?')) {
                return;
            }
            $('#frmCreate')[0].reset();
            $('.rc-campo .err').removeClass('err');
            $('.rc-error').hide();
            $('#rc-repetido').removeClass('ver');
            $('#rc-cuenta').text('');
            $('#rc-estado').removeClass('mal').html('Los campos con <i style="color:#B42318;font-style:normal">*</i> son obligatorios');
            $('#institucionrem').focus();
        });

        $('#institucionrem').focus();
    });
</script>
