<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Print extends Controller {

    public function action_hr() {
        $auth = Auth::instance();
        if ($auth->logged_in() && isset($_GET['code'])) {
            //echo 'hola';
            $nur = $_GET['code'];
            require Kohana::find_file('vendor/fpdf17', 'fpdf');
            require Kohana::find_file('vendor/fpdf17', 'code39');
            $modelo = New Model_Hojasruta();
            $hojaruta = $modelo->imprimir($nur);
            $this->autoRender = false;
            foreach ($hojaruta as $rs) {
                $pdf = new PDF_Code39('P', 'mm', 'Letter');
                $pdf->SetMargins(10, 10, 5);
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 20);
                $pdf->SetX(10);
                $pdf->SetFont('Arial', 'B', 18);
                $src = DOCROOT . 'static/logos/' . $rs->logo;
                if (file_exists($src)) {
                    $image_file = $src;
                    $pdf->Image($image_file, 10, 3, 63, 24, 'png', '', '', FALSE, 300, '', FALSE, FALSE, 1);
                }
                $pdf->Ln(12);
                $pdf->SetXY(150, 11);
                $pdf->SetFont('Arial', '', 14);
                //hoja de seguimiento    
                if ($rs->id_tipo != 6):  //tipo 6 = carta externa
                    $pdf->Cell(60, 6, 'HOJA DE RUTA INTERNA', 1, FALSE, 'C');
                    $pdf->Code39(151, 5, $rs->nur, 0.71, 5);
                //$pdf->Code39(152,21,$rs->nur,0.71,8);    
                else:
                    $pdf->Code39(151, 5, $rs->nur, 0.71, 5);
                    $pdf->Cell(60, 6, 'HOJA DE RUTA EXTERNA', 1, FALSE, 'C');
                endif;
                //$pdf->Code39(155,21,$rs->nur,0.71,8);
                //fin codigo barra                                                                     
                $pdf->SetX(145);
                //NUA
//    $pdf->SetFont('Arial', 'B', 10);    
//    $pdf->Cell(65, 5, $rs->sigla, 'TR',FALSE,'C');
                $pdf->SetXY(150, 17);
                $pdf->SetFont('Arial', '', 18);
                $pdf->Cell(60, 9, $rs->nur, 1, FALSE, 'C');

                $pdf->SetXY(145, 5);
                $pdf->Cell(65, 19, '', 0, FALSE, 'C');


                $pdf->SetXY(10, 29);
                //columna 1
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(25, 10, 'PROCEDENCIA:', 'TBL', FALSE, 'L');
                $pdf->SetFont('helvetica', 'B', 8);

                if (trim($rs->institucion_remitente) != '') {
                    if (strlen($rs->institucion_remitente) > 100) {
                        $pdf->MultiCell(115, 10, utf8_decode(strtoupper($rs->institucion_remitente)), 'T', 'L');
                    } else {
                        $pdf->Cell(115, 10, utf8_decode(strtoupper($rs->institucion_remitente)), 'T', 'L');
                    }
                } else {
                    if (strlen($rs->entidad) > 80) {
                        $pdf->MultiCell(115, 5, utf8_decode(strtoupper($rs->entidad)), 'T', 'L');
                    } else {
                        $pdf->Cell(115, 10, utf8_decode(strtoupper($rs->entidad)), 'T', 'L');
                    }
                }
                $pdf->SetXY(150, 29);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(60, 5, 'CITE ORIGINAL', 'TRL', FALSE, 'C');
                $pdf->SetXY(150, 34);
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(60, 5, utf8_decode($rs->cite_original), 'RL', FALSE, 'C');
                $pdf->Ln();

                //REMITENTE
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(25, 10, 'REMITENTE:', 'TL', FALSE, 'L');
                $pdf->Cell(140, 6, utf8_decode($rs->nombre_remitente), 'T', FALSE, 'L');

                $pdf->Cell(13, 5, 'FECHA:', 'LT', FALSE, 'L');
                $pdf->Cell(22, 5, date('d/m/Y', strtotime($rs->fecha_creacion)), 'TR', FALSE, 'L');
                $pdf->SetXY(175, 44);
                $pdf->Cell(13, 5, 'HORA:', 'L', FALSE, 'L');
                $pdf->Cell(22, 5, date('h:i:s A', strtotime($rs->fecha_creacion)), 'R', FALSE, 'L');

                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetXY(35, 44);
                $pdf->Cell(140, 3, utf8_decode($rs->cargo_remitente), 0, FALSE, 'L');
                $pdf->SetFont('helvetica', '', 9);
                //DETINATARIO
                if (strlen(trim($rs->cargo_destinatario)) == 0) {
                    $pdf->SetXY(10, 49);
                    $pdf->SetFont('helvetica', '', 7);
                    $pdf->Cell(25, 10, 'DESTINATARIO:', 'TL', FALSE, 'L');
                    $pdf->MultiCell(175, 3, utf8_decode($rs->nombre_destinatario), 'TR', 'L');
                    //$pdf->MultiCell($w, $h, $txt, $border, $align)
                    $pdf->Ln();
                    $pdf->SetFont('helvetica', '', 9);
                } else {
                    $pdf->SetXY(10, 49);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->Cell(25, 10, 'DESTINATARIO:', 'TL', FALSE, 'L');
                    $pdf->Cell(175, 6, utf8_decode($rs->nombre_destinatario), 'TR', FALSE, 'L');
                    $pdf->Ln();
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->Cell(25, 3, '', 0, FALSE, 'L');
                    $pdf->Cell(175, 3, utf8_decode($rs->cargo_destinatario), 'R', FALSE, 'L');
                    $pdf->SetFont('helvetica', '', 9);
                }
                //proceso
                //fecha

                $pdf->SetXY(10, 59);
                $pdf->SetFontSize(8);
                $pdf->Cell(25, 10, 'REFERENCIA:', 'LT', FALSE, 'L');
                $pdf->SetFont('helvetica', '', 7);
                if (strlen($rs->referencia) > 121) {
                    //$pdf->SetFont('helvetica', '', 6); 
                    if (strlen($rs->referencia) > 240)
                        $text = substr($rs->referencia, 0, 240) . '..';
                    else
                        $text = $rs->referencia;
                    $pdf->MultiCell(175, 5, utf8_decode($text), 'TR', 'L');
                    $pdf->Ln(1);
                }
                else {
                    $pdf->Cell(175, 10, utf8_decode($rs->referencia), 'TR', 'L');
                    $pdf->Ln(10);
                }

                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(25, 5, 'PROCESO', 'LTB', FALSE, 'l');
                $pdf->Cell(47, 5, utf8_decode($rs->proceso), 'TRB', 'L');
                $pdf->SetFont('helvetica', '', 7);
                $pdf->Cell(108, 5, 'ADJUNTO: ' . utf8_decode($rs->adjuntos), 1, FALSE, 'L');
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(20, 5, 'HOJAS : ' . $rs->hojas, 1, FALSE, 'L');
                $pdf->Ln(10);
                //primera pagina
                $pdf->SetXY(10, 70);
                $t = 0;
                $proveidos = $modelo->proveidos($nur);
                foreach ($proveidos as $p) {
                    $t++;
                    $pdf->Ln(4);
                    $pdf->SetFontSize(10);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                    $pdf->SetFillColor(0);
                    $pdf->Cell(192, 7, utf8_decode($p->nombre_receptor), 1, FALSE, 'L');
                    $pdf->SetFillColor(0);
                    $pdf->ln();
                    $pdf->SetFontSize(5);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(200, 1, '', 'RL', FALSE);
                    $pdf->Ln(1);
                    $acciones = array(1 => 'ATENCION URGENTE', 2 => 'ELABORAR INFORME', 3 => 'ELABORAR RESPUESTA', 4 => 'PARA SU CONSIDERACION',
                        5 => 'PARA SU CONOCIMIENTO', 6 => 'PARA VoBo', 7 => 'ARCHIVAR', '8' => 'OTRO');
                    $anchos = array(1 => 21, 2 => 20, 3 => 23, 4 => 25,
                        5 => 24, 6 => 15, 7 => 17, 8 => 16);
                    for ($j = 1; $j < 9; $j++) {
                        $pdf->Cell($anchos[$j], 5, $acciones[$j], 1, FALSE, 'C');
                        if ($p->accion == $j) {
                            $pdf->Cell(4, 5, 'X', 1, FALSE, 'L', TRUE);
                        } else {
                            $pdf->Cell(4, 5, '', 1, FALSE, 'L');
                        }
                        //ESPACIO
                        $pdf->Cell(1, 5, '', 0, FALSE, 'L');
                    }
                    $pdf->Ln();
                    $pdf->Cell(200, 1, '', 'BRL', FALSE);
                    $pdf->Ln(1);
                    $pa = $pdf->GetY();
                    //proveido
                    $pdf->SetFontSize(8);
                    $pdf->MultiCell(144, 5, utf8_decode($p->proveido), 'RL', 'L');
                    $pdf->SetXY(10, $pa);
                    $pdf->SetFontSize(10);
                    $pdf->Cell(144, 40, '', 'RL', FALSE, 'L');
                    $pdf->SetTextColor(230, 230, 230);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(56, 40, 'Sello Recibido', 1, FALSE, 'C');
                    $pdf->SetTextColor(0);
                    $pdf->Ln(40);
                    $pdf->SetFillColor(240, 245, 255);

                    $pdf->SetFontSize(10);
                    $pdf->Cell(20, 5, 'Adjunto:', 1, FALSE, 'L', true);
                    $pdf->Cell(124, 5, '', 1, FALSE, 'L');
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(20, 5, 'Hora:', 1, FALSE, 'L', true);
                    $pdf->Cell(36, 5, '', 1, FALSE, 'L');
                    $pdf->Ln();
                }
                if ($t <= 7) {
                    for ($i = 1; $i <= (7 - $t); $i++) {
                        $pdf->Ln(4);
                        $pdf->SetFontSize(10);
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                        $pdf->SetFillColor(0);
                        $pdf->Cell(192, 7, '', 1, FALSE, 'L');
                        $pdf->SetFillColor(0);
                        $pdf->ln();
                        $pdf->SetFontSize(5);
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(200, 1, '', 'RL', FALSE);
                        $pdf->Ln(1);
                        $acciones = array(1 => 'ATENCION URGENTE', 2 => 'ELABORAR INFORME', 3 => 'ELABORAR RESPUESTA', 4 => 'PARA SU CONSIDERACION',
                            5 => 'PARA SU CONOCIMIENTO', 6 => 'PARA VoBo', 7 => 'ARCHIVAR', '8' => 'OTRO');
                        $anchos = array(1 => 21, 2 => 20, 3 => 23, 4 => 25,
                            5 => 24, 6 => 15, 7 => 17, 8 => 16);
                        for ($j = 1; $j < 9; $j++) {
                            $pdf->Cell($anchos[$j], 5, $acciones[$j], 1, FALSE, 'C');

                            $pdf->Cell(4, 5, '', 1, FALSE, 'L');

                            //ESPACIO
                            $pdf->Cell(1, 5, '', 0, FALSE, 'L');
                        }
                        $pdf->Ln();
                        $pdf->Cell(200, 1, '', 'BRL', FALSE);
                        $pdf->Ln(1);
                        $pa = $pdf->GetY();
                        //proveido
                        $pdf->SetFontSize(8);
                        $pdf->MultiCell(144, 5, utf8_decode(''), 'RL', 'L');
                        $pdf->SetXY(10, $pa);
                        $pdf->SetFontSize(10);
                        $pdf->Cell(144, 40, '', 'RL', FALSE, 'L');
                        $pdf->SetTextColor(230, 230, 230);
                        $pdf->SetFontSize(20);
                        $pdf->Cell(56, 40, 'Sello Recibido', 1, FALSE, 'C');
                        $pdf->SetTextColor(0);
                        $pdf->Ln(40);
                        $pdf->SetFillColor(240, 245, 255);

                        $pdf->SetFontSize(10);
                        $pdf->Cell(20, 5, 'Adjunto:', 1, FALSE, 'L', true);
                        $pdf->Cell(124, 5, '', 1, FALSE, 'L');
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(20, 5, 'Hora:', 1, FALSE, 'L', true);
                        $pdf->Cell(36, 5, '', 1, FALSE, 'L');
                        $pdf->Ln();
                    }
                    $y = $pdf->GetY();
                    $pdf->SetXY(191, $y);
                    $pdf->SetFontSize(7);
                    $pdf->Cell(20, 1, $rs->nur, 0, FALSE, 'L');
                    //$pdf->ln();
                }

		//hoja extra
for ($i = 1; $i <= 4; $i++) {
                        $pdf->Ln(4);
                        $pdf->SetFontSize(10);
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                        $pdf->SetFillColor(0);
                        $pdf->Cell(192, 7, '', 1, FALSE, 'L');
                        $pdf->SetFillColor(0);
                        $pdf->ln();
                        $pdf->SetFontSize(5);
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(200, 1, '', 'RL', FALSE);
                        $pdf->Ln(1);
                        $acciones = array(1 => 'ATENCION URGENTE', 2 => 'ELABORAR INFORME', 3 => 'ELABORAR RESPUESTA', 4 => 'PARA SU CONSIDERACION',
                            5 => 'PARA SU CONOCIMIENTO', 6 => 'PARA VoBo', 7 => 'ARCHIVAR', '8' => 'OTRO');
                        $anchos = array(1 => 21, 2 => 20, 3 => 23, 4 => 25,
                            5 => 24, 6 => 15, 7 => 17, 8 => 16);
                        for ($j = 1; $j < 9; $j++) {
                            $pdf->Cell($anchos[$j], 5, $acciones[$j], 1, FALSE, 'C');

                            $pdf->Cell(4, 5, '', 1, FALSE, 'L');

                            //ESPACIO
                            $pdf->Cell(1, 5, '', 0, FALSE, 'L');
                        }
                        $pdf->Ln();
                        $pdf->Cell(200, 1, '', 'BRL', FALSE);
                        $pdf->Ln(1);
                        $pa = $pdf->GetY();
                        //proveido
                        $pdf->SetFontSize(8);
                        $pdf->MultiCell(144, 5, utf8_decode(''), 'RL', 'L');
                        $pdf->SetXY(10, $pa);
                        $pdf->SetFontSize(10);
                        $pdf->Cell(144, 40, '', 'RL', FALSE, 'L');
                        $pdf->SetTextColor(230, 230, 230);
                        $pdf->SetFontSize(20);
                        $pdf->Cell(56, 40, 'Sello Recibido', 1, FALSE, 'C');
                        $pdf->SetTextColor(0);
                        $pdf->Ln(40);
                        $pdf->SetFillColor(240, 245, 255);

                        $pdf->SetFontSize(10);
                        $pdf->Cell(20, 5, 'Adjunto:', 1, FALSE, 'L', true);
                        $pdf->Cell(124, 5, '', 1, FALSE, 'L');
                        $pdf->SetFillColor(240, 245, 255);
                        $pdf->Cell(20, 5, 'Hora:', 1, FALSE, 'L', true);
                        $pdf->Cell(36, 5, '', 1, FALSE, 'L');
                        $pdf->Ln();
                    }
$y = $pdf->GetY();
                    $pdf->SetXY(191, $y);
                    $pdf->SetFontSize(7);
                    $pdf->Cell(20, 1, $rs->nur, 0, FALSE, 'L');

                //$pdf->Cell(20, 5, $rs->nur, 0, FALSE, 'L');
                //$pdf->ln();
                //segunda APgina

                if (stripos($nur, '/')) {
                    $nur = explode('/', $nur);
                    $nur = $nur[0] . $nur[1];
                }
                $pdf->Output('Hoja Ruta ' . $nur . '.pdf', 'I');
            }
        } else {
            $this->request->redirect('/error404');
        }
    }

    public function action_agrupado() {
        $auth = Auth::instance();
        if ($auth->logged_in() && isset($_GET['code'])) {
            //echo 'hola';
            $nur = $_GET['code'];
            require Kohana::find_file('vendor/fpdf17', 'fpdf');
            require Kohana::find_file('vendor/fpdf17', 'code39');
            //verificamos de que la hoja de ruta es el padre de hojas de ruta hijos
            $padre = ORM::factory('agrupaciones')->where('padre', '=', $nur)->find();
            if ($padre->loaded()) {
                $documento = ORM::factory('documentos')->where('nur', '=', $nur)
                                ->and_where('original', '=', 1)->find();
                $user = $auth->get_user();
                $entidad = ORM::factory('entidades')->where('id', '=', $user->id_entidad)->find();

                $pdf = new PDF_Code39('P', 'mm', 'Letter');
                $pdf->SetMargins(15, 10, 5);
                $pdf->AddPage();
                $pdf->SetFont('Arial', '', 18);
                $pdf->SetY(20);
                $pdf->Cell(185, 10, 'CARATULA DE AGRUPACION', 1, FALSE, 'C');
                $pdf->Ln();
                $image_file = 'media/logos/' . $entidad->logo2;
                $pdf->Image($image_file, 80, 32, 50, 20, 'png', '', '', FALSE, 200, '', FALSE, FALSE, 1);
                $pdf->SetY(30);
                $pdf->Cell(185, 25, '', 'LR', FALSE, 'C');
                $pdf->Ln();
                $pdf->SetFont('Arial', '', 16);
                $pdf->Cell(185, 10, $documento->nur, 'LR', FALSE, 'C');
                $pdf->Ln();
                $pdf->Code39(80, 65, $documento->nur, 0.71, 8);
                $pdf->Ln();
                $pdf->SetY(65);
                $pdf->Cell(185, 10, '', 'LRB', FALSE, 'C');
                $pdf->Ln();
                //$pdf->
                $pdf->SetWidths(array(30, 155));
                $pdf->Row(array("Referencia: ", utf8_decode($documento->referencia)));

                $pdf->Cell(30, 10, 'Agrupado por:', 1, FALSE, 'L');
                $pdf->Cell(155, 5, utf8_decode($padre->nombre), 'LR', FALSE, 'L');
                $pdf->Ln();
                $pdf->SetX(45);
                $pdf->Cell(155, 5, utf8_decode($padre->cargo), 'LRB', FALSE, 'L');
                $pdf->Ln();
                $pdf->Cell(30, 10, 'Fecha:', 1, FALSE, 'L');
                $pdf->Cell(57, 10, date('d-m-Y', strtotime($padre->fecha)), 1, FALSE, 'L');
                $pdf->Cell(40, 10, 'Hora:', 1, FALSE, 'L');
                $pdf->Cell(58, 10, date('H:i:s', strtotime($padre->fecha)), 1, FALSE, 'L');


                $pdf->Ln(20);
                $pdf->Cell(185, 10, 'HOJA(S) DE RUTA AGRUPADO(S)', 1, FALSE, 'C');
                $pdf->Ln();
                $pdf->SetFillColor(240, 245, 255);
                $pdf->Cell(23, 5, 'HOJA  RUTA', 1, FALSE, 'C', TRUE);
                $pdf->Cell(42, 5, 'CITE ORIGINAL', 1, FALSE, 'C', TRUE);
                $pdf->Cell(79, 5, 'REFERENCIA', 1, FALSE, 'C', TRUE);
                $pdf->Cell(23, 5, 'RECEPCION', 1, FALSE, 'C', TRUE);
                $pdf->Cell(18, 5, 'OFICIAL', 1, FALSE, 'C', TRUE);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Ln();
                $mHojaruta = new Model_Hojasruta();
                $hijos = $mHojaruta->HRhijos($documento->nur);
                $pdf->SetWidths(array(23, 42, 79, 23, 18));
                foreach ($hijos as $h) {
                    $pdf->Row(array(
                        utf8_decode($h['nur']),
                        utf8_decode($h['cite_original']),
                        utf8_decode($h['referencia']),
                        utf8_decode($h['fecha_recepcion']),
                        utf8_decode($h['oficial'])));
                }
                $pdf->Output('hoja_ruta_agrupada.pdf', 'I');

                //echo $documento->referencia;
            } else {
                $this->request->redirect('error404');
            }
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_pendientes() {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            require Kohana::find_file('vendor/fpdf17', 'fpdf');
            require Kohana::find_file('vendor/fpdf17', 'code39');
            //verificamos de que la hoja de ruta es el padre de hojas de ruta hijos
            $user = $auth->get_user();

            $pdf = new PDF_Code39('P', 'mm', 'Letter');
            $pdf->SetMargins(15, 10, 5);
            $pdf->AddPage('L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(245, 10, 'Correspondencia Pendiente - Sistema de Gestion de Correspondencia', 1, FALSE, 'C');
            $pdf->Ln();
            $pdf->Cell(20, 5, 'Usuario', 1, FALSE, 'L');
            $pdf->Cell(140, 5, $user->nombre, 1, FALSE, 'L');
            $pdf->Cell(20, 5, 'Fecha', 'LRB', FALSE, 'L');
            $pdf->Cell(65, 5, date('d-m-Y H:i:s'), 1, FALSE, 'L');
            $pdf->Ln();
            $pdf->Cell(20, 5, 'Cargo', 'LRB', FALSE, 'L');
            $pdf->Cell(140, 5, $user->cargo, 'LRB', FALSE, 'L');
            $pdf->Cell(20, 5, 'Correo', 'LRB', FALSE, 'L');
            $pdf->Cell(65, 5, $user->email, 'LRB', FALSE, 'L');
            $pdf->Ln(10);
            //$pdf->
            $pdf->SetFont('Arial', '', 7);
            $mHojaruta = new Model_Hojasruta();
            $hijos = $mHojaruta->pendientes($user->id);
            //titulo
            $pdf->SetFillColor(240, 245, 255);
            $pdf->Cell(5, 5, 'N', 1, FALSE, 'C', TRUE);
            $pdf->Cell(20, 5, 'HOJA RUTA', 1, FALSE, 'C', TRUE);
            $pdf->Cell(42, 5, 'CITE ORIGINAL', 1, FALSE, 'C', TRUE);
            $pdf->Cell(75, 5, 'REFERENCIA', 1, FALSE, 'C', TRUE);
            $pdf->Cell(60, 5, 'REMITENTE', 1, FALSE, 'C', TRUE);
            $pdf->Cell(17, 5, 'RECEPCION', 1, FALSE, 'C', TRUE);
            $pdf->Cell(9, 5, 'D->R', 1, FALSE, 'C', TRUE);
            $pdf->Cell(9, 5, 'R->F', 1, FALSE, 'C', TRUE);
            $pdf->Cell(10, 5, 'OFICIAL', 1, FALSE, 'C', TRUE);
            $pdf->Ln();
            $pdf->SetWidths(array(5, 20, 42, 75, 60, 17, 9, 9, 10));
            $i = 1;
            foreach ($hijos as $h) {
                $pdf->Row(array(
                    $i,
                    utf8_decode($h['nur']),
                    utf8_decode($h['cite_original']),
                    utf8_decode($h['referencia']),
                    utf8_decode($h['nombre_emisor'] . "\n" . $h['cargo_emisor']),
                    utf8_decode($h['fecha_recepcion']),
                    utf8_decode($h['dias_recepcion']),
                    utf8_decode($h['dias_ahora']),
                    utf8_decode($h['oficial']),
                ));
                $i++;
            }
            $pdf->Output('hoja_ruta_agrupada.pdf', 'I');

            //echo $documento->referencia;
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_enviados() {
        $auth = Auth::instance();
        if ($auth->logged_in()) {
            require Kohana::find_file('vendor/fpdf17', 'fpdf');
            require Kohana::find_file('vendor/fpdf17', 'code39');
            //verificamos de que la hoja de ruta es el padre de hojas de ruta hijos
            $user = $auth->get_user();

            $pdf = new PDF_Code39('P', 'mm', 'Letter');
            $pdf->SetMargins(15, 10, 5);
            $pdf->AddPage('L');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(245, 10, 'Correspondencia Pendiente - Sistema de Gestion de Correspondencia', 1, FALSE, 'C');
            $pdf->Ln();
            $pdf->Cell(20, 5, 'Usuario', 1, FALSE, 'L');
            $pdf->Cell(140, 5, $user->nombre, 1, FALSE, 'L');
            $pdf->Cell(20, 5, 'Fecha', 'LRB', FALSE, 'L');
            $pdf->Cell(65, 5, date('d-m-Y H:i:s'), 1, FALSE, 'L');
            $pdf->Ln();
            $pdf->Cell(20, 5, 'Cargo', 'LRB', FALSE, 'L');
            $pdf->Cell(140, 5, $user->cargo, 'LRB', FALSE, 'L');
            $pdf->Cell(20, 5, 'Correo', 'LRB', FALSE, 'L');
            $pdf->Cell(65, 5, $user->email, 'LRB', FALSE, 'L');
            $pdf->Ln(10);
            //$pdf->
            $pdf->SetFont('Arial', '', 7);
            $mHojaruta = new Model_Hojasruta();
            $hijos = $mHojaruta->pendientes($user->id, $fecha1, $fecha2);
            //titulo
            $pdf->SetFillColor(240, 245, 255);
            $pdf->Cell(5, 5, 'N', 1, FALSE, 'C', TRUE);
            $pdf->Cell(20, 5, 'HOJA RUTA', 1, FALSE, 'C', TRUE);
            $pdf->Cell(42, 5, 'CITE ORIGINAL', 1, FALSE, 'C', TRUE);
            $pdf->Cell(75, 5, 'REFERENCIA', 1, FALSE, 'C', TRUE);
            $pdf->Cell(60, 5, 'REMITENTE', 1, FALSE, 'C', TRUE);
            $pdf->Cell(17, 5, 'RECEPCION', 1, FALSE, 'C', TRUE);
            $pdf->Cell(9, 5, 'D->R', 1, FALSE, 'C', TRUE);
            $pdf->Cell(9, 5, 'R->F', 1, FALSE, 'C', TRUE);
            $pdf->Cell(10, 5, 'OFICIAL', 1, FALSE, 'C', TRUE);
            $pdf->Ln();
            $pdf->SetWidths(array(5, 20, 42, 75, 60, 17, 9, 9, 10));
            $i = 1;
            foreach ($hijos as $h) {
                $pdf->Row(array(
                    $i,
                    utf8_decode($h['nur']),
                    utf8_decode($h['cite_original']),
                    utf8_decode($h['referencia']),
                    utf8_decode($h['nombre_emisor'] . "\n" . $h['cargo_emisor']),
                    utf8_decode($h['fecha_recepcion']),
                    utf8_decode($h['dias_recepcion']),
                    utf8_decode($h['dias_ahora']),
                    utf8_decode($h['oficial']),
                ));
                $i++;
            }
            $pdf->Output('hoja_ruta_agrupada.pdf', 'I');

            //echo $documento->referencia;
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_word() {
        
    }

}
