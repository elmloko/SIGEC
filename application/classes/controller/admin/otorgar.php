<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Otorgar extends Controller_Minitemplate
{
    protected $user;

    // se abre dentro de un modal del panel: solo para administradores
    public function before()
    {
        parent::before();
        $auth = Auth::instance();
        if (!$auth->logged_in() OR (int) $auth->get_user()->nivel !== 5 OR (int) $auth->get_user()->habilitado !== 1) {
            $this->request->redirect('/login');
        }
        $this->user = $auth->get_user();
    }

    public function action_index($id = 0)
    {
        $user = ORM::factory('users', (int) $id);
        if (!$user->loaded()) {
            $this->template->content = 'Usuario inexistente';
            return;
        }
        // a quienes ya les puede asignar plazos
        $elegidos = array();
        $filas = DB::query(Database::SELECT, 'SELECT id_usuario_hijo FROM usuarios_habilitados_plazos WHERE id_usuario_padre = :id')
                ->param(':id', (int) $user->id)
                ->execute();
        foreach ($filas as $f) {
            $elegidos[(int) $f['id_usuario_hijo']] = (int) $f['id_usuario_hijo'];
        }
        // personas activas, mas las ya elegidas aunque esten de baja (para no perderlas al guardar)
        $personas = DB::query(Database::SELECT, 'SELECT u.id, u.nombre, u.cargo, u.habilitado, o.oficina
                FROM users u
                LEFT JOIN oficinas o ON o.id = u.id_oficina
                WHERE u.id <> :id AND (u.habilitado = 1 OR u.id IN (
                        SELECT id_usuario_hijo FROM usuarios_habilitados_plazos WHERE id_usuario_padre = :id))
                ORDER BY u.nombre')
                ->param(':id', (int) $user->id)
                ->execute()->as_array();
        $oficina = ORM::factory('oficinas', $user->id_oficina);

        $this->template->content = View::factory('admin/otorgar_plazos')
                ->bind('user', $user)
                ->set('personas', $personas)
                ->set('elegidos', $elegidos)
                ->set('oficina', $oficina->loaded() ? $oficina->oficina : '');
    }

}
