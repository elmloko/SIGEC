<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller
{

    protected $user;

    public function before()
    {
    }

    /*
    action_destinatatiosPorUsuario($id='nombre completo')
    */
    public function action_destinatatiosPorUsuario($id)
    {
        $nombre_usuario = $_GET['nombre_usuario'];
        //$nombre_completo = $_GET['nombre_completo'];
        //echo $id_usuario_logueado;
        //echo $id_usuario_destinatario;

        $query = "  SELECT 
                        u.nombre, u.cargo
                    FROM
                        sigec.users u
                            INNER JOIN
                        sigec.destinatarios d ON u.id = d.id_destino
                    WHERE
                        d.id_usuario IN (   SELECT 
                                                id
                                            FROM
                                                users
                                            WHERE
                                                username LIKE '%" . $nombre_usuario . "%'
                                        );";

        $resultSet = db::query(Database::SELECT, $query, FALSE)
            ->execute()
            ->as_array();

        // echo $query;

        header('Content-Type: application/json');
        echo json_encode($resultSet);
    }

    // ========================================================
}
