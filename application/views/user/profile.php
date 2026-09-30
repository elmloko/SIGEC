<?php
$dir_fotos = DOCROOT . 'static/fotos/';
$foto_de = function ($username, $genero) use ($dir_fotos) {
    $f = $dir_fotos . $username . '.jpg';
    return file_exists($f) ? '/static/fotos/' . $username . '.jpg?v=' . filemtime($f) : '/static/fotos/' . ($genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
};
$tiene_foto = file_exists($dir_fotos . $user->username . '.jpg');
$url_perfil = '/user/profile/' . ($es_propio ? '' : $user->id);
$abrir_recorte = $foto_tmp && isset($_GET['recortar']);
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$kpis = array(
    array('Documentos generados', $actividad['documentos'], 'fa-file-text-o', $es_propio ? '/document' : ''),
    array('Derivaciones realizadas', $actividad['derivaciones'], 'fa-paper-plane', $es_propio ? '/bandeja/enviados' : ''),
    array('Pendientes', $actividad['pendientes'], 'fa-clock-o', $es_propio ? '/bandeja/pendientes' : ''),
    array('Por recibir', $actividad['por_recibir'], 'fa-inbox', $es_propio ? '/bandeja' : ''),
);
?>
<style>
    .pf-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .pf-card .btn {
        margin: 0;
    }
    .pf-card .btn .fa {
        margin-right: 5px;
    }
    .pf-alerta {
        margin-bottom: 14px;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 13px;
    }
    .pf-alerta.ok {
        background: #E6F4EC;
        color: #1E6B3E;
    }
    .pf-alerta.error {
        background: #FDE8E8;
        color: #B42318;
    }

    /* cabecera */
    .pf-banda {
        position: relative;
        height: 110px;
        border-radius: 14px 14px 0 0;
        background: linear-gradient(120deg, var(--correos-azul-oscuro, #123E73) 0%, var(--correos-azul, #1A549A) 60%, #2A6BB8 100%);
        overflow: hidden;
    }
    .pf-banda:after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 5px;
        background: var(--correos-amarillo, #FECB34);
    }
    .pf-banda .pf-sello {
        position: absolute;
        right: 26px;
        top: 50%;
        transform: translateY(-50%);
        height: 64px;
        opacity: .18;
        filter: brightness(0) invert(1);
    }
    .pf-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px 22px;
        padding: 0 26px 20px;
    }
    .pf-foto {
        position: relative;
        flex: 0 0 auto;
        margin-top: -56px;
    }
    .pf-foto img {
        display: block;
        width: 116px;
        height: 116px;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 4px 14px rgba(18, 62, 115, .25);
        object-fit: cover;
        background: #fff;
    }
    .pf-foto-btn {
        position: absolute;
        right: 2px;
        bottom: 6px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 3px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-amarillo, #FECB34);
        color: var(--correos-azul-oscuro, #123E73);
        font-size: 14px;
        cursor: pointer;
        transition: transform .15s;
    }
    .pf-foto-btn:hover {
        transform: scale(1.08);
        text-decoration: none;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pf-quien {
        flex: 1 1 280px;
        min-width: 0;
        padding-bottom: 2px;
    }
    .pf-quien h2 {
        margin: 12px 0 2px;
        font-size: 22px;
        font-weight: 700;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pf-quien .pf-cargo {
        font-size: 14px;
        color: #5b6675;
    }
    .pf-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }
    .pf-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 14px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    .pf-chip .fa {
        color: var(--correos-azul, #1A549A);
    }
    .pf-accesos {
        display: flex;
        gap: 12px;
        padding-bottom: 4px;
    }
    .pf-acceso {
        min-width: 120px;
        padding: 10px 14px;
        border-radius: 12px;
        background: var(--correos-fondo, #F3F5F8);
        text-align: center;
    }
    .pf-acceso b {
        display: block;
        font-size: 20px;
        color: var(--correos-azul, #1A549A);
    }
    .pf-acceso span {
        font-size: 11.5px;
        color: #7a8594;
    }

    /* actividad */
    .pf-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }
    .pf-kpi {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        color: inherit;
        transition: transform .15s, box-shadow .15s;
    }
    a.pf-kpi:hover {
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(18, 62, 115, .16);
    }
    .pf-kpi .fa {
        flex: 0 0 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-azul-suave, #EAF1F9);
        color: var(--correos-azul, #1A549A);
        font-size: 18px;
    }
    .pf-kpi b {
        display: block;
        font-size: 22px;
        line-height: 1.1;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pf-kpi span {
        font-size: 12px;
        color: #7a8594;
    }

    /* columnas */
    .pf-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 400px;
        gap: 18px;
        align-items: start;
    }
    .pf-card-cab {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
    }
    .pf-card-cab h3 {
        margin: 0;
        flex: 1 1 auto;
        font-size: 16px;
        font-weight: 600;
        color: var(--correos-azul-oscuro, #123E73);
    }
    .pf-card-cab h3 .fa {
        margin-right: 6px;
        color: var(--correos-azul, #1A549A);
    }
    .pf-card-cuerpo {
        padding: 18px 20px;
    }

    /* formulario */
    .pf-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 150px;
        gap: 14px 16px;
    }
    .pf-completo {
        grid-column: 1 / -1;
    }
    .pf-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
    }
    .pf-campo input {
        width: 100%;
        height: 40px;
        padding: 6px 12px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 9px;
        font-size: 14px;
        color: #2d3748;
    }
    .pf-campo input:focus {
        outline: none;
        border-color: var(--correos-azul, #1A549A);
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .12);
    }
    .pf-campo small {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: #9aa4b2;
    }
    .pf-fijos {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px dashed var(--correos-borde, #DCE3EC);
    }
    .pf-fijo small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        color: #8a94a3;
    }
    .pf-fijo span {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        word-break: break-word;
    }
    .pf-pie {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid #EEF2F7;
        background: #FAFBFC;
        border-radius: 0 0 14px 14px;
    }
    .pf-pie .pf-nota {
        margin-right: auto;
        font-size: 12px;
        color: #8a94a3;
    }
    .pf-seguridad {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .pf-seguridad > .fa {
        flex: 0 0 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--correos-amarillo-suave, #FFF7DD);
        color: #B7791F;
        font-size: 18px;
    }
    .pf-seguridad div {
        flex: 1 1 auto;
        font-size: 13px;
        color: #5b6675;
    }
    .pf-seguridad b {
        display: block;
        color: #2d3748;
    }

    /* destinatarios */
    .pf-contador {
        padding: 1px 9px;
        border-radius: 11px;
        background: var(--correos-azul, #1A549A);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }
    .pf-buscar {
        position: relative;
        margin: 14px 16px 6px;
    }
    .pf-buscar .fa {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a3;
    }
    .pf-buscar input {
        width: 100%;
        height: 36px;
        padding: 4px 10px 4px 32px;
        border: 1px solid var(--correos-borde, #DCE3EC);
        border-radius: 18px;
        background: var(--correos-fondo, #F3F5F8);
        font-size: 13px;
    }
    .pf-lista {
        max-height: 470px;
        margin: 0;
        padding: 4px 8px 10px;
        overflow-y: auto;
        list-style: none;
    }
    .pf-dest {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        border-radius: 10px;
    }
    .pf-dest:hover {
        background: var(--correos-fondo, #F3F5F8);
    }
    .pf-dest img {
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
    }
    .pf-dest div {
        flex: 1 1 auto;
        min-width: 0;
        line-height: 1.3;
    }
    .pf-dest b {
        display: block;
        font-size: 13px;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pf-dest span {
        display: block;
        font-size: 11px;
        color: #7a8594;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pf-quitar {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #b7c0cc;
        opacity: 0;
        transition: opacity .15s, background .15s, color .15s;
    }
    .pf-dest:hover .pf-quitar,
    .pf-quitar:focus {
        opacity: 1;
    }
    .pf-quitar:hover {
        background: #FDE8E8;
        color: #D32F2F;
        text-decoration: none;
    }
    .pf-vacio {
        padding: 26px 20px;
        text-align: center;
        font-size: 13px;
        color: #9aa4b2;
    }

    /* modales de la foto */
    .pf-modal .modal-content {
        border-radius: 14px;
        overflow: hidden;
    }
    .pf-modal .modal-header {
        background: var(--correos-azul, #1A549A);
        border-bottom: 3px solid var(--correos-amarillo, #FECB34);
        color: #fff;
    }
    .pf-modal .modal-title {
        color: #fff;
    }
    .pf-modal .close {
        color: #fff;
        opacity: .9;
        text-shadow: none;
    }
    .pf-modal .dropzone {
        min-height: 190px;
        margin: 18px;
        border: 2px dashed var(--correos-borde, #DCE3EC);
        border-radius: 12px;
        background: var(--correos-fondo, #F3F5F8);
    }
    .pf-modal .dropzone .dz-message {
        margin: 40px 0;
        font-size: 14px;
        color: #5b6675;
    }
    .pf-modal .dropzone .dz-message .fa {
        display: block;
        margin-bottom: 8px;
        font-size: 34px;
        color: var(--correos-azul, #1A549A);
    }
    .pf-recorte {
        padding: 16px;
        background: #2d3748;
        text-align: center;
    }
    /* Jcrop escala la imagen (boxWidth/boxHeight): sin max-width, que la deformaria */
    .pf-recorte img,
    .pf-recorte .jcrop-holder img {
        max-width: none !important;
        max-height: none !important;
    }
    .pf-recorte .jcrop-holder {
        margin: 0 auto;
    }
    .pf-modal .modal-footer {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pf-modal .modal-footer .pf-nota {
        margin-right: auto;
        font-size: 12px;
        color: #7a8594;
        text-align: left;
    }
    .pf-pendiente {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding: 10px 14px;
        border-radius: 10px;
        background: var(--correos-amarillo-suave, #FFF7DD);
        font-size: 13px;
        color: #8a6100;
    }
    .pf-pendiente .btn {
        margin-left: auto;
    }
    @media (max-width: 1199px) {
        .pf-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .pf-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767px) {
        .pf-form,
        .pf-fijos {
            grid-template-columns: minmax(0, 1fr);
        }
        .pf-accesos {
            width: 100%;
        }
        .pf-acceso {
            flex: 1 1 0;
        }
    }
</style>

<div class="col-lg-12">
    <?php foreach ($info as $e): ?>
        <div class="pf-alerta ok"><i class="fa fa-check-circle"></i> <?php echo HTML::chars($e); ?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $e): ?>
        <div class="pf-alerta error"><i class="fa fa-exclamation-triangle"></i> <?php echo HTML::chars($e); ?></div>
    <?php endforeach; ?>
    <?php if ($foto_tmp && !$abrir_recorte): ?>
        <div class="pf-pendiente"><i class="fa fa-crop"></i> Tiene una foto subida que falta recortar.
            <a href="#" class="btn btn-sm btn-primary pf-abrir-recorte"><i class="fa fa-crop"></i> Recortar ahora</a></div>
    <?php endif; ?>

    <!-- cabecera -->
    <div class="pf-card">
        <div class="pf-banda"></div>
        <div class="pf-cab">
            <div class="pf-foto">
                <img src="<?php echo HTML::chars($foto_de($user->username, $user->genero)); ?>" alt="Foto de <?php echo HTML::chars($user->nombre); ?>"/>
                <a href="#" class="pf-foto-btn pf-abrir-subida" title="<?php echo $tiene_foto ? 'Cambiar foto' : 'Subir foto'; ?>"><i class="fa fa-camera"></i></a>
            </div>
            <div class="pf-quien">
                <h2><?php echo HTML::chars($user->nombre); ?></h2>
                <div class="pf-cargo"><?php echo HTML::chars($user->cargo); ?></div>
                <div class="pf-chips">
                    <span class="pf-chip"><i class="fa fa-user"></i> <?php echo HTML::chars($user->username); ?></span>
                    <?php if ($oficina->loaded()): ?><span class="pf-chip"><i class="fa fa-building-o"></i> <?php echo HTML::chars($oficina->oficina); ?></span><?php endif; ?>
                    <?php if ($user->mosca != ''): ?><span class="pf-chip"><i class="fa fa-tag"></i> Mosca <?php echo HTML::chars($user->mosca); ?></span><?php endif; ?>
                    <span class="pf-chip"><i class="fa fa-envelope-o"></i> <?php echo HTML::chars($user->email); ?></span>
                </div>
            </div>
            <div class="pf-accesos">
                <div class="pf-acceso"><b><?php echo $n($user->logins); ?></b><span>ingresos al sistema</span></div>
                <div class="pf-acceso"><b style="font-size:14px;line-height:26px"><?php echo $user->last_login ? HTML::chars(Date::fuzzy_span($user->last_login)) : '—'; ?></b><span>último ingreso</span></div>
            </div>
        </div>
    </div>

    <!-- actividad -->
    <div class="pf-kpis">
        <?php foreach ($kpis as $k): ?>
            <<?php echo $k[3] ? 'a href="' . $k[3] . '"' : 'div'; ?> class="pf-kpi">
                <i class="fa <?php echo $k[2]; ?>"></i>
                <div><b><?php echo $n($k[1]); ?></b><span><?php echo $k[0]; ?></span></div>
            </<?php echo $k[3] ? 'a' : 'div'; ?>>
        <?php endforeach; ?>
    </div>

    <div class="pf-grid">
        <div>
            <!-- datos -->
            <form method="post" action="<?php echo $url_perfil; ?>" class="pf-card" name="form-datos">
                <div class="pf-card-cab">
                    <h3><i class="fa fa-user"></i><?php echo $es_propio ? 'Mis datos' : 'Datos del usuario'; ?></h3>
                </div>
                <div class="pf-card-cuerpo">
                    <div class="pf-form">
                        <div class="pf-campo">
                            <label for="pf-nombre">Nombre completo</label>
                            <?php echo Form::input('nombre', $user->nombre, array('id' => 'pf-nombre', 'required' => 'required', 'autocomplete' => 'off')); ?>
                        </div>
                        <div class="pf-campo">
                            <label for="pf-mosca">Mosca</label>
                            <?php echo Form::input('mosca', $user->mosca, array('id' => 'pf-mosca', 'maxlength' => '10', 'autocomplete' => 'off')); ?>
                            <small>Iniciales en los documentos</small>
                        </div>
                        <div class="pf-campo pf-completo">
                            <label for="pf-cargo">Cargo</label>
                            <?php echo Form::input('cargo', $user->cargo, array('id' => 'pf-cargo', 'autocomplete' => 'off')); ?>
                            <small>Así aparece como remitente en los documentos que genera.</small>
                        </div>
                    </div>
                    <div class="pf-fijos">
                        <div class="pf-fijo"><small>Usuario</small><span><?php echo HTML::chars($user->username); ?></span></div>
                        <div class="pf-fijo"><small>Correo</small><span><?php echo HTML::chars($user->email); ?></span></div>
                        <div class="pf-fijo"><small>Oficina</small><span><?php echo $oficina->loaded() ? HTML::chars($oficina->oficina) : '—'; ?></span></div>
                    </div>
                </div>
                <div class="pf-pie">
                    <span class="pf-nota">El usuario, el correo y la oficina los cambia el administrador.</span>
                    <button type="submit" name="submit-usuario" value="1" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Guardar cambios</button>
                </div>
            </form>

            <?php if ($es_propio): ?>
                <!-- seguridad -->
                <div class="pf-card">
                    <div class="pf-card-cuerpo pf-seguridad">
                        <i class="fa fa-lock"></i>
                        <div><b>Contraseña</b>Cámbiela periódicamente y no la comparta con nadie.</div>
                        <a href="/user/pass" class="btn btn-default-bright"><i class="fa fa-key"></i> Cambiar contraseña</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- destinatarios -->
        <div class="pf-card">
            <div class="pf-card-cab">
                <h3><i class="fa fa-users"></i>Destinatarios</h3>
                <span class="pf-contador" id="pf-contador"><?php echo count($destinatarios); ?></span>
                <a href="#" class="btn btn-sm btn-primary" id="addDestinatario" rel="<?php echo (int) $user->id; ?>" title="Agregar destinatario"><i class="fa fa-user-plus"></i> Agregar</a>
            </div>
            <?php if (count($destinatarios)): ?>
                <div class="pf-buscar">
                    <i class="fa fa-search"></i>
                    <input type="search" id="pf-buscar" placeholder="Buscar por nombre, cargo u oficina…" autocomplete="off"/>
                </div>
                <ul class="pf-lista" id="pf-lista">
                    <?php foreach ($destinatarios as $d): ?>
                        <li class="pf-dest">
                            <img src="<?php echo HTML::chars($foto_de($d['username'], $d['genero'])); ?>" alt=""/>
                            <div>
                                <b><?php echo HTML::chars($d['nombre']); ?></b>
                                <span><?php echo HTML::chars($d['cargo']); ?></span>
                                <?php if (!empty($d['oficina'])): ?><span><?php echo HTML::chars($d['oficina']); ?></span><?php endif; ?>
                            </div>
                            <a href="/user/xdes/?id_des=<?php echo (int) $d['id']; ?>&amp;id_user=<?php echo (int) $user->id; ?>" class="pf-quitar delDestinatario"
                               rel="<?php echo HTML::chars($d['nombre']); ?>" title="Quitar de la lista"><i class="fa fa-times"></i></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="pf-vacio" id="pf-sin-resultados" style="display:none">Nadie coincide con la búsqueda.</div>
            <?php else: ?>
                <div class="pf-vacio"><i class="fa fa-users" style="display:block;font-size:28px;margin-bottom:6px;color:#c5ccd6"></i>
                    Todavía no tiene destinatarios. Agréguelos para elegirlos rápido al generar documentos.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- subir foto -->
<div id="pf-modal-subir" class="modal fade pf-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button" aria-label="Cerrar">&times;</button>
                <h4 class="modal-title"><i class="fa fa-camera"></i> <?php echo $tiene_foto ? 'Cambiar foto de perfil' : 'Subir foto de perfil'; ?></h4>
            </div>
            <form action="/user/subirfoto" class="dropzone" id="pf-dropzone">
                <input type="hidden" name="idp" value="<?php echo (int) $user->id; ?>"/>
                <div class="dz-message"><i class="fa fa-cloud-upload"></i><b>Arrastre su foto aquí</b> o haga clic para elegirla<br/><small>Formato JPG, hasta 5 MB. Después podrá recortarla.</small></div>
            </form>
        </div>
    </div>
</div>

<!-- recortar foto -->
<?php if ($foto_tmp): ?>
    <div id="pf-modal-recorte" class="modal fade pf-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" type="button" aria-label="Cerrar">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-crop"></i> Recortar foto</h4>
                </div>
                <div class="pf-recorte">
                    <img src="/static/fotos/tmp/<?php echo HTML::chars($user->username); ?>.jpg?v=<?php echo @filemtime($dir_fotos . 'tmp/' . $user->username . '.jpg'); ?>" id="cropbox" alt=""/>
                </div>
                <form method="post" action="<?php echo $url_perfil; ?>" class="modal-footer" id="coords">
                    <input type="hidden" id="x1" name="x1"/>
                    <input type="hidden" id="y1" name="y1"/>
                    <input type="hidden" id="w" name="w"/>
                    <input type="hidden" id="h" name="h"/>
                    <span class="pf-nota">Arrastre el recuadro para elegir la parte de la foto que se verá (siempre cuadrada).</span>
                    <button type="button" class="btn btn-default-bright" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="scrop" value="1" class="btn btn-primary"><i class="fa fa-check"></i> Guardar foto</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    if (window.Dropzone) {
        Dropzone.autoDiscover = false;
    }
    $(function () {
        function abrirModal(id) {
            // los modales van al body para quedar encima de todo
            $(id).appendTo('body').modal('show');
        }

        // subir foto
        $('.pf-abrir-subida').on('click', function (e) {
            e.preventDefault();
            abrirModal('#pf-modal-subir');
        });
        if (window.Dropzone && $('#pf-dropzone').length) {
            new Dropzone('#pf-dropzone', {
                paramName: 'file',
                maxFilesize: 5,
                maxFiles: 1,
                acceptedFiles: '.jpg,.jpeg,image/jpeg',
                dictInvalidFileType: 'Solo se permiten fotos JPG.',
                dictFileTooBig: 'La foto supera los 5 MB.',
                success: function () {
                    // al recargar se abre el recorte
                    window.location.href = '<?php echo $url_perfil; ?>' + '?recortar=1';
                },
                error: function (file, msg) {
                    var texto = (msg && msg.error) ? msg.error : msg;
                    $(file.previewElement).find('.dz-error-message span').text(texto);
                    $(file.previewElement).addClass('dz-error');
                }
            });
        }

        // recortar
        var $crop = $('#cropbox');
        function iniciarRecorte() {
            if (!$crop.length || !$.fn.Jcrop || $crop.data('iniciado')) {
                return;
            }
            $crop.data('iniciado', 1);
            var img = $crop[0];
            var ancho = img.naturalWidth, alto = img.naturalHeight, lado = Math.min(ancho, alto);
            var guardar = function (c) {
                $('#x1').val(c.x);
                $('#y1').val(c.y);
                $('#w').val(c.w);
                $('#h').val(c.h);
            };
            $crop.Jcrop({
                aspectRatio: 1,
                // Jcrop reduce la imagen para que entre en el modal y devuelve coordenadas en pixeles reales
                boxWidth: $('.pf-recorte').width(),
                boxHeight: Math.round(window.innerHeight * 0.6),
                setSelect: [(ancho - lado) / 2, (alto - lado) / 2, (ancho + lado) / 2, (alto + lado) / 2],
                minSize: [40, 40],
                onChange: guardar,
                onSelect: guardar
            });
        }
        function abrirRecorte() {
            abrirModal('#pf-modal-recorte');
            $('#pf-modal-recorte').one('shown.bs.modal', function () {
                if ($crop[0].complete) {
                    iniciarRecorte();
                } else {
                    $crop.one('load', iniciarRecorte);
                }
            });
        }
        $('.pf-abrir-recorte').on('click', function (e) {
            e.preventDefault();
            abrirRecorte();
        });
        <?php if ($abrir_recorte): ?>
        abrirRecorte();
        <?php endif; ?>

        // destinatarios
        $('#addDestinatario').on('click', function (e) {
            e.preventDefault();
            eModal.iframe('/content/destinos/' + $(this).attr('rel'), 'Agregar destinatario');
        });
        $('a.delDestinatario').on('click', function () {
            return confirm('¿Quitar de su lista de destinatarios a:\n' + $(this).attr('rel') + '?');
        });
        $('#pf-buscar').on('keyup search', function () {
            var t = $.trim($(this).val().toLowerCase()), visibles = 0;
            $('#pf-lista li').each(function () {
                var ok = $(this).text().toLowerCase().indexOf(t) > -1;
                $(this).toggle(ok);
                visibles += ok ? 1 : 0;
            });
            $('#pf-sin-resultados').toggle(visibles === 0);
        });
    });
</script>
