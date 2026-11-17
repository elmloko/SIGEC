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
            $this->request->redirect('bandeja/pendientes');
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
                WHERE s.id IN :ids AND s.derivado_a = :u AND s.estado = 2')
                ->param(':ids', $ids)
                ->param(':u', (int) $this->user->id)
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

            $principal = (int) $_POST['principal'];
            // solo se agrupan hojas pendientes en la bandeja del propio usuario
            $padre = ORM::factory('seguimiento')->where('id', '=', $principal)
                ->and_where('derivado_a', '=', $this->user->id)->and_where('estado', '=', 2)->find();
            if ($padre->loaded()) {
                $unidas = 0;
                foreach ((array) Arr::get($_POST, 'seg', array()) as $k => $v) {
                    $hijo = ORM::factory('seguimiento')->where('id', '=', (int) $v)
                        ->and_where('derivado_a', '=', $this->user->id)->and_where('estado', '=', 2)->find();
                    if ($hijo->loaded() && $padre->nur != $hijo->nur) {
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
                            $unidas++;
                        }
                    }
                }
                if ($unidas > 0) {
                    // el seguimiento de la principal queda marcado como que tiene hijos
                    $padre->hijo = 1;
                    $padre->save();
                    $_POST = array();
                    $this->request->redirect('bandeja/agrupado/?hr=' . urlencode($padre->nur));
                }
            }
        }
        // nada valido que agrupar (hojas ajenas, ya movidas o sin seleccion)
        $this->request->redirect('bandeja/pendientes');
    }

    // seguimiento pendiente (estado 2) en la bandeja del usuario; vacio si es ajeno o ya se movio
    private function seg_pendiente($id)
    {
        return ORM::factory('seguimiento')->where('id', '=', (int) $id)
            ->and_where('derivado_a', '=', $this->user->id)
            ->and_where('estado', '=', 2)
            ->find();
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
                        $seg = $this->seg_pendiente($v);
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
                    $seg = $this->seg_pendiente($v);
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
        // la vista user/recepcionar ya no existe: lo "por recibir" se atiende en la bandeja de entrada
        $this->request->redirect('bandeja');
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

    // ultimo paso oficial (no copia) de una hoja de ruta: dice donde esta ahora y en que estado
    private function ultimo_paso($nur)
    {
        return DB::query(Database::SELECT, 'SELECT s.id, s.derivado_a, s.nombre_receptor, s.cargo_receptor, s.a_oficina,
                    s.nombre_emisor, s.de_oficina, s.fecha_emision, s.fecha_recepcion, s.estado
                FROM seguimiento s
                WHERE s.nur = :nur AND s.oficial > 0
                ORDER BY s.id DESC LIMIT 1')
            ->param(':nur', $nur)
            ->execute()->current();
    }

    // la agrupacion solo se puede deshacer mientras la principal siga pendiente en la bandeja del usuario
    private function puede_desagrupar($ultimo)
    {
        return $ultimo && (int) $ultimo['derivado_a'] === (int) $this->user->id && (int) $ultimo['estado'] === 2;
    }

    //detalle agrupado
    public function action_agrupado()
    {
        $nur = trim(Arr::get($_GET, 'hr', ''));
        // hojas unidas a la principal, con su documento original y quien/cuando las agrupo
        $hijos = DB::query(Database::SELECT, 'SELECT a.id AS id_agrupacion, a.hijo AS nur, a.id_seguimiento, a.nombre AS agrupado_por,
                    a.cargo AS agrupado_cargo, a.fecha AS fecha_agrupado,
                    d.id AS id_doc, d.codigo, d.cite_original, d.referencia, d.nombre_destinatario, d.cargo_destinatario,
                    d.nombre_remitente, d.cargo_remitente, d.institucion_remitente, d.fecha_creacion, d.hojas,
                    s.estado AS estado_seg, s.derivado_a
                FROM agrupaciones a
                LEFT JOIN documentos d ON d.nur = a.hijo AND d.original = 1
                LEFT JOIN seguimiento s ON s.id = a.id_seguimiento
                WHERE a.padre = :nur
                ORDER BY a.id')
            ->param(':nur', $nur)
            ->execute()->as_array();

        $this->template->title .= ' / Correspondencia Agrupada';
        $this->template->titulo .= ' Agrupada';
        $this->template->descripcion = 'Hoja de ruta Agrupado';
        if (!$hijos) {
            $this->template->content = '<div class="error"><b>Error:</b> La hoja de ruta <b>' . HTML::chars($nur) . '</b> no tiene hojas agrupadas.</div>';
            return;
        }
        $this->estilos_bandeja();
        $this->template->styles['static/css/bandeja-acciones.css?v=' . @filemtime(DOCROOT . 'static/css/bandeja-acciones.css')] = 'all';

        $padre = ORM::factory('documentos')->where('nur', '=', $nur)->and_where('original', '=', 1)->find();
        // documentos de la principal (el original y los que se le anadieron despues)
        $documentos = DB::query(Database::SELECT, 'SELECT d.id, d.codigo, d.referencia, d.fecha_creacion, d.original, d.hojas, t.tipo
                FROM documentos d
                LEFT JOIN tipos t ON t.id = d.id_tipo
                WHERE d.nur = :nur
                ORDER BY d.original DESC, d.id')
            ->param(':nur', $nur)
            ->execute()->as_array();

        // recorrido de la principal y de cada hoja agrupada
        $nurs = array($nur);
        foreach ($hijos as $hj) {
            $nurs[] = $hj['nur'];
        }
        $recorrido = array();
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
        $estados = DB::query(Database::SELECT, 'SELECT id, estado FROM estados')->execute()->as_array('id', 'estado');
        $ultimo = $this->ultimo_paso($nur);

        $session = Session::instance();
        $this->template->content = View::factory('bandeja/agrupado')
            ->set('hijos', $hijos)
            ->set('padre', $padre)
            ->set('nur', $nur)
            ->set('documentos', $documentos)
            ->set('recorrido', $recorrido)
            ->set('estados', $estados)
            ->set('ultimo', $ultimo)
            ->set('puede_desagrupar', $this->puede_desagrupar($ultimo))
            ->set('mensaje', $session->get_once('agrupado_mensaje', ''))
            ->set('error', $session->get_once('agrupado_error', ''));
    }

    // quita una hoja de la agrupacion y la devuelve a pendientes del usuario
    public function action_desagrupar()
    {
        $session = Session::instance();
        $padre_nur = trim(Arr::get($_POST, 'padre', ''));
        $id_agrupacion = (int) Arr::get($_POST, 'id', 0);
        if ($this->request->method() !== 'POST' || $padre_nur === '' || !$id_agrupacion) {
            $this->request->redirect('bandeja/pendientes');
        }
        $volver = 'bandeja/agrupado/?hr=' . urlencode($padre_nur);

        $agrupacion = ORM::factory('agrupaciones')->where('id', '=', $id_agrupacion)->and_where('padre', '=', $padre_nur)->find();
        $ultimo = $this->ultimo_paso($padre_nur);
        if (!$agrupacion->loaded() || !$this->puede_desagrupar($ultimo)) {
            $session->set('agrupado_error', 'No se puede quitar la hoja: la principal ya no está pendiente en su bandeja.');
            $this->request->redirect($volver);
        }
        $hijo = ORM::factory('seguimiento', $agrupacion->id_seguimiento);
        if (!$hijo->loaded() || (int) $hijo->estado !== 6 || (int) $hijo->derivado_a !== (int) $this->user->id) {
            $session->set('agrupado_error', 'La hoja ' . $agrupacion->hijo . ' ya no está agrupada a su nombre.');
            $this->request->redirect($volver);
        }

        $db = Database::instance();
        $db->begin();
        try {
            $hijo->estado = 2; // vuelve a pendiente
            $hijo->save();
            $nur_hijo = $agrupacion->hijo;
            $agrupacion->delete();
            $quedan = ORM::factory('agrupaciones')->where('padre', '=', $padre_nur)->count_all();
            if ($quedan == 0) {
                // la principal deja de tener hijos
                DB::update('seguimiento')->set(array('hijo' => 0))->where('id', '=', $ultimo['id'])->execute();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            Kohana::$log->add(Log::ERROR, 'desagrupar: ' . $e->getMessage());
            $session->set('agrupado_error', 'No se pudo quitar la hoja de la agrupación.');
            $this->request->redirect($volver);
        }

        if ($quedan == 0) {
            $this->request->redirect('bandeja/pendientes');
        }
        $session->set('agrupado_mensaje', 'La hoja ' . $nur_hijo . ' se quitó del grupo y volvió a sus pendientes.');
        $this->request->redirect($volver);
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
            // la vista acceso_denegado no existe; se informa sin romper la pagina
            $this->template->content = '<div class="error"><b>Error:</b> Ese documento no está en su archivo o ya fue desarchivado.</div>';
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
}

?>
