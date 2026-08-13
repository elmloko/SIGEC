<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Download extends Controller
{

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
                //  $file='/archivos/'.$archivo->sub_directorio.'/'.$archivo->nombre_archivo;              
                $file = '/backup/backup_sigec/sigec/archivo/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
                $filetemp = substr($archivo->nombre_archivo, 13);
                header("Content-Disposition: attachment; filename=" . $filetemp . "\n\n");
                header("Content-Type: " . $archivo->extension);
                header("Content-Length: " . filesize($file));
                readfile($file);
                exit;
            } else {
                echo 'Archivo Inexistente.!!';
            }
        }
    }

    public function action_manual()
    {
        /*
        $id = 93705;
        $archivo = ORM::factory('archivos', $id);
        if ($archivo->loaded()) {
            //ahora vemos que solo el que estee autorizado pueda descargar
            //  $file='/archivos/'.$archivo->sub_directorio.'/'.$archivo->nombre_archivo;
            $file = '/var/www/html/adjuntos/sigec/archivo/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
            $filetemp = substr($archivo->nombre_archivo, 13);
            header("Content-Disposition: attachment; filename=" . $filetemp . "\n\n");
            header("Content-Type: " . $archivo->extension);
            header("Content-Length: " . filesize($file));
            readfile($file);
            exit;
        } else {
            echo 'Archivo Inexistente.!!';
        }
        */
        //ahora vemos que solo el que estee autorizado pueda descargar
        $file = '/backup/backup_sigec/sigec/archivo/Manual-de-Usuario-SIGEC.pdf';
        $file_temp = 'Manual-de-Usuario-SIGEC.pdf';
        $extension = 'application/pdf';
        header("Content-Disposition: attachment; filename=" . $file_temp . "\n\n");
        header("Content-Type: " . $extension);
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit;
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
                //  $file='/archivos/'.$archivo->sub_directorio.'/'.$archivo->nombre_archivo;              
                $file = '/backup/backup_sigec/sigec/archivo/' . $archivo->sub_directorio . '/' . $archivo->nombre_archivo;
                $filetemp = substr($archivo->nombre_archivo, 13);
                header("Content-Disposition: attachment; filename=" . $filetemp . "\n\n");
                header("Content-Type: " . $archivo->extension);
                header("Content-Length: " . filesize($file));
                readfile($file);
                exit;
            } else {
                echo 'Archivo Inexistente.!!';
            }
        }
    }

}

