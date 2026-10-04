<?php
defined('SYSPATH') or die ('no tiene acceso');
//descripcion del modelo productos
class Model_Estados extends ORM{
    protected $_table_names_plural = false;
    //protected $_sorting = array('fecha_publicacion' => 'DESC');
    
    
    public function bajaUser($id){
        $id = SqlSafe::value($id); // se pega en el SQL entre comillas
         $sql="DELETE from roles_users where user_id='$id'";
        return $this->_db->query(Database::DELETE,$sql,TRUE);
    }
}
?>
