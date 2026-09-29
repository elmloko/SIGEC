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

        try {
            $existe = $remote ? RemoteArchivo::exists($file) : is_file($file);
            $tamanio = $existe ? ($remote ? RemoteArchivo::filesize($file) : filesize($file)) : 0;
        } catch (Exception $e) {
            $this->aviso(503, 'Servidor de archivos no disponible',
                'No se pudo conectar con el servidor donde se guardan los archivos digitales. '
                . 'Intente nuevamente en unos minutos o comuníquese con el área de sistemas.');
            return;
        }
        if (!$existe) {
            $this->aviso(404, 'Archivo no encontrado', 'El archivo no existe en el servidor de archivos.');
            return;
        }

        if (ob_get_level()) {
            ob_end_clean();
        }

        header("Content-Description: File Transfer");
        header("Content-Type: " . ($content_type ?: 'application/octet-stream'));
        header("Content-Disposition: " . ($inline ? 'inline' : 'attachment') . "; filename=\"" . str_replace('"', '', $filename) . "\"");
        header("Content-Transfer-Encoding: binary");

        header("Content-Length: " . $tamanio);
        if ($remote) {
            RemoteArchivo::stream_download($file);
        } else {
            readfile($file);
        }
        exit;
    }

    // pagina simple de aviso; se ve bien tanto en el visor (iframe) como abierta directamente
    private function aviso($codigo, $titulo, $mensaje)
    {
        $this->autoRender = false;
        http_response_code($codigo);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>' . HTML::chars($titulo) . '</title></head>'
            . '<body style="margin:0;font-family:Arial,sans-serif;background:#F3F5F8;color:#123E73;">'
            . '<div style="max-width:520px;margin:12vh auto;padding:28px;background:#fff;border-radius:8px;'
            . 'border-top:4px solid #FECB34;box-shadow:0 1px 4px rgba(18,62,115,.15);text-align:center;">'
            . '<div style="font-size:40px;line-height:1;margin-bottom:10px;">&#9888;</div>'
            . '<h2 style="margin:0 0 10px;font-size:20px;">' . HTML::chars($titulo) . '</h2>'
            . '<p style="margin:0;color:#4a5568;line-height:1.5;">' . HTML::chars($mensaje) . '</p>'
            . '</div></body></html>';
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
