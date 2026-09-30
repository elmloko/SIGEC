<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_content extends Controller_Minitemplate {

    // estas pantallas se abren dentro de los modales del panel: solo para administradores
    public function before() {
        parent::before();
        $auth = Auth::instance();
        if (!$auth->logged_in() OR (int) $auth->get_user()->nivel !== 5 OR (int) $auth->get_user()->habilitado !== 1) {
            $this->request->redirect('/login');
        }
    }

    public function action_destinos($id = '') {
        $o_destinatarios = New Model_Destinatarios();
        $destinos = $o_destinatarios->destinos_nuevos($id);
        $this->template->content = View::factory('admin/lista_destinos')
                ->bind('destinos', $destinos);
    }

    //lista de documentos a adicionar
    public function action_documentos($id = '') {
        $o_destinatarios = New Model_Documentos();
        $documentos = $o_destinatarios->documentos_nuevos($id);
        $this->template->content = View::factory('admin/lista_documentos')
                ->bind('documentos', $documentos);
    }

    public function action_lista($id = '') {
        $entidad = ORM::factory('entidades', array('id' => $id));
        if ($entidad->loaded()) {
            $oficinas = $entidad->oficinas->find_all();
            $this->template->content = View::factory('/admin/oficinas')
                    ->bind('oficinas', $oficinas);
        } else {
            $this->template->content = 'Error: No se encontro la entidad';
        }
    }

    public function action_addUser() {
        var_dump($_POST);
    }

    // modal para ver/cambiar la contraseña por defecto que se asigna a usuarios nuevos
    public function action_passDefecto() {
        $config = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
        // esta tabla no tiene columna 'id', asi que loaded() no es fiable aqui; se valida por 'campo'
        $passActual = ($config->campo === 'passDefecto') ? $config->valor : '';
        $this->template->content = View::factory('admin/pass_defecto')
                ->bind('passActual', $passActual);
    }

    // estadisticas rapidas de un usuario: entrada, pendientes, archivo y documentos generados
    public function action_userStats($id = 0) {
        $user = ORM::factory('users', (int) $id);
        if (!$user->loaded()) {
            $this->template->content = 'Usuario inexistente';
            return;
        }
        $oSeg = New Model_Seguimiento();
        $r = $oSeg->nestados($user->id)->current();
        $stats = array(
            'norecibido' => $r ? (int) $r['norecibido'] : 0,
            'pendientes' => $r ? (int) $r['pendientes'] : 0,
            'archivo' => $r ? (int) $r['archivo'] : 0,
            'documentos' => $r ? (int) $r['documentos'] : 0,
        );
        // lo que mas dice de una persona: cuanto tarda y que tan atrasada esta
        $extra = DB::query(Database::SELECT, "SELECT
                    (SELECT COUNT(*) FROM seguimiento WHERE derivado_por = :id) AS derivaciones,
                    (SELECT MAX(DATEDIFF(NOW(), fecha_emision)) FROM seguimiento
                        WHERE derivado_a = :id AND estado IN (1, 2)) AS mas_viejo,
                    (SELECT COUNT(*) FROM seguimiento
                        WHERE derivado_a = :id AND estado = 1 AND fecha_emision < NOW() - INTERVAL 3 DAY) AS sin_recibir_3d,
                    (SELECT ROUND(AVG(DATEDIFF(fecha_recepcion, fecha_emision)), 1) FROM seguimiento
                        WHERE derivado_a = :id AND fecha_recepcion IS NOT NULL
                          AND fecha_emision >= NOW() - INTERVAL 6 MONTH) AS dias_recibir")
                ->param(':id', (int) $user->id)
                ->execute()->current();
        // documentos generados en los ultimos 6 meses
        $por_mes = DB::query(Database::SELECT, "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS n
                FROM documentos
                WHERE id_user = :id AND fecha_creacion >= DATE_FORMAT(CURDATE() - INTERVAL 5 MONTH, '%Y-%m-01')
                GROUP BY mes")
                ->param(':id', (int) $user->id)
                ->execute()->as_array('mes', 'n');
        $oficina = ORM::factory('oficinas', $user->id_oficina);

        $this->template->content = View::factory('admin/user_stats')
            ->bind('user', $user)
            ->bind('stats', $stats)
            ->set('extra', $extra)
            ->set('por_mes', $por_mes)
            ->set('oficina', $oficina->loaded() ? $oficina->oficina : '');
    }

    // detalle (drill-down) de una de las tarjetas de estadisticas: entrada, pendientes, archivo o documentos
    public function action_userStatsList($id) {
        $tipo = Arr::get($_GET, 'tipo', '');
        $user = ORM::factory('users', array('id' => $id));
        if (!$user->loaded()) {
            $this->template->content = 'Usuario inexistente';
            return;
        }

        $oSeg = New Model_Seguimiento();
        $titulos = array(
            'entrada' => 'Entrada (No recibidos)',
            'pendientes' => 'Pendientes (Accion pendiente)',
            'archivo' => 'Archivo (Archivados)',
            'documentos' => 'Documentos generados',
        );

        switch ($tipo) {
            case 'entrada':
                $result = $oSeg->entrada($id);
                break;
            case 'pendientes':
                $result = $oSeg->pendiente($id);
                break;
            case 'archivo':
                $result = $oSeg->archivo($id);
                break;
            case 'documentos':
                $result = ORM::factory('documentos')
                    ->where('id_user', '=', $id)
                    ->order_by('fecha_creacion', 'DESC')
                    ->find_all();
                break;
            default:
                $result = array();
                break;
        }

        $this->template->content = View::factory('admin/user_stats_list')
            ->bind('user', $user)
            ->bind('tipo', $tipo)
            ->bind('titulo', $titulos[$tipo] = Arr::get($titulos, $tipo, 'Detalle'))
            ->bind('result', $result);
    }

    public function action_userDetalle($id = 0) {

        $user = ORM::factory('users', (int) $id);
        if (!$user->loaded()) {
            $this->template->content = 'Usuario inexistente';
            return;
        }
        $permitidos = array();
        foreach ($user->tipo->find_all() as $d) {
            $permitidos[(int) $d->id] = (int) $d->id;
        }
        $tipos = ORM::factory('tipos')->where('activo', '=', 1)->order_by('prioridad')->order_by('tipo')->find_all();
        // cuantos documentos de cada tipo lleva generados: sirve para avisar antes de quitarle un permiso
        $usados = DB::query(Database::SELECT, 'SELECT id_tipo, COUNT(*) AS n FROM documentos WHERE id_user = :id GROUP BY id_tipo')
                ->param(':id', (int) $user->id)
                ->execute()->as_array('id_tipo', 'n');
        $oficina = ORM::factory('oficinas', $user->id_oficina);
        $this->template->content = View::factory('admin/user_detalle')
                ->set('permitidos', $permitidos)
                ->set('tipos', $tipos)
                ->set('usados', $usados)
                ->set('oficina', $oficina->loaded() ? $oficina->oficina : '')
                ->bind('user', $user);
    }

}

?>
