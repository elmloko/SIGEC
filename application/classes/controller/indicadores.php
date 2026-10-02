<?php

defined('SYSPATH') or die('Acceso denegado');

/**
 * "Mis indicadores": tablero personal del rol usuario (Model_niveles::NIVEL_USUARIO).
 * Siempre muestra los datos del usuario logueado; no acepta otro usuario por parametro.
 * Reutiliza el modelo y la vista del tablero por persona del Centro de indicadores (reports/persona).
 */
class Controller_Indicadores extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {
        parent::before();
        // solo el rol usuario; el administrador tiene el Centro de indicadores (reports/tablero)
        if ((int) $this->user->nivel !== Model_niveles::NIVEL_USUARIO) {
            $this->request->redirect((int) $this->user->nivel === Model_niveles::NIVEL_ADMIN ? 'reports/tablero' : 'dashboard');
        }
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'indicadores');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    public function action_index() {
        list($desde, $hasta) = Model_Indicadores::periodo($_GET);
        $usuario = (int) $this->user->id;
        $f = array(
            'desde' => $desde, 'hasta' => $hasta, 'oficina' => 0, 'oficinas' => array(),
            'usuario' => $usuario, 'usuarios' => array('' => array($usuario => $this->user->nombre)),
        );

        $nombre = 'indicadores:propio:' . $desde . ':' . $hasta . ':' . $usuario;
        $datos = Kohana::cache($nombre, NULL, 600);
        if ($datos === NULL || Arr::get($_GET, 'refrescar')) {
            $m = new Model_Indicadores($desde, $hasta, 0, $usuario);
            $datos = $m->tablero_persona();
            $datos['generado'] = date('Y-m-d H:i:s');
            Kohana::cache($nombre, $datos, 600);
        }

        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa a <b>Mis indicadores</b>');
        $this->template->title .= ' / Mis indicadores';
        $this->template->titulo .= 'Mis indicadores';
        $this->template->descripcion = 'Mi desempeño y mi bandeja';
        $this->template->styles = array('media/css/indicadores.css?v=20261002c' => 'all');
        $this->template->scripts = array('static/js/libs/chartjs/chart.umd.min.js');
        $this->template->content = View::factory('reportes/indicadores/persona')
                ->set('f', $f)
                ->set('d', $datos)
                ->set('pagina', 'persona')
                ->set('modo', 'propio')
                ->set('plazo', Model_Indicadores::PLAZO_DIAS);
    }

}
