<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Bandeja extends Controller_DefaultTemplate
{

    protected $user;
    protected $menus;

    public function before()
    {

        parent::before();
    }

    public function after()
    {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'bandeja');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    // estilos y script compartidos de la bandeja (entrada, pendientes, enviados, archivo)
    private function estilos_bandeja()
    {
        $version = '?v=' . @filemtime(DOCROOT . 'static/css/bandeja.css') . @filemtime(DOCROOT . 'static/js/bandeja.js');
        $this->template->styles['static/css/bandeja.css' . $version] = 'all';
        $this->template->scripts[] = 'static/js/bandeja.js' . $version;
    }

    public function action_index()
    {
        $oSeg = New Model_Seguimiento();
//echo $this->user->id;
        $entrada = $oSeg->entrada($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen'); $this->estilos_bandeja();
        $this->template->title .= ' de Entrada';
        $this->template->titulo .= 'Entrada';
        $this->template->descripcion = 'lista de Correspondencia que le derivaron.';
        $this->template->content = View::factory('bandeja/entrada')
            ->bind('entrada', $entrada)
            ->bind('user', $this->user);
    }

    public function action_inbox()
    {
        $oSeg = New Model_Seguimiento();
        $inbox = $oSeg->estado(1, $this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->title .= '/ Recepción de correspondencia';
        $this->template->titulo .= 'Enviada';
        $this->template->descripcion = ' Correspondencia derivada que aun no entrego';
        $this->template->content = View::factory('bandeja/entrada')
            ->bind('norecibidos', $inbox);
    }

    public function action_doa()
    {
        if (!isset($_POST['id_seg'])) {
            $this->request->redirect('error404');
        }
        $ids = array_values(array_filter(array_map('intval', (array) $_POST['id_seg'])));
        if (!$ids) {
            $this->request->redirect('bandeja/pendientes');
        }
        $hojas = $this->hojas_seleccionadas($ids);
        $this->estilos_bandeja();
        $this->template->styles['static/css/bandeja-acciones.css?v=' . @filemtime(DOCROOT . 'static/css/bandeja-acciones.css')] = 'all';

        if ($_POST['accion'] == 0) { // 0 = archivar correspondencia
            // recorrido (derivaciones) de cada hoja de ruta, para ver el flujo antes de archivar
            $recorrido = array();
            if ($hojas) {
                $nurs = array();
                foreach ($hojas as $s) {
                    $nurs[] = $s['nur'];
                }
                $pasos = DB::query(Database::SELECT, 'SELECT s.id, s.nur, s.nombre_emisor, s.de_oficina, s.nombre_receptor,
                            s.a_oficina, s.fecha_emision, s.fecha_recepcion, s.estado, s.oficial, s.proveido
                        FROM seguimiento s
                        WHERE s.nur IN :nurs
                        ORDER BY s.id')
                        ->param(':nurs', array_unique($nurs))
                        ->execute()->as_array();
                foreach ($pasos as $p) {
                    $recorrido[$p['nur']][] = $p;
                }
            }
            // carpetas disponibles con lo que el usuario ya archivo en cada una (la mas reciente se preselecciona)
            $carpetas = DB::query(Database::SELECT, 'SELECT c.id, c.carpeta, COUNT(a.id) AS cc, MAX(a.fecha) AS ultima
                    FROM carpetas c
                    LEFT JOIN archivados a ON a.id_carpeta = c.id AND a.id_user = :u
                    WHERE c.id_oficina = :o
                       OR c.id IN (SELECT ar.id_carpeta FROM archivados ar WHERE ar.id_user = :u)
                    GROUP BY c.id, c.carpeta
                    ORDER BY c.carpeta')
                    ->param(':o', (int) $this->user->id_oficina)
                    ->param(':u', (int) $this->user->id)
                    ->execute()->as_array();

            $this->template->title .= ' / Archivar correspondencia';
            $this->template->titulo .= 'Archivar correspondencia';
            $this->template->descripcion = 'Archivar hojas de ruta en una carpeta';
            $this->template->content = View::factory('bandeja/archivar')
                ->set('carpetas', $carpetas)
                ->set('hojas', $hojas)
                ->set('recorrido', $recorrido)
                ->set('usuario', $this->user);
        } else {
            $this->template->title .= ' / Agrupar correspondencia';
            $this->template->titulo .= 'Agrupar correspondencia';
            $this->template->descripcion = 'Agrupar hojas de ruta bajo una principal';
            $this->template->content = View::factory('bandeja/agrupar')
                ->set('hojas', $hojas);
        }
    }

    // hojas de ruta seleccionadas en la bandeja con su documento y remitente, en el orden en que se marcaron
    private function hojas_seleccionadas(array $ids)
    {
        $filas = DB::query(Database::SELECT, 'SELECT s.id, s.nur, s.nombre_emisor, s.cargo_emisor, s.de_oficina,
                    s.oficial, s.prioridad, s.proveido, s.fecha_emision AS fecha, s.fecha_recepcion,
                    DATEDIFF(NOW(), s.fecha_emision) AS dias, a.accion,
                    (SELECT d.referencia FROM documentos d WHERE d.nur = s.nur ORDER BY d.id LIMIT 1) AS referencia,
                    (SELECT d.codigo FROM documentos d WHERE d.nur = s.nur ORDER BY d.id LIMIT 1) AS codigo,
                    (SELECT d.id FROM documentos d WHERE d.nur = s.nur ORDER BY d.id LIMIT 1) AS id_doc
                FROM seguimiento s
                LEFT JOIN acciones a ON a.id = s.accion
                WHERE s.id IN :ids')
                ->param(':ids', $ids)
                ->execute()->as_array('id');
        $hojas = array();
        foreach ($ids as $id) {
            if (isset($filas[$id])) {
                $hojas[] = $filas[$id];
            }
        }
        return $hojas;
    }

    //archivar correspondencia final
    public function action_agruparf()
    {
        if (isset($_POST['principal'])) {

            $principal = $_POST['principal'];
            $padre = ORM::factory('seguimiento', $principal);
            if ($padre->loaded()) {
                foreach ($_POST['seg'] as $k => $v) {
                    $hijo = ORM::factory('seguimiento', $v);
                    if ($padre->nur != $hijo->nur) {
                        $agrupar = ORM::factory('agrupaciones');
                        $agrupar->padre = $padre->nur;
                        $agrupar->hijo = $hijo->nur;
                        $agrupar->id_seguimiento = $hijo->id;
                        $agrupar->id_user = $this->user->id;
                        $agrupar->nombre = $this->user->nombre;
                        $agrupar->cargo = $this->user->cargo;
                        $agrupar->fecha = date('Y-m-d H:i:s');
                        $agrupar->save();
                        if ($agrupar->id > 0) { //si se agrupo! entonces cambiamos el estado del hijo
                            $hijo->estado = 6;
                            $hijo->save();
                        }
                    }
                }
                //por le decimos al seguimiento del padre que tiene hijos jiji
                $padre->hijo = 1;
                $padre->save();
                $_POST = array();
                $this->request->redirect('bandeja/agrupado/?hr=' . $padre->nur);
            }
        }
    }

    //archivar correspondencia final
    public function action_archivarf()
    {
        if ($_POST && !empty($_POST['seg'])) {
            if ($_POST['tipo'] == 0) {  //nueva carpeta
                $nombre_carpeta = mb_substr(trim(preg_replace('/\s+/u', ' ', (string) Arr::get($_POST, 'carpeta_input', ''))), 0, 100, 'UTF-8');
                if ($nombre_carpeta == '') {
                    $nombre_carpeta = 'Carpeta ' . date('d/m/Y H:i');
                }
                // si la oficina ya tiene una carpeta con ese nombre se usa esa en vez de duplicarla
                $carpeta = ORM::factory('carpetas')
                    ->where('id_oficina', '=', $this->user->id_oficina)
                    ->and_where('carpeta', '=', $nombre_carpeta)
                    ->find();
                if (!$carpeta->loaded()) {
                    $carpeta = ORM::factory('carpetas');
                    $carpeta->id_oficina = $this->user->id_oficina;
                    $carpeta->carpeta = $nombre_carpeta;
                    $carpeta->fecha_creacion = date('Y-m-d H:i:s');
                    $carpeta->save();
                }
                if ($carpeta->id > 0) { //si se creo la carpeta entonces:
                    $id_carpeta = $carpeta->id;
                    foreach ($_POST['seg'] as $k => $v) {
                        $seg = ORM::factory('seguimiento', $v);
                        if ($seg->loaded()) {
                            $archivo = ORM::factory('archivados');
                            $archivo->id_user = $this->user->id;
                            $archivo->nur = $seg->nur;
                            $archivo->id_carpeta = $id_carpeta;
                            $archivo->observaciones = mb_substr(trim((string) Arr::get($_POST, 'observaciones', '')), 0, 1000, 'UTF-8');
                            $archivo->fecha = date('Y-m-d H:i:s');
                            $archivo->save();
                            $seg->estado = 10;
                            $seg->id_archivo = $archivo->id;
                            $seg->save();
                        }
                    }
                    $_POST = array();
                }
            } else {
                $id_carpeta = (int) Arr::get($_POST, 'carpeta_lista', 0);
                if (!ORM::factory('carpetas', $id_carpeta)->loaded()) {
                    $this->request->redirect('bandeja/pendientes');
                }
                foreach ($_POST['seg'] as $k => $v) {
                    $seg = ORM::factory('seguimiento', $v);
                    if ($seg->loaded()) {
                        $carpeta = ORM::factory('archivados');
                        $carpeta->id_user = $this->user->id;
                        $carpeta->nur = $seg->nur;
                        $carpeta->id_carpeta = $id_carpeta;
                        $carpeta->observaciones = mb_substr(trim((string) Arr::get($_POST, 'observaciones', '')), 0, 1000, 'UTF-8');
                        $carpeta->fecha = date('Y-m-d H:i:s');
                        $carpeta->save();
                        $seg->estado = 10;
                        $seg->id_archivo = $carpeta->id;
                        $seg->save();
                    }
                }
                $_POST = array();
            }
            // abre directamente la carpeta donde se archivo
            $this->request->redirect('bandeja/archivo' . (!empty($id_carpeta) ? '?c=' . (int) $id_carpeta : ''));
        }
        $this->request->redirect('bandeja/pendientes');
    }

    //correlativo para un NURI -1=nuri / -2 = nur
    public function nuevo($type = -1)
    {
        $oCorrelativo = ORM::factory('correlativo')
            ->where('id_tipo', '=', $type)
            ->find();
        $oCorrelativo->correlativo = $oCorrelativo->correlativo + 1;
        $oCorrelativo->save();
        $codigo = '000' . $oCorrelativo->correlativo;
        if ($type == -1)
            $tipo = 'I/';
        else
            $tipo = '';
        $codigo = $tipo . date('Y') . '-' . substr($codigo, -4);
        return $codigo;
    }

    public function action_pendientes()
    {
        if (isset($_GET['id'])) {
            $user = ORM::factory('users')
                ->where('id', '=', $_GET['id'])
                ->and_where('superior', '=', $this->user->id)
                ->find();
            if ($user->id) {
                $oSeg = New Model_Seguimiento();
                $entrada = $oSeg->pendiente($user->id);
                $carpetas = ORM::factory('carpetas')->where('id_oficina', '=', $user->id_oficina)->find_all();
                $arrCarpetas = array();
                foreach ($carpetas as $c) {
                    $arrCarpetas[$c->id] = $c->carpeta;
                }
                $oDoc = New Model_Tipos();
                $documentos = $oDoc->misTipos($this->user->id);
                $options = array();
                foreach ($documentos as $d) {
                    $options[$d->id] = $d->tipo;
                }

                $oTipo = New Model_Tipos();
                //$mistipos = $oTipo->lista($this->user->id);
                $mistipos = $oTipo->misTipos($this->user->id);


                $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen'); $this->estilos_bandeja();
                $this->template->title .= ' / Corresponsdencia Pendientes';
                $this->template->titulo .= 'Pendientess ';
                $this->template->descripcion = 'Correspondencia recibida para pronta respuesta';
                $this->template->content = View::factory('bandeja/lista_pendientes')
                    ->bind('entrada', $entrada)
                    ->bind('carpetas', $arrCarpetas)
                    ->bind('user', $user)
                    ->bind('options', $options)
                    ->bind('tipos', $mistipos);
            } else {
                $this->template->content = 'No esta autorizado';
            }
        } else {
            $oSeg = New Model_Seguimiento();
            $entrada = $oSeg->pendiente($this->user->id);
            /*
            $carpetas = ORM::factory('carpetas')->where('id_oficina', '=', $this->user->id_oficina)->find_all();
            $arrCarpetas = array();
            foreach ($carpetas as $c) {
                $arrCarpetas[$c->id] = $c->carpeta;
            }
            */
            $oDoc = New Model_Tipos();
            $documentos = $oDoc->misTipos($this->user->id);
            /*
            $options = array();
            foreach ($documentos as $d) {
                $options[$d->id] = $d->tipo;
            }
            */
            $oTipo = New Model_Tipos();
            //$mistipos = $oTipo->lista($this->user->id);
            $mistipos = $oTipo->misTipos($this->user->id);

            $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen'); $this->estilos_bandeja();
            $this->template->title .= ' / Correspondencia Pendiente';
            $this->template->titulo .= 'Pendientes ';
            $this->template->descripcion = 'Correspondencia recibida para pronta respuesta';
            $this->template->content = View::factory('bandeja/pendientes')
                ->bind('entrada', $entrada)
                //->bind('carpetas', $arrCarpetas)
                ->bind('user', $this->user)
                //->bind('options', $options)
                ->bind('tipos', $mistipos);
        }
    }

    /* mis archivos */

    public function action_archivo()
    {
        // carpetas con lo que este usuario tiene archivado. Se cuenta igual que se lista el
        // contenido (seguimiento archivado del usuario), asi el numero de la carpeta coincide
        // con lo que se ve al abrirla
        $carpetas = DB::query(Database::SELECT, 'SELECT c.id, c.carpeta, COUNT(*) AS cc, MAX(a.fecha) AS ultima
                FROM seguimiento s
                INNER JOIN archivados a ON a.id = s.id_archivo
                INNER JOIN carpetas c ON c.id = a.id_carpeta
                WHERE s.derivado_a = :u AND s.estado = 10
                GROUP BY c.id, c.carpeta
                ORDER BY c.carpeta')
                ->param(':u', (int) $this->user->id)
                ->execute()->as_array();
        $this->template->title .= ' / Correspondencia Archivada';
        $this->template->titulo .= 'Archivo';
        $this->template->descripcion = 'Carpetas y documentos archivados';
        $this->template->styles = array('media/css/tablas.css' => 'all'); $this->estilos_bandeja();
        $this->template->content = View::factory('bandeja/archivadores')
            ->set('carpetas', $carpetas)
            ->set('destinos', $this->carpetas_destino())
            ->set('abrir', (int) Arr::get($_GET, 'c', 0));
    }

    // carpetas a las que el usuario puede mover: las de su oficina y las que ya usa
    private function carpetas_destino()
    {
        return DB::query(Database::SELECT, 'SELECT c.id, c.carpeta FROM carpetas c
                WHERE c.id_oficina = :o
                   OR c.id IN (SELECT a.id_carpeta FROM archivados a WHERE a.id_user = :u)
                GROUP BY c.id, c.carpeta
                ORDER BY c.carpeta')
                ->param(':o', (int) $this->user->id_oficina)
                ->param(':u', (int) $this->user->id)
                ->execute()->as_array();
    }

    // contenido de una carpeta, en JSON, para mostrarlo en la misma pantalla del archivo
    public function action_carpetajson($id = 0)
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $filas = DB::query(Database::SELECT, "SELECT s.id AS seg, s.nur, s.nombre_emisor, s.de_oficina, s.proveido,
                    DATE_FORMAT(s.fecha_emision, '%d/%m/%Y') AS recibido,
                    DATE_FORMAT(a.fecha, '%d/%m/%Y %H:%i') AS archivado, a.observaciones,
                    d.id AS doc, d.codigo, d.cite_original, d.referencia
                FROM seguimiento s
                INNER JOIN archivados a ON a.id = s.id_archivo
                LEFT JOIN documentos d ON d.nur = s.nur AND d.original = 1
                WHERE s.derivado_a = :u AND s.estado = 10 AND a.id_carpeta = :c
                GROUP BY s.id
                ORDER BY a.fecha DESC")
                ->param(':u', (int) $this->user->id)
                ->param(':c', (int) $id)
                ->execute()->as_array();
        echo json_encode(array('ok' => TRUE, 'filas' => $filas));
    }

    // mueve documentos archivados de una carpeta a otra (o a una carpeta nueva)
    public function action_mover()
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        if ($this->request->method() !== Request::POST) {
            echo json_encode(array('ok' => FALSE, 'msg' => 'Solicitud no válida.'));
            return;
        }
        $segs = array_filter(array_map('intval', (array) Arr::get($_POST, 'seg', array())));
        if (!$segs) {
            echo json_encode(array('ok' => FALSE, 'msg' => 'No eligió ningún documento.'));
            return;
        }
        $nueva = trim((string) Arr::get($_POST, 'nueva', ''));
        if ($nueva !== '') {
            $c = ORM::factory('carpetas');
            $c->id_oficina = $this->user->id_oficina;
            $c->carpeta = mb_substr($nueva, 0, 100, 'UTF-8');
            $c->fecha_creacion = date('Y-m-d H:i:s');
            $c->save();
            $destino = (int) $c->id;
        } else {
            $destino = (int) Arr::get($_POST, 'destino', 0);
            $permitida = FALSE;
            foreach ($this->carpetas_destino() as $c) {
                if ((int) $c['id'] === $destino) {
                    $permitida = TRUE;
                }
            }
            if (!$permitida) {
                echo json_encode(array('ok' => FALSE, 'msg' => 'Esa carpeta no está disponible.'));
                return;
            }
        }
        // solo lo archivado por el propio usuario
        $movidos = DB::query(Database::UPDATE, 'UPDATE archivados a
                INNER JOIN seguimiento s ON s.id_archivo = a.id
                SET a.id_carpeta = :d
                WHERE s.id IN (' . implode(',', $segs) . ') AND s.derivado_a = :u AND s.estado = 10')
                ->param(':d', $destino)
                ->param(':u', (int) $this->user->id)
                ->execute();
        if ((int) $movidos === 0) {
            echo json_encode(array('ok' => FALSE, 'msg' => 'No se movió nada: esos documentos no están en su archivo.'));
            return;
        }
        $nombre = ORM::factory('carpetas', $destino)->carpeta;
        $this->save($this->user->id_entidad, $this->user->id, 'Movió ' . $movidos . ' documento(s) archivado(s) a la carpeta <b>' . $nombre . '</b>');
        echo json_encode(array('ok' => TRUE, 'movidos' => (int) $movidos, 'destino' => $destino, 'nombre' => $nombre));
    }

    public function action_folder($id = '')
    {
        // la carpeta ahora se abre dentro de la pantalla del archivo
        $this->request->redirect('/bandeja/archivo?c=' . (int) $id);
        $oArchivo = New Model_Archivados();
        $carpeta = $oArchivo->carpeta($id, $this->user->id);
        $carpetas = ORM::factory('carpetas', $id);
        if (sizeof($carpeta) > 0) {
            $user = $this->user;
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->title .= ' / Carpeta - ' . $carpeta[0]['carpeta'];
            $this->template->titulo .= ' Carpeta - ' . $carpeta[0]['carpeta'];
            $this->template->descripcion = 'Correspondencia archivada en esta carpeta';
            $this->template->content = View::factory('bandeja/carpeta')
                ->bind('carpeta', $carpeta)
                ->bind('carpetas', $carpetas)
                ->bind('user', $user);
        }
    }

    public function action_recepcion()
    {
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->estado(1, $this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all');
        $this->template->title .= '<li><span>Entrada</span></li>';
        $this->template->content = View::factory('user/recepcionar')
            ->bind('norecibidos', $entrada);
    }

    public function action_receive($id = '')
    {
        if ($id != '') {
            $seguimiento = ORM::factory('seguimiento')->where('id', '=', $id)->and_where('derivado_a', '=', $this->user->id)->and_where('estado', '=', 1)->find();
            if ($seguimiento->loaded()) {
                $seguimiento->fecha_recepcion = date('Y-m-d H:i:s');
                $seguimiento->estado = 2; //2=pendiente oficial
                $seguimiento->save();
                //guardamos en vitacora
                $this->save($this->user->id_entidad, $this->user->id, $this->user->nombre . ' | <b>' . $this->user->cargo . '</b> Recepciono la hoja de ruta ' . $seguimiento->nur);

                $this->request->redirect('./bandeja');
            } else {
                $this->template->content = 'No se pudo recepcionar correspondencia.';
            }
        } else {
            $this->template->content = 'No se pudo recepcionar correspondencia o ya fue recepcionada.';
        }
    }

    //detalle agrupado
    public function action_agrupado()
    {
        $nur = Arr::get($_GET, 'hr', '');
        $hijos = array();
        $padre = ORM::factory('agrupaciones')->where('padre', '=', $nur)->find_all();
        foreach ($padre as $p) {
            //obtenemos los hijos
            $hijo = ORM::factory('documentos')->where('nur', '=', $p->hijo)->and_where('original', '=', 1)->find();
            if ($hijo->loaded()) {
                $hijos[$hijo->nur] = array(
                    'id_nur' => $hijo->nur,
                    'nur' => $hijo->nur,
                    'documento' => $hijo->codigo,
                    'referencia' => $hijo->referencia,
                    'destinatario' => $hijo->nombre_destinatario,
                    'cargo' => $hijo->cargo_destinatario,
                );
            }
        }
        if (sizeof($hijos) > 0) {
            $padre = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
            $this->template->styles = array('media/css/tablas.css' => 'all');
            $this->template->title .= ' / Correspondencia Agrupada';
            $this->template->titulo .= ' Agrupada';
            $this->template->descripcion = 'Hoja de ruta Agrupado';
            $this->template->content = View::factory('bandeja/agrupado')
                ->bind('hijos', $hijos)
                ->bind('padre', $padre);
        } else {
            $this->template->title .= ' / Agrupada';
            $this->template->titulo .= ' Agrupada';
            $this->template->descripcion = 'Hoja de ruta Agrupado';
            $this->template->content = '<div class="error"><b>Error:</b> NO agrupado !!! </div>';
        }
    }

    public function action_unarchive($id = '')
    {
        $seguimiento = ORM::factory('seguimiento')->where('id', '=', $id)->and_where('derivado_a', '=', $this->user->id)->find();
        if ($seguimiento->id) {
            //debemos eliminar de archivos
            $archivo = ORM::factory('archivados', array('id' => $seguimiento->id_archivo));
            $archivo->delete();
            //cambiamos el estado a pendiente
            $seguimiento->estado = 2;
            $seguimiento->id_archivo = 0;
            $seguimiento->save();
            $this->request->redirect('/bandeja/pendientes');
        } else {
            $this->template->content = View::factory('acceso_denegado');
        }
    }

    //correspondencia de salida
    public function action_enviados()
    {
        $info = array();
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->enviados($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all'); $this->estilos_bandeja();
        $this->template->content = View::factory('bandeja/enviados')
            ->bind('entrada', $entrada)
            ->bind('info', $info);
    }

    //imprimir enviado
    public function action_printDeriv($id = '')
    {
        $seg = ORM::factory('seguimiento', $id);
        if ($seg->loaded()) {
            if (($seg->derivado_por == $this->user->id) && ($seg->estado == '1')) {
                $oSeg = New Model_Seguimiento();
                $derivado = $oSeg->derivado($id);
                $this->template->content = View::factory('bandeja/print_deriv')
                    ->bind('derivado', $derivado);
            } else {
                echo 'no se puede';
            }
        }
    }

    //imprimir enviado
    public function action_cancel()
    {
        $id = $_GET['id'];
        $info = array();
        $seg = ORM::factory('seguimiento', array('id' => $id));
        $nur = $seg->nur;
        if ($seg->loaded()) {
            if (($seg->derivado_por == $this->user->id) && ($seg->estado == '1')) {
                $padre = $seg->id_seguimiento; //si tiene seguimiento anterior?
                $oficial = $seg->oficial;
                $seg->delete();
                //si tiene seguimiento
                if ($padre > 0) {
                    $oSeg = New Model_Seguimiento();
                    $oSeg->delete_deriv($padre);
                    $seguimiento = ORM::factory('seguimiento', array('id' => $padre));
                    if ($seguimiento->oficial == 2)
                        $seguimiento->oficial = 1;
                    $seguimiento->estado = 2; //pendiente
                    $seguimiento->save();
                    $info['info'] = '<b>Restaurado! : </b>La hoja de ruta <b>' . $seguimiento->nur . ' </b> fue restauradoa a <a href="/bandeja/pendientes"> Correspondencia recibida</a>, si quiere volver a derivar click <a href="/route/deriv/?hr=' . $seguimiento->nur . '" > aqui </a>.';
                } //primera derivacion
                else {
                    if ($oficial == 1) {
                        $documento = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
                        $documento->estado = 0;
                        $documento->save();
                        $info['info'] = '<b>Restaurado! : </b>La hoja de ruta <b>' . $documento->nur . ' </b> fue restaurada, para volver derivar busque el documento <a href="/document/edit/' . $documento->id . '">' . $documento->codigo . '</a> , o haga click <a href="/route/deriv/?hr=' . $documento->nur . '" > aqui </a>.';
                    }
                }
            } else {
                $error['error'] = '<b>Error!: </b>La hoja de ruta ya fue recibida por el destinatario o usted no lo tenia en su bandeja de salida';
            }
        }
        $oSeg = New Model_Seguimiento();
        $entrada = $oSeg->enviados($this->user->id);
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->title .= ' / enviada';
        $this->template->content = View::factory('bandeja/enviados')
            ->bind('entrada', $entrada)
            ->bind('info', $info)
            ->bind('error', $error);
    }

    public function action_grouped()
    {
        $count = ORM::factory('agrupaciones')->where('id_user', '=', $this->user->id)->count_all();
        $pagination = Pagination::factory(array(
            'total_items' => $count,
            'current_page' => array('source' => 'query_string', 'key' => 'page'),
            'items_per_page' => 50,
            'view' => 'pagination/floating',
        ));
        $page_links = $pagination->render();
        $oDocumentos = New Model_Documentos();
        $agrupados = $oDocumentos->agrupaciones($this->user->id, $pagination->offset, $pagination->items_per_page);
        $this->template->title .= '| Agrupados';
        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('bandeja/agrupados')
            ->bind('result', $agrupados)
            ->bind('count', $count)
            ->bind('page_links', $page_links);
    }

    /* BANDEJA DE RECLAMOS */
    public function action_reclamos()
    {
        $user = $this->user;

        // === [INICIO] PARAMETROS DE LA BASE DE DATOS ===
        $database_instance = Database::instance('sigec');
        $database_instance_array = (Array)$database_instance;

        //      Se adiciona { "\0*\0" } cuando el atributo del objeto es 'protected'
        $params_database_instance = $database_instance_array["\0*\0" . '_config']['connection'];
        $hostname_DB = $params_database_instance['hostname'];
        $database_name = $params_database_instance['database'];
        $username_DB = $params_database_instance['username'];
        $password_DB = $params_database_instance['password'];

        $database = new PDO('mysql:host=' . $hostname_DB . ';dbname=' . $database_name, $username_DB, $password_DB);
        $database->exec("SET NAMES 'utf8';");
        // === [FIN] PARAMETROS DE LA BASE DE DATOS ===

        $sql_observaciones_externas = "CALL get_observaciones_externas_user('$user->id');";
        $observaciones_externas = $database->prepare($sql_observaciones_externas);
        $observaciones_externas->execute();
        $result_observaciones_externas = $observaciones_externas->fetchAll(PDO::FETCH_OBJ);
        do {
            $observaciones_externas->fetchAll(PDO::FETCH_OBJ);
        } while ($observaciones_externas->nextRowSet());

        $this->template->styles = array('media/css/tablas.css' => 'all', 'media/css/modal.css' => 'screen');
        $this->template->title .= ' / Correspondencia con Reclamos';
        $this->template->titulo .= 'Reclamos ';
        $this->template->descripcion = 'Correspondencia para pronta respuesta';
        $this->template->content = View::factory('bandeja/lista_reclamos')
            ->bind('result_observaciones_externas', $result_observaciones_externas)
            ->bind('user', $this->user);
    }
}

?>
