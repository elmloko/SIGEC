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
        $this->auto_render = FALSE;
        if (!$this->request->is_ajax()) {
            $this->request->redirect('error404');
            return;
        }
        // sin ningun tipo marcado tambien es una respuesta valida: se le quitan todos los permisos
        $tipos = Arr::get($_POST, 'tipos', array());
        if (!is_array($tipos)) {
            $tipos = array($tipos);
        }
        $user = ORM::factory('users', (int) Arr::get($_POST, 'id_user', 0));
        if (!$user->loaded()) {
            echo json_encode(array('ok' => FALSE, 'msg' => 'El usuario no existe.'));
            return;
        }
        // solo tipos de documento que existan y esten activos
        $validos = DB::query(Database::SELECT, 'SELECT id FROM tipos WHERE activo = 1')->execute()->as_array('id', 'id');
        $guardar = array_unique(array_intersect(array_map('intval', $tipos), array_map('intval', $validos)));
        try {
            $oTipos = new Model_Tipos();
            $oTipos->quitar($user->id);
            foreach ($guardar as $v) {
                $user->add('tipo', $v);
            }
            $user->save();
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'addDocumentos: ' . $e->getMessage());
            echo json_encode(array('ok' => FALSE, 'msg' => 'No se pudo guardar. Intente de nuevo.'));
            return;
        }
        echo json_encode(array('ok' => TRUE, 'cantidad' => count($guardar)));
    }

    public function action_otorgarPlazosDeRespuestaAUsuarios()
    {
        $this->auto_render = FALSE;
        if (!$this->request->is_ajax()) {
            $this->request->redirect('error404');
            return;
        }
        $user = ORM::factory('users', (int) Arr::get($_POST, 'id_user', 0));
        if (!$user->loaded()) {
            echo json_encode(array('ok' => FALSE, 'msg' => 'El usuario no existe.'));
            return;
        }
        // sin nadie marcado tambien es valido: se le quita el privilegio por completo
        $elegidos = Arr::get($_POST, 'id_usuarios_que_reciben_plazos', array());
        if (!is_array($elegidos)) {
            $elegidos = array($elegidos);
        }
        $elegidos = array_unique(array_filter(array_map('intval', $elegidos)));
        // solo personas que existan, y nunca uno mismo
        $validos = array();
        if ($elegidos) {
            $filas = DB::query(Database::SELECT, 'SELECT id FROM users WHERE id IN (' . implode(',', $elegidos) . ') AND id <> :id')
                    ->param(':id', (int) $user->id)
                    ->execute();
            foreach ($filas as $f) {
                $validos[] = (int) $f['id'];
            }
        }
        try {
            DB::query(Database::DELETE, 'DELETE FROM usuarios_habilitados_plazos WHERE id_usuario_padre = :id')
                    ->param(':id', (int) $user->id)
                    ->execute();
            foreach ($validos as $v) {
                DB::query(Database::INSERT, 'INSERT INTO usuarios_habilitados_plazos (id_usuario_padre, id_usuario_hijo) VALUES (:padre, :hijo)')
                        ->param(':padre', (int) $user->id)
                        ->param(':hijo', $v)
                        ->execute();
            }
        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'otorgarPlazos: ' . $e->getMessage());
            echo json_encode(array('ok' => FALSE, 'msg' => 'No se pudo guardar. Intente de nuevo.'));
            return;
        }
        echo json_encode(array('ok' => TRUE, 'cantidad' => count($validos)));
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
            // formato viejo a proposito: asi cambiarPassDefecto la reconoce, y al ingresar se obliga a cambiarla
            $password = Auth::instance()->hash_legado($oPassword->valor);
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

    // cambia la contraseña por defecto del sistema y la actualiza solo en los
    // usuarios que todavia la tienen sin modificar (los que ya la cambiaron no se tocan)
    public function action_cambiarPassDefecto()
    {
        if (!$this->request->is_ajax() || !$_POST) {
            echo json_encode(array('ok' => false, 'msg' => 'Solicitud invalida'));
            return;
        }

        $nuevo = trim(Arr::get($_POST, 'nuevo_pass', ''));
        if (strlen($nuevo) < 6) {
            echo json_encode(array('ok' => false, 'msg' => 'La contraseña debe tener al menos 6 caracteres'));
            return;
        }

        $auth = Auth::instance();
        $config = ORM::factory('configuracion')->where('campo', '=', 'passDefecto')->find();
        // esta tabla no tiene columna 'id', asi que loaded() no es fiable aqui; se valida por 'campo'
        if ($config->campo !== 'passDefecto') {
            echo json_encode(array('ok' => false, 'msg' => 'No se encontro la configuracion de contraseña por defecto'));
            return;
        }

        $hashAnterior = $auth->hash_legado($config->valor);
        $hashNuevo = $auth->hash_legado($nuevo);

        // solo se actualizan los usuarios cuyo password actual sea igual al hash de la contraseña por defecto anterior
        $afectados = ORM::factory('users')->where('password', '=', $hashAnterior)->find_all();
        $count = 0;
        foreach ($afectados as $u) {
            $u->password = $hashNuevo;
            $u->save();
            $count++;
        }

        // la tabla configuracion no tiene llave primaria, por lo que $config->save() de ORM
        // no puede saber que la fila ya existe (loaded() siempre da false) y terminaria
        // insertando una fila nueva en vez de actualizar; se hace un UPDATE directo
        DB::update('configuracion')
            ->set(array('valor' => $nuevo))
            ->where('campo', '=', 'passDefecto')
            ->execute();

        echo json_encode(array('ok' => true, 'count' => $count));
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

    // se devuelven datos, no HTML: la vista los escapa al mostrarlos
    public function action_usuariosconectados()
    {
        $result = array();
        $mUsers = new Model_Users();
        foreach ($mUsers->usuariosconectados() as $c) {
            $foto = DOCROOT . 'static/fotos/' . $c['username'] . '.jpg';
            $result[] = array(
                'id' => (int) $c['id'],
                'nombre' => $c['nombre'],
                'cargo' => $c['cargo'],
                'minutos' => (float) $c['segundos'],
                'foto' => file_exists($foto) ? '/static/fotos/' . $c['username'] . '.jpg' : '/static/fotos/' . ($c['genero'] == 'mujer' ? 'mujer' : 'hombre') . '.jpg',
            );
        }
        echo json_encode($result);
    }

    public function action_bitacora()
    {
        $result = array();
        $mUsers = new Model_Users();
        $page = max(1, (int) Arr::get($_GET, 'page', 1));
        $limit = (int) Arr::get($_GET, 'limit', 15);
        if ($limit < 1 || $limit > 100) {
            $limit = 15;
        }
        $q = trim((string) Arr::get($_GET, 'q', ''));
        foreach ($mUsers->actividades($page, $limit, $q) as $c) {
            $result[] = array(
                'fecha' => $c['fecha_hora'],
                'accion_realizada' => $c['accion_realizada'],
                'ip' => $c['ip_usuario'],
                'usuario' => $c['usuario'],
                'id_usuario' => (int) $c['id_usuario'],
            );
        }
        $total = $mUsers->actividades_total($q);
        echo json_encode(array(
            'data' => $result,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => (int) ceil($total / $limit)
        ));
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
          echo '<li> '.HTML::anchor('/admin/user/x_des/?id_user='.$id_user.'&id_destino='.$d->id,'[x]',array('class'=>'delDes')).' <span>'.HTML::chars($d->nombre).'</span> | '.HTML::chars($d->cargo). '</li>';
          }

         */
    }

    public function action_usuariosjson()
    {
// $this->view->disable();
//$user = '150';
        // estado=1 solo usuarios de alta, estado=0 solo usuarios de baja, sin estado todos
        $estado = Arr::get($_GET, 'estado', '');
        $where_estado = ($estado === '1' || $estado === '0') ? "WHERE u.habilitado = $estado" : "";
        $esql = "SELECT u.id,u.username,CONCAT(u.nombre,'<br><b>',u.cargo,'</b>') as usuario,u.email,u.logins,u.habilitado,n.nivel,o.oficina,e.entidad,
                from_unixtime(u.last_login) as last_login,u.mosca
                FROM users u INNER JOIN niveles n  ON u.nivel=n.id
                INNER JOIN oficinas o ON u.id_oficina=o.id
                INNER JOIN entidades e ON o.id_entidad=e.id
                $where_estado
                order by username";

        $query = "SELECT * FROM ( " . $esql . " ) as d";
        $pagenum = SqlSafe::int($_GET['pagenum']);
        $pagesize = SqlSafe::int($_GET['pagesize']);
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
                    $filtervalue = SqlSafe::value($_GET["filtervalue" . $i]);
// get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
// get the filter's column.
                    $filterdatafield = SqlSafe::field($_GET["filterdatafield" . $i]);
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

            $sortfield = SqlSafe::field($_GET['sortdatafield']);
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
                'username' => HTML::chars($row['username']),
                'usuario' => '<a href="/user/profile/' . $row['id'] . '" title="Ver perfil del usuario" class="text-primary-dark">' . $row['usuario'] . '</a>',
                'email' => HTML::chars($row['email']),
                'logins' => $row['logins'],
                'habilitado' => $row['habilitado'],
                'oficina' => HTML::chars($row['oficina']),
                'entidad' => HTML::chars($row['entidad']),
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
        $pagenum = SqlSafe::int($_GET['pagenum']);
        $pagesize = SqlSafe::int($_GET['pagesize']);
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
                    $filtervalue = SqlSafe::value($_GET["filtervalue" . $i]);
                    // get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = SqlSafe::field($_GET["filterdatafield" . $i]);
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

            $sortfield = SqlSafe::field($_GET['sortdatafield']);
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
                'cite_original' => HTML::chars($row['cite_original']),
                'tipo' => $row['tipo'],
                'destinatario' => $row['destinatario'],
                //'cargo_destinatario' => $row['cargo_destinatario'],
                'institucion_destinatario' => HTML::chars($row['institucion_destinatario']),
                'institucion_remitente' => HTML::chars($row['institucion_remitente']),
                'remitente' => $row['remitente'],
                ///'nombre_remitente' => $row['nombre_remitente'],
                //'cargo_remitente' => $row['cargo_remitente'],
                'referencia' => HTML::chars($row['referencia']),
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
