<?php defined('SYSPATH') or die('No direct script access.');

class Model_Gestiones extends ORM{
protected $_table_names_plural = false;

public function reporteFecha($modalidad,$fecha1,$fecha2){
    $sql="SELECT * FROM gestiones g INNER JOIN users u  ON g.id_user=u.id
        WHERE modalidad LIKE '$modalidad%'
        AND fechaCreacion BETWEEN '$fecha1' AND '$fecha2'
        ";
    return db::query(Database::SELECT, $sql)->execute();
}



public function reporteproponente($proponente,$fecha1,$fecha2){
    $sql="SELECT * FROM gestiones g INNER JOIN users u ON g.id_user=u.id  
        WHERE proponente = '$proponente'
        AND fechaCreacion BETWEEN '$fecha1' AND '$fecha2'
        ";
    return db::query(Database::SELECT, $sql)->execute();
}

public function lista()
    {
        $sql="SELECT COUNT(*) as cantidad, modalidad FROM gestiones
            GROUP BY modalidad";
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

/* public function validate_gestiones($arr) {
        return Validate::factory($arr)
            ->filter(TRUE, 'trim')
            ->rule('numContratacion', 'not_empty')
                ->rule('objetoContra', 'not_empty')
                ->rule('modalidad', 'not_empty')
                ->rule('niCuce', 'not_empty')
                ->rule('proponente', 'not_empty')
                ->rule('ordenCompra', 'not_empty')
                ->rule('precioContra', 'not_empty')
                ->rule('nomProceso', 'not_empty')
                ->rule('fuente', 'not_empty')
                ->rule('nomUnidad', 'not_empty')
                ->rule('numHojaruta', 'not_empty');
    } */
    public function labels(){
        return array('title' => 'Gestiones',); //revisar
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
