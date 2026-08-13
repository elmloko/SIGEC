<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export extends Controller {
    public function action_excel()
    {      
    $auth=  Auth::instance();
    if($auth->logged_in())
    {    
    
    $id_segs=array_values($_POST['id_seg']);
    
    $sql="SELECT s.nur, s.a_oficina,s.nombre_receptor,cargo_receptor,s.fecha_emision,s.oficial,d.cite_original, d.referencia,s.proveido FROM seguimiento s 
    INNER JOIN nurs_documentos n ON s.nur=n.nur
    INNER JOIN documentos d ON n.id_documento=d.id
    WHERE d.original='1'
    AND  s.id IN (";
    foreach ($_POST['id_seg'] as $k=>$v):
        $sql.=$v.", ";
    endforeach;
    $sql=substr($sql,0, -2);
    $sql.=")";

    $modelo=New Model_Hojasruta();
    $report=$modelo->select($sql);
    
    $data=array();
    $i=1;
    
    $oficial=array(0=>'Copia',1=>'Oficial');
    foreach ($report as $rs)    
    {    
// Display this code source is asked.
//if (isset($_GET['source'])) exit('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file(__FILE__,true).'</body></html>');
$data[]=array(
    'i'=>$i,    
    'nur'=>$rs->nur,    
    'oficina'=>  utf8_decode($rs->a_oficina),    
    'nombre'=>  utf8_decode($rs->nombre_receptor),
    'cargo'=>$rs->cargo_receptor,
    'cite'=>  utf8_decode($rs->cite_original),
    'proveido'=>  utf8_decode($rs->proveido),
    'oficial'=>$oficial[$rs->oficial],
    'fecha'=>date('d/m/Y',  strtotime($rs->fecha_emision))
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
$template='/srv/sigec/templates/demo_ms.xlsx';
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
$file_name='enviados'.time().'.xlsx';
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
}