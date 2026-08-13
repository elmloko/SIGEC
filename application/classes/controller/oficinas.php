<?php
defined('SYSPATH') or die('Acceso denegado');
class Controller_oficinas extends Controller_Defaulttemplate{
    public $lista='';
    public function action_index(){
        $entidades=ORM::factory('entidades')->find_all();        
    }
    //action para adicionar una nueva oficina
   public function action_add()
    {
       $auth =Auth::instance();
       if($auth->logged_in())
               {
           $user=ORM::factory('users',$auth->get_user());
            if($user->nivel==3) //nivel 3 administrador del sistema
            {
                 $oficinas=ORM::factory('oficinas')->find_all();
                 $options=array(''=>'Seleccione oficina...');
                 foreach ($oficinas as $o){
                     $options[$o->id]=$o->nombre;
                 }
                 $this->template->menu   =View::factory('admin/menu');
                 $this->template->content=View::factory('oficina/add')
                                          ->bind('options', $options)
                                          ->bind('message', $message);
              if ($_POST)
                {
                try {
                    $verificar_sigla=  ORM::factory('oficinas')->where('sigla','=',trim($_POST['sigla']))->find_all();
                    if(sizeof($verificar_sigla)>0){ $message = "La Oficina con sigla '{$_POST['sigla']}' ya esta registrada!!!"; }
                    else{
                    // Create the user using form values
                    $oficina = ORM::factory('oficinas');
                    $oficina->padre=$_POST['padre'];
                    $oficina->nombre=trim($_POST['nombre']);
                    $oficina->sigla=trim($_POST['sigla']);
                    $oficina->save();
                    $_POST = array();
                    // Set success message
                    $message = "Tu registraste a '{$oficina->nombre}' como nueva oficina";
                    }
                    } catch (ORM_Validation_Exception $e) {
                    // Set failure message
                    $message = 'There were errors, please see form below.';
                    // Set errors using custom messages
                    $errors = $e->errors('models');
                     }
                } //fin if POST
            }
            else
             {
             $this->template->content=View::factory('errors/user');
             }  
       }
   }
   public function action_lista($ide=''){
            $entidad=ORM::factory('entidades',$ide);
            if($entidad->loaded())
            {
            $oficina=ORM::factory('oficinas')
                        ->where('id_entidad','=',$entidad->id)    
                        ->and_where('padre','=',0)
                        ->find();
            $this->lista='<ul id="oficina">';
            // echo '<ul>';           
            $this->listar($oficina->id,$entidad->entidad,'MDPyEP');
         //   echo '</ul>';
            $this->lista.='</ul>';
            $config=array();
            //$config=  ORM::factory('configuracion',1);
            $this->template->menu=  View::factory('admin/menu');
            $this->template->content   = View::factory('oficina/lista')
                                        ->bind('lista', $this->lista)
                                        ->bind('config', $config);
            }
       }
   
   
   public function listar($id,$oficina,$sigla){
       $h=ORM::factory('oficinas')->where('padre','=',$id)->count_all();              
       //echo '<li>'.$oficina;       
	   $this->lista.='<li class="oficina">'.HTML::anchor('admin/user/lista/'.$id,$oficina.' | '.$sigla);
       if($h>0){
       //echo '<ul>';
       $this->lista.='<ul>';
       $hijos=ORM::factory('oficinas')->where('padre','=',$id)->find_all();
        foreach($hijos as $hijo){
                  $oficina=$hijo->oficina;
                  $this->listar($hijo->id,$oficina,$hijo->sigla);
         }
         $this->lista.='</ul>';
       // echo '</ul>';
       }
        else{
            $this->lista.='</li>';
         //   echo '</li>';
        }
   }
   public function action_list($id=''){
            $user=  ORM::factory('users',$auth->get_user());
            if($user->nivel==3){
            $oficina=  ORM::factory('oficinas',$id);
            $usuarios=ORM::factory('users')->where('id_oficina','=',$id)->find_all();
            $this->template->menu=  View::factory('admin/menu');
            $this->template->content=View::factory('user/list')->bind('usuarios', $usuarios)
                ->bind('oficina', $oficina);
            }
            else{
                $this->template->content=View::factory('errors/user');
            }
        
    }
    
}
?>
