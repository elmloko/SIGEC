<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_content extends Controller_Minitemplate {

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

    // estadisticas rapidas de un usuario: entrada, pendientes, archivo y documentos generados
    public function action_userStats($id) {
        $user = ORM::factory('users', array('id' => $id));
        if ($user->loaded()) {
            $oSeg = New Model_Seguimiento();
            $r = $oSeg->nestados($id)->current();
            $stats = array(
                'norecibido' => $r ? (int) $r['norecibido'] : 0,
                'pendientes' => $r ? (int) $r['pendientes'] : 0,
                'archivo' => $r ? (int) $r['archivo'] : 0,
                'documentos' => $r ? (int) $r['documentos'] : 0,
            );
            $this->template->content = View::factory('admin/user_stats')
                ->bind('user', $user)
                ->bind('stats', $stats);
        } else {
            $this->template->content = 'Usuario inexistente';
        }
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
            ->bind('titulo', $titulos[$tipo])
            ->bind('result', $result);
    }

    public function action_userDetalle($id) {

        $user = ORM::factory('users', array('id' => $id));
        if ($user->loaded()) {
            $documentos = $user->tipo->find_all();
            $aDocumentos = array();
            foreach ($documentos as $d) {
                $aDocumentos[$d->id] = $d->id;
            }
            $this->template->content = View::factory('admin/user_detalle')
            ->bind('documentos', $aDocumentos)
            ->bind('user', $user);
        } else {
            $this->template->content = 'Usuario inexistente';
        }
    }

}

?>
