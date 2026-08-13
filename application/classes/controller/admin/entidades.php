<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Entidades extends Controller_AdminTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'admin');
        $oSM = New Model_menus();
        parent::after();
    }

    // lista de oficinas
    public function action_index() {
        $entidades = ORM::factory('entidades')->find_all();
        $this->template->content = View::factory('admin/entidades/lista')
                ->bind('entidades', $entidades);
    }

    public function action_logo($id) {
        $entidad = ORM::factory('entidades', $id);
        if ($entidad->loaded()) {


            if (isset($_POST['scrop'])) {
                //$this->view->disable();
                $targ_w = $targ_h = 180;
                $targ_w = 240;
                $targ_h = 90;
                $jpeg_quality = 100;

                $src = DOCROOT . 'static/logos/' . $entidad->logo;
                $logo='static/logos/' . $entidad->logo;
                $img_r = imagecreatefrompng($src);
                $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x1'], $_POST['y1'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);

                //header('Content-type: image/jpeg');                
                imagepng($dst_r, DOCROOT . 'static/logos/' . 1, $jpeg_quality);
                imagedestroy($dst_r);
                // unlink($src);
                // $_POST=array();
            }
            $this->template->styles = array(
                'static/plugins/dropzone/css/dropzone.css' => 'screen',
                'static/plugins/dropzone/css/basic.css' => 'screen',
                'static/css/logo.css' => 'screen'
            );
            $this->template->scripts = array(
                'static/js/logoEntidad.js',
                'static/plugins/dropzone/dropzone.min.js',
                'static/plugins/jscrop/js/jquery.Jcrop.js',
            );
            $this->template->content = View::factory('admin/entidades/logo')
                    ->bind('entidad', $entidad);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_subirlogo() {
        $foto = $_POST['username'] . '.jpg';
        $post = Validation::factory($_FILES)
                ->rule('file', 'Upload::not_empty')
                ->rule('file', 'Upload::type', array(':value', array('jpg','png')));
        // ->rule('archivo', 'Upload::size', array(':value', '20M'));
        //si pasa la validacion guardamamos 
        if (file_exists('static/logos/' . $foto)) {
            rename('static/logos/' . $foto, 'static/logos/' . time() . '_' . $foto);
        }
        if (file_exists('static/fotos/' . $foto)) {
            rename('static/fotos/' . $foto, 'static/logos/' . time() . '_' . $foto);
        }
        $path = 'static/logos/';
        $logo = uniqid() . '.png';
        $filename = upload::save($_FILES['file'], $logo, $path);
        $image = Image::factory($filename);
        $image->resize(240, 240);
        $image->save();
        $entidad = ORM::factory('entidades', $_POST['idp']);
        if ($entidad->loaded()) {
            $entidad->logo = $logo;
            $entidad->save();
        }
        /* if ($this->request->hasFiles() == true) {
          foreach ($this->request->getUploadedFiles() as $file) {
          //echo $file->getName(), " ", $file->getSize(), "\n";
          if (file_exists('tmp/' . $ci . '.jpg')) {
          rename('tmp/' . $ci . '.jpg', 'tmp/' . time() . '_' . $ci . '.jpg');
          }
          if (file_exists('personal/' . $ci . '.jpg')) {
          rename('personal/' . $ci . '.jpg', 'personal/' . time() . '_' . $ci . '.jpg');
          }
          $file->moveTo('tmp/' . $ci . '.jpg');
          $image = new Phalcon\Image\Adapter\GD('tmp/' . $ci . '.jpg');
          $image->resize(500, 500);
          if ($image->save()) {
          echo 'success';
          }
          }
          }
         */
        $_POST = null;
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

    public function action_nuevo() {
        $errors = array();
        $mensaje = '';
        if (isset($_POST['entidad'])) {
            //verificamos que la sigla de la entidad no exista ya
            $entidad = ORM::factory('entidades', array('sigla' => $_POST['sigla']));
            if ($entidad->id) {
                $sigla = $_POST['sigla'];
                $errors[] = "Ya existe una entidad con la sigla: $sigla , escriba otra por favor";
            } else {
                $entidad->entidad = Arr::get($_POST, 'entidad');
                $entidad->sigla = Arr::get($_POST, 'sigla');
                $entidad->sigla2 = Arr::get($_POST, 'sigla2');
                $entidad->direccion = Arr::get($_POST, 'direccion');
                $entidad->telefono = Arr::get($_POST, 'telefono');
                $entidad->save();
                $_POST = array();
                $mensaje = $entidad->entidad;
            }
        }

        $this->template->content = View::factory('admin/entidades/nuevo')
                ->bind('errors', $errors)
                ->bind('mensaje', $mensaje);
    }

    public function action_edit($id) {
        $errors = "";
        $mensaje = '';
        //verificamos que la sigla de la entidad no exista ya
        $e = ORM::factory('entidades', $id);
        if ($e->loaded()) {
            if (isset($_POST['id'])) {
                $ent = ORM::factory('entidades')->where('id', '=', $_POST['id'])
                        //S->and_where('sigla2','<>',$_POST['sigla2'])
                        ->find();
                if ($ent->loaded()) {
                    $ent->entidad = Arr::get($_POST, 'entidad');
                    $ent->sigla = Arr::get($_POST, 'sigla');
                    $ent->sigla2 = Arr::get($_POST, 'sigla2');
                    $ent->direccion = Arr::get($_POST, 'direccion');
                    $ent->telefono = Arr::get($_POST, 'telefono');
                    $ent->save();
                    $e = $ent;
                    //   echo $_POST['sigla'];

                    $_POST = array();
                    $mensaje = "Se modifico correctamente datos de la entidad.";
                } else {
                    $errors = "Ocurrio un error al guardar la entidad";
                }
            }

            $entidad = array();

            $entidad['id'] = $e->id;
            $entidad['entidad'] = $e->entidad;
            $entidad['sigla'] = $e->sigla;
            $entidad['sigla2'] = $e->sigla2;
            $entidad['direccion'] = $e->direccion;
            $entidad['telefono'] = $e->telefono;


            $this->template->content = View::factory('admin/entidades/edit')
                    ->bind('errors', $errors)
                    ->bind('entidad', $entidad)
                    ->bind('mensaje', $mensaje);
        }
    }

}

?>
