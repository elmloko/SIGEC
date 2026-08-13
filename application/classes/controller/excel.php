<?php defined('SYSPATH') or die('No direct script access.');

class Controller_excel extends Controller {
    protected $user;
    protected $menus;

public function before() 
    {
        $auth =  Auth::instance();     
        if($auth->logged_in())
        {        
            $session=Session::instance();
            $this->user=$session->get('auth_user');
        parent::before();       
        }
        else
        {
            $url= substr($_SERVER['REQUEST_URI'],1);
            $this->request->redirect('/login?url='.$url);
        }        
    }
//documento a excel para impresion    
public function action_sol_pasajes($id='')
{     
    $sql="SELECT d.id, d.cite_original,d.nombre_remitente,o.oficina,d.cargo_remitente,d.referencia,d.nur,p.lugar,p.dias,p.fecha_salida,p.fecha_retorno,p.pasaje,p.viatico,t.tipo_viaje,m.medio_transporte FROM documentos d 
        INNER JOIN users u ON u.id=d.id_user
        INNER JOIN oficinas o ON o.id=d.id_oficina
        INNER JOIN pasajes p ON d.id=p.id_documento
        INNER JOIN tipoviaje t ON t.id=p.tipo_viaje
        INNER JOIN mediotransporte m ON m.id=p.medio_transporte
        WHERE d.id='$id'";

    $modelo=New Model_Hojasruta();
    $report=$modelo->select($sql);    
    
    $data=array();
    $i=1;    
    $marca=array(0=>'',1=>'X');
    foreach ($report as $rs)    
    {    
        $pasaje='';
        $viatico='';
        if($rs->pasaje==1)
            $pasaje='X';
        if($rs->viatico==1)
            $viatico='X';
       
// Display this code source is asked.
//if (isset($_GET['source'])) exit('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file(__FILE__,true).'</body></html>');
$data[]=array(
    //'i'=>$i,    
    'hoja_ruta'=>$rs->nur,    
    'oficina'=>  utf8_decode($rs->oficina),    
    'nombre'=>  utf8_decode($rs->nombre_remitente),
    'cargo'=> utf8_decode($rs->cargo_remitente),    
    'pasaje'=>$pasaje,
    'viatico'=>$viatico,
    'tipo_viaje'=>utf8_decode($rs->tipo_viaje),
    'lugar'=>  utf8_decode($rs->lugar),
    'transporte'=>  utf8_decode($rs->medio_transporte),
    'fecha_salida'=>date('d/m/Y',  strtotime($rs->fecha_salida)),
    'hora_salida'=>date('H:i:s',  strtotime($rs->fecha_salida)),
    'fecha_retorno'=>date('d/m/Y',  strtotime($rs->fecha_retorno)),
    'hora_retorno'=>date('H:i:s',  strtotime($rs->fecha_retorno)),
    'dias'=>$rs->dias,
    'referencia'=>$rs->referencia,
    'cite'=>  utf8_decode($rs->cite_original)
);
$i++;
}
$fecha=date('d/m/Y');
$this->autoRender=false; 
    require Kohana::find_file('vendor/tbs', 'tbs_class_php5');
    require Kohana::find_file('vendor/tbs', 'tbs_plugin_opentbs');          
$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
//cargamos el template segun el tipo de documento
$template='templates/form0002.xlsx';
$debug=0;
$suffix='';

// Load the template
$TBS->LoadTemplate($template);

if ($debug==2) { // debug mode 2
	$TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
	exit;
} elseif ($debug==1) { // debug mode 1
	$TBS->Plugin(OPENTBS_DEBUG_INFO);
	exit;
}

$TBS->MergeBlock('a', $data);
// Define the name of the output file
// delete comments
$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

//$file_name = str_replace('.','_'.date('Y-m-d').'.',$template);
$file_name='solicitud_viaje'.time().'.xlsx';
// Output as a download file (some automatic fields are merged here)
if ($debug==3) { // debug mode 3
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} elseif ($suffix==='') {
	// download
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name);
} else {
	// save as file
	$file_name = str_replace('.','_'.$suffix.'.',$file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
}
    
       
}
//documento a excel para impresion    
public function action_inf_viaje($id='')
{     
    $sql="SELECT d.id,d.fecha_creacion, d.cite_original,d.nombre_remitente,o.oficina,d.cargo_remitente,d.referencia,d.nur,v.lugar,v.fecha_salida,v.fecha_retorno,v.viatico,v.medio_transporte1,v.medio_transporte2
        ,v.no_descripcion,v.cedula_identidad,d.contenido,v.resolucion,v.pases,v.form110,v.cuenta_corriente,v.cuenta_utc,
        v.fondos_copia,v.pasaje_aereo,v.pasaje_terrestre,v.form604,v.planilla_invitados
        FROM documentos d 
        INNER JOIN users u ON u.id=d.id_user
        INNER JOIN oficinas o ON o.id=d.id_oficina
        INNER JOIN viajes v ON d.id=v.id_documento       
        INNER JOIN mediotransporte m ON m.id=v.medio_transporte1
	INNER JOIN mediotransporte mm ON mm.id=v.medio_transporte2
        WHERE d.id='$id'";

    $modelo=New Model_Hojasruta();
    $report=$modelo->select($sql);        
    $data=array();
    $i=1;    
    $marca=array(0=>'',1=>'X');
    foreach ($report as $rs)    
    {            
        $viatico='';
        if($rs->viatico==1)
            $viatico='X';
       $fecha=date('d/m/Y',  strtotime($rs->fecha_creacion));
// Display this code source is asked.
//if (isset($_GET['source'])) exit('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file(__FILE__,true).'</body></html>');
$data[]=array(
    //'i'=>$i,    
    'hoja_ruta'=>$rs->nur,    
    'oficina'=>  utf8_decode($rs->oficina),    
    'nombre'=>  utf8_decode($rs->nombre_remitente),
    'cargo'=> utf8_decode($rs->cargo_remitente),        
    'viatico1'=>'',
    'viatico0'=>'',
    'viatico'.$rs->viatico=>'X',
    //'tipo_viaje'=>utf8_decode($rs->tipo_viaje),
    'lugar'=>  utf8_decode($rs->lugar),
   // 'transporte1'=>  utf8_decode($rs->transporte1),
   // 'transporte2'=>  utf8_decode($rs->transporte2),
    'fecha_salida'=>date('d/m/Y',  strtotime($rs->fecha_salida)),
    'hora_salida'=>date('H:i:s',  strtotime($rs->fecha_salida)),
    'fecha_retorno'=>date('d/m/Y',  strtotime($rs->fecha_retorno)),
    'hora_retorno'=>date('H:i:s',  strtotime($rs->fecha_retorno)),
    //'dias'=>$rs->dias,
    'referencia'=>$rs->referencia,    
    'cite'=>  utf8_decode($rs->cite_original),
    'descripcion'=>  utf8_decode($rs->contenido),
    'no_descripcion'=>  utf8_decode($rs->no_descripcion),
    'ci'=>  $rs->cedula_identidad,
    'transporte11'=>'',   
    'transporte12'=>'',   
    'transporte13'=>'',   
    'transporte14'=>'',   
    'transporte1'.$rs->medio_transporte1=>'X',   
    'transporte21'=>'',   
    'transporte22'=>'',   
    'transporte23'=>'',   
    'transporte24'=>'',   
    'transporte2'.$rs->medio_transporte2=>'X',  
    'fecha' => $fecha     

);
$i++;
}
$fecha=date('d/m/Y');
$this->autoRender=false; 
    require Kohana::find_file('vendor/tbs', 'tbs_class_php5');
    require Kohana::find_file('vendor/tbs', 'tbs_plugin_opentbs');          
$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin
//cargamos el template segun el tipo de documento
$template='templates/form0004.xlsx';
$debug=0;
$suffix='';

// Load the template
$TBS->LoadTemplate($template);

if ($debug==2) { // debug mode 2
	$TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
	exit;
} elseif ($debug==1) { // debug mode 1
	$TBS->Plugin(OPENTBS_DEBUG_INFO);
	exit;
}

$TBS->MergeBlock('a', $data);
// Define the name of the output file
// delete comments
$TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

//$file_name = str_replace('.','_'.date('Y-m-d').'.',$template);
$file_name='informe_viaje'.time().'.xlsx';
// Output as a download file (some automatic fields are merged here)
if ($debug==3) { // debug mode 3
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} elseif ($suffix==='') {
	// download
	$TBS->Show(OPENTBS_DOWNLOAD, $file_name);
} else {
	// save as file
	$file_name = str_replace('.','_'.$suffix.'.',$file_name);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $file_name);
}
    
       
}

}