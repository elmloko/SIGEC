<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Aajax extends Controller {

    protected $user;

    public function before() {
        $auth = Auth::instance();
        //si el usuario esta logeado entocnes mostramos el menu
        if ($auth->logged_in()) {
            $session = Session::instance();
            $this->user = $session->get('auth_user');
            parent::before();
        } else {
            $this->request->redirect('/login');
        }
    }

   

    public function action_documentosjson($user) {
        // $this->view->disable();
        //$user = '150';
        $esql = "SELECT d.id,d.nombre_destinatario,d.cargo_destinatario,d.nombre_remitente,d.cargo_remitente,d.estado,
                d.referencia,d.nur,d.cite_original,DATE_FORMAT(d.fecha_creacion,'%d/%m/%Y %H:%i:%s') as fecha,fecha_creacion,t.tipo ,d.original
                FROM documentos d 
                INNER JOIN tipos t ON d.id_tipo=t.id
                WHERE id_user='$user'
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
            }

            if (strlen($row['nur']) < 2 && $row['estado'] == 0) {
                $link = '<a href="/document/asignar/' . $row['id'] . '" title="Asignar Hoja de Ruta al documento" class="text-xl text-accent-light"><i class="md md-class md-2x"></i></a>';
            }
            if (strlen($row['nur']) > 1 && $row['estado'] == 0) {
                $link = '<a href="/route/deriv/?hr=' . $row['nur'] . '" title="Derivar Hora de Ruta" class="text-xl text-warning"><i class="md md-play-circle-outline"></i></a>';
            }

            $orders[] = array(
                //'id' => "/documento/edit/" . $row['id'],
                'id' => $row['id'],
                'idd' => $row['id'],
                'nur' => '<a href="/route/deriv/?hr=' . $row['nur'] . '" title="Derivar Hora de Ruta" class="text-primary-dark">' . $row['nur'] . '</a>',
                'cite_original' => $row['cite_original'],
                'tipo' => $row['tipo'],
                'nombre_destinatario' => $row['nombre_destinatario'],
                'cargo_destinatario' => $row['cargo_destinatario'],
                // 'institucion_destinatario' => $row['institucion_destinatario'],
                // 'institucion_remitente' => $row['institucion_remitente'],
                'nombre_remitente' => $row['nombre_remitente'],
                'cargo_remitente' => $row['cargo_remitente'],
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
