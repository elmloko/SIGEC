<?php
$foto = file_exists(DOCROOT . 'static/fotos/' . $u->username . '.jpg') ? '/static/fotos/' . $u->username . '.jpg' : '/static/fotos/' . ($u->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
$activo = (int) $u->habilitado === 1;
$rol_actual = '';
foreach ($niveles as $n) {
    if ((int) $n->id === (int) $u->nivel) {
        $rol_actual = $n->nivel;
    }
}
$h = function ($s) {
    return HTML::chars($s);
};
$err = function ($campo) use ($error) {
    return isset($error[$campo]) ? '<div class="ue-error"><i class="fa fa-exclamation-circle"></i> ' . HTML::chars($error[$campo]) . '</div>' : '';
};
$cls = function ($campo) use ($error) {
    return isset($error[$campo]) ? ' con-error' : '';
};
// personas por id (para mostrar al superior actual aunque este de baja o en otra oficina)
$por_id = array();
foreach ($personas as $p) {
    $por_id[(int) $p['id']] = $p;
}
$sup = isset($por_id[(int) $u->superior]) ? $por_id[(int) $u->superior] : NULL;
$js_personas = array();
foreach ($personas as $p) {
    $js_personas[] = array('id' => (int) $p['id'], 'n' => trim($p['nombre']), 'c' => trim($p['cargo']), 'o' => (int) $p['id_oficina'], 'h' => (int) $p['habilitado'], 'j' => (int) $p['dependencia'] === 0 ? 1 : 0);
}
$js_oficinas = array();
foreach ($oficinas as $o) {
    $js_oficinas[] = array('id' => (int) $o['id'], 'e' => (int) $o['id_entidad'], 'n' => trim($o['oficina']), 's' => trim($o['sigla']));
}
$iconos_rol = array(1 => 'fa-shield', 2 => 'fa-user', 3 => 'fa-star', 4 => 'fa-inbox', 5 => 'fa-cogs');
?>
<style>
    .ue-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    .ue-card .btn {
        margin: 0;
    }
    .ue-card .btn .fa {
        margin-right: 5px;
    }
    /* cabecera */
    .ue-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px 18px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .ue-cab img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #E8EFF8;
    }
    .ue-cab-texto {
        flex: 1 1 280px;
        min-width: 0;
    }
    .ue-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .ue-cab p {
        margin: 3px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .ue-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }
    .ue-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 12px;
        font-weight: 600;
    }
    .ue-chip.ok {
        background: #E6F6EC;
        color: #1E7B45;
    }
    .ue-chip.baja {
        background: #FDECEC;
        color: #B42318;
    }
    .ue-chip.gris {
        background: #F1F3F6;
        color: #5b6574;
        font-weight: 500;
    }
    /* distribucion */
    .ue-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 18px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .ue-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    .ue-seccion {
        padding: 18px 22px 8px;
        border-bottom: 1px solid #EEF1F5;
    }
    .ue-seccion:last-of-type {
        border-bottom: 0;
    }
    .ue-seccion h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 14px;
        font-size: 14px;
        font-weight: 700;
        color: #123E73;
    }
    .ue-seccion h3 .fa {
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
    .ue-seccion h3 small {
        font-weight: 400;
        color: #8a94a3;
        font-size: 12px;
    }
    .ue-campos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 4px 16px;
    }
    .ue-campo {
        margin-bottom: 12px;
    }
    .ue-campo.ancho {
        grid-column: 1 / -1;
    }
    .ue-campo label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 600;
        color: #4a5566;
    }
    .ue-campo input[type=text],
    .ue-campo input[type=email],
    .ue-campo select {
        width: 100%;
        height: 38px;
        padding: 6px 11px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13.5px;
        color: #1f2937;
        box-shadow: none;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .ue-campo input:focus,
    .ue-campo select:focus {
        border-color: #1A549A;
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .14);
    }
    .ue-campo.con-error input,
    .ue-campo.con-error select,
    .ue-campo.con-error .select2-selection {
        border-color: #D92D20 !important;
    }
    .ue-ayuda {
        margin-top: 4px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .ue-error {
        margin-top: 4px;
        font-size: 12px;
        color: #B42318;
    }
    .ue-con-boton {
        display: flex;
        gap: 6px;
    }
    .ue-con-boton input {
        flex: 1;
    }
    .ue-con-boton button {
        flex: 0 0 auto;
        height: 38px;
        padding: 0 10px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #F6F8FB;
        color: #1A549A;
        font-size: 12px;
        font-weight: 600;
    }
    .ue-con-boton button:hover {
        background: #EEF3FA;
    }
    /* opciones tipo tarjeta (genero, jefatura, rol) */
    .ue-opciones {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .ue-opcion {
        position: relative;
        margin: 0;
        cursor: pointer;
    }
    .ue-opcion input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .ue-opcion span {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 38px;
        padding: 0 14px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: #4a5566;
        transition: all .15s;
    }
    .ue-opcion input:checked + span {
        border-color: #1A549A;
        background: #EEF3FA;
        color: #123E73;
        font-weight: 700;
        box-shadow: inset 0 0 0 1px #1A549A;
    }
    .ue-opcion input:focus + span {
        box-shadow: 0 0 0 3px rgba(26, 84, 154, .18);
    }
    .ue-roles {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 10px;
    }
    .ue-roles .ue-opcion span {
        height: auto;
        min-height: 62px;
        padding: 10px 12px;
        align-items: flex-start;
    }
    .ue-roles .ue-opcion .fa {
        flex: 0 0 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F1F3F6;
        color: #5b6574;
    }
    .ue-roles .ue-opcion input:checked + span .fa {
        background: #1A549A;
        color: #fff;
    }
    .ue-roles b {
        display: block;
        color: inherit;
        font-size: 13px;
    }
    .ue-roles small {
        display: block;
        margin-top: 1px;
        font-size: 11.5px;
        font-weight: 400;
        color: #8a94a3;
        line-height: 1.3;
    }
    /* aviso dentro del formulario */
    .ue-nota {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 0 0 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 12.5px;
    }
    .ue-nota .fa {
        margin-top: 2px;
    }
    .ue-errores {
        margin: 0 0 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #FDECEC;
        color: #912018;
        font-size: 13px;
    }
    .ue-errores b {
        color: #912018;
    }
    /* barra de guardar */
    .ue-barra {
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
        background: rgba(255, 255, 255, .96);
    }
    .ue-barra .ue-estado {
        flex: 1 1 200px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    .ue-barra .ue-estado.cambios {
        color: #B7791F;
        font-weight: 600;
    }
    .ue-barra .btn-primary {
        background: #1A549A;
        border-color: #1A549A;
        padding: 8px 20px;
        font-weight: 700;
    }
    /* panel lateral */
    .ue-lado h4 {
        margin: 0;
        padding: 14px 18px 10px;
        font-size: 13px;
        font-weight: 700;
        color: #123E73;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .ue-datos {
        margin: 0;
        padding: 0 18px 12px;
        list-style: none;
    }
    .ue-datos li {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px dashed #EEF1F5;
        font-size: 12.5px;
        color: #6b7686;
    }
    .ue-datos li:last-child {
        border-bottom: 0;
    }
    .ue-datos li b {
        color: #1f2937;
        font-weight: 600;
        text-align: right;
    }
    .ue-acciones {
        padding: 0 10px 10px;
    }
    .ue-acciones a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 9px;
        color: #334155;
        font-size: 13px;
        text-decoration: none;
    }
    .ue-acciones a:hover {
        background: #F3F6FA;
        color: #123E73;
    }
    .ue-acciones a .fa {
        width: 18px;
        text-align: center;
        color: #1A549A;
    }
    .ue-acciones a.peligro,
    .ue-acciones a.peligro .fa {
        color: #B42318;
    }
    .ue-acciones a.peligro:hover {
        background: #FDECEC;
    }
    .ue-acciones a.alta,
    .ue-acciones a.alta .fa {
        color: #1E7B45;
    }
    .ue-acciones hr {
        margin: 6px 10px;
        border-color: #EEF1F5;
    }
    .ue-dep {
        margin: 0;
        padding: 0 18px 14px;
        list-style: none;
        max-height: 260px;
        overflow: auto;
    }
    .ue-dep li {
        padding: 6px 0;
        font-size: 12.5px;
        border-bottom: 1px dashed #EEF1F5;
    }
    .ue-dep li:last-child {
        border-bottom: 0;
    }
    .ue-dep a {
        color: #1A549A;
        font-weight: 600;
    }
    .ue-dep span {
        display: block;
        color: #8a94a3;
        font-size: 11.5px;
    }
    .ue-vacio {
        padding: 0 18px 16px;
        font-size: 12.5px;
        color: #8a94a3;
    }
    /* select2 con la misma forma que los demas campos */
    .ue-campo .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #D5DCE6;
        border-radius: 9px;
    }
    .ue-campo .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 11px;
        font-size: 13.5px;
    }
    .ue-campo .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .ue-s2 small {
        display: block;
        color: #8a94a3;
        font-size: 11px;
    }
    .select2-results__option--highlighted .ue-s2 small {
        color: #dbe6f5;
    }
    #ue-aviso {
        display: none;
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 2000;
        padding: 12px 18px;
        border-radius: 10px;
        background: #1E7B45;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .18);
    }
    #ue-aviso.error {
        background: #B42318;
    }
</style>

<div class="ue-card ue-cab">
    <img src="<?php echo $foto; ?>" alt="">
    <div class="ue-cab-texto">
        <h2><?php echo $h($u->nombre); ?></h2>
        <p><?php echo $h($u->cargo); ?><?php echo $oficina_actual ? ' · ' . $h($oficina_actual) : ''; ?></p>
        <div class="ue-chips">
            <span class="ue-chip gris"><i class="fa fa-at"></i> <?php echo $h($u->username); ?></span>
            <?php if ($activo): ?>
                <span class="ue-chip ok"><i class="fa fa-check-circle"></i> Activo</span>
            <?php else: ?>
                <span class="ue-chip baja"><i class="fa fa-ban"></i> De baja</span>
            <?php endif; ?>
            <span class="ue-chip"><i class="fa <?php echo Arr::get($iconos_rol, (int) $u->nivel, 'fa-user'); ?>"></i> <?php echo $h($rol_actual); ?></span>
            <?php if ((int) $u->id === $yo): ?>
                <span class="ue-chip gris"><i class="fa fa-info-circle"></i> Es su propia cuenta</span>
            <?php endif; ?>
        </div>
    </div>
    <a href="/admin/user<?php echo $activo ? '' : '#baja'; ?>" class="btn btn-default-bright"><i class="fa fa-arrow-left"></i> Volver a usuarios</a>
</div>

<div class="ue-grid">
    <form method="post" action="/admin/user/edit/<?php echo (int) $u->id; ?>" class="ue-card" id="ue-form" autocomplete="off">
        <?php if (count($error) > 0): ?>
            <div style="padding:18px 22px 0">
                <div class="ue-errores"><b><i class="fa fa-exclamation-triangle"></i> No se guardó.</b> Revise los campos marcados en rojo.</div>
            </div>
        <?php endif; ?>

        <div class="ue-seccion">
            <h3><i class="fa fa-user"></i> Datos personales</h3>
            <div class="ue-campos">
                <div class="ue-campo ancho<?php echo $cls('nombre'); ?>">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo $h($datos['nombre']); ?>" required>
                    <?php echo $err('nombre'); ?>
                </div>
                <div class="ue-campo<?php echo $cls('cedula_identidad'); ?>">
                    <label for="cedula_identidad">Carnet de identidad</label>
                    <input type="text" name="cedula_identidad" id="cedula_identidad" value="<?php echo $h($datos['cedula_identidad']); ?>">
                </div>
                <div class="ue-campo">
                    <label>Género</label>
                    <div class="ue-opciones">
                        <label class="ue-opcion"><input type="radio" name="genero" value="hombre" <?php echo $datos['genero'] != 'mujer' ? 'checked' : ''; ?>><span><i class="fa fa-male"></i> Hombre</span></label>
                        <label class="ue-opcion"><input type="radio" name="genero" value="mujer" <?php echo $datos['genero'] == 'mujer' ? 'checked' : ''; ?>><span><i class="fa fa-female"></i> Mujer</span></label>
                    </div>
                    <div class="ue-ayuda">Se usa en los documentos (Sr. / Sra.).</div>
                </div>
            </div>
        </div>

        <div class="ue-seccion">
            <h3><i class="fa fa-key"></i> Cuenta <small>— con qué ingresa y cómo firma</small></h3>
            <div class="ue-campos">
                <div class="ue-campo<?php echo $cls('username'); ?>">
                    <label for="username">Usuario</label>
                    <input type="text" name="username" id="username" value="<?php echo $h($datos['username']); ?>" required>
                    <?php echo $err('username'); ?>
                    <div class="ue-ayuda">Cambiarlo cambia el nombre con el que ingresa.</div>
                </div>
                <div class="ue-campo<?php echo $cls('email'); ?>">
                    <label for="email">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="<?php echo $h($datos['email']); ?>" required>
                    <?php echo $err('email'); ?>
                </div>
                <div class="ue-campo<?php echo $cls('mosca'); ?>">
                    <label for="mosca">Rúbrica (mosca)</label>
                    <div class="ue-con-boton">
                        <input type="text" name="mosca" id="mosca" value="<?php echo $h($datos['mosca']); ?>" maxlength="20" style="text-transform:uppercase" required>
                        <button type="button" id="ue-sugerir" title="Iniciales del nombre">Iniciales</button>
                    </div>
                    <?php echo $err('mosca'); ?>
                    <div class="ue-ayuda">Aparece al pie de los documentos que genera.</div>
                </div>
            </div>
        </div>

        <div class="ue-seccion">
            <h3><i class="fa fa-sitemap"></i> Ubicación y jerarquía</h3>
            <div class="ue-campos">
                <div class="ue-campo<?php echo $cls('id_entidad'); ?>" <?php echo count($entidades) < 2 ? 'style="display:none"' : ''; ?>>
                    <label for="id_entidad">Entidad</label>
                    <?php echo Form::select('id_entidad', $entidades, $datos['id_entidad'], array('id' => 'id_entidad')); ?>
                </div>
                <div class="ue-campo<?php echo $cls('id_oficina'); ?>">
                    <label for="id_oficina">Oficina</label>
                    <select name="id_oficina" id="id_oficina"></select>
                    <?php echo $err('id_oficina'); ?>
                </div>
                <div class="ue-campo<?php echo $cls('cargo'); ?>">
                    <label for="cargo">Cargo</label>
                    <input type="text" name="cargo" id="cargo" value="<?php echo $h($datos['cargo']); ?>" required>
                    <?php echo $err('cargo'); ?>
                </div>
                <div class="ue-campo">
                    <label>En su oficina es</label>
                    <div class="ue-opciones">
                        <label class="ue-opcion"><input type="radio" name="dependencia" value="0" <?php echo (int) $datos['dependencia'] === 0 ? 'checked' : ''; ?>><span><i class="fa fa-star"></i> Jefe</span></label>
                        <label class="ue-opcion"><input type="radio" name="dependencia" value="1" <?php echo (int) $datos['dependencia'] !== 0 ? 'checked' : ''; ?>><span><i class="fa fa-user"></i> Personal dependiente</span></label>
                    </div>
                </div>
                <div class="ue-campo ancho<?php echo $cls('superior'); ?>">
                    <label for="superior">Superior inmediato</label>
                    <div id="ue-nota-superior"></div>
                    <select name="superior" id="superior" style="width:100%"></select>
                    <?php echo $err('superior'); ?>
                    <div class="ue-ayuda">A quien le reporta. Aparece automáticamente en su libreta de destinatarios y en las vías de sus documentos.</div>
                </div>
            </div>
        </div>

        <div class="ue-seccion">
            <h3><i class="fa fa-shield"></i> Rol en el sistema</h3>
            <?php echo $err('nivel'); ?>
            <div class="ue-roles">
                <?php foreach ($niveles as $n): ?>
                    <?php // el rol de administrador del panel no se reparte desde aqui: solo se ve en quien ya lo tiene
                    if ((int) $n->id === 5 && (int) $u->nivel !== 5) {
                        continue;
                    } ?>
                    <label class="ue-opcion">
                        <input type="radio" name="nivel" value="<?php echo (int) $n->id; ?>" <?php echo (int) $datos['nivel'] === (int) $n->id ? 'checked' : ''; ?>>
                        <span>
                            <i class="fa <?php echo Arr::get($iconos_rol, (int) $n->id, 'fa-user'); ?>"></i>
                            <span style="display:block;padding:0;border:0;height:auto;background:none;box-shadow:none">
                                <b><?php echo $h($n->nivel); ?></b>
                                <small><?php echo (int) $n->id === 5 ? 'Entra al panel de administración' : $h($n->descripcion); ?></small>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div style="height:12px"></div>
        </div>

        <div class="ue-barra">
            <span class="ue-estado" id="ue-estado">Sin cambios</span>
            <a href="/admin/user<?php echo $activo ? '' : '#baja'; ?>" class="btn btn-default-bright">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="ue-guardar"><i class="fa fa-check"></i> Guardar cambios</button>
        </div>
    </form>

    <div class="ue-lado">
        <div class="ue-card">
            <h4>Actividad</h4>
            <ul class="ue-datos">
                <li>Último ingreso <b><?php echo $u->last_login ? date('d/m/Y H:i', $u->last_login) : 'Nunca'; ?></b></li>
                <li>Ingresos <b><?php echo number_format((int) $u->logins, 0, ',', '.'); ?></b></li>
                <li>Registrado <b><?php echo $u->fecha_creacion ? date('d/m/Y', $u->fecha_creacion) : '—'; ?></b></li>
            </ul>
        </div>
        <div class="ue-card">
            <h4>Acciones</h4>
            <div class="ue-acciones">
                <a href="#" data-accion="documentos"><i class="fa fa-file-text-o"></i> Documentos permitidos</a>
                <a href="#" data-accion="estadisticas"><i class="fa fa-bar-chart"></i> Estadísticas</a>
                <a href="#" data-accion="plazos"><i class="fa fa-clock-o"></i> Asignar plazos</a>
                <a href="#" data-accion="reset"><i class="fa fa-unlock-alt"></i> Restablecer contraseña</a>
                <?php if ((int) $u->id !== $yo): ?>
                    <hr>
                    <?php if ($activo): ?>
                        <a href="#" data-accion="baja" class="peligro"><i class="fa fa-ban"></i> Dar de baja</a>
                    <?php else: ?>
                        <a href="#" data-accion="alta" class="alta"><i class="fa fa-check-circle"></i> Dar de alta</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="ue-card">
            <h4>Le reportan (<?php echo count($dependientes); ?>)</h4>
            <?php if (count($dependientes) == 0): ?>
                <div class="ue-vacio">Nadie tiene a esta persona como superior.</div>
            <?php else: ?>
                <ul class="ue-dep">
                    <?php foreach ($dependientes as $d): ?>
                        <li><a href="/admin/user/edit/<?php echo (int) $d['id']; ?>"><?php echo $h($d['nombre']); ?></a><span><?php echo $h($d['cargo']); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<div id="ue-aviso"></div>

<script>
    $(function () {
        var OFICINAS = <?php echo json_encode($js_oficinas); ?>;
        var PERSONAS = <?php echo json_encode($js_personas); ?>;
        var ID = <?php echo (int) $u->id; ?>;
        var NOMBRE = <?php echo json_encode(trim($u->nombre)); ?>;
        var USUARIO = <?php echo json_encode($u->username); ?>;
        var inicial = {oficina: <?php echo (int) $datos['id_oficina']; ?>, superior: <?php echo (int) $datos['superior']; ?>};
        var $form = $('#ue-form'), $ent = $('#id_entidad'), $of = $('#id_oficina'), $sup = $('#superior');

        function esc(s) {
            return $('<div>').text(s == null ? '' : s).html();
        }
        function persona(id) {
            for (var i = 0; i < PERSONAS.length; i++) if (PERSONAS[i].id === id) return PERSONAS[i];
            return null;
        }
        function aviso(txt, error) {
            $('#ue-aviso').toggleClass('error', !!error).text(txt).stop(true, true).fadeIn(150).delay(3200).fadeOut(300);
        }

        // oficinas de la entidad elegida
        function llenarOficinas(sel) {
            var e = parseInt($ent.val(), 10), html = '';
            OFICINAS.forEach(function (o) {
                if (o.e === e) html += '<option value="' + o.id + '"' + (o.id === sel ? ' selected' : '') + '>' + esc(o.n) + (o.s ? ' (' + esc(o.s) + ')' : '') + '</option>';
            });
            $of.html(html);
        }

        // candidatos a superior: primero su oficina, luego el resto (solo activos)
        function llenarSuperior(sel) {
            var of = parseInt($of.val(), 10), propia = '', otras = '', actual = persona(sel);
            PERSONAS.forEach(function (p) {
                if (!p.h) return;
                var op = '<option value="' + p.id + '" data-c="' + esc(p.c) + '"' + (p.id === sel ? ' selected' : '') + '>' + esc(p.n) + (p.j ? ' ★' : '') + '</option>';
                if (p.o === of) propia += op; else otras += op;
            });
            var html = '<option value="0"' + (sel === 0 ? ' selected' : '') + ' data-c="Es el jefe más alto o no reporta a nadie en el sistema">— Ninguno —</option>';
            if (actual && !actual.h) {
                html += '<optgroup label="Superior actual (de baja)"><option value="' + actual.id + '" selected data-c="Dado de baja: elija a otra persona">' + esc(actual.n) + '</option></optgroup>';
            }
            html += '<optgroup label="De la misma oficina">' + propia + '</optgroup><optgroup label="Otras oficinas">' + otras + '</optgroup>';
            $sup.html(html).trigger('change.select2');
            notaSuperior();
        }

        // jefe de una oficina (para proponerlo al cambiar de oficina)
        function jefeDe(of) {
            for (var i = 0; i < PERSONAS.length; i++) {
                var p = PERSONAS[i];
                if (p.h && p.j && p.o === of) return p.id;
            }
            return 0;
        }

        function notaSuperior() {
            var id = parseInt($sup.val(), 10) || 0, p = persona(id), txt = '';
            if (p && !p.h) txt = 'El superior registrado (<b>' + esc(p.n) + '</b>) está dado de baja. Elija a quién reporta ahora.';
            else if (id === 0 && $('input[name=dependencia]:checked').val() === '1') txt = 'Marcó "Personal dependiente" pero no tiene superior. Elija a su jefe inmediato.';
            $('#ue-nota-superior').html(txt ? '<div class="ue-nota"><i class="fa fa-exclamation-triangle"></i><span>' + txt + '</span></div>' : '');
        }

        function formato(o) {
            if (!o.id) return o.text;
            var c = $(o.element).data('c');
            return $('<div class="ue-s2">').text(o.text).append(c ? $('<small>').text(c) : '');
        }

        llenarOficinas(inicial.oficina);
        llenarSuperior(inicial.superior);
        $ent.select2({width: '100%'});
        $of.select2({width: '100%'});
        $sup.select2({width: '100%', templateResult: formato});

        $ent.on('change', function () {
            llenarOficinas(0);
            $of.trigger('change');
        });
        $of.on('change', function () {
            var of = parseInt($of.val(), 10), s = parseInt($sup.val(), 10) || 0, p = persona(s);
            // si el superior no es de la nueva oficina, se propone al jefe de esa oficina
            if (!p || p.o !== of) s = jefeDe(of);
            llenarSuperior(s);
        });
        $sup.on('change', notaSuperior);
        $('input[name=dependencia]').on('change', notaSuperior);

        // rubrica sugerida con las iniciales del nombre
        $('#ue-sugerir').on('click', function () {
            var ini = $('#nombre').val().trim().split(/\s+/).map(function (w) {
                return w.charAt(0);
            }).join('').toUpperCase();
            if (ini) $('#mosca').val(ini).trigger('input');
        });
        $('#username').on('input', function () {
            this.value = this.value.toLowerCase().replace(/\s+/g, '');
        });

        // cambios sin guardar
        var original = $form.serialize(), enviando = false;
        $form.on('input change', function () {
            var cambio = $form.serialize() !== original;
            $('#ue-estado').toggleClass('cambios', cambio).text(cambio ? 'Hay cambios sin guardar' : 'Sin cambios');
        });
        <?php if (count($error) > 0): ?>
        original = '';
        $form.trigger('change');
        <?php endif; ?>
        $form.on('submit', function () {
            enviando = true;
            $('#ue-guardar').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando…');
        });
        $(window).on('beforeunload', function () {
            if (!enviando && $form.serialize() !== original) return 'Hay cambios sin guardar.';
        });

        // acciones del panel lateral
        $('.ue-acciones').on('click', 'a[data-accion]', function (e) {
            e.preventDefault();
            var accion = $(this).data('accion');
            if (accion === 'documentos') {
                eModal.iframe('/admin/content/userDetalle/' + ID, 'Documentos permitidos: ' + USUARIO);
            } else if (accion === 'estadisticas') {
                eModal.iframe('/admin/content/userStats/' + ID, 'Estadísticas de: ' + USUARIO);
            } else if (accion === 'plazos') {
                eModal.iframe('/admin/otorgar/index/' + ID, 'Usuarios con privilegios: ' + USUARIO);
            } else if (accion === 'reset') {
                if (!confirm('¿Restablecer la contraseña de "' + NOMBRE + '" a la contraseña por defecto?')) return;
                $.post('/admin/ajax/resetPass', {id: ID}, function (r) {
                    aviso(r > 0 ? 'Contraseña restablecida.' : 'No se pudo restablecer la contraseña.', !(r > 0));
                }).fail(function () {
                    aviso('No se pudo restablecer la contraseña.', true);
                });
            } else if (accion === 'baja' || accion === 'alta') {
                var baja = accion === 'baja';
                if (!confirm(baja ? '¿Dar de baja a "' + NOMBRE + '"? Ya no podrá ingresar al sistema.' : '¿Dar de alta a "' + NOMBRE + '"? Podrá volver a ingresar al sistema.')) return;
                $.post('/admin/ajax/' + accion, {id: ID}, function () {
                    enviando = true;
                    location.reload();
                }).fail(function () {
                    aviso('No se pudo completar la acción.', true);
                });
            }
        });
    });
</script>
