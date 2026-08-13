<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Word extends Controller {
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
//tipo de documento informe    
public function action_informe($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');        
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(1050);
    $table->addCell(1300)->addImage('media/images/escudo.png');
    $table->addCell(5000)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontNormal,$style2);
    $table->addCell(1300)->addImage('media/logos/'.$entidad->logo);    
   // $table->addRow(200);
   // $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
   // $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
   // $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    //$table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    //$table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

   // $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    $section->addTextBreak(1);
    $section->addText('INFORME',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
  'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Informe'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}    
/*NOTA INTERNA*/    
public function action_nota($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');    
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    $section->addTextBreak(1);
    $section->addText('NOTA INTERNA',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Nota_Interna'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}    
//nota

//carta externa

public function action_carta($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');    
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    //fecha de la carta
    $section->addTextBreak(1);
    $section->addText('La Paz, '.Date::fecha($documento->fecha_creacion),$fontNormal,$style1);
    //cite del informe    
    $section->addText($documento->cite_original,$fontBold,$style1);    
    $section->addTextBreak(2);
    
    $section->addText($documento->titulo.':',$fontNormal,$style1);
    $section->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $section->addText($documento->cargo_destinatario,$fontBold,$style1);
    $section->addText($documento->institucion_destinatario,$fontNormal,$style1);
    $section->addText('Presente:',array('bold'=>true,'name'=>'Arial', 'size'=>11,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE),$style1);
    $section->addTextBreak(2);
    //
    $section->addText('Ref.: '.strtoupper($documento->referencia),array('bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE),$style3);    
    $section->addTextBreak(1);
    // HTML Dom object:
    
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.

    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://test.local', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/documentation/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
  'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Carta'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}    
    


/**/
//CIRCULAR
/*/
 * 
 */

public function action_circular($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');          
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    $section->addText('CIRCULAR',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
 'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='circular'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}    
    
  
/*MEMOS*/
 
public function action_memo($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');       
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    $section->addText('MEMORANDUM',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
  'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Memorandum'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}
 //INSTRUCTIVO
public function action_intructivo($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');    
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    //tipo documento
    $section->addText('INSTRUCTIVO',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
 'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Instructivo'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}
 //COMUNICADO
public function action_comunicado($id='')
{
    $documento=ORM::factory('documentos',$id);
    if($documento->loaded())
    {
    $entidad=ORM::factory('entidades',$documento->id_entidad);        
    require Kohana::find_file('vendor/htmltodocx/phpword', 'PHPWord');
    require Kohana::find_file('vendor/htmltodocx/simplehtmldom', 'simple_html_dom');
    require Kohana::find_file('vendor/htmltodocx/htmltodocx_converter', 'h2d_htmlconverter');
    require Kohana::find_file('vendor/htmltodocx', 'styles','inc');        
    // New Word Document:
    ob_start(); //start output buffering    
    $phpword_object = new PHPWord();
    //styles for section
    $sectionStyle = array('orientation' => null,
			    'marginLeft' => 1700,
			    'marginRight' => 1200,
			    'marginTop' => 5,
			    'marginBottom' => 500);
    //others styles
    $style1=array('spaceAfter'=>1,'spaceBefore'=>1);
    $style2=array('spaceAfter'=>1,'spaceBefore'=>1,'align'=>'center');
    $style3=array('spaceAfter'=>.5,'spaceBefore'=>.5,'align'=>'right');
    $style4=array('spaceAfter'=>2,'spaceBefore'=>1,'align'=>'right');
    //font style
    $fontStyle = array('color'=>'666666', 'size'=>7);
    $fontStyleHeader = array('color'=>'666666', 'size'=>5);
    $fontStyle2 = array('color'=>'666666', 'size'=>8,'italic'=>true);
    $fontNormal=array('name'=>'Arial', 'size'=>11);
    $fontBold=array('name'=>'Arial', 'size'=>11,'bold'=>true);
    $styleTable = array('borderTopSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>80);
    $styleTableH = array('borderBottomSize'=>6, 'borderColor'=>'666666', 'cellMargin'=>10);
    
    $section = $phpword_object->createSection($sectionStyle);    
    
    $header = $section->createHeader();
    
    $table = $header->addTable(); 
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600)->addText('ESTADO PLURINACIONAL DE BOLIVIA',$fontStyleHeader,$style3);
    $table->addRow(200);
    $table->addCell(4700)->addText('',$fontStyleHeader,$style3);
    $table->addCell(3600,$styleTableH)->addText('MINISTERIO DE OBRAS PÚBLICAS, SERVICIOS Y VIVIENDA',$fontStyleHeader,$style3);
    $table->addRow();
   /* $table->addCell(4000)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3700)->addText('',$fontStyleHeader,$style4);
    $table->addRow();*/
    $table->addCell(4700)->addText('',$fontStyleHeader,$style4);
    $table->addCell(3600)->addText('DIRECCION GENERAL  DE ASUNTOS ADMINISTRATIVOS',$fontStyleHeader,$style4);
    

    $header->addWatermark('media/logos/'.$entidad->logo, array('marginTop'=>-75, 'marginLeft'=>560));    
   // $table->addRow();        
   // $table->addCell(11000)->addText('2012 Año de la No Violencia Contra la Niñez y',$fontStyle2,$style3);
  //  $table->addRow();   
   // $table->addCell(10000)->addText('Adolescencia en el Estado Plurinacional de Bolivia',$fontStyle2,$style3);
        
    // Add footer    
  // $phpword_object->addTableStyle('myOwnTableStyle', $styleTable);
    
    $footer = $section->createFooter();
    $table=$footer->addTable();
    $table->addRow();        
    $table->addCell(7000,$styleTableH)->addText('',$fontStyle,$style1);
    $table->addCell(4000,$styleTableH)->addText('www.oopp.gob.bo',$fontBold,$style3);  
    $table->addRow();        
    $table->addCell(7000)->addText($entidad->direccion,$fontStyle,$style1);
    $table->addCell(4000)->addText('Telf.: '.$entidad->telefono,$fontStyle,$style3);  
    
    
    
    
    //tipo documento
    $section->addText('COMUNICADO',array('size'=>14),$style2);
    //cite del informe
    $section->addText($documento->cite_original,array('bold'=>true,'size'=>12),$style2);
    $section->addText($documento->nur,array('bold'=>true,'size'=>12),$style2);
    $section->addText('');
    
    //
    
    $table=$section->addTable();
    $table->addRow();
    $table->addCell(900)->addText('A',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_destinatario,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_destinatario),$fontBold);
    if($documento->nombre_via!="")
    {
        $table->addRow();
        $table->addCell(900)->addText('VIA',$fontBold,$style1);
        $table->addCell(100)->addText(':',$fontBold,$style1);
        $table->addCell(7500)->addText($documento->nombre_via,$fontNormal,$style1);
        $table->addRow();
        $table->addCell(900)->addText('');
        $table->addCell(100)->addText('',array('bold'=>true),$style1);
        $table->addCell(7500)->addText(strtoupper($documento->cargo_via),$fontBold);
    }
    $table->addRow();
    $table->addCell(900)->addText('DE',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText($documento->nombre_remitente,$fontNormal,$style1);
    $table->addRow();
    $table->addCell(900)->addText('');
    $table->addCell(100)->addText('',array('bold'=>true),$style1);
    $table->addCell(7500)->addText(strtoupper($documento->cargo_remitente),$fontBold);    
    //referencia
    $table->addRow();
    $table->addCell(900)->addText('REF.',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(strtoupper($documento->referencia),array('name'=>'Arial','size'=>11,'bold'=>true,'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE));
    
    //fecha
    $table->addRow();
    $table->addCell(900)->addText('FECHA',$fontBold,$style1);
    $table->addCell(100)->addText(':',$fontBold,$style1);
    $table->addCell(7500)->addText(Date::fecha($documento->fecha_creacion),$fontNormal);
    // HTML Dom object:
    $section->addText(''); //adicionamos un estacio
    $html_dom = new simple_html_dom();
    $html_dom->load('<html><body>' . $documento->contenido . '</body></html>');
    // N    ote, we needed to nest the html in a couple of dummy elements.
    // Create the dom array of elements which we are going to work on:
    $html_dom_array = $html_dom->find('html',0)->children();

    // Provide some initial settings:
    $initial_state = array(
    //     Required parameters:
    'phpword_object' => &$phpword_object, // Must be passed by reference.
    'base_root' => 'http://sigec.localhost', // Required for link elements - change it to your domain.
    'base_path' => '/htmltodocx/', // Path from base_root to whatever url your links are relative to.
  
  // Optional parameters - showing the defaults if you don't set anything:
  'current_style' => array('size' => '11'), // The PHPWord style on the top element - may be inherited by descendent elements.
  'parents' => array(0 => 'body'), // Our parent is body.
  'list_depth' => 0, // This is the current depth of any current list.
  'context' => 'section', // Possible values - section, footer or header.
  'pseudo_list' => TRUE, // NOTE: Word lists not yet supported (TRUE is the only option at present).
  'pseudo_list_indicator_font_name' => 'Wingdings', // Bullet indicator font.
  'pseudo_list_indicator_font_size' => '7', // Bullet indicator size.
  'pseudo_list_indicator_character' => 'l ', // Gives a circle bullet point with wingdings.
  'table_allowed' => TRUE, // Note, if you are adding this html into a PHPWord table you should set this to FALSE: tables cannot be nested in PHPWord.
  'structure_document' => TRUE, // h1..h6 titles will be Heading 1... Heading 6 styles in the Word document. Note that in this case child elements will be parsed as text only.
  
  // Optional - no default:    
 'style_sheet' => htmltodocx_styles_example(), // This is an array (the "style sheet") - returned by h2d_styles_example() here (in styles.inc) - see this function for an example of how to construct this array.
  'table_of_contents_id' => 'word-table-of-contents', // If structure_document is TRUE, and this is defined, then any div element with this id will have its contents replaced with the Word table of contents when the Word document is created.
  );

// Convert the HTML and put it into the PHPWord object
htmltodocx_insert_html($section, $html_dom_array[0]->nodes, $initial_state);
//mosca y copias
$section->addTextBreak(3);
$section->addText($documento->mosca_remitente,$fontStyle,$style1);
$section->addText('Cc: '.$documento->copias,$fontStyle,$style1);
// Clear the HTML dom object:
$html_dom->clear(); 
unset($html_dom);

// Save File
$h2d_file_uri = tempnam('', 'htd');
$objWriter = PHPWord_IOFactory::createWriter($phpword_object, 'Word2007');
$objWriter->save($h2d_file_uri);

$filename='Comunicado'.substr($documento->codigo, -9,4);
// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
    header('Content-Length: ' . filesize($h2d_file_uri));
    ob_clean();
    flush();
    $status = readfile($h2d_file_uri);
    unlink($h2d_file_uri);
    exit;
    }
}

}