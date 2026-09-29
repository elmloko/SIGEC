<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Download extends Controller
{

    private function archivo_base_path()
    {
        return rtrim(Kohana::$config->load('archivo')->get('path'), '/\\');
    }

    // $inline = TRUE muestra el archivo en el navegador (vista previa) en vez de forzar la descarga
    private function send_file($file, $filename, $content_type, $inline = FALSE)
    {
        $remote = RemoteArchivo::is_enabled();

        if ($remote) {
            if (!RemoteArchivo::exists($file)) {
                $this->autoRender = false;
                http_response_code(404);
                echo 'Archivo no encontrado en el servidor.';
                return;
            }
        } elseif (!is_file($file)) {
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
        header("Content-Disposition: " . ($inline ? 'inline' : 'attachment') . "; filename=\"" . str_replace('"', '', $filename) . "\"");
        header("Content-Transfer-Encoding: binary");

        if ($remote) {
            header("Content-Length: " . RemoteArchivo::filesize($file));
            RemoteArchivo::stream_download($file);
        } else {
            header("Content-Length: " . filesize($file));
            readfile($file);
        }
        exit;
    }

    public function action_file($id = '')
    {
        $auth = Auth::instance();
        if ($auth->logged_in() && $id != '') {
            $session = Session::instance();
            $user = $session->get('auth_user');
            $this->autoRender = false;
            $archivo = ORM::factory('archivos', $id);
            if ($archivo->loaded()) {
                //ahora vemos que solo el que estee autorizado pueda descargar
                $file = RemoteArchivo::is_enabled()
                    ? $archivo->sub_directorio . '/' . $archivo->nombre_archivo
                    : $this->archivo_base_path() . '/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
                $filetemp = substr($archivo->nombre_archivo, 13);
                $this->send_file($file, $filetemp, $archivo->extension);
            } else {
                echo 'Archivo Inexistente.!!';
            }
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

    public function action_index()
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            $id = $_GET['file'];
            $session = Session::instance();
            $user = $session->get('auth_user');
            $this->autoRender = false;
            $archivo = ORM::factory('archivos', $id);
            if ($archivo->loaded()) {
                //ahora vemos que solo el que estee autorizado pueda descargar
                $file = RemoteArchivo::is_enabled()
                    ? $archivo->sub_directorio . '/' . $archivo->nombre_archivo
                    : $this->archivo_base_path() . '/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
                $filetemp = substr($archivo->nombre_archivo, 13);
                // ?ver=1 abre el PDF en el navegador (vista previa); los demas tipos siempre se descargan
                $es_pdf = stripos($archivo->extension, 'pdf') !== FALSE || preg_match('/\.pdf$/i', $filetemp);
                $inline = Arr::get($_GET, 'ver') == '1' && $es_pdf;
                $this->send_file($file, $filetemp, $inline ? 'application/pdf' : $archivo->extension, $inline);
            } else {
                echo 'Archivo Inexistente.!!';
            }
        }
    }

}
