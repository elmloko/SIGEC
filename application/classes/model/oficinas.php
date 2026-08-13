<?php

defined('SYSPATH') or die('no tiene acceso');

//descripcion del modelo productos
class Model_Oficinas extends ORM {

    protected $_table_names_plural = false;
    protected $_sorting = array('oficina' => 'ASC');
    //7una ofician tiene varios funcionarios (usuarios)
    protected $_has_many = array(
        'users' => array(
            'model' => 'users',
            'foreign_key' => 'id_oficina'
        ),
        'tipo' => array(
            'through' => 'tipo_oficina',
        ),
        'entidad' => array(
            'model' => 'entidades',
            'through' => 'entidades_oficinas',
            'foreign_key' => 'id_oficina',
            'far_key' => 'id_entidad',
        ),
    );
    protected $_belogn_to = array(
        'entidades' => array(
            'model' => 'entidades',
            'foreign_key' => 'id_oficina'
        )
    );

    //lista de oficinas segun entidad
    public function lista($id) {
        $sql = "SELECT id as value,oficina as text,sigla FROM oficinas WHERE id_entidad='$id'";
        return db::query(Database::SELECT, $sql)->execute();
    }

    //lista de oficinas segun entidad
    public function listaOficinas($id) {

        /*
        if ($id > 0) {

            $sql = "SELECT 
                        o.id,
                        o.padre,
                        o.oficina,
                        o.sigla,
                        o.estado,
                        e.entidad,
                        o.sigla,
                        e.id AS id_entidad
                    FROM
                        oficinas o
                            INNER JOIN
                        entidades e ON o.id_entidad = e.id
                    WHERE
                        e.id = '$id'";

        } else {

            $sql = "SELECT 
                        o.id,
                        o.padre,
                        o.oficina,
                        o.sigla,
                        o.estado,
                        e.entidad,
                        o.sigla,
                        e.id AS id_entidad
                    FROM
                        oficinas o
                            INNER JOIN
                        entidades e ON o.id_entidad = e.id";
        }
        */
        if ($id > 0) {

            $sql = "SELECT 
                        o.id,
                        o.padre,
                        o.oficina,
                        o.sigla,
                        o.estado,
                        e.entidad,
                        o.sigla,
                        e.id AS id_entidad
                    FROM
                        oficinas o
                            INNER JOIN
                        entidades e ON o.id_entidad = e.id
                    WHERE
                        (e.id = '$id') AND (o.estado = '1')";

        } else {

            $sql = "SELECT 
                        o.id,
                        o.padre,
                        o.oficina,
                        o.sigla,
                        o.estado,
                        e.entidad,
                        o.sigla,
                        e.id AS id_entidad
                    FROM
                        oficinas o
                            INNER JOIN
                        entidades e ON o.id_entidad = e.id
                    WHERE
                        o.estado = '1'";
        }

        return db::query(Database::SELECT, $sql)->execute();
    }

    //generar nuevo codigo de documentos para la oficina
    public function correlativo($id_oficina, $id_tipo, $gestion) {
        $result = ORM::factory('correlativo')
                ->where('id_oficina', '=', $id_oficina)
                ->and_where('id_tipo', '=', $id_tipo)
                ->and_where('gestion', '=', $gestion)
                ->find();
        if ($result->loaded()) {
            $result->correlativo = $result->correlativo + 1;
            $result->save();
            return substr('000' . $result->correlativo, -4);
        } else {
            $correlativo = ORM::factory('correlativo');
            $correlativo->id_oficina = $id_oficina;
            $correlativo->id_tipo = $id_tipo;
            $correlativo->correlativo = 1;
            $correlativo->gestion = $gestion;
            $correlativo->save();
            return '0001';
        }
    }
    //generar nuevo codigo de documentos para la oficina
    public function correlativoJuridico($id_oficina, $id_tipo, $gestion) {
        $result = ORM::factory('correlativo')
                ->where('id_oficina', '=', $id_oficina)
                ->and_where('id_tipo', '=', $id_tipo)
                ->and_where('gestion', '=', $gestion)
                ->find();
        if ($result->loaded()) {
            $result->correlativo = $result->correlativo + 1;
            $result->save();
            return substr('000' . $result->correlativo, -4);
        } else {
            $correlativo = ORM::factory('correlativo');
            $correlativo->id_oficina = $id_oficina;
            $correlativo->id_tipo = $id_tipo;
            $correlativo->correlativo = 1;
            $correlativo->gestion = $gestion;
            $correlativo->save();
            return '0001';
        }
    }

    //function que obtine el cite (sigla) de la oficiana
    public function sigla($id_oficina) {
        $result = ORM::factory('oficinas', array('id' => $id_oficina));
        if ($result->loaded()) {
            return $result->sigla;
        } else {
            return FALSE;
        }
    }

    public function tipo($id_tipo) {
        $result = ORM::factory('tipos', array('id' => $id_tipo));
        if ($result->loaded()) {
            return $result->abreviatura;
        } else {
            return FALSE;
        }
    }

    public function oficina($id) {
        $results = ORM::factory("oficinas")
                ->join('users', 'INNER')
                ->on("users.id_oficina", "=", "oficinas.id")
                ->where("users.id", "=", $id)
                ->find();
        return $results;
    }

    public function lista_oficinas() {
        $sql = "SELECT o.id,e.id as id_entidad,o.oficina,e.entidad,e.sigla as sigla_entidad,o.sigla 
            FROM oficinas o INNER JOIN entidades e ON o.id_entidad=e.id";
        return db::query(Database::SELECT, $sql)->execute();
    }

}

?>
