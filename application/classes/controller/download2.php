<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Download2 extends Controller
{
    private function archivo_base_path()
    {
        return rtrim(Kohana::$config->load('archivo')->get('path'), '/\\');
    }

    private function send_file($file, $filename, $content_type)
    {
        if (!is_file($file)) {
            $this->autoRender = false;
            http_response_code(404);
            echo 'Archivo no encontrado en el servidor.';
            return;
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header("Content-Description: File Transfer");
        header("Content-Type: " . ($content_type ?: 'application/octet-stream'));
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit;
    }

    public function action_index()
    {
        $auth = Auth::instance();
        $id = $_GET['file'];
        $session = Session::instance();
        $user = $session->get('auth_user');
        $this->autoRender = false;
        $archivo = ORM::factory('archivos', $id);
        if ($archivo->loaded()) {
            //ahora vemos que solo el que estee autorizado pueda descargar
            $file = $this->archivo_base_path() . '/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
            $filetemp = substr($archivo->nombre_archivo, 13);
            $this->send_file($file, $filetemp, $archivo->extension);
        } else {
            echo 'Archivo Inexistente.!!';
        }
    }

    public function action_manual()
    {
        $this->autoRender = false;
        $file = $this->archivo_base_path() . '/Manual-de-Usuario-SIGEC.pdf';
        $file_temp = 'Manual-de-Usuario-SIGEC.pdf';
        $extension = 'application/pdf';
        $this->send_file($file, $file_temp, $extension);
    }

}
