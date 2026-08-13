<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_content extends Controller_Minitemplate {

    protected $user;

    public function before() {
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

    public function action_destinos($id = '') {
        if ($id != '') {
            $o_destinatarios = New Model_Destinatarios();
            $destinos = $o_destinatarios->destinos_nuevos($this->user->id, $this->user->id_entidad);
            //$this->template->styles = array('media/css/tablas.css' => 'all');
            //$this->template->scripts = array('media/js/jquery.tablesorter.min.js');
            $this->template->content = View::factory('user/lista_usuarios')
                    ->bind('destinos', $destinos)
                    ->bind('id', $id);
        }
        echo 'Error 404';
    }

    public function action_destinosadmin($id = '') {
        $o_destinatarios = New Model_Destinatarios();
        $destinos = $o_destinatarios->destinos_nuevos($id);
        //$this->template->styles = array('media/css/tablas.css' => 'all');
        //$this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('user/lista_usuarios')
                ->bind('destinos', $destinos);
    }

    //exportar a imagen

    public function action_export() {
        $this->autoRender = false;
        /**
         * This file is part of the exporting module for Highcharts JS.
         * www.highcharts.com/license
         * 
         *  
         * Available POST variables:
         *
         * $filename  string   The desired filename without extension
         * $type      string   The MIME type for export. 
         * $width     int      The pixel width of the exported raster image. The height is calculated.
         * $svg       string   The SVG source code to convert.
         */
// Options
        define('BATIK_PATH', 'batik-rasterizer.jar');

///////////////////////////////////////////////////////////////////////////////
        ini_set('magic_quotes_gpc', 'off');

        $type = $_POST['type'];
        $svg = (string) $_POST['svg'];
        $filename = (string) $_POST['filename'];

// prepare variables
        if (!$filename)
            $filename = 'chart';
        if (get_magic_quotes_gpc()) {
            $svg = stripslashes($svg);
        }



        $tempName = md5(rand());

// allow no other than predefined types
        if ($type == 'image/png') {
            $typeString = '-m image/png';
            $ext = 'png';
        } elseif ($type == 'image/jpeg') {
            $typeString = '-m image/jpeg';
            $ext = 'jpg';
        } elseif ($type == 'application/pdf') {
            $typeString = '-m application/pdf';
            $ext = 'pdf';
        } elseif ($type == 'image/svg+xml') {
            $ext = 'svg';
        }
        $outfile = "/temp/$tempName.$ext";

        if (isset($typeString)) {

            // size
            if ($_POST['width']) {
                $width = (int) $_POST['width'];
                if ($width)
                    $width = "-w $width";
            }

            // generate the temporary file
            if (!file_put_contents("/temp/$tempName.svg", $svg)) {
                die("Couldn't create temporary file. Check that the directory permissions for
			the /temp directory are set to 777.");
            }

            // do the conversion
            $output = shell_exec("java -jar " . BATIK_PATH . " $typeString -d $outfile $width temp/$tempName.svg");

            // catch error
            if (!is_file($outfile) || filesize($outfile) < 10) {
                echo "<pre>$output</pre>";
                echo "Error while converting SVG. ";

                if (strpos($output, 'SVGConverter.error.while.rasterizing.file') !== false) {
                    echo "
			<h4>Debug steps</h4>
			<ol>
			<li>Copy the SVG:<br/><textarea rows=5>" . htmlentities(str_replace('>', ">\n", $svg)) . "</textarea></li>
			<li>Go to <a href='http://validator.w3.org/#validate_by_input' target='_blank'>validator.w3.org/#validate_by_input</a></li>
			<li>Paste the SVG</li>
			<li>Click More Options and select SVG 1.1 for Use Doctype</li>
			<li>Click the Check button</li>
			</ol>";
                }
            }

            // stream it
            else {
                $this->autoRender = false;
                header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
                header("Content-Type: $type");
                echo file_get_contents($outfile);
            }

            // delete it
//	unlink("/temp/$tempName.svg");
//	unlink($outfile);
// SVG can be streamed directly back
        } else if ($ext == 'svg') {
            header("Content-Disposition: attachment; filename=\"$filename.$ext\"");
            header("Content-Type: $type");
            echo $svg;
        } else {
            echo "Invalid type";
        }
    }

}

?>