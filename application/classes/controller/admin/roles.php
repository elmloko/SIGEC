<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Configuraciones > Roles y permisos: que puede hacer cada rol (nivel) y que pestanas ve.
 *  - permisos: tabla nivel_permisos (ver clase Permisos);
 *  - pestanas: tabla nivelmenu (la pestana Reportes depende del permiso "ver_reportes").
 * Solo el administrador (Controller_AdminTemplate exige nivel 5).
 */
class Controller_Admin_Roles extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        parent::after();
    }

    /** Roles de la tabla niveles, con cuantas personas tiene cada uno. */
    private function niveles()
    {
        return DB::query(Database::SELECT, 'SELECT n.id, n.nivel, n.descripcion,
                    (SELECT COUNT(*) FROM users u WHERE u.nivel = n.id AND u.habilitado = 1) AS activos,
                    (SELECT COUNT(*) FROM users u WHERE u.nivel = n.id) AS total
                FROM niveles n ORDER BY n.id')
                ->execute()->as_array();
    }

    /** Menus de la base; los de reportes no se editan aqui (dependen del permiso "ver_reportes"). */
    private function menus_editables()
    {
        $menus = DB::query(Database::SELECT, "SELECT m.id, m.menu, m.controlador, m.logo, m.index,
                    (SELECT COUNT(*) FROM submenus s WHERE s.id_menu = m.id AND s.habilitado = '1') AS opciones,
                    (SELECT GROUP_CONCAT(s.submenu ORDER BY s.id SEPARATOR ', ') FROM submenus s WHERE s.id_menu = m.id AND s.habilitado = '1') AS lista
                FROM menus m ORDER BY m.index, m.id")
                ->execute()->as_array();
        foreach ($menus as &$m) {
            $m['menu'] = html_entity_decode((string) $m['menu'], ENT_QUOTES, 'UTF-8');
            $m['reportes'] = trim((string) $m['controlador']) === 'reports';
        }
        return $menus;
    }

    /** Pestanas asignadas: [id_nivel][id_menu] = TRUE */
    private function asignados()
    {
        $asignados = array();
        foreach (DB::query(Database::SELECT, 'SELECT id_nivel, id_menu FROM nivelmenu')->execute() as $f) {
            $asignados[(int) $f['id_nivel']][(int) $f['id_menu']] = TRUE;
        }
        return $asignados;
    }

    public function action_index()
    {
        $niveles = $this->niveles();
        $permisos = Permisos::lista();
        $menus = $this->menus_editables();
        $tabla_lista = Permisos::tabla_lista();
        $error = '';

        if ($this->request->method() === Request::POST) {
            if (!$tabla_lista) {
                $error = 'Todavía no existe la tabla <b>nivel_permisos</b> en la base de datos; pida al área de sistemas que la cree.';
            } else {
                $error = $this->guardar($niveles, $permisos, $menus);
                if ($error === '') {
                    $this->request->redirect('/admin/roles');
                }
            }
        }

        $valores = array();
        foreach ($niveles as $n) {
            foreach (array_keys($permisos) as $clave) {
                $valores[$n['id']][$clave] = Permisos::tiene($n['id'], $clave);
            }
        }

        $this->template->title .= ' / Roles y permisos';
        $this->template->titulo .= 'Roles y permisos';
        $this->template->descripcion .= 'Qué puede ver, descargar y usar cada rol';
        $this->template->content = View::factory('admin/config/roles')
                ->set('niveles', $niveles)
                ->set('permisos', $permisos)
                ->set('valores', $valores)
                ->set('menus', $menus)
                ->set('asignados', $this->asignados())
                ->set('tabla_lista', $tabla_lista)
                ->set('error', $error)
                ->set('mensaje', Session::instance()->get_once('roles_mensaje'))
                ->set('nivel_propio', (int) $this->user->nivel);
    }

    /** Guarda permisos y pestanas de todos los roles; devuelve '' o el mensaje de error. */
    private function guardar($niveles, $permisos, $menus)
    {
        $post_permisos = (array) Arr::get($_POST, 'permiso', array());
        $post_menus = (array) Arr::get($_POST, 'menu', array());
        $editables = array();
        $nombres_menu = array();
        foreach ($menus as $m) {
            if (!$m['reportes']) {
                $editables[] = (int) $m['id'];
                $nombres_menu[(int) $m['id']] = $m['menu'];
            }
        }
        $antes = $this->asignados();
        $cambios = array();

        $db = Database::instance();
        $db->begin();
        try {
            foreach ($niveles as $n) {
                $id = (int) $n['id'];
                $marcados = isset($post_permisos[$id]) ? (array) $post_permisos[$id] : array();
                $nuevos = array();
                $detalle = array();
                foreach ($permisos as $clave => $p) {
                    $nuevos[$clave] = !empty($marcados[$clave]);
                    if ($nuevos[$clave] !== Permisos::tiene($id, $clave)) {
                        $detalle[] = ($nuevos[$clave] ? '+' : '−') . $p[0];
                    }
                }
                Permisos::guardar($id, $nuevos, $this->user->id);

                $elegidos = array();
                foreach (isset($post_menus[$id]) ? (array) $post_menus[$id] : array() as $id_menu => $v) {
                    if (in_array((int) $id_menu, $editables, TRUE)) {
                        $elegidos[] = (int) $id_menu;
                    }
                }
                foreach ($editables as $id_menu) {
                    $tenia = !empty($antes[$id][$id_menu]);
                    $tiene = in_array($id_menu, $elegidos, TRUE);
                    if ($tenia !== $tiene) {
                        $detalle[] = ($tiene ? '+' : '−') . 'pestaña ' . $nombres_menu[$id_menu];
                    }
                }
                DB::query(Database::DELETE, 'DELETE FROM nivelmenu WHERE id_nivel = :n AND id_menu IN :m')
                        ->param(':n', $id)
                        ->param(':m', $editables)
                        ->execute();
                foreach ($elegidos as $id_menu) {
                    DB::query(Database::INSERT, 'INSERT INTO nivelmenu (id_nivel, id_menu) VALUES (:n, :m)')
                            ->param(':n', $id)
                            ->param(':m', $id_menu)
                            ->execute();
                }
                if ($detalle) {
                    $cambios[] = '<b>' . HTML::chars($n['nivel']) . '</b>: ' . HTML::chars(implode(', ', $detalle));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            Kohana::$log->add(Log::ERROR, 'Roles y permisos: ' . $e->getMessage());
            return 'No se pudieron guardar los cambios. No se modificó nada; intente de nuevo.';
        }

        if ($cambios) {
            $this->save($this->user->id_entidad, $this->user->id, 'Cambió roles y permisos. ' . implode('; ', $cambios));
            Session::instance()->set('roles_mensaje', 'Se guardaron los cambios. Las personas los verán en su próxima página (no hace falta que vuelvan a entrar).');
        } else {
            Session::instance()->set('roles_mensaje', 'No había cambios que guardar.');
        }
        return '';
    }
}
