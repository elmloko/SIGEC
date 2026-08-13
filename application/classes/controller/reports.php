<?php

defined('SYSPATH') or die('Acceso denegado');

class Controller_Reports extends Controller_DefaultTemplate {

    protected $user;
    protected $menus;

    public function before() {

        parent::before();
    }

    public function after() {
        $this->template->menutop = View::factory('templates/menutop')->bind('menus', $this->menus)->set('controller', 'reports');
        $this->template->nombre = $this->user->nombre;
        $this->template->username = $this->user->username;
        $this->template->email = $this->user->email;
        parent::after();
    }

    public function action_route() {
        $this->template->styles = array(
            'static/css/theme-default/libs/bootstrap-datepicker/datepicker3.css' => 'screen',
            'static/css/theme-default/libs/select2/select2.css' => 'screen',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'static/js/libs/bootstrap-datepicker/bootstrap-datepicker.js',
            'static/js/libs/select2/select2.min.js'
        );
        $this->template->content = View::factory('reportes/hojasruta');
    }

    //pendientes nuevos
    public function action_pendientes() {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo.= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes');
    }

    //pendientes agrupado por oficina
    public function action_agrupadoPorOficina() {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo.= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes_agrupado_por_oficina');
    }

    // Pendientes con retraso
    public function action_pendientesConRetraso() {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo.= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes_con_retraso');
    }    

    public function action_documentos() {
        $o_reports = New Model_documentos();
        $correlativos = $o_reports->correlativos($this->user->id_oficina);
        $documentos = $o_reports->documentos_generados($this->user->id_oficina);
        $tipos = ORM::factory('tipos')->where('doc', '=', 0)->find_all();
        $a_doc = array();
        foreach ($tipos as $t) {
            $a_doc[$t->id] = array('tipo' => $t->plural, 'cantidad' => 0, 'id' => $t->id, 'correlativo' => 0);
        }
        foreach ($documentos as $d) {
            $id = $d['id'];
            $a_doc[$id]['cantidad'] = $d['cantidad'];
        }
        foreach ($correlativos as $c) {
            $id = $c['id'];
            $a_doc[$id]['correlativo'] = $c['correlativo'];
        }

        $oficinas = array(0 => '[TODAS]');
        $oOficinas = ORM::factory('oficinas')->find_all();
        foreach ($oOficinas as $o) {
            $oficinas[$o->id] = $o->oficina;
        }

        $this->template->title.='/ Reporte de Documentos';
        $this->template->titulo.='Documentos';
        $this->template->descripcion = 'correlativo y total de documentos generados de su oficina';
        $this->template->styles = array(
            'media/css/select2.css' => 'screen'
        );
        $user = $this->user;

        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa a reporte <b>documentos</b>');
        $this->template->scripts = array('media/Highcharts/js/modules/exporting.js',
            'media/Highcharts/js/highcharts-more.js',
            'media/Highcharts/js/highcharts.js', 'media/js/select2.min.js');
        $this->template->content = View::factory('reportes/documentos_oficina')
                ->bind('documentos', $a_doc)
                ->bind('user', $user)
                ->bind('oficinas', $oficinas);
    }

    public function action_historial() {
        $this->template->title.='/ Reporte de Documentos';
        $this->template->titulo.='Documentos';
        $this->template->descripcion = 'Correlativo y total de documentos generados de su oficina';
        $this->template->styles = array(
            'media/amcharts/style.css' => 'screen',
        );
        $user = $this->user;
        $this->template->scripts = array(
            'media/highstock/js/modules/exporting.js',
            'media/highstock/js/highstock.js',
        );
        $this->template->content = View::factory('reportes/historial');

        $this->save($this->user->id_entidad, $this->user->id, 'Genera reporte <b>Historial de  documentos</b>');
    }

// dashboard de reportes



    public function action_index() {

        $gestion = date('Y');
        $mes = (int) date('m') - 1;
        if ($_POST) {
            $mes = $_POST['mes'];
        }
        $gestiones = array(0 => 'TODO', 2013 => '2013', '2014' => '2014','2015'=>'2015');
        $oMeses = ORM::factory('meses')
                ->find_all();
        $meses = array(0 => 'TODOS');
        foreach ($oMeses as $m) {
            $meses[$m->id] = $m->mes;
        }
        $this->template->title.=' / Dashboard';
        $this->template->titulo.=' Dashboard';
        $this->template->descripcion = 'Indicadores';
        // $descripcion = 'ACTUALIZADO A FECHA: <b>' . date('d/m/Y', strtotime($fecha->fecha_actualizacion)) . '</b>';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'screen',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
            'media/css/select2.css' => 'screen'
        );
        $this->template->scripts = array(
            'media/jqwidgets/jqxgauge.js',
            'media/jqwidgets/jqxchart.js',
            'media/scripts/gettheme.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxcore.js',
            'media/Highcharts/js/modules/exporting.js',
            'media/Highcharts/js/highcharts-more.js',
            'media/Highcharts/js/highcharts.js', 'media/js/select2.min.js'
                //'media/js/jquery-1.8.2.js'
        );
        //lista de todas las oficinas

        $oficinas = array(0 => '[TODAS]');
        $oOficinas = ORM::factory('oficinas')->find_all();
        foreach ($oOficinas as $o) {
            $oficinas[$o->id] = $o->oficina;
        }
        $user = $this->user;
        $this->template->content = View::factory('reportes/dashboard')
                ->bind('gestion', $gestion)
                ->bind('gestiones', $gestiones)
                ->bind('meses', $meses)
                ->bind('mes', $mes)
                ->bind('user', $user)
                ->bind('descripcion', $descripcion)
                ->bind('oficinas', $oficinas)
                ->bind('gestiones', $gestiones);
    }

    public function action_jqwdocumentos() {
        $oficina = $_GET['oficina'];
        $tipodocumento = ORM::factory('tipos')->where('plural', '=', trim($_GET['tipo']))->find();
        if ($tipodocumento->loaded()) {
            $ofi = ORM::factory('oficinas', $oficina);
            $tipo = $tipodocumento->id;
            $this->template->titulo.=$ofi->oficina;
            $this->template->descripcion = 'Procesos generados';
            $this->template->styles = array(
                'media/jqwidgets/styles/jqx.custom.css' => 'all',
                'media/jqwidgets/styles/jqx.base.css' => 'all',
            );
            $this->template->scripts = array(
                //'media/jqwidgets/scripts/demos.js',
                'media/jqwidgets/globalization/globalize.js',
                'media/jqwidgets/jqxcalendar.js',
                'media/jqwidgets/jqxdatetimeinput.js',
                'media/jqwidgets/jqxwindow.js',
                'media/jqwidgets/jqxnumberinput.js',
                'media/jqwidgets/jqxgrid.aggregates.js',
                'media/jqwidgets/jqxgrid.columnsresize.js',
                'media/jqwidgets/jqxgrid.columnsreorder.js',
                'media/jqwidgets/jqxdata.export.js',
                'media/jqwidgets/jqxgrid.export.js',
                'media/jqwidgets/jqxgrid.edit.js',
                'media/jqwidgets/jqxgrid.grouping.js',
                'media/jqwidgets/jqxgrid.selection.js',
                'media/jqwidgets/jqxgrid.filter.js',
                'media/jqwidgets/jqxgrid.pager.js',
                'media/jqwidgets/jqxgrid.sort.js',
                'media/jqwidgets/jqxdata.js',
                'media/jqwidgets/jqxgrid.js',
                'media/jqwidgets/jqxdropdownlist.js',
                'media/jqwidgets/jqxlistbox.js',
                'media/jqwidgets/jqxcheckbox.js',
                'media/jqwidgets/jqxmenu.js',
                'media/jqwidgets/jqxdata.export.js',
                'media/jqwidgets/jqxgrid.export.js',
                'media/jqwidgets/jqxscrollbar.js',
                'media/jqwidgets/jqxbuttons.js',
                'media/jqwidgets/jqxcore.js',
            );
            $this->save($this->user->id_entidad, $this->user->id, 'Genera reporte documentos generados: ' . $tipodocumento->plural);
            $this->template->content = View::factory('reportes/jqwdocs')
                    ->bind('oficina', $oficina)
                    ->bind('tipo_doc', $tipodocumento)
                    ->bind('tipo', $tipo);
        }
    }

    public function action_vitacora() {

        $this->template->titulo.='Vitacora';
        $this->template->descripcion = 'Acciones realizadas en el sistema';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa a ver la vitacora');
        $this->template->content = View::factory('reportes/vitacora');
    }

    public function action_explosion() {
        $oficina = $_GET['oficina'];
        $ofi = ORM::factory('oficinas', $oficina);
        $this->template->titulo.=$ofi->oficina;
        $this->template->descripcion = 'Procesos generados';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->template->content = View::factory('reportes/jqwexplosion')
                ->bind('oficina', $oficina);
    }

    public function action_jqwdoc() {
        $documento = $_GET['documento'];
        $tipo = ORM::factory('tipos')->where('plural', '=', trim($documento))->find();
        $this->template->titulo.=$documento;
        $this->template->descripcion = 'Documentos generados por tipo';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.metro.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/scripts/gettheme.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->template->content = View::factory('reportes/jqwdocumentos')
                ->bind('tipo', $tipo);
    }

    public function action_despacho() {

        $this->template->titulo.=" Documentacion Ingresada";
        $this->template->descripcion.=" Geren el reporte y luego exportelo a excel";
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.metro.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->template->content = View::factory('reportes/general');
    }

    //Correspondencia enviada a direferentes unidades
    public function action_enviados() {
        //GENERAMOS EL REPORTE
        $enviados = array();
        if (isset($_POST['submit'])) {
            $id = $this->user->id;
            $ido = $_POST['oficina'];
            $fecha1 = date::dateformat($_POST['fecha1']) . ' 00:00:00';
            $fecha2 = date::dateformat($_POST['fecha2']) . ' 11:59:59';
            $mSeguimiento = new Model_Seguimiento();
            if ($_POST['oficina'] > 0) {
                $enviados = $mSeguimiento->enviadosOficina($id, $ido, $fecha1, $fecha2);
            } else {
                $enviados = $mSeguimiento->enviadosTodos($id, $fecha1, $fecha2);
            }
            //var_dump($enviados);
        }

        $entidad = $this->user->id_entidad;
        $oOficinas = ORM::factory('oficinas')->where('id_entidad', '=', $entidad)->find_all();
        $oficinas = array(0 => '[ TODOS ]');
        foreach ($oOficinas as $o) {
            $oficinas[$o->id] = $o->oficina;
        }
        $this->template->title.='/ Reporte de Enviados';
        $this->template->titulo.='Correspondencia Enviada';
        $this->template->descripcion = ' Hojas de ruta enviados';
        $this->template->styles = array('media/css/select2.css' => 'screen', 'media/css/tablas.css' => 'screen');
        $this->template->scripts = array('media/js/select2.min.js');
        $this->template->content = View::factory('reportes/enviados')
                ->bind('oficinas', $oficinas)
                ->bind('enviados', $enviados);
    }

    //pendientes oficina
    public function action_pendientes_oficina() {

        $this->request->redirect('/reports/pendientes');
        //echo $this->user->dependencia;
        if ($this->user->dependencia == 0) {
            //usuarios que pertences a mi oficina  
            $users = ORM::factory('users')->where('id_oficina', '=', $this->user->id_oficina)->or_where('superior', '=', $this->user->id)->find_all();
            $pendientes = array();
            foreach ($users as $u) {
                $oficial = ORM::factory('seguimiento')->where('derivado_a', '=', $u->id)->and_where('estado', '=', 2)->and_where('oficial', '=', 1)->count_all();
                $copia = ORM::factory('seguimiento')->where('derivado_a', '=', $u->id)->and_where('estado', '=', 2)->and_where('oficial', '=', 0)->count_all();
                $archivado = ORM::factory('seguimiento')->where('derivado_a', '=', $u->id)->and_where('estado', '=', 10)->count_all();
                $pendientes[] = array(
                    'nombre' => $u->nombre,
                    'cargo' => $u->cargo,
                    'id' => $u->id,
                    'oficial' => $oficial,
                    'copia' => $copia,
                    'archivado' => $archivado
                );
            }
            $oficina = ORM::factory('oficinas', $this->user->id_oficina);
            $this->template->title = 'Reportes / Pendientes Oficina';
            $this->template->titulo.='Pendientes Oficina';
            $this->template->descripcion.='Pendientes Oficina';
            $this->template->scripts = array(
                //                           'media/Highcharts/js/modules/canvas-tools.js',
                'media/Highcharts/js/modules/exporting.js',
                'media/Highcharts/js/highcharts.js',
            );
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->content = View::factory('reportes/pendientes_oficina')
                    ->bind('pendientes', $pendientes)
                    ->bind('oficina', $oficina);
            //var_dump($pendientes);
        }
    }

 //pendientes despacho
    public function action_pendientesDespacho() {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo.= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes_despacho');
    }

    //pendientes fuera de plazo
    public function action_fueraDePlazo()
    {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo .= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes_fuera_de_plazo');
    }

    // pendientes fuera de plazo, (por oficina)
    public function action_fueraDePlazoOficina()
    {
        $this->template->descripcion = 'Procesos generados';
        $this->template->titulo .= 'Pendientes';
        $this->template->styles = array(
            'media/jqwidgets/styles/jqx.custom.css' => 'all',
            'media/jqwidgets/styles/jqx.base.css' => 'all',
        );
        $this->template->scripts = array(
            //'media/jqwidgets/scripts/demos.js',
            'media/jqwidgets/globalization/globalize.js',
            'media/jqwidgets/jqxcalendar.js',
            'media/jqwidgets/jqxdatetimeinput.js',
            'media/jqwidgets/jqxwindow.js',
            'media/jqwidgets/jqxnumberinput.js',
            'media/jqwidgets/jqxgrid.aggregates.js',
            'media/jqwidgets/jqxgrid.columnsresize.js',
            'media/jqwidgets/jqxgrid.columnsreorder.js',
            'media/jqwidgets/jqxgrid.export.js',
            'media/jqwidgets/jqxdata.export.js',
            'media/jqwidgets/jqxgrid.edit.js',
            'media/jqwidgets/jqxgrid.grouping.js',
            'media/jqwidgets/jqxgrid.selection.js',
            'media/jqwidgets/jqxgrid.filter.js',
            'media/jqwidgets/jqxgrid.pager.js',
            'media/jqwidgets/jqxgrid.sort.js',
            'media/jqwidgets/jqxchart.js',
            'media/jqwidgets/jqxdata.js',
            'media/jqwidgets/jqxgrid.js',
            'media/jqwidgets/jqxdropdownlist.js',
            'media/jqwidgets/jqxlistbox.js',
            'media/jqwidgets/jqxcheckbox.js',
            'media/jqwidgets/jqxmenu.js',
            'media/jqwidgets/jqxscrollbar.js',
            'media/jqwidgets/jqxbuttons.js',
            'media/jqwidgets/jqxcore.js',
        );
        $this->save($this->user->id_entidad, $this->user->id, 'Ingresa al reporte de Pendientes');
        $this->template->content = View::factory('reportes/pendientes_fuera_de_plazo_por_oficina');
    }

    public function action_entidad() {
        $oEntidad = ORM::factory('entidades')->find_all();
        $entidades = array();
        foreach ($oEntidad as $e) {
            $entidades[$e->id] = $e->entidad;
        }
        $this->template->content = View::factory('reportes/form_entidades')
                ->bind('entidades', $entidades);
    }

    //pendientes oficina
    public function action_general() {
        if ($this->user->dependencia == 0) {
            //usuarios que pertences a mi oficina  
            $oficina = ORM::factory('oficinas', $this->user->id_oficina);
            $entidad = ORM::factory('entidades', $oficina->id_entidad);
            $oficinas = ORM::factory('oficinas')->where('id_entidad', '=', $oficina->id_entidad)->find_all();
            $pendientes = array();
            foreach ($oficinas as $u) {
                $oficial = ORM::factory('seguimiento')->where('id_a_oficina', '=', $u->id)->and_where('estado', '=', 2)->and_where('oficial', '=', 1)->count_all();
                $copia = ORM::factory('seguimiento')->where('id_a_oficina', '=', $u->id)->and_where('estado', '=', 2)->and_where('oficial', '=', 0)->count_all();
                $archivado = ORM::factory('seguimiento')->where('id_a_oficina', '=', $u->id)->and_where('estado', '=', 10)->count_all();
                $pendientes[] = array(
                    'nombre' => $u->oficina,
                    'cargo' => $u->sigla,
                    'id' => $u->id,
                    'oficial' => $oficial,
                    'copia' => $copia,
                    'archivado' => $archivado
                );
            }
            $this->template->scripts = array('media/Highcharts/js/highcharts.js',
                'media/Highcharts/js/modules/exporting.js');
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->content = View::factory('reportes/pendientes_oficinas')
                    ->bind('pendientes', $pendientes)
                    ->bind('entidad', $entidad)
                    ->bind('oficina', $oficina);
            //var_dump($pendientes);
        }
    }

    public function action_document($id = '') {
        $oficina = ORM::factory('oficinas', $this->user->id_oficina);

        $tipo = ORM::factory('tipos', array('id' => $id));
        $count = $tipo->documentos->where('id_oficina', '=', $this->user->id_oficina)->and_where('id_tipo', '=', $tipo->id)->count_all();
        // Creamos una instancia de paginacion + configuracion
        $pagination = Pagination::factory(array(
                    'total_items' => $count,
                    'current_page' => array('source' => 'query_string', 'key' => 'page'),
                    'items_per_page' => 50,
                    'view' => 'pagination/floating',
        ));
        $results = $tipo->documentos
                ->where('id_oficina', '=', $this->user->id_oficina)
                ->and_where('id_tipo', '=', $tipo->id)
                ->order_by('fecha_creacion', 'DESC')
                ->limit($pagination->items_per_page)
                ->offset($pagination->offset)
                ->find_all();
        // Render the pagination links
        $page_links = $pagination->render();

        //$documentos=ORM::factory('documentos')->where('id_tipo','=',$id)->and_where('id_oficina','=',$this->user->id_oficina)->find_all();
        $this->template->title.='/ Reporte de Documentos';
        $this->template->titulo.=$oficina->oficina;
        $this->template->descripcion = $tipo->plural . ' generados de mi oficina';
        $this->template->styles = array('media/css/tablas.css' => 'screen');
        $this->template->scripts = array('media/js/jquery.tablesorter.min.js');
        $this->template->content = View::factory('reportes/documentos_oficina')
                ->bind('documentos', $results)
                ->bind('page_links', $page_links);
    }

    public function action_personalizado() {
        if (isset($_POST['submit'])) {
            var_dump($_POST);
        }
        $o_oficinas = ORM::factory('oficinas')->find_all();
        $oficinas = array();
        foreach ($o_oficinas as $e) {
            $oficinas[$e->id] = $e->oficina;
        }
        $o_estados = ORM::factory('estados')->find_all();
        $estados = array();
        foreach ($o_estados as $e) {
            $estados[$e->id] = $e->estado;
        }
        $this->template->title.='| Reporte perzonalizado';
        //$this->template->styles=array('media/css/jquery-ui-1.8.16.custom.css'=>'screen');
        // $this->template->scripts=array('media/js/jquery-ui-1.8.16.custom.min.js');
        $this->template->content = View::factory('reportes/avanzado')
                ->bind('estados', $estados)
                ->bind('oficinas', $oficinas);
    }

    public function action_recibida() {
        // var_dump($_POST['oficina']);

        if (isset($_POST['submit'])) {
            $fecha1 = $this->fecha2fecha($_POST['fecha1']);
            $fecha2 = $this->fecha2fecha($_POST['fecha2']);
            $tipo_rep = $_POST['tipo'];
            $fecha1 = $fecha1 . ' 00:00:00';
            $fecha2 = $fecha2 . ' 23:59:00';
            if (strtotime($fecha1) > strtotime($fecha1)) {
                $aux = $fecha1;
                $fecha1 = $fecha2;
                $fecha2 = $aux;
            }
            $o_reporte = New Model_Reportes();
            if ($_POST['oficina'] > 0) {
                //echo $_POST['tipo'];
                $oficina = ORM::factory('oficinas', $_POST['oficina']);
                $oficina = $oficina->oficina;
                //  $tipo_rep = $_POST['tipo'];
                $results = $o_reporte->recepcionado($_POST['oficina'], $this->user->id, $fecha1, $fecha2, $tipo_rep);
                //var_dump($results);
                //print_r($results->count());
            } else {
                $oficina = 'Todas las oficinas';
                $results = $o_reporte->recepcionado_all($this->user->id, $fecha1, $fecha2, $tipo_rep);

                // var_dump($results);
                // echo 'holasss';
                //print_r($results->count());
            }
            $this->save($this->user->id_entidad, $this->user->id, 'Genera reporte <b>Correspondencia recibida</b> de ' . $oficina . ' de ' . $_POST['fecha1'] . ' a ' . $_POST['fecha2']);
            $resultado = array();
            foreach ($results as $r) {

                /*
                $resultado[$r['nur']] = array(
                    'nur' => $r['nur'],
                    'cite_original' => $r['cite_original'],
                    'fecha_creacion' => $r['fecha_creacion'],
                    'institucion_remitente' => $r['institucion_remitente'],
                    'nombre_remitente' => $r['nombre_remitente'],
                    'cargo_remitente' => $r['cargo_remitente'],
                    'referencia' => $r['referencia'],
                    'fecha_emision' => $r['fecha_emision'],
                    'nombre_destinatario' => $r['nombre_destinatario'],
                    'cargo_destinatario' => $r['cargo_destinatario'],
                    'estado' => $r['estado'],
                    'nombre_emisor' => $r['nombre_emisor'],
                );
                */
                $resultado[] = array(
                    'nur' => $r['nur'],
                    'cite_original' => $r['cite_original'],
                    'fecha_creacion' => $r['fecha_creacion'],
                    'institucion_remitente' => $r['institucion_remitente'],
                    'nombre_remitente' => $r['nombre_remitente'],
                    'cargo_remitente' => $r['cargo_remitente'],
                    'referencia' => $r['referencia'],
                    'fecha_emision' => $r['fecha_emision'],
                    'nombre_destinatario' => $r['nombre_destinatario'],
                    'cargo_destinatario' => $r['cargo_destinatario'],
                    'estado' => $r['estado'],
                    'nombre_emisor' => $r['nombre_emisor'],
                );                
            }

            $_POST = array();
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->title.=' / Correspondencia recibida';
            $this->template->titulo.='Correspondencia recibida';
            $titulo = $this->template->titulo;
            $this->template->descripcion = 'de <b>' . Date::fecha_corta($fecha1) . '</b> a  <b>' . Date::fecha_corta($fecha2) . '</b> DE <b>' . $oficina . '</b>';
            $descripcion = 'de <b>' . Date::fecha_corta($fecha1) . '</b> a  <b>' . Date::fecha_corta($fecha2) . '</b> DE <b>' . $oficina . '</b>';
            $this->template->styles = array(
                'static/css/jquery-ui-1.12.1/jquery-ui.css' => 'screen',
            );
            $this->template->scripts = array(
                'static/js/libs/jquery-ui/jquery-ui.min.js'
            );            
            $this->template->content = View::factory('reportes/vista')
                    ->bind('titulo', $titulo)
                    ->bind('descripcion', $descripcion)
                    ->bind('results', $resultado)
                    ->bind('oficina', $oficina)
                    ->bind('fecha1', $fecha1)
                    ->bind('fecha2', $fecha2)
                    ->bind('tipo_rep', $tipo_rep);
        } else {
            $user = $this->user;
            $oficinas = $this->oficinas2($this->user->id_entidad);
            $fecha_inicio = date('Y-m-d', $this->user->fecha_creacion);
            $this->template->titulo.='Correspondencia recibida';
            $this->template->descripcion = 'Elija opciones que corresponda';
            $this->template->styles = array(
                'static/css/jquery-ui-1.12.1/jquery-ui.css' => 'screen',
            );
            $this->template->scripts = array(
                'static/js/libs/jquery-ui/jquery-ui.min.js'
            );            
            $this->template->content = View::factory('reportes/recibida')
                    ->bind('fecha_inicio', $fecha_inicio)
                    ->bind('user', $user)
                    ->bind('oficinas', $oficinas);
        }
    }

    public function action_enviada() {
        if (isset($_POST['submit'])) {
            $fecha1 = $_POST['fecha1'] . ' 00:00:00';
            $fecha2 = $_POST['fecha2'] . ' 23:59:00';
            if (strtotime($fecha1) > strtotime($fecha2)) {
                $fecha1 = $_POST['fecha2'] . ' 23:59:00';
                $fecha2 = $_POST['fecha1'] . ' 00:00:00';
            }
            $o_reporte = New Model_Reportes();
            if ($_POST['oficina'] > 0) {
                $oficina = ORM::factory('oficinas', $_POST['oficina']);
                $id_oficina = $oficina->id;
                $oficina = $oficina->oficina;
                $results = $o_reporte->enviado($_POST['oficina'], $this->user->id, $fecha1, $fecha2);
            } else {
                $oficina = 'Todas las oficinas';
                $id_oficina = 0;
                $results = $o_reporte->enviado_all($this->user->id, $fecha1, $fecha2);
            }
            $id_user = $this->user->id;
            $this->template->title.=' / Correspondencia enviada';
            $this->template->titulo.='Correspondencia enviada';
            $this->template->descripcion.='de <b>' . Date::fecha_corta($fecha1) . '</b> a <b>' . Date::fecha_corta($fecha2) . '</b> a <b>' . $oficina . '</b>';
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->content = View::factory('reportes/vista2')
                    ->bind('results', $results)
                    ->bind('oficina', $oficina)
                    ->bind('id_oficina', $id_oficina)
                    ->bind('id_user', $id_user)
                    ->bind('fecha1', $fecha1)
                    ->bind('fecha2', $fecha2);
        } else {
            $oficinas = $this->oficinas2($this->user->id_entidad);
            $fecha_inicio = date('Y-m-d', $this->user->fecha_creacion);
            $this->template->title.=' / Correspondencia enviada';
            $this->template->titulo.='Correspondencia enviada';
            $this->template->descripcion = 'Elija Opciones que corresponda';
            $this->template->styles = array(
                'static/css/jquery-ui-1.12.1/jquery-ui.css' => 'screen',
            );
            $this->template->scripts = array(
                'static/js/libs/jquery-ui/jquery-ui.min.js'
            );
            $this->template->content = View::factory('reportes/enviada')
                    ->bind('fecha_inicio', $fecha_inicio)
                    ->bind('oficinas', $oficinas);
        }
    }

    public function action_personal() {
        if (isset($_POST['submit'])) {
            $fecha1 = $_POST['fecha1'] . ' 00:00:00';
            $fecha2 = $_POST['fecha2'] . ' 23:59:00';
            if (strtotime($fecha1) > strtotime($fecha2)) {
                $fecha1 = $_POST['fecha2'] . ' 23:59:00';
                $fecha2 = $_POST['fecha1'] . ' 00:00:00';
            }
            $o_reporte = New Model_Reportes();
            $oficina = ORM::factory('oficinas', $_POST['oficina']);
            $id_oficina = $oficina->id;
            $oficina = $oficina->oficina;
            $id_estado = $_POST['estado'];
            $estado = ORM::factory('estados', $id_estado);
            $results = $o_reporte->personal($_POST['oficina'], $_POST['estado'], $fecha1, $fecha2);
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->title.='/ Reporte perzonalizado';
            $this->template->titulo.=' Correspondencia ' . $estado->plural . ' / <v>' . $oficina . '</v>';
            $this->template->descripcion = 'DE <b>' . $fecha1 . '</b> a <b>' . $fecha2 . '</b>';
            $this->template->content = View::factory('reportes/vista3')
                    ->bind('results', $results)
                    ->bind('oficina', $oficina)
                    ->bind('id_oficina', $id_oficina)
                    ->bind('estado', $estado)
                    ->bind('fecha1', $fecha1)
                    ->bind('titulo', $this->template->titulo)
                    ->bind('fecha2', $fecha2);
        } else {
            $oficinas = array();
            $oficina = ORM::factory('oficinas', $this->user->id_oficina);
            $oficinas[$oficina->id] = $oficina->oficina;
            $o_oficinas = ORM::factory('oficinas')->where('padre', '=', $this->user->id_oficina)->find_all();
            foreach ($o_oficinas as $e) {
                $oficinas[$e->id] = $e->oficina;
            }
            $o_estados = ORM::factory('estados')->find_all();
            $estados = array();
            foreach ($o_estados as $e) {
                $estados[$e->id] = $e->estado;
            }
            $this->template->title.='/ Reporte perzonalizado';
            $this->template->titulo.='Reporte perzonalizado';
            $this->template->descripcion = 'ELIJA LAS OPCIONES';
            $this->template->styles = array(
                'static/css/jquery-ui-1.12.1/jquery-ui.css' => 'screen',
            );
            $this->template->scripts = array(
                'static/js/libs/jquery-ui/jquery-ui.min.js'
            );
//        $this->template->styles=array('media/css/jquery-ui-1.8.16.custom.css'=>'screen');
            //      $this->template->scripts=array('media/js/jquery-ui-1.8.16.custom.min.js');
            $this->template->content = View::factory('reportes/avanzado')
                    ->bind('estados', $estados)
                    ->bind('oficinas', $oficinas);
        }
    }

    //options oficinas 
    public function oficinas() {
        $o_oficinas = ORM::factory('oficinas')->find_all();
        $oficinas = array(0 => 'Todos');
        foreach ($o_oficinas as $e) {
            $oficinas[$e->id] = $e->oficina;
        }
        return $oficinas;
    }

    public function oficinas2($ide = 1) {
        $o_oficinas = ORM::factory('oficinas')->where('id_entidad', '=', $ide)->find_all();
        $oficinas = array(0 => '[TODAS LAS OFICINAS]');
        foreach ($o_oficinas as $e) {
            $oficinas[$e->id] = $e->oficina;
        }
        return $oficinas;
    }

    public function action_stadistic() {
        $this->template->title.=' / Reportes Estadisticos';
        $this->template->titulo.='Estadisticos';
        $this->template->descripcion.='Estadisticas';
    }

    //ADECUACION
    public function action_report_adec() {
        if (isset($_POST['submit'])) {
            $oficina = ORM::factory('oficinas', $this->user->id_oficina);
            $o_reporte = New Model_Reportes();
            $results = $o_reporte->report_adec($_POST['u'], $this->user->id, $oficina);
            $this->template->styles = array('media/css/tablas.css' => 'screen');
            $this->template->title.='/ Reporte perzonalizado';
            $this->template->titulo.=' Correspondencia  / <v>Pendientes de Dependientes</v>';
            $this->template->descripcion = 'DE <b>eee</b> a <b>hhfh</b>';
            $this->template->content = View::factory('reportes/vista3_1')
                    ->bind('results', $results)
                    ->bind('titulo', $this->template->titulo);
        } else {
            //echo $this->user->id;
            $usuarios = array();
            $usuarios = ORM::factory('users')->where('id_oficina', '=', $this->user->id_oficina)->find_all();
            //$o_oficinas=ORM::factory('oficinas')->where('padre','=',$this->user->id_oficina)->find_all();
            $u = array();
            $u['-1'] = 'TODOS';
            foreach ($usuarios as $e) {
                $u[$e->id] = $e->nombre;
            }
            //$o_estados=ORM::factory('estados')->find_all();
            //$estados=array();
            //foreach($o_estados as $e)
            //{
            //   $estados[$e->id]=$e->estado;
            //}
            $this->template->title.='/ Reporte perzonalizado';
            $this->template->titulo.='Reporte perzonalizado';
            $this->template->descripcion = 'ELIJA LAS OPCIONES';
//        $this->template->styles=array('media/css/jquery-ui-1.8.16.custom.css'=>'screen');
            //      $this->template->scripts=array('media/js/jquery-ui-1.8.16.custom.min.js');
            $this->template->content = View::factory('reportes/report_adec')
                    ->bind('u', $u);
            //->bind('oficinas',$oficinas);*/
        }
    }

    public function fecha2fecha($date = null) {
        if (strpos($date, '-') > 0) {
            $fecha = explode('-', $date);
            if (isset($fecha[2]))
                return $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
            else
                return date('00/00/0000');
        } else {
            $fecha = explode('/', $date);
            if (isset($fecha[2]))
                return $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0];
            else
                return '0000-00-00';
        }
    }

}

?>
