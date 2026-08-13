<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_nurs extends ORM{
    protected $_table_names_plural = false;
    //7una ofician tiene varios funcionarios (usuarios)
    protected $_has_many=array(
        'documentos' =>array(
            'model'=>'documentos',
            'through' => 'nurs_documentos',
            'foreign_key' => 'nur',
	    'far_key' => 'id_documento',
        ),
    );
    //generar un correlativo
    public function correlativo($id_tipo,$a,$id_entidad,$gestion){
        $result=ORM::factory('correlativo')
                ->where('id_tipo','=',$id_tipo)
                ->and_where('id_entidad','=',$id_entidad)
                ->and_where('gestion','=',$gestion)
                ->find();
        
        //si existe el tipo para la entidad entonces 
        if($result->loaded())
        {
        $result->correlativo=$result->correlativo+1;        
        $result->save(); 
        $codigo=$a.date('Y').'-'.substr('0000'.$result->correlativo,-5);                                           
        return $codigo;               
        }
        else
        {
        //agregamos uno nuevo            
        $result->id_tipo=$id_tipo;
        $result->id_entidad=$id_entidad;
        $result->gestion=$gestion;
        $result->correlativo=1;
        $result->save();        
        $codigo=$a.date('Y').'-00001';                                           
        return $codigo;
        }
    }
    //function para asignar un nur
    public function asignarNur($codigo,$id_user,$username=''){
        $nur=ORM::factory('nurs');
        $nur->nur=$codigo;
        $nur->id_user=$id_user;
        $nur->fecha_creacion=date('Y-m-d H:i:s');
        $nur->username=$username;
        $nur->save();
        return $nur->nur;
    }
    
    
    
    public function oficina($id){
        $results = ORM::factory("oficinas")
                ->join('users', 'INNER')
                ->on("users.id_oficina","=","oficinas.id")
                ->where("users.id", "=",$id)                
                ->find();
                return $results;
    }
}
?>
