<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_archivo extends Controller_DefaultTemplate {

    protected $user;

      public function before() {
        parent::before();
    }

    public function action_index() {
        
    }

    public function action_add($id = '') {
        if ($_POST) {
            for ($i = 0; $i < count($_FILES ['archivo']) - 1; $i++) {
                echo $_FILES ['archivo']['name'][$i];
            }
            // var_dump($_FILES);
        }
        $documento = ORM::factory('documentos')->where('id', '=', $id)->and_where('id_user', '=', $this->user->id)->find();
        $this->template->content = View::factory('documentos/add_file')
                ->bind('documento', $documento);
    }

    public function action_eliminar($id = '') {
        if ($id != '') {
            $archivo = ORM::factory('archivos')
                    ->where('id', '=', $id)
                    ->and_where('id_user', '=', $this->user->id)
                    ->find();
            $envio = $archivo->loaded() ? EstadoDocumento::de(ORM::factory('documentos', $archivo->id_documento)) : NULL;
            if ($envio && $envio['recibido']) {
                // el archivo ya viajo con la hoja de ruta y el destinatario lo recibio
                $this->template->content = 'Este archivo ya fue recibido por el destinatario y no se puede eliminar.';
            } elseif ($archivo->loaded()) {
                $archivo->estado = 0;
                $archivo->save();
                $this->request->redirect('documento/edit/' . $archivo->id_documento);
            } else {
                $this->template->content = 'Usted no tiene permisos para eliminar este archivo';
            }
        }
    }

}

?>
