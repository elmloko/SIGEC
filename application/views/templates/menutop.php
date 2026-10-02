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

// "Mis indicadores": tablero personal, disponible para todos los usuarios (no esta en la base);
// va justo encima de Busqueda; si el usuario no tiene Busqueda, en segundo lugar
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

// pagina actual (documento/* pertenece a "document")
$actual = $normalizar(Request::initial()->uri());
$actual = preg_replace('#^documento(/|$)#', 'document$1', $actual);

// el enlace mas especifico que coincide con la pagina actual queda activo
$mejor = '';
foreach ($principales as $mid => $m) {
    foreach ($menu[$mid] as $v) {
        $r = $normalizar($enlace($m, count($menu[$mid]) > 1 ? $v['accion'] : NULL));
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
    $es_carpeta = count($subs) > 1;
    $icono = $m->logo ? $m->logo : 'fa fa-circle-o';
    $titulo = $etiqueta($m->menu);
    $total_insignia = ($m->controlador === 'bandeja') ? $por_recibir + $pendientes : 0;
    ?>
    <li class="<?php echo $es_carpeta ? 'gui-folder' : ''; ?>">
        <?php if ($es_carpeta): ?>
            <a href="javascript:;" class="<?php echo ($controller == $m->controlador) ? 'active' : ''; ?>">
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
                    $href = $enlace($m, $v['accion']);
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
