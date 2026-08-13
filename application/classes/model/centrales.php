<?php defined('SYSPATH') or die('No direct script access.');

class Model_Centrales extends ORM{
protected $_table_names_plural = false;

public function lista()
    {
        $sql="SELECT COUNT(*) as cantidad, numCaja FROM centrales
            GROUP BY numCaja";
        return db::query(Database::SELECT, $sql)->execute()->as_array();
    }


//protected $_table_name = 'modalidades';
//protected $_primary_key = 'idModalidad';
//protected $_db_group = 'default';
/*protected $_has_many = array(
        
        'archivo' => array(
            'model'   => 'archivogestion',
            'through' => 'archivos',
        ),
        'archivogestion' => array(
            'model' => 'archivogestion',
            'foreign_key' => 'id',
            'through' => 'archivos',
            'far_key' => 'idArchigestion',
        ),
    );
*/
   /* public function rules(){
        return array(
            'nomModalidad' => array(
                array('not_empty'),
            ),
            'modaDescripcion' => array(
                array('not_empty'),
                array('alpha_dash'),
                array(array($this, 'uniq_contra'), array(':value', ':field')),
            ),
        );
    }*/
    public function labels(){
        return array('title' => 'Archivo Central',); //revisar
    }

    public function filters(){
        return array(
            TRUE => array(array('trim'),),
            'title' => array(array('strip_tags'),),
        );
    }
    
    public function uniq_contra($value, $field){
        $page = ORM::factory($this->_object_name)->where($field,'=',$value)->and_where($this->_primary_key,'!=',$this->pk())->find();
        if ($page->pk()){return false;}
        return true;
    }
} 
