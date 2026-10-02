<?php
$datos_usuario_logueado = Auth::instance()->get_user();
$id_oficina_despacho = (string) $datos_usuario_logueado->id_oficina;

// agrupa los submenus de cada menu
$menu = array();
$principales = array();
foreach ($menus as $m) {
    $menu[$m->id][$m->id_submenu] = array('submenu' => $m->submenu, 'accion' => $m->accion);
    if (!isset($principales[$m->id])) {
        $principales[$m->id] = $m;
    }
}

// el menu de reportes queda solo con el Centro de indicadores (sus demas paginas se abren desde sus pestañas);
// los submenus de reportes antiguos de la base se ocultan
foreach ($principales as $mid => $m) {
    if ($m->controlador === 'reports') {
        $menu[$mid] = array(0 => array('submenu' => 'Tablero de indicadores', 'accion' => 'tablero'));
    }
}

// carpetas que se muestran como carpeta aunque les quede un solo submenu
$siempre_carpeta = array();

// administrador: Entidades, Oficinas y Tipos de documento pasan a la carpeta "Configuraciones",
// ubicada justo despues de Administracion (cada submenu guarda su enlace original en 'href')
if ((int) $datos_usuario_logueado->nivel === Model_niveles::NIVEL_ADMIN):
$es_configuracion = function ($m, $v) {
    $href = strtolower('/' . trim($m->controlador, '/') . '/' . trim((string) $v['accion'], '/'));
    $texto = strtolower(trim(html_entity_decode((string) $v['submenu'], ENT_QUOTES, 'UTF-8')));
    return (bool) preg_match('#/(entidades|oficinas|tipos)(/|$)#', $href)
        || (bool) preg_match('/^(entidades|oficinas|tipos? de documentos?)$/u', $texto);
};
$configuracion = array();
$despues_de = NULL;
foreach ($principales as $mid => $m) {
    foreach ($menu[$mid] as $sid => $v) {
        if ($es_configuracion($m, $v)) {
            $v['href'] = '/' . trim($m->controlador, '/') . ($v['accion'] !== NULL && $v['accion'] !== '' ? '/' . $v['accion'] : '/');
            $configuracion[$sid] = $v;
            unset($menu[$mid][$sid]);
            if ($despues_de === NULL) {
                $despues_de = $mid;
            }
            $siempre_carpeta[$mid] = TRUE; // Administracion sigue siendo carpeta aunque le quede una opcion
        }
    }
}
if ($configuracion) {
    $config = (object) array('id' => 'configuraciones', 'menu' => 'Configuraciones', 'controlador' => '', 'logo' => 'fa fa-cogs');
    $siempre_carpeta['configuraciones'] = TRUE;
    $menu['configuraciones'] = $configuracion;
    $posicion = array_search($despues_de, array_keys($principales), TRUE) + 1;
    $principales = array_slice($principales, 0, $posicion, TRUE) + array('configuraciones' => $config) + array_slice($principales, $posicion, NULL, TRUE);
}
// la carpeta "Usuario" del menu principal se oculta para el administrador (sus paginas siguen accesibles por enlace);
// el menu que se queda sin opciones tambien desaparece
foreach ($principales as $mid => $m) {
    if (!$menu[$mid] || preg_match('/^usuarios?$/u', strtolower(trim(html_entity_decode((string) $m->menu, ENT_QUOTES, 'UTF-8'))))) {
        unset($principales[$mid]);
    }
}
endif;

// "Mis indicadores": tablero personal (no esta en la base), solo para el rol usuario;
// va justo encima de Busqueda (si no la tiene, en segundo lugar)
if ((int) $datos_usuario_logueado->nivel === Model_niveles::NIVEL_USUARIO):
$mis_indicadores = (object) array('id' => 'mis-indicadores', 'menu' => 'Mis indicadores', 'controlador' => 'indicadores', 'logo' => 'fa fa-line-chart');
$posicion = 1;
foreach (array_values($principales) as $i => $m) {
    $nombre_menu = strtolower(trim(html_entity_decode((string) $m->menu, ENT_QUOTES, 'UTF-8')));
    $nombre_menu = strtr($nombre_menu, array('ú' => 'u', 'Ú' => 'u'));
    if (in_array(trim($m->controlador, '/'), array('search', 'busqueda'), TRUE) || strpos($nombre_menu, 'busqueda') === 0) {
        $posicion = $i;
        break;
    }
}
$principales = array_slice($principales, 0, $posicion, TRUE) + array('mis-indicadores' => $mis_indicadores) + array_slice($principales, $posicion, NULL, TRUE);
$menu['mis-indicadores'] = array(0 => array('submenu' => 'Mis indicadores', 'accion' => ''));
endif;

// textos con tildes y nombres mas claros (los de la base se mantienen)
$etiquetas = array(
    'Busqueda' => 'Búsqueda',
    'Busqueda Avanzada' => 'Búsqueda avanzada',
    'Busqueda avanzada' => 'Búsqueda avanzada',
    'Entrante' => 'Por recibir',
    'Archivada' => 'Archivo',
);
$etiqueta = function ($t) use ($etiquetas) {
    $t = trim(html_entity_decode((string) $t, ENT_QUOTES, 'UTF-8'));
    return isset($etiquetas[$t]) ? $etiquetas[$t] : $t;
};
// submenus que solo ve despacho (oficina 73)
$solo_despacho = array('Pendientes Despacho', 'Fuera de Plazo', 'Fuera de Plazo (oficina)');

// ruta de un enlace, sin barras ni "index"
$normalizar = function ($ruta) {
    $ruta = trim(strtolower((string) $ruta), '/');
    return preg_replace('#/index$#', '', $ruta);
};
$enlace = function ($m, $accion) {
    return '/' . trim($m->controlador, '/') . ($accion !== NULL && $accion !== '' ? '/' . $accion : '/');
};
// enlace de un submenu (los movidos a Configuraciones conservan el suyo)
$enlace_sub = function ($m, $v) use ($enlace) {
    return isset($v['href']) ? $v['href'] : $enlace($m, $v['accion']);
};
$es_carpeta_de = function ($mid) use (&$menu, &$siempre_carpeta) {
    return count($menu[$mid]) > 1 || isset($siempre_carpeta[$mid]);
};

// pagina actual (documento/* pertenece a "document")
$actual = $normalizar(Request::initial()->uri());
$actual = preg_replace('#^documento(/|$)#', 'document$1', $actual);

// el enlace mas especifico que coincide con la pagina actual queda activo
$mejor = '';
foreach ($principales as $mid => $m) {
    foreach ($menu[$mid] as $v) {
        $r = $normalizar($es_carpeta_de($mid) ? $enlace_sub($m, $v) : $enlace($m, NULL));
        if ($r !== '' && ($actual === $r || strpos($actual . '/', $r . '/') === 0) && strlen($r) > strlen($mejor)) {
            $mejor = $r;
        }
    }
}

// contadores de la bandeja (indice derivado_a + estado)
$contador = DB::query(Database::SELECT, 'SELECT SUM(estado = 1) AS por_recibir, SUM(estado = 2) AS pendientes FROM seguimiento WHERE derivado_a = :u AND estado IN (1, 2)')
        ->param(':u', (int) $datos_usuario_logueado->id)
        ->execute()->current();
$por_recibir = (int) $contador['por_recibir'];
$pendientes = (int) $contador['pendientes'];
$insignia = function ($n, $clase = '') {
    return $n > 0 ? '<span class="mn-badge ' . $clase . '">' . ($n > 99 ? '99+' : $n) . '</span>' : '';
};
?>
<?php foreach ($principales as $mid => $m): ?>
    <?php
    $subs = $menu[$mid];
    $es_carpeta = $es_carpeta_de($mid);
    // Administracion y Configuraciones comparten controlador: se activa la que contiene la pagina actual
    if (isset($siempre_carpeta[$mid])) {
        $carpeta_activa = FALSE;
        foreach ($subs as $v) {
            $carpeta_activa = $carpeta_activa || $normalizar($enlace_sub($m, $v)) === $mejor;
        }
    } else {
        $carpeta_activa = ($controller == $m->controlador);
    }
    $icono = $m->logo ? $m->logo : 'fa fa-circle-o';
    $titulo = $etiqueta($m->menu);
    $total_insignia = ($m->controlador === 'bandeja') ? $por_recibir + $pendientes : 0;
    ?>
    <li class="<?php echo $es_carpeta ? 'gui-folder' : ''; ?>">
        <?php if ($es_carpeta): ?>
            <a href="javascript:;" class="<?php echo $carpeta_activa ? 'active' : ''; ?>">
                <div class="gui-icon"><i class="<?php echo HTML::chars($icono); ?>"></i></div>
                <span class="title"><?php echo HTML::chars($titulo); ?><?php echo $insignia($total_insignia, $por_recibir > 0 ? 'mn-alerta' : ''); ?></span>
            </a>
            <ul>
                <?php
                ksort($subs);
                foreach ($subs as $v):
                    if (in_array(trim($v['submenu']), $solo_despacho, TRUE) && $id_oficina_despacho !== '73') {
                        continue;
                    }
                    $href = $enlace_sub($m, $v);
                    $sin_accion = ($v['accion'] === NULL || $v['accion'] === '');
                    $n = 0;
                    if ($m->controlador === 'bandeja') {
                        $n = $sin_accion ? $por_recibir : ($v['accion'] === 'pendientes' ? $pendientes : 0);
                    }
                    ?>
                    <li>
                        <a href="<?php echo HTML::chars($href); ?>" class="<?php echo $normalizar($href) === $mejor ? 'active' : ''; ?>">
                            <span class="title"><?php echo HTML::chars($etiqueta($v['submenu'])); ?><?php echo $insignia($n, $sin_accion ? 'mn-alerta' : ''); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php $href = $m->controlador === 'reports' ? '/reports/tablero' : '/' . trim($m->controlador, '/'); ?>
            <a href="<?php echo HTML::chars($href); ?>" class="<?php echo ($controller == $m->controlador || $normalizar($href) === $mejor) ? 'active' : ''; ?>">
                <div class="gui-icon"><i class="<?php echo HTML::chars($icono); ?>"></i></div>
                <span class="title"><?php echo HTML::chars($titulo); ?><?php echo $insignia($total_insignia); ?></span>
            </a>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
