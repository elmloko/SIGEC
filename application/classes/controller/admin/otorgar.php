<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Otorgar extends Controller_Minitemplate
{
    protected $user;

    public function before()
    {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            //$session = Session::instance();
            //$this->user = $session->get('auth_user');
            $this->user = $auth->get_user();
            parent::before();
        } else {

        }
    }

    public function action_index($id = '')
    {
        $user = ORM::factory('users', array('id' => $id));
        $rango_de_ids = array(1, $id);

        if ($user->loaded()) {
            $users_all = ORM::factory('users')
                ->where('id', 'NOT IN', $rango_de_ids)
                ->order_by('nombre', 'desc')
                ->find_all();

            $query_usuarios_que_reciben_plazos = "  SELECT 
                                                        *
                                                    FROM
                                                        usuarios_habilitados_plazos
                                                    WHERE
                                                        id_usuario_padre = '$id'";
            $result_query = DB::query(Database::SELECT, $query_usuarios_que_reciben_plazos)->execute();

            $usuarios_que_reciben_plazos = array();
            foreach ($result_query as $usuario) {
                $usuarios_que_reciben_plazos[$usuario['id_usuario_hijo']] = $usuario['id_usuario_hijo'];
            }

            $this->template->content = View::factory('admin/otorgar_plazos')
                ->bind('user', $user)
                ->bind('users_all', $users_all)
                ->bind('usuarios_que_reciben_plazos', $usuarios_que_reciben_plazos);
        } else {
            $this->template->content = 'Usuario inexistente';
        }
    }

}

?>
