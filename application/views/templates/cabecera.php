<?php
/*
 * Barra superior (layout.php y layout_admin.php).
 * Variables: $usuario (usuario de la sesion), $titulo (titulo de la pagina, opcional).
 */
$foto = file_exists(DOCROOT . 'static/fotos/' . $usuario->username . '.jpg')
    ? '/static/fotos/' . $usuario->username . '.jpg?' . @filemtime(DOCROOT . 'static/fotos/' . $usuario->username . '.jpg')
    : '/static/fotos/' . $usuario->genero . '.jpg';

// titulo de la pagina actual (algunos controladores lo arman con etiquetas)
$titulo_pagina = isset($titulo) ? trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string) $titulo, ENT_QUOTES, 'UTF-8')))) : '';
$titulo_pagina = trim($titulo_pagina, " /|");
// "Correspondencia / Pendientes" -> "Correspondencia › Pendientes" (sin tocar AGBC/2026-01711)
$titulo_pagina = preg_replace('#\s+/\s*|\s*/\s+#', ' › ', $titulo_pagina);

// correspondencia por recibir y pendientes (indice derivado_a + estado)
$cnt = DB::query(Database::SELECT, 'SELECT SUM(estado = 1) AS por_recibir, SUM(estado = 2) AS pendientes FROM seguimiento WHERE derivado_a = :u AND estado IN (1, 2)')
        ->param(':u', (int) $usuario->id)->execute()->current();
$por_recibir = (int) $cnt['por_recibir'];
$pendientes = (int) $cnt['pendientes'];

$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$hoy = $dias[date('w')] . ', ' . date('j') . ' de ' . $meses[(int) date('n')];
?>
<header id="header" class="cb-header">
    <div class="headerbar">
        <div class="headerbar-left">
            <ul class="header-nav header-nav-options">
                <li>
                    <a class="btn btn-icon-toggle menubar-toggle cb-toggle" data-toggle="menubar" href="javascript:void(0);" title="Mostrar / ocultar menú">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
                <li class="header-nav-brand">
                    <div class="brand-holder cb-marca">
                        <?php if (!empty($admin)): ?>
                            <a href="/admin" title="Inicio de administración">
                                <b>ADMINISTRACIÓN</b>
                                <small>SIGEC · Correos de Bolivia</small>
                            </a>
                        <?php else: ?>
                            <a href="/" title="Ir al inicio">
                                <b>CORRESPONDENCIA</b>
                                <small>Correos de Bolivia</small>
                            </a>
                        <?php endif; ?>
                        <?php if ($titulo_pagina !== ''): ?>
                            <span class="cb-pagina" title="<?php echo HTML::chars($titulo_pagina); ?>"><?php echo HTML::chars($titulo_pagina); ?></span>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>

        <div class="headerbar-right">
            <!-- busqueda rapida -->
            <form action="/search/rapida" method="get" class="cb-buscar" role="search" autocomplete="off">
                <i class="fa fa-search"></i>
                <input type="search" name="q" id="cb-q" placeholder="Hoja de ruta, cite o referencia…" aria-label="Buscar"/>
                <kbd title="Presione / para buscar">/</kbd>
            </form>

            <span class="cb-fecha hidden-xs"><i class="fa fa-calendar"></i> <?php echo $hoy; ?></span>

            <!-- por recibir -->
            <a href="/bandeja" class="cb-aviso<?php echo $por_recibir > 0 ? ' cb-aviso-activo' : ''; ?>"
               title="<?php echo $por_recibir; ?> por recibir · <?php echo $pendientes; ?> pendientes">
                <i class="fa fa-bell"></i>
                <?php if ($por_recibir > 0): ?><span class="cb-aviso-n"><?php echo $por_recibir > 99 ? '99+' : $por_recibir; ?></span><?php endif; ?>
            </a>

            <!-- perfil -->
            <ul class="header-nav header-nav-profile">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle ink-reaction cb-perfil" data-toggle="dropdown">
                        <img src="<?php echo HTML::chars($foto); ?>" alt=""/>
                        <span class="profile-info">
                            <?php echo HTML::chars($usuario->nombre); ?>
                            <small><?php echo HTML::chars($usuario->cargo != '' ? $usuario->cargo : $usuario->email); ?></small>
                        </span>
                        <i class="fa fa-angle-down cb-flecha"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right animation-dock cb-menu">
                        <li class="cb-menu-cab">
                            <img src="<?php echo HTML::chars($foto); ?>" alt=""/>
                            <div>
                                <b><?php echo HTML::chars($usuario->nombre); ?></b>
                                <span><?php echo HTML::chars($usuario->email); ?></span>
                            </div>
                        </li>
                        <li class="cb-menu-resumen">
                            <a href="/bandeja"><b><?php echo $por_recibir; ?></b> por recibir</a>
                            <a href="/bandeja/pendientes"><b><?php echo $pendientes; ?></b> pendientes</a>
                        </li>
                        <li><a href="/user/profile"><i class="fa fa-fw fa-user"></i> Mi perfil</a></li>
                        <li><a href="/user/destinatarios"><i class="fa fa-fw fa-users"></i> Mis destinatarios</a></li>
                        <li><a href="/user/pass"><i class="fa fa-fw fa-unlock-alt"></i> Cambiar contraseña</a></li>
                        <li class="divider"></li>
                        <li><a href="/user/logout" class="cb-salir"><i class="fa fa-fw fa-power-off"></i> Cerrar sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>
<script>
    // "/" enfoca la busqueda rapida (salvo que se este escribiendo en otro campo)
    document.addEventListener('keydown', function (e) {
        var t = e.target, tag = (t.tagName || '').toLowerCase();
        if (e.key === '/' && tag !== 'input' && tag !== 'textarea' && tag !== 'select' && !t.isContentEditable) {
            var q = document.getElementById('cb-q');
            if (q) {
                e.preventDefault();
                q.focus();
            }
        }
    });
</script>
