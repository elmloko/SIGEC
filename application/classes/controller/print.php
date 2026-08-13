<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Print extends Controller
{

    public function action_hr()
    {
        $auth = Auth::instance();
        if ($auth->logged_in() && isset($_GET['code'])) {
            $pro = 0;
            if (isset($_GET['p'])) {
                $pro = $_GET['p'];
            }

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

                if (is_null($rs->logo)) {
                    $rs->logo = '564e43d862172.png';
                }

		$src = DOCROOT . 'media/LOGO 19-2-26.png';
                //$src = DOCROOT . 'static/logos/' . $rs->logo;
                if (file_exists($src)) {
                    $image_file = $src;
                    $info = pathinfo($src);
                    // continue only if this is a JPEG image
                    if (strtolower($info['extension']) == 'jpg') {
                        $pdf->Image($image_file, 10, 3, 63, 24, 'jpg', '', '', FALSE, 300, '', FALSE, FALSE, 1);
                    } else {
                        $pdf->Image($image_file, 10, 3, 63, 24, 'png', '', '', FALSE, 300, '', FALSE, FALSE, 1);
                    }
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
                /*
                        $pdf->SetXY(150, 29);
                        $pdf->SetFont('helvetica', '', 8);
                        $pdf->Cell(60, 5, 'CITE ORIGINAL', 'TRL', FALSE, 'C');
                        $pdf->SetXY(150, 34);
                        $pdf->SetFont('helvetica', 'B', 8);
                        $pdf->Cell(60, 5, utf8_decode($rs->cite_original), 'RL', FALSE, 'C');
                        $pdf->Ln();
                */

                $pdf->SetXY(135, 29);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(75, 5, 'CITE ORIGINAL', 'TRL', FALSE, 'C');
                $pdf->SetXY(135, 34);
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(75, 5, utf8_decode($rs->cite_original), 'RL', FALSE, 'C');
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
                } else {
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

                $nro_proveido = 0;
                foreach ($proveidos as $p) {
                    $nro_proveido++;

                    $pdf->Ln(5);
                    $pdf->SetFontSize(10);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(30, 7, utf8_decode('Proveido Nº: ' . $nro_proveido), 1, FALSE, 'L', true);
                    $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                    $pdf->SetFillColor(0);
                    $receptor = utf8_decode($p->nombre_receptor);
                    $cargo_del_receptor = utf8_decode($p->cargo_receptor);

                    // Cuando seleccionamos imprimir 'sin proveido'
                    //if ($pro == 0) {
                    //    $receptor = "";
                    //    $cargo_del_receptor = "";
                    //}

                    // [INICIO] SEGUIDO DEL PROVEIDO: MOSTRAR 'NOMBRE' Y 'CARGO'
                    $pdf->SetFont('helvetica', '', 7);
                    $pdf->Cell(162, 4, $receptor, 'TR', FALSE, 'L');
                    $pdf->Ln(3);
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->Cell(38, 4, '', 0, FALSE, 'L');
                    $pdf->Cell(162, 4, $cargo_del_receptor, 'RB', FALSE, 'L');

                    $pdf->SetFont('helvetica', '', 7);
                    $pdf->ln();
                    $pdf->SetFontSize(5);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(200, 1, '', 'RL', FALSE);
                    $pdf->Ln(1);
                    // [FIN] SEGUIDO DEL PROVEIDO: MOSTRAR 'NOMBRE' Y 'CARGO'
                    
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
                    $proveido = utf8_decode($p->proveido);
                    //proveido
                    if ($pro == 0) {
                        $proveido = "";
                    }

                    $pdf->MultiCell(144, 5, $proveido, 'RL', 'L');
                    $pdf->SetXY(10, $pa);
                    $pdf->SetFontSize(10);
                    $pdf->Cell(144, 38, '', 'RL', FALSE, 'L');
                    $pdf->SetTextColor(230, 230, 230);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(56, 38, 'Sello Recibido', 1, FALSE, 'C');
                    $pdf->SetTextColor(0);
                    $pdf->Ln(38);
                    $pdf->SetFillColor(240, 245, 255);

                    $pdf->SetFontSize(10);
                    $pdf->Cell(20, 4, 'Adjunto:', 1, FALSE, 'L', true);
                    $pdf->Cell(124, 4, '', 1, FALSE, 'L');
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(20, 4, 'Hora:', 1, FALSE, 'L', true);
                    $pdf->Cell(36, 4, '', 1, FALSE, 'L');
                    if ($t < 4) {
                        $pdf->Ln(6);
                    } else {
                        $pdf->Ln(6);
                    }


                    $yy = $pdf->GetY();
                    if ($yy > 250) {
                        // $y = $pdf->GetY();
                        // $pdf->SetXY(20, 20);
                        //$pdf->SetXY(10, 4);
                        $pdf->SetFontSize(9);
                        $pdf->Cell(100, 2, utf8_decode("CITE: " . $rs->cite_original), 0, FALSE, 'L');
                        $pdf->Cell(100, 2, $rs->nur, 0, FALSE, 'R');
                        $pdf->SetXY(10, 9);
                    }

                    //si la cantidad de derivaciones es igual a 7 o superior
                    if ($t == 7) {
                        $t = 0;
                    } else {
                        $t++;
                    }
                }

                //si la cantidad de derivaciones es igual a 7 o superior
                /*
                if ($nro_proveido <= 3) {
                    $t = 3 - $nro_proveido;
                }
                if ($nro_proveido >= 4) {
                    $nro_pagina = $pdf->PageNo();
                    $nro_max_proveido_por_pagina = ($nro_pagina * 3) + ($nro_pagina - 1);
                    $t = $nro_max_proveido_por_pagina - $nro_proveido;
                }
                */

                /*
                if ($nro_proveido <= 3) {
                    $nro_pagina = $pdf->PageNo() + 1;
                    $nro_max_proveido_por_pagina = ($nro_pagina * 3) + ($nro_pagina - 1);

                    $t = $nro_max_proveido_por_pagina;
                } else {
                    if ($nro_proveido >= 4) {
                        $nro_pagina = $pdf->PageNo() + 2;
                        $nro_max_proveido_por_pagina = ($nro_pagina * 3) + ($nro_pagina - 1);

                        $t = $nro_max_proveido_por_pagina - $nro_proveido;
                        $t = $nro_max_proveido_por_pagina;
                    }
                }
                */

                $cantidad_de_proveidos = count($proveidos);
                $tope_proveidos_a_completar = 7 - $t;
                $tope_proveidos_a_completar = $cantidad_de_proveidos + $tope_proveidos_a_completar;
                for ($i = $cantidad_de_proveidos + 1; $i <= $tope_proveidos_a_completar; $i++) {

                    $pdf->Ln(5);
                    $yy = $pdf->GetY();
                    if ($yy > 250) {
                        // $y = $pdf->GetY();
                        // $pdf->SetXY(20, 20);
                        //$pdf->SetXY(10, 4);
                        $pdf->SetFontSize(9);
                        $pdf->Cell(100, 2, utf8_decode("CITE: " . $rs->cite_original), 0, FALSE, 'L');
                        $pdf->Cell(100, 2, $rs->nur, 0, FALSE, 'R');
                        $pdf->SetXY(10, 13);
                    }


                    $pdf->SetFontSize(10);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(30, 7, utf8_decode('Proveido Nº: ' . $i), 1, FALSE, 'L', true);
                    $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                    $pdf->SetFillColor(0);
                    $pdf->Cell(162, 7, '', 1, FALSE, 'L');
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
                    $pdf->Cell(144, 38, '', 'RL', FALSE, 'L');
                    $pdf->SetTextColor(230, 230, 230);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(56, 38, 'Sello Recibido', 1, FALSE, 'C');
                    $pdf->SetTextColor(0);
                    $pdf->Ln(38);
                    $pdf->SetFillColor(240, 245, 255);

                    $pdf->SetFontSize(10);
                    $pdf->Cell(20, 4, 'Adjunto:', 1, FALSE, 'L', true);
                    $pdf->Cell(124, 4, '', 1, FALSE, 'L');
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(20, 4, 'Hora:', 1, FALSE, 'L', true);
                    $pdf->Cell(36, 4, '', 1, FALSE, 'L');
                    $pdf->Ln(4);
                }

                //hoja extra
                $numero_hojas_extra = 2;
                for ($i = $tope_proveidos_a_completar + 1; $i <= $tope_proveidos_a_completar + ($numero_hojas_extra * 4); $i++) {

                    $pdf->Ln(6);
                    $yy = $pdf->GetY();
                    if ($yy > 250) {
                        $pdf->SetFontSize(9);
                        $pdf->Cell(100, 2, utf8_decode("CITE: " . $rs->cite_original), 0, FALSE, 'L');
                        $pdf->Cell(100, 2, $rs->nur, 0, FALSE, 'R');
                        $pdf->SetXY(10, 13);
                    }

                    $pdf->SetFontSize(10);
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(30, 7, utf8_decode('Proveido Nº: ' . $i), 1, FALSE, 'L', true);
                    $pdf->Cell(8, 7, 'A:', 1, FALSE, 'L', true);
                    $pdf->SetFillColor(0);
                    $pdf->Cell(162, 7, '', 1, FALSE, 'L');
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
                    $pdf->Cell(144, 38, '', 'RL', FALSE, 'L');
                    $pdf->SetTextColor(230, 230, 230);
                    $pdf->SetFontSize(20);
                    $pdf->Cell(56, 38, 'Sello Recibido', 1, FALSE, 'C');
                    $pdf->SetTextColor(0);
                    $pdf->Ln(38);
                    $pdf->SetFillColor(240, 245, 255);

                    $pdf->SetFontSize(10);
                    $pdf->Cell(20, 4, 'Adjunto:', 1, FALSE, 'L', true);
                    $pdf->Cell(124, 4, '', 1, FALSE, 'L');
                    $pdf->SetFillColor(240, 245, 255);
                    $pdf->Cell(20, 4, 'Hora:', 1, FALSE, 'L', true);
                    $pdf->Cell(36, 4, '', 1, FALSE, 'L');
                    $pdf->Ln(4);
                }

                // $y = $pdf->GetY();
                // $pdf->SetXY(171, $y + 1);
                // $pdf->SetFontSize(10);
                // $pdf->Cell(20, 1, $rs->nur, 0, FALSE, 'L');
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

    public function action_agrupado()
    {
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

    public function action_pendientes()
    {
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

    public function action_enviados()
    {
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

//impremir seguimiento
    //impremir seguimiento
    public function action_seguimiento()
    {
        $auth = Auth::instance();
        if ($auth->logged_in()) {

            require Kohana::find_file('vendor/fpdf17', 'fpdf');
            require Kohana::find_file('vendor/fpdf17', 'code39');
            //verificamos de que la hoja de ruta es el padre de hojas de ruta hijos
            $user = $auth->get_user();

            $hr = $_GET['hr'];
            //$hr = 'MT/2015-04144';

            $documento = ORM::factory('documentos')->where('nur', '=', $hr)->and_where('original', '=', 1)->find();
            $tipo = ORM::factory('tipos', $documento->id_tipo);
            $proceso = ORM::factory('procesos', $documento->id_proceso);
            $detalle = array(
                'nur' => $hr,
                'fecha' => $documento->fecha_creacion,
                'codigo' => $documento->cite_original,
                'id_documento' => $documento->id,
                'tipo' => $tipo->tipo,
                'proceso' => $proceso->proceso,
                'referencia' => $documento->referencia,
                'remitente' => $documento->nombre_remitente,
                'cargo_remitente' => $documento->cargo_remitente,
                'destinatario' => $documento->nombre_destinatario,
                'cargo_destinatario' => $documento->cargo_destinatario,
                'adjunto' => $documento->adjuntos,
            );

            $archivo = ORM::factory('archivos')
                ->where('id_documento', '=', $documento->id)
                ->find_all();
            //$seguimiento=ORM::factory('seguimiento')->where('nur','=',$id)->find_all();
            $oSeg = New Model_Seguimiento();
            $seguimiento = $oSeg->seguimiento($hr);
            //$f = $oSeg->archivado($id);
            //$oficina = $this->user->id_oficina;
            //$user = $this->user;
            //agrupaciones
            $agrupado = ORM::factory('agrupaciones')->where('hijo', '=', $hr)->find();
            $pdf = new PDF_Code39('P', 'mm', 'Letter');
            $pdf->SetMargins(15, 10, 5);
            $pdf->AddPage('L');
            $pdf->SetFont('Arial', '', 14);


            $pdf->Cell(246, 10, 'Seguimiento de la hoja de ruta : ' . $hr, 1, FALSE, 'C');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Ln();

            $pdf->SetWidths(array(25, 221));

            $pdf->Row(array(
                utf8_decode('Referencia'),
                utf8_decode($detalle['referencia'])
            ));
            //segunda linea
            $pdf->SetWidths(array(25, 130, 19, 72));
            $pdf->Row(array(
                utf8_decode('Cite original'),
                utf8_decode($detalle['codigo']),
                utf8_decode('Proceso'),
                utf8_decode($detalle['proceso'])
            ));
            $pdf->Row(array(
                utf8_decode('Destinatario'),
                utf8_decode($detalle['destinatario']) . ' | ' . utf8_decode($detalle['cargo_destinatario']),
                utf8_decode('Tipo doc.'),
                utf8_decode($detalle['tipo'])
            ));
            $pdf->Row(array(
                utf8_decode('Remitente'),
                utf8_decode($detalle['remitente']) . ' | ' . utf8_decode($detalle['cargo_remitente']),
                utf8_decode('Fecha'),
                utf8_decode(Date::fecha($detalle['fecha']) . ' ' . date('H:i:s', strtotime($detalle['fecha'])))
            ));
            $pdf->Ln();
            //$pdf->
            //si es agrupado
            if (isset($agrupado->id)):
                $pdf->Cell(246, 10, $agrupado->padre, 1, FALSE, 'C');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Ln();
            endif;
            if (isset($agrupado->id)):
                $pdf->Cell(246, 10, 'Una copia pertenece a la Hoja de Ruta principal:' . $agrupado->padre, 1, FALSE, 'C');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Ln();
            endif;

            $count = 0;
            $hijo = 0;
            foreach ($seguimiento as $s):

                $pasos = "";
                if ($s->oficial > 0) {
                    if ($s->id_estado == 1) {
                        $pasos = 'media/flag.png';
                    } else
                        $pasos = 'media/paw.png';
                }

                $pasos2 = "";
                if (($s->oficial > 0) && ($s->id_estado == 6)) {
                    $pasos2 = 'media/flag.png';
                }
                $pasos2 = "";
                if (($s->oficial > 0) && ($s->id_estado == 2)) {
                    $pasos2 = 'media/flag.png';
                }

                if (($s->oficial > 0) && ($s->id_estado == 4)) {
                    $pasos2 = 'media/paw.png';
                }
                if (($s->oficial > 0) && ($s->id_estado == 6)) {
                    $pasos2 = 'media/flag.png';
                }
                if (($s->oficial > 0) && ($s->id_estado == 10)) {
                    $pasos2 = 'media/flag.png';
                }
                /*
                $x=$pdf->GetX();
                $y=$pdf->GetY();
                $pdf->MultiCell(8, 5,$pasos , 'LTR', 'L');
                $pdf->SetXY($x+8, $y);
                $pdf->MultiCell(98, 5,$s->de_oficina , 'LTR', 'L');
                $pdf->SetXY($x+98, $y);
                $pdf->MultiCell(8, 5,$pasos2 , 'LTR', 'L');
                $pdf->SetXY($x+8, $y);
                $pdf->MultiCell(98, 5,$s->a_oficina , 'LTR', 'L');
                $pdf->SetXY($x+98, $y);
                $pdf->MultiCell(22, 5,'' , 'LTR', 'L');
                $pdf->SetXY($x+22, $y);
                $pdf->SetWidths(array(8, 98, 8, 98, 22));
                $pdf->Ln();
                */

                if ($pdf->GetY() > 170) {
                    $pdf->Ln(15);
                }

                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(101, 4, utf8_decode($s->de_oficina), 'LT', 0, 'L');
                if ($pasos != '') {
                    $y = $pdf->GetY();
                    $y++;
                    //$pdf->Image($file, $x, $y, $w, $h, $type)
                    $pdf->Image($pasos, 115, $y, 8, 7, 'png');
                    //$pdf->SetY($y);
                    $pdf->Cell(8, 4, '', 'TR', 0, 'L');
                } else {
                    $pdf->Cell(8, 4, $pasos, 'TR', 0, 'L');
                }
                $pdf->Cell(101, 4, utf8_decode($s->a_oficina), 'LT', 0, 'L');

                if ($pasos2 != '') {
                    $y = $pdf->GetY();
                    $y++;
                    //$pdf->Image($file, $x, $y, $w, $h, $type)
                    $pdf->Image($pasos2, 215, $y, 8, 7, 'png');
                    //$pdf->SetY($y);
                    $pdf->Cell(8, 4, '', 'TR', 0, 'L');

                } else {
                    $pdf->Cell(8, 4, $pasos2, 'TR', 0, 'L');
                }


                // MOSTRAR TEXTO DE JUSTIFICACIONES
                $logo_observacion = 'media/exclamation-triangle.png';

                $queryJustificacion = " SELECT
                                                COUNT(1) AS nro_resultados
                                            FROM
                                                observacion_seguimiento
                                            WHERE
                                                (id_estado = '2')
                                                    AND (id_seguimiento = '$s->id');";

                $y = $pdf->GetY();
                $y++;
                $resultSetJustificacion = db::query(Database::SELECT, $queryJustificacion, FALSE)
                    ->execute()
                    ->as_array();

                $nro_resultados = $resultSetJustificacion[0]['nro_resultados'];

                if ((intval($nro_resultados) >= 1) === TRUE) {
                    $pdf->Image($logo_observacion, 225, $y, 7, 7, 'png');
                } else {
                    //$pdf->Cell(31, 4,'<<<<<<<<<<<<< 0', 'LTR', 0, 'L');
                }


                $pdf->Cell(31, 4, utf8_decode(''), 'LTR', 0, 'L');

                $pdf->Ln(4);
                $pdf->Cell(101, 4, utf8_decode($s->nombre_emisor), 'L', 0, 'L');
                $pdf->Cell(8, 4, '', 'R', 0, 'L');
                $pdf->Cell(101, 4, utf8_decode($s->nombre_receptor), 'L', 0, 'L');
                $pdf->Cell(8, 4, '', 'R', 0, 'L');
                $pdf->Cell(31, 4, utf8_decode($s->estado), 'LR', 0, 'L');
                $pdf->Ln(4);

                $pdf->Cell(101, 4, utf8_decode($s->cargo_emisor), 'L', 0, 'L');
                $pdf->Cell(8, 4, '', 'R', 0, 'L');
                $pdf->Cell(101, 4, utf8_decode($s->cargo_receptor), 'L', 0, 'L');
                $pdf->Cell(8, 4, '', 'R', 0, 'L');
                $pdf->Cell(31, 4, utf8_decode(''), 'LR', 0, 'L');

                $pdf->Ln(4);

                $pdf->Cell(109, 4, utf8_decode(Date::fecha_medium($s->fecha_emision)) . ' - ' . $s->hora_emision, 'LR', 0, 'R');
                // Verificamos si la 'fecha_recepcion' no es nulo
                if (!is_null($s->fecha_recepcion) && !is_null($s->hora_recepcion)) {
                    $pdf->Cell(109, 4, utf8_decode(Date::fecha_medium($s->fecha_recepcion)) . ' - ' . $s->hora_recepcion, 'LR', 0, 'R');
                } else {
                    $pdf->Cell(109, 4, utf8_decode($s->fecha_recepcion) . ' - ' . $s->hora_recepcion, 'LR', 0, 'R');
                }

                // $pdf->Cell(109, 4, utf8_decode(Date::fecha_medium($s->fecha_recepcion)) . ' - ' . $s->hora_recepcion, 'LR', 0, 'R');
                $pdf->Cell(31, 4, utf8_decode($s->accion), 'LR', 0, 'L');

                $pdf->Ln(4);
                $pdf->SetWidths(array(249));
                $pdf->Row(array(utf8_decode($s->proveido)));
                $pdf->Ln(3);
            endforeach;
            $pdf->Ln();
            $pdf->Cell(109, 4, utf8_decode("Fecha de impresión: " . Date::fecha_medium(date('Y-m-d'))));

            $pdf->Output('hoja_ruta_.pdf', 'I');

            //echo $documento->referencia;
        } else {
            $this->request->redirect('error404');
        }
    }

    public function action_word()
    {

    }

}

