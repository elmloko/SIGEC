<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_ajax extends Controller
{

    public function before()
    {
        parent::before();
        $auth = Auth::instance();
        if (!$auth->logged_in()) {
            $this->request->redirect('/login');
        }
        $usuario = $auth->get_user();
        if ((int) $usuario->habilitado !== 1) {
            // cuenta deshabilitada: se cierra la sesion (destruye sesion nativa + tokens "recordarme") y se expulsa
            $auth->logout(TRUE, TRUE);
            $this->request->redirect('/login');
        }
        if ((int) $usuario->nivel !== 5) {
            $this->request->redirect('/login');
        }
    }

    //mensajes

    public function action_mensaje($id = '')
    {
        $mensaje = array(
            1 => 'Seleccione un usuario por favor.',
            2 => 'Error al seleccionar usuario.',
            3 => 'Contraseña restablecida correctamente',
            4 => 'Error al reestablecer la contraseña'
        );

        echo $mensaje[$id];
    }

    public function action_baja()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id'];
            $usuario = ORM::factory('users')->where('id', '=', $id)
                ->find();
            if ($usuario->loaded()) {
                $usuario->habilitado = 0;
                $usuario->save();
                $mUser = new Model_Estados();
                echo $user = $mUser->bajaUser($id);
            }
            //echo json_encode($);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_addDocumentos()
    {
        if ($this->request->is_ajax()) {
            $tipos = $_POST['tipos'];
            $id_user = $_POST['id_user'];
            // var_dump($tipos);
            //eliminamos los documentos asignados
            $oTipos = new Model_Tipos();
            $oTipos->quitar($id_user);
            foreach ($tipos as $k => $v) {
                try {
                    $user = ORM::factory('users', $id_user);
                    //$user->id;
                    $user->add('tipo', $v);
                    $user->save();
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }


            //echo json_encode($);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_otorgarPlazosDeRespuestaAUsuarios()
    {
        if ($this->request->is_ajax()) {

            $id_user = $_POST['id_user'];
            $id_usuarios = $_POST['id_usuarios_que_reciben_plazos'];

            $sql = "DELETE FROM usuarios_habilitados_plazos 
                    WHERE
                        id_usuario_padre = '$id_user';";
            $result_query_quitar_privilegios = DB::query(Database::DELETE, $sql)->execute();

            foreach ($id_usuarios as $key => $value) {

                try {
                    $sql_insertar = " INSERT INTO usuarios_habilitados_plazos (id_usuario_padre, id_usuario_hijo)
                                      VALUES ('$id_user', '$value')";

                    $result_query_insertar = DB::query(Database::DELETE, $sql_insertar)->execute();
                    echo json_encode($result_query_insertar);

                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
        } else {
            $this->request->redirect('error404');
        }
    }    

    public function action_alta()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id'];
            $usuario = ORM::factory('users')->where('id', '=', $id)
                ->find();
            if ($usuario->loaded()) {
                $usuario->habilitado = 1;
                $usuario->save();
                //añadimos el rol para que pueda logearse
                $rol = ORM::factory('usersrol');
                $rol->user_id = $id;
                $rol->role_id = 1;
                $rol->save();
            }
            //echo json_encode($);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_resetPass()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id'];
            $oPassword = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
            $password = hash_hmac('sha256', $oPassword->valor, '2, 4, 6, 7, 9, 15, 20, 23, 25, 30'); //sigec users
            // Create the user using form values
            $user = ORM::factory('users', $id);
            if ($user->loaded()) {
                $user->password = $password;
                $user->save();
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    public function action_conectados()
    {
        $result = array();
        $mUsers = new Model_Users();
        $activos = $mUsers->conectados();
        $cantidad = 0;
        foreach ($activos as $c) {
            $cantidad = $c['cantidad'];
        }
//$result[] = [{"name":"Test1", "data":[[1415567095000, 2117]]}, {"name":"Test2", "data":[[1415567095000, 2414]]}];
        $result[] = array(
            "name" => "Usuarios",
            "data" => array(
                array_values(array(time() * 1000, $cantidad))
            )
        );
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function action_usuariosconectados()
    {
        $result = array();
        $mUsers = new Model_Users();
        $activos = $mUsers->usuariosconectados();
        $cantidad = 0;

        foreach ($activos as $c) {
            $result[] = array(
                'fecha' => $c['last_active'],
                'segundos' => $c['segundos'],
                'nombre' => '<a href="/user/profile/' . $c['id'] . '">' . $c['nombre'] . '</a>',
                'cargo' => $c['cargo'],
            );
        }
//$result[] = [{"name":"Test1", "data":[[1415567095000, 2117]]}, {"name":"Test2", "data":[[1415567095000, 2414]]}];
        /* $result[] = array(
          "name" => "Usuarios",
          "data" => array(
          array_values(array(time() * 1000, $cantidad))
          )
          );
         */
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function action_bitacora()
    {
        $result = array();
        $mUsers = new Model_Users();

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 15;
        if ($page < 1) $page = 1;
        if ($limit < 1 || $limit > 100) $limit = 15;

        $activos = $mUsers->actividades($page, $limit);
        $total = $mUsers->actividades_total();

        foreach ($activos as $c) {
            $result[] = array(
                'fecha' => $c['fecha_hora'],
                'accion_realizada' => $c['accion_realizada'],
                'ip' => $c['ip_usuario'],
                'usuario' => $c['usuario']
            );
        }

        echo json_encode(array(
            'data' => $result,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => (int) ceil($total / $limit)
        ), JSON_NUMERIC_CHECK);
    }

    public function action_superior()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id_oficina'];
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $id)
                ->and_where('superior', '=', 0)
                ->find_all();
            $result = array();
            foreach ($usuarios as $u) {
                $result[] = array(
                    'value' => $u->id,
                    'text' => $u->nombre,
                );
            }
            if (sizeof($result) == 0) {
                $result[] = array(
                    0 => 'Superior'
                );
            }
            echo json_encode($result);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_oficinas()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id_entidad'];
            $oOficinas = new Model_Oficinas();
            $oficinas = $oOficinas->lista($id);
            $result = array();
            foreach ($oficinas as $o) {
                $result[] = array(
                    'value' => $o['value'],
                    'text' => $o['text'],
                );
            }
            echo json_encode($result);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_oficinassigla()
    {
        if ($this->request->is_ajax()) {
            $id = $_POST['id_entidad'];
            $oOficinas = new Model_Oficinas();
            $oficinas = $oOficinas->lista($id);
            //$result = array();
            $result[] = array(
                'value' => 0,
                'text' => 'Oficina Inicial'
            );
            foreach ($oficinas as $o) {
                $result[] = array(
                    'value' => $o['value'],
                    'text' => $o['text'] . " | " . $o['sigla'],
                );
            }
            echo json_encode($result);
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_addUser()
    {
        $id_user = $_POST['id'];
        $destinos = explode(';', $_POST['destinos']);
        foreach ($destinos as $k => $v) {
            if ($v != '') {
                $destino = ORM::factory('destinatarios');
                $destino->id_usuario = $id_user;
                $destino->id_destino = $v;
                $destino->save();
            }
        }
        /*         $result='';
          $o_destinos=New Model_Destinatarios();
          $destinatarios=$o_destinos->destinos($id_user);
          foreach($destinatarios as $d)
          {
          echo '<li> '.HTML::anchor('/admin/user/x_des/?id_user='.$id_user.'&id_destino='.$d->id,'[x]',array('class'=>'delDes')).' <span>'.$d->nombre.'</span> | '.$d->cargo. '</li>';
          }

         */
    }

    public function action_usuariosjson()
    {
// $this->view->disable();
//$user = '150';
        $esql = "SELECT u.id,u.username,CONCAT(u.nombre,'<br><b>',u.cargo,'</b>') as usuario,u.email,u.logins,u.habilitado,n.nivel,o.oficina,e.entidad,
                from_unixtime(u.last_login) as last_login,u.mosca
                FROM users u INNER JOIN niveles n  ON u.nivel=n.id
                INNER JOIN oficinas o ON u.id_oficina=o.id
                INNER JOIN entidades e ON o.id_entidad=e.id
                order by username";

        $query = "SELECT * FROM ( " . $esql . " ) as d";
        $pagenum = $_GET['pagenum'];
        $pagesize = $_GET['pagesize'];
        $start = $pagenum * $pagesize;

        $query = "SELECT * FROM ( " . $esql . " ) as d LIMIT $start, $pagesize";

        $sql = "SELECT COUNT(*) as found_rows FROM ( " . $esql . " ) as d ";
        $mDocumentos = new Model_Documentos();
        $result = $mDocumentos->ejecutarsql_array($sql);
        $total_rows = $result[0]['found_rows'];
//filter data
        $filterquery = "";

// filter data.
        if (isset($_GET['filterscount'])) {
            $filterscount = $_GET['filterscount'];

            if ($filterscount > 0) {
                $where = " WHERE (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i = 0; $i < $filterscount; $i++) {
// get the filter's value.
                    $filtervalue = $_GET["filtervalue" . $i];
// get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
// get the filter's column.
                    $filterdatafield = $_GET["filterdatafield" . $i];
// get the filter's operator.
                    $filteroperator = $_GET["filteroperator" . $i];

                    if ($tmpdatafield == "") {
                        $tmpdatafield = $filterdatafield;
                    } else if ($tmpdatafield <> $filterdatafield) {
                        $where .= ")AND(";
                    } else if ($tmpdatafield == $filterdatafield) {
                        if ($tmpfilteroperator == 0) {
                            $where .= " AND ";
                        } else
                            $where .= " OR ";
                    }

// build the "WHERE" clause depending on the filter's condition, value and datafield.
                    switch ($filtercondition) {
                        case "NOT_EMPTY":
                        case "NOT_NULL":
                            $where .= " " . $filterdatafield . " NOT LIKE '" . "" . "'";
                            break;
                        case "EMPTY":
                        case "NULL":
                            $where .= " " . $filterdatafield . " LIKE '" . "" . "'";
                            break;
                        case "CONTAINS_CASE_SENSITIVE":
                            $where .= " BINARY  " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                            break;
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                            break;
                        case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                            break;
                        case "EQUAL_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " = '" . $filtervalue . "'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue . "'";
                            break;
                        case "NOT_EQUAL_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " <> '" . $filtervalue . "'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue . "'";
                            break;
                        case "STARTS_WITH_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                            break;
                        case "ENDS_WITH_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                            break;
                    }

                    if ($i == $filterscount - 1) {
                        $where .= ")";
                    }

                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;
                }
// build the query.
                $query = "SELECT * FROM ( " . $esql . ") as d " . $where;
                $filterquery = $query;
                $result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
                $sql = "SELECT FOUND_ROWS() AS found_rows;";
                $rows = mysql_query($sql);
                $rows = mysql_fetch_assoc($rows);
                $new_total_rows = $rows['found_rows'];
                $query = "SELECT * FROM (" . $esql . ") as d " . $where . " LIMIT $start, $pagesize";
                $total_rows = $new_total_rows;
            }
        }
//sort data
        if (isset($_GET['sortdatafield'])) {

            $sortfield = $_GET['sortdatafield'];
            $sortorder = $_GET['sortorder'];

            if ($sortorder != '') {
                if ($_GET['filterscount'] == 0) {
                    if ($sortorder == "desc") {
                        $query = "SELECT * FROM (" . $esql . ") as d ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
                    } else if ($sortorder == "asc") {
                        $query = "SELECT * FROM (" . $esql . ") as d ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
                    }
                } else {
                    if ($sortorder == "desc") {
                        $filterquery .= " ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
                    } else if ($sortorder == "asc") {
                        $filterquery .= " ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
                    }
                    $query = $filterquery;
                }
            }
        }
//ejecucion de consulta        
        $result = $mDocumentos->ejecutarsql_array($query);

        $orders = null;
        foreach ($result as $row) {


            $orders[] = array(
//'id' => "/documento/edit/" . $row['id'],
                'id' => $row['id'],
                'username' => $row['username'],
                'usuario' => '<a href="/user/profile/' . $row['id'] . '" title="Ver perfil del usuario" class="text-primary-dark">' . $row['usuario'] . '</a>',
                'email' => $row['email'],
                'logins' => $row['logins'],
                'habilitado' => $row['habilitado'],
                'oficina' => $row['oficina'],
                'entidad' => $row['entidad'],
                'last_login' => $row['last_login'],
                'mosca' => $row['mosca'],
                // 'institucion_destin
                'edit' => '<a href="/admin/user/edit/' . $row['id'] . '" class="text-xl text-primary-dark"  title="Editar ' . $row['id'] . '" ><i class="md md-mode-edit"><i/></a>',
                //    'link' => $link,
            );
        }
        $data[] = array(
            'TotalRows' => $total_rows,
            'Rows' => $orders
        );
//echo $query;
        echo json_encode($data);

// echo $query;
    }

    public function action_addDoc()
    {
        $id_user = $_POST['id'];
        $documentos = explode(';', $_POST['documentos']);
        foreach ($documentos as $k => $v) {
            if ($v != '') {
                $user = ORM::factory('users', $id_user);
                $user->add('tipo', $v);
                $user->save();
            }
        }
    }

    //======================================
    //  lista de documentos
    //=====================================
    public function action_documentosjson()
    {
        // $this->view->disable();
        //$user = '150';
        $esql = "SELECT d.id,CONCAT(d.nombre_destinatario,'<br>',d.cargo_destinatario) as destinatario,
                CONCAT(d.nombre_remitente,'<br>',d.cargo_remitente) as remitente,
                d.institucion_remitente,d.institucion_destinatario,d.estado,
                d.referencia,d.nur,d.cite_original,DATE_FORMAT(d.fecha_creacion,'%d/%m/%Y %H:%i:%s') as fecha,fecha_creacion,t.tipo ,d.original
                FROM documentos d 
                INNER JOIN tipos t ON d.id_tipo=t.id                
                ORDER BY fecha_creacion DESC";

        $query = "SELECT * FROM ( " . $esql . " ) as d";
        $pagenum = $_GET['pagenum'];
        $pagesize = $_GET['pagesize'];
        $start = $pagenum * $pagesize;

        $query = "SELECT * FROM ( " . $esql . " ) as d LIMIT $start, $pagesize";

        $sql = "SELECT COUNT(*) as found_rows FROM ( " . $esql . " ) as d ";
        $mDocumentos = new Model_Documentos();
        $result = $mDocumentos->ejecutarsql_array($sql);
        $total_rows = $result[0]['found_rows'];
        //filter data
        $filterquery = "";

        // filter data.
        if (isset($_GET['filterscount'])) {
            $filterscount = $_GET['filterscount'];

            if ($filterscount > 0) {
                $where = " WHERE (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i = 0; $i < $filterscount; $i++) {
                    // get the filter's value.
                    $filtervalue = $_GET["filtervalue" . $i];
                    // get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = $_GET["filterdatafield" . $i];
                    // get the filter's operator.
                    $filteroperator = $_GET["filteroperator" . $i];

                    if ($tmpdatafield == "") {
                        $tmpdatafield = $filterdatafield;
                    } else if ($tmpdatafield <> $filterdatafield) {
                        $where .= ")AND(";
                    } else if ($tmpdatafield == $filterdatafield) {
                        if ($tmpfilteroperator == 0) {
                            $where .= " AND ";
                        } else
                            $where .= " OR ";
                    }

                    // build the "WHERE" clause depending on the filter's condition, value and datafield.
                    switch ($filtercondition) {
                        case "NOT_EMPTY":
                        case "NOT_NULL":
                            $where .= " " . $filterdatafield . " NOT LIKE '" . "" . "'";
                            break;
                        case "EMPTY":
                        case "NULL":
                            $where .= " " . $filterdatafield . " LIKE '" . "" . "'";
                            break;
                        case "CONTAINS_CASE_SENSITIVE":
                            $where .= " BINARY  " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                            break;
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "%'";
                            break;
                        case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue . "%'";
                            break;
                        case "EQUAL_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " = '" . $filtervalue . "'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue . "'";
                            break;
                        case "NOT_EQUAL_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " <> '" . $filtervalue . "'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue . "'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue . "'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue . "'";
                            break;
                        case "STARTS_WITH_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue . "%'";
                            break;
                        case "ENDS_WITH_CASE_SENSITIVE":
                            $where .= " BINARY " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue . "'";
                            break;
                    }

                    if ($i == $filterscount - 1) {
                        $where .= ")";
                    }

                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;
                }
                // build the query.
                $query = "SELECT * FROM ( " . $esql . ") as d " . $where;
                $filterquery = $query;
                $result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
                $sql = "SELECT FOUND_ROWS() AS found_rows;";
                $rows = mysql_query($sql);
                $rows = mysql_fetch_assoc($rows);
                $new_total_rows = $rows['found_rows'];
                $query = "SELECT * FROM (" . $esql . ") as d " . $where . " LIMIT $start, $pagesize";
                $total_rows = $new_total_rows;
            }
        }
        //sort data
        if (isset($_GET['sortdatafield'])) {

            $sortfield = $_GET['sortdatafield'];
            $sortorder = $_GET['sortorder'];

            if ($sortorder != '') {
                if ($_GET['filterscount'] == 0) {
                    if ($sortorder == "desc") {
                        $query = "SELECT * FROM (" . $esql . ") as d ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
                    } else if ($sortorder == "asc") {
                        $query = "SELECT * FROM (" . $esql . ") as d ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
                    }
                } else {
                    if ($sortorder == "desc") {
                        $filterquery .= " ORDER BY" . " " . $sortfield . " DESC LIMIT $start, $pagesize";
                    } else if ($sortorder == "asc") {
                        $filterquery .= " ORDER BY" . " " . $sortfield . " ASC LIMIT $start, $pagesize";
                    }
                    $query = $filterquery;
                }
            }
        }
        //ejecucion de consulta        
        $result = $mDocumentos->ejecutarsql_array($query);

        $orders = null;
        foreach ($result as $row) {
            if ($row['estado'] == 1) {
                $link = '<a href="/route/trace/?hr=' . $row['nur'] . '" title="Derivado: Ver seguimiento" class="text-xl text-success"><i class="md md- md-verified-user "></i></a>';
                $link .= '<a href="/print/hr/?code=' . $row['nur'] . '" target="_blank" title="Imprimir Hoja de ruta" class="text-xl text-primary-dark"><i class="md md- md-print"></i></a>';
            }

            if (strlen($row['nur']) < 2 && $row['estado'] == 0) {
                $link = '<a href="/document/asignar/' . $row['id'] . '" title="Asignar Hoja de Ruta al documento" class="text-xl text-accent-light"><i class="md md-class md-2x"></i></a>';
            }
            if (strlen($row['nur']) > 1 && $row['estado'] == 0) {
                $link = '<a href="/route/deriv/?hr=' . $row['nur'] . '" title="Imprmir hoja de ruta" class="text-xl text-warning"><i class="md md-play-circle-outline"></i></a>';
            }

            $orders[] = array(
                //'id' => "/documento/edit/" . $row['id'],
                'id' => $row['id'],
                'idd' => $row['id'],
                'nur' => '<a href="/route/deriv/?hr=' . $row['nur'] . '" title="Derivar Hora de Ruta" class="text-primary-dark">' . $row['nur'] . '</a>',
                'cite_original' => $row['cite_original'],
                'tipo' => $row['tipo'],
                'destinatario' => $row['destinatario'],
                //'cargo_destinatario' => $row['cargo_destinatario'],
                'institucion_destinatario' => $row['institucion_destinatario'],
                'institucion_remitente' => $row['institucion_remitente'],
                'remitente' => $row['remitente'],
                ///'nombre_remitente' => $row['nombre_remitente'],
                //'cargo_remitente' => $row['cargo_remitente'],
                'referencia' => $row['referencia'],
                'fecha_creacion' => $row['fecha_creacion'],
                'estado' => $row['estado'],
                'edit' => '<a href="/documento/edit/' . $row['id'] . '" class="text-xl text-primary-dark"  title="Editar ' . $row['tipo'] . '" ><i class="md md-mode-edit"><i/></a>',
                'link' => $link,
            );
        }
        $data[] = array(
            'TotalRows' => $total_rows,
            'Rows' => $orders
        );
        //echo $query;
        echo json_encode($data);

        // echo $query;
    }

}

?>
