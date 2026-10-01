<?php
$h = function ($s) {
    return HTML::chars($s);
};
$n = function ($v) {
    return number_format((int) $v, 0, ',', '.');
};
$pendientes = (int) $cuentas['pendientes'];
$foto = file_exists(DOCROOT . 'static/fotos/' . $user->username . '.jpg') ? '/static/fotos/' . $user->username . '.jpg' : '/static/fotos/' . ($user->genero == 'mujer' ? 'mujer' : 'hombre') . '.jpg';
?>
<style>
    .vt-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(18, 62, 115, .12);
        margin-bottom: 18px;
    }
    /* cabecera */
    .vt-cab {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px 18px;
        padding: 18px 22px;
        border-top: 4px solid #FECB34;
        border-radius: 14px;
    }
    .vt-cab img {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #E8EFF8;
    }
    .vt-cab-texto {
        flex: 1 1 260px;
        min-width: 0;
    }
    .vt-cab h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #123E73;
    }
    .vt-cab p {
        margin: 2px 0 0;
        font-size: 13px;
        color: #6b7686;
    }
    .vt-recibir {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 20px;
        border-radius: 10px;
        background: #1A549A;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }
    .vt-recibir:hover {
        background: #123E73;
        color: #fff;
        text-decoration: none;
    }
    /* titulo de bloque */
    .vt-seccion {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0 2px 10px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #8a94a3;
    }
    /* tarjetas */
    .vt-grilla {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .vt-kpi {
        display: block;
        padding: 14px 16px;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(18, 62, 115, .1);
        text-decoration: none;
        transition: transform .12s, box-shadow .12s;
    }
    .vt-kpi:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(18, 62, 115, .16);
        text-decoration: none;
    }
    .vt-kpi-cab {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .vt-kpi-icono {
        flex: 0 0 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    .vt-kpi-cab b {
        font-size: 26px;
        font-weight: 600;
        color: #123E73;
        line-height: 1;
    }
    .vt-kpi-nombre {
        display: block;
        margin-top: 8px;
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .vt-kpi-pie {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        color: #8a94a3;
    }
    .vt-kpi-ver {
        display: block;
        margin-top: 8px;
        font-size: 11.5px;
        font-weight: 600;
        color: #1A549A;
        opacity: 0;
        transition: opacity .12s;
    }
    .vt-kpi:hover .vt-kpi-ver {
        opacity: 1;
    }
    .vt-kpi.urgente {
        box-shadow: 0 1px 3px rgba(180, 35, 24, .2);
        border-top: 3px solid #B42318;
    }
    /* accesos */
    .vt-accesos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px;
        padding: 0 0 4px;
    }
    .vt-acceso {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 12px 14px;
        border: 1px solid #E3E8EF;
        border-radius: 11px;
        background: #fff;
        text-decoration: none;
        transition: border-color .12s, background .12s;
    }
    .vt-acceso:hover {
        border-color: #B9C8DC;
        background: #F7FAFD;
        text-decoration: none;
    }
    .vt-acceso .fa,
    .vt-acceso .md {
        flex: 0 0 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EEF3FA;
        color: #1A549A;
        font-size: 14px;
    }
    .vt-acceso b {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #1f2937;
    }
    .vt-acceso small {
        display: block;
        margin-top: 1px;
        font-size: 11.5px;
        color: #8a94a3;
        line-height: 1.35;
    }
    .vt-aviso {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #FFF7DD;
        color: #7A5A00;
        font-size: 13px;
    }
    .vt-aviso a {
        color: #7A5A00;
        text-decoration: underline;
        font-weight: 600;
    }
</style>

<div class="vt-card vt-cab">
    <img src="<?php echo $foto; ?>" alt="">
    <div class="vt-cab-texto">
        <h2><?php echo $h($user->nombre); ?></h2>
        <p><?php echo $h($user->cargo); ?><?php echo $oficina ? ' · ' . $h($oficina) : ''; ?></p>
    </div>
    <a href="/ventanilla" class="vt-recibir"><i class="fa fa-inbox"></i> Recepcionar correspondencia</a>
</div>

<?php if ($antiguo > 7 AND $pendientes > 0): ?>
    <div class="vt-aviso">
        <i class="fa fa-exclamation-triangle" style="margin-top:2px"></i>
        <span>Hay correspondencia recibida hace <b><?php echo $antiguo; ?> días</b> que todavía no se deriva.
            <a href="/ventanilla/pendientes">Ver los pendientes</a>.</span>
    </div>
<?php endif; ?>

<div class="vt-seccion"><i class="fa fa-inbox"></i> Su trabajo</div>
<div class="vt-grilla">
    <a href="/ventanilla/pendientes" class="vt-kpi<?php echo $pendientes > 0 ? ' urgente' : ''; ?>">
        <span class="vt-kpi-cab">
            <span class="vt-kpi-icono" style="background:#FDE8E8;color:#B42318"><i class="fa fa-clock-o"></i></span>
            <b><?php echo $n($pendientes); ?></b>
        </span>
        <span class="vt-kpi-nombre">Por derivar</span>
        <span class="vt-kpi-pie">Recibidos y todavía sin enviar</span>
        <span class="vt-kpi-ver"><?php echo $pendientes > 0 ? 'Ver la lista →' : 'Todo al día'; ?></span>
    </a>
    <a href="/ventanilla/listar?estado=1" class="vt-kpi">
        <span class="vt-kpi-cab">
            <span class="vt-kpi-icono" style="background:#E6F4EC;color:#227547"><i class="fa fa-paper-plane"></i></span>
            <b><?php echo $n($cuentas['derivados']); ?></b>
        </span>
        <span class="vt-kpi-nombre">Derivados</span>
        <span class="vt-kpi-pie">Ya enviados a su destinatario</span>
        <span class="vt-kpi-ver">Ver la lista →</span>
    </a>
    <a href="/ventanilla/listar" class="vt-kpi">
        <span class="vt-kpi-cab">
            <span class="vt-kpi-icono" style="background:#EEF3FA;color:#1A549A"><i class="fa fa-files-o"></i></span>
            <b><?php echo $n($cuentas['total']); ?></b>
        </span>
        <span class="vt-kpi-nombre">Recepcionados</span>
        <span class="vt-kpi-pie">Todo lo registrado desde esta ventanilla</span>
        <span class="vt-kpi-ver">Ver la lista →</span>
    </a>
    <span class="vt-kpi" style="cursor:default">
        <span class="vt-kpi-cab">
            <span class="vt-kpi-icono" style="background:#FFF3DC;color:#B26B00"><i class="fa fa-calendar"></i></span>
            <b><?php echo $n($cuentas['hoy']); ?></b>
        </span>
        <span class="vt-kpi-nombre">Recibidos hoy</span>
        <span class="vt-kpi-pie">Registrados en el día de hoy</span>
    </span>
</div>

<div class="vt-seccion"><i class="fa fa-th-large"></i> Accesos</div>
<div class="vt-card" style="padding:16px 18px">
    <div class="vt-accesos">
        <a href="/ventanilla" class="vt-acceso">
            <i class="fa fa-inbox"></i>
            <span>
                <b>Recepcionar</b>
                <small>Registrar un documento que llega a ventanilla.</small>
            </span>
        </a>
        <a href="/ventanilla/pendientes" class="vt-acceso">
            <i class="fa fa-clock-o"></i>
            <span>
                <b>Pendientes de derivar</b>
                <small>Lo recibido que todavía no se envió.</small>
            </span>
        </a>
        <a href="/derivados" class="vt-acceso">
            <i class="fa fa-paper-plane"></i>
            <span>
                <b>Derivados</b>
                <small>Seguir lo que ya se envió.</small>
            </span>
        </a>
        <a href="/search/advanced" class="vt-acceso">
            <i class="fa fa-search"></i>
            <span>
                <b>Buscar</b>
                <small>Encontrar por hoja de ruta, cite o referencia.</small>
            </span>
        </a>
        <a href="/user/profile" class="vt-acceso">
            <i class="fa fa-user"></i>
            <span>
                <b>Mi perfil</b>
                <small>Sus datos y su foto.</small>
            </span>
        </a>
        <a href="/user/pass" class="vt-acceso">
            <i class="fa fa-lock"></i>
            <span>
                <b>Cambiar contraseña</b>
                <small>Su contraseña de acceso al sistema.</small>
            </span>
        </a>
    </div>
</div>
