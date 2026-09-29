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
        if (!$auth->logged_in()) {
            $this->request->redirect('error404');
        }
        require Kohana::find_file('vendor/fpdf17', 'fpdf');
        require Kohana::find_file('vendor/fpdf17', 'code39');
        $user = $auth->get_user();
        $hr = trim(Arr::get($_GET, 'hr', ''));

        $documento = ORM::factory('documentos')->where('nur', '=', $hr)->and_where('original', '=', 1)->find();
        $tipo = ORM::factory('tipos', $documento->id_tipo);
        $proceso = ORM::factory('procesos', $documento->id_proceso);
        $agrupado = ORM::factory('agrupaciones')->where('hijo', '=', $hr)->find();
        $oSeg = New Model_Seguimiento();
        $pasos = array();
        foreach ($oSeg->seguimiento($hr) as $s) {
            $pasos[] = $s;
        }
        $actual = SeguimientoUtil::paso_actual($pasos);
        $ultimo_oficial = SeguimientoUtil::ultimo_oficial($pasos);
        $tenencia = SeguimientoUtil::tenencia($pasos);
        $ruta = SeguimientoUtil::ruta($pasos);
        // prioridad del tramite (si tiene alerta registrada)
        $alerta = DB::query(Database::SELECT, 'SELECT s.prioridad, a.fecha FROM alertas a INNER JOIN seguimiento s ON a.id_seguimiento = s.id WHERE s.nur = :nur LIMIT 1')
                ->param(':nur', $hr)->execute()->current();
        $urgente = $alerta && (int) $alerta['prioridad'] === 1;

        $pdf = new Pdf_Seguimiento('L', 'mm', 'Letter');
        $pdf->hr = 'Hoja de ruta ' . $hr;
        $pdf->impreso_por = $user->nombre;
        $pdf->logo = DOCROOT . 'media/LOGO 19-2-26.png';
        $pdf->SetTitle($pdf->t('Seguimiento ' . $hr));
        $pdf->SetMargins(12, 10, 12);
        $pdf->SetAutoPageBreak(TRUE, 14);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $m = 12;
        $ancho = 279.4 - 24;

        // ===== datos del documento =====
        $y = $pdf->GetY();
        $pdf->SetFont('Arial', 'B', 11);
        $referencia = $pdf->t($documento->referencia != '' ? $documento->referencia : 'Sin referencia');
        $alto_ref = $pdf->alto_texto($ancho - 8, 5.2, $referencia);
        $alto_caja = 8 + $alto_ref + 24;
        $pdf->color_linea(Pdf_Seguimiento::$borde);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($m, $y, $ancho, $alto_caja, 2.5, 'D');
        $pdf->SetXY($m + 4, $y + 3);
        $pdf->SetFont('Arial', '', 6.5);
        $pdf->color_texto(Pdf_Seguimiento::$gris);
        $pdf->Cell(40, 3.5, $pdf->t('REFERENCIA'), 0, 2);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->color_texto(Pdf_Seguimiento::$azul_oscuro);
        $pdf->MultiCell($ancho - 8, 5.2, $referencia, 0, 'L');
        $yc = $pdf->GetY() + 2;
        $pdf->color_linea(Pdf_Seguimiento::$borde);
        $pdf->Line($m + 4, $yc - 1, $m + $ancho - 4, $yc - 1);
        $col = ($ancho - 8) / 4;
        $campos = array(
            array('DOCUMENTO ORIGINAL', $documento->cite_original, ''),
            array('TIPO DE DOCUMENTO', $tipo->tipo, ''),
            array('PROCESO', $proceso->proceso != '' ? $proceso->proceso : '—', ''),
            array('CREADO', $documento->fecha_creacion ? Date::fecha($documento->fecha_creacion) . ' · ' . date('H:i', strtotime($documento->fecha_creacion)) : '—', ''),
        );
        foreach ($campos as $i => $c) {
            $pdf->SetXY($m + 4 + $i * $col, $yc);
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->Cell($col - 3, 3.5, $pdf->t($c[0]), 0, 2);
            $pdf->SetFont('Arial', 'B', 8.5);
            $pdf->color_texto(Pdf_Seguimiento::$texto);
            $pdf->Cell($col - 3, 4.5, $pdf->t($c[1]), 0, 0);
        }
        $yc += 10;
        $personas = array(
            array('REMITENTE', $documento->nombre_remitente, $documento->cargo_remitente),
            array('DESTINATARIO', $documento->nombre_destinatario, $documento->cargo_destinatario),
        );
        foreach ($personas as $i => $p) {
            $pdf->SetXY($m + 4 + $i * 2 * $col, $yc);
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->Cell(2 * $col - 3, 3.5, $pdf->t($p[0]), 0, 2);
            $pdf->SetFont('Arial', 'B', 8.5);
            $pdf->color_texto(Pdf_Seguimiento::$texto);
            $pdf->Cell(2 * $col - 3, 4.5, $pdf->t($p[1]) . '   ', 0, 0);
            $pdf->SetFont('Arial', '', 7.5);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->SetXY($m + 4 + $i * 2 * $col + $pdf->GetStringWidth($pdf->t($p[1])) * 8.5 / 7.5 + 4, $yc + 3.8);
            $pdf->Cell(2 * $col - 3, 4.5, $pdf->t($p[2]), 0, 0);
        }
        $pdf->SetY($y + $alto_caja + 5);

        if (!$pasos) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->Cell($ancho, 20, $pdf->t('Esta hoja de ruta aún no fue derivada.'), 0, 1, 'C');
            $pdf->Output('seguimiento_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $hr) . '.pdf', 'I');
            exit;
        }

        // ===== resumen =====
        $paso_res = $actual !== NULL ? $pasos[$actual] : ($ultimo_oficial !== NULL ? $pasos[$ultimo_oficial] : end($pasos));
        $abierto = $actual !== NULL;
        $desde_res = (int) $paso_res->id_estado === 1 ? $paso_res->fecha_emision . ' ' . $paso_res->hora_emision
            : ($paso_res->fecha_recepcion ? $paso_res->fecha_recepcion . ' ' . $paso_res->hora_recepcion : $paso_res->fecha_emision);
        $dias_res = SeguimientoUtil::dias_entre($desde_res);
        $n_oficiales = 0;
        foreach ($pasos as $p) {
            $n_oficiales += (int) $p->oficial > 0 ? 1 : 0;
        }
        $dias_creado = $documento->fecha_creacion ? SeguimientoUtil::dias_entre($documento->fecha_creacion) : NULL;
        $resumen = array(
            array($abierto ? 'AHORA ESTÁ CON' : 'ÚLTIMO DESTINO', $paso_res->nombre_receptor,
                $paso_res->estado . ($abierto && $dias_res !== NULL ? ' · hace ' . ($dias_res == 1 ? '1 día' : $dias_res . ' días') : ''),
                $abierto && $dias_res > 7),
            array('RECORRIDO', count($pasos) . ' paso' . (count($pasos) == 1 ? '' : 's'),
                $n_oficiales . ' oficial' . ($n_oficiales == 1 ? '' : 'es') . ' · ' . (count($pasos) - $n_oficiales) . ' en copia', FALSE),
            array('TIEMPO DEL TRÁMITE', $dias_creado !== NULL ? ($dias_creado == 1 ? '1 día' : $dias_creado . ' días') : '—', 'desde que se creó', FALSE),
            array('PRIORIDAD', $urgente ? 'Urgente' : 'Normal',
                $alerta && $alerta['fecha'] ? 'Plazo: ' . date('d/m/Y', strtotime($alerta['fecha'])) : 'Sin plazo definido', $urgente),
        );
        $y = $pdf->GetY();
        $gap = 4;
        $wbox = ($ancho - 3 * $gap) / 4;
        foreach ($resumen as $i => $r) {
            $x = $m + $i * ($wbox + $gap);
            $pdf->color_relleno($r[3] ? array(253, 232, 232) : Pdf_Seguimiento::$fondo);
            $pdf->RoundedRect($x, $y, $wbox, 16, 2.5, 'F');
            $pdf->color_relleno($r[3] ? array(211, 47, 47) : Pdf_Seguimiento::$azul);
            $pdf->Rect($x, $y + 3, 1.2, 10, 'F');
            $pdf->SetXY($x + 4, $y + 2.2);
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->Cell($wbox - 6, 3.5, $pdf->t($r[0]), 0, 2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->color_texto($r[3] ? array(180, 35, 24) : Pdf_Seguimiento::$azul_oscuro);
            $pdf->Cell($wbox - 6, 5.5, $pdf->t($r[1]), 0, 2);
            $pdf->SetFont('Arial', '', 7);
            $pdf->color_texto(Pdf_Seguimiento::$gris);
            $pdf->Cell($wbox - 6, 3.5, $pdf->t($r[2]), 0, 0);
        }
        $pdf->SetY($y + 20);

        // ===== camino (pasos oficiales) =====
        if (count($ruta) > 1) {
            $pdf->SetFont('Arial', 'B', 7.5);
            $pdf->color_texto(Pdf_Seguimiento::$azul_oscuro);
            $pdf->Cell(18, 4.5, $pdf->t('Camino:'), 0, 0);
            $x = $pdf->GetX();
            $yr = $pdf->GetY();
            foreach ($ruta as $k => $nodo) {
                $es_actual = $nodo['paso'] !== NULL && $nodo['paso'] === $actual;
                $texto = $pdf->t(($k === 0 ? '' : $k . '. ') . $nodo['oficina']);
                $pdf->SetFont('Arial', $es_actual ? 'B' : '', 7);
                $w = $pdf->GetStringWidth($texto) + 5;
                if ($x + $w + 5 > $m + $ancho) {
                    $x = $m + 18;
                    $yr += 6;
                }
                $pdf->color_relleno($es_actual ? Pdf_Seguimiento::$amarillo : Pdf_Seguimiento::$azul_suave);
                $pdf->RoundedRect($x, $yr, $w, 4.8, 1.5, 'F');
                $pdf->color_texto(Pdf_Seguimiento::$azul_oscuro);
                $pdf->SetXY($x, $yr + 0.3);
                $pdf->Cell($w, 4.2, $texto, 0, 0, 'C');
                $x += $w;
                if ($k < count($ruta) - 1) {
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->color_texto(Pdf_Seguimiento::$gris_claro);
                    $pdf->SetXY($x, $yr + 0.3);
                    $pdf->Cell(5, 4.2, '>', 0, 0, 'C');
                    $x += 5;
                }
            }
            $pdf->SetY($yr + 8);
        }

        if (isset($agrupado->id)) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->color_relleno(Pdf_Seguimiento::$amarillo_suave);
            $pdf->color_texto(array(138, 97, 0));
            $pdf->Cell($ancho, 6, $pdf->t('Esta hoja de ruta fue agrupada dentro de la hoja de ruta principal ' . $agrupado->padre), 0, 1, 'L', TRUE);
            $pdf->Ln(3);
        }

        // ===== recorrido paso a paso =====
        $pdf->titulo_seccion('Recorrido de la hoja de ruta');
        $x_caja = $m + 11;
        $w_caja = $ancho - 11;
        $w_col = ($w_caja - 8 - 10) / 2;
        foreach ($pasos as $i => $s) {
            $estado = (int) $s->id_estado;
            $es_actual = ($i === $actual);
            $oficial = (int) $s->oficial > 0;
            $proveido = trim($s->proveido) != '' ? $pdf->t($s->proveido) : '';
            $justificaciones = (int) DB::query(Database::SELECT, "SELECT COUNT(1) AS n FROM observacion_seguimiento WHERE id_estado = '2' AND id_seguimiento = :id")
                    ->param(':id', (int) $s->id)->execute()->get('n');
            $docs = array();
            foreach (ORM::factory('documentos')->where('id_seguimiento', '=', $s->id)->find_all() as $d) {
                $docs[] = $d->codigo;
            }
            $archivado = $estado === 10 ? $oSeg->hrArchivada($s->nur, $s->derivado_a) : FALSE;
            $extras = array();
            if ($docs) {
                $extras[] = 'Documentos: ' . implode(', ', $docs);
            }
            if ($archivado) {
                $extras[] = 'Archivado en: ' . $archivado['carpeta'] . (trim($archivado['observaciones']) != '' ? ' (Obs.: ' . $archivado['observaciones'] . ')' : '');
            }
            if ($justificaciones) {
                $extras[] = 'Tiene ' . $justificaciones . ' justificación(es) por retraso';
            }

            // alto del bloque para no cortarlo entre paginas
            $pdf->SetFont('Arial', '', 7.5);
            $alto_prov = $proveido !== '' ? $pdf->alto_texto($w_caja - 10, 3.8, $proveido) + 3 : 0;
            $alto_extras = $extras ? $pdf->alto_texto($w_caja - 10, 3.6, $pdf->t(implode('   ·   ', $extras))) + 1 : 0;
            $alto = 7 + 21 + $alto_prov + $alto_extras + 3;
            if ($pdf->GetY() + $alto > $pdf->h - 16) {
                $pdf->AddPage();
                $pdf->titulo_seccion('Recorrido de la hoja de ruta (continuación)');
            }
            $y = $pdf->GetY();

            // numero del paso
            $pdf->color_relleno($es_actual ? Pdf_Seguimiento::$amarillo : ($oficial ? Pdf_Seguimiento::$azul : Pdf_Seguimiento::$gris_claro));
            $pdf->RoundedRect($m, $y + 1, 8, 8, 4, 'F');
            $pdf->SetFont('Arial', 'B', 8.5);
            $pdf->color_texto($es_actual ? Pdf_Seguimiento::$azul_oscuro : array(255, 255, 255));
            $pdf->SetXY($m, $y + 2.5);
            $pdf->Cell(8, 5, $i + 1, 0, 0, 'C');
            // linea que une los pasos
            if ($i < count($pasos) - 1) {
                $pdf->color_relleno(Pdf_Seguimiento::$borde);
                $pdf->Rect($m + 3.6, $y + 9.5, 0.8, $alto - 7, 'F');
            }

            // caja
            $pdf->SetLineWidth($es_actual ? 0.6 : 0.25);
            $pdf->color_linea($es_actual ? Pdf_Seguimiento::$amarillo : Pdf_Seguimiento::$borde);
            if ($es_actual || !$oficial) {
                $pdf->color_relleno($es_actual ? array(255, 253, 244) : array(250, 251, 252));
                $pdf->RoundedRect($x_caja, $y, $w_caja, $alto - 3, 2.5, 'DF');
            } else {
                $pdf->RoundedRect($x_caja, $y, $w_caja, $alto - 3, 2.5, 'D');
            }
            $pdf->SetLineWidth(0.25);

            // etiquetas
            $xe = $x_caja + 4;
            $ye = $y + 2.5;
            if ($es_actual) {
                $xe += $pdf->etiqueta($xe, $ye, 'Aquí está ahora', Pdf_Seguimiento::$amarillo, Pdf_Seguimiento::$azul_oscuro) + 1.5;
            }
            $colores = isset(Pdf_Seguimiento::$estados[$estado]) ? Pdf_Seguimiento::$estados[$estado] : Pdf_Seguimiento::$estados[10];
            $xe += $pdf->etiqueta($xe, $ye, $s->estado, $colores[0], $colores[1]) + 1.5;
            $xe += $pdf->etiqueta($xe, $ye, $oficial ? 'Oficial' : 'Copia', $oficial ? Pdf_Seguimiento::$azul : array(238, 242, 247), $oficial ? array(255, 255, 255) : array(74, 85, 104)) + 1.5;
            if (isset($tenencia[$i])) {
                $td = $tenencia[$i]['dias'];
                $col_t = $td > 7 ? array(array(253, 232, 232), array(180, 35, 24)) : ($td > 2 ? array(array(255, 243, 220), array(178, 107, 0)) : array(array(230, 244, 236), array(34, 117, 71)));
                $pdf->etiqueta($xe, $ye, ($tenencia[$i]['en_curso'] ? 'La tiene hace ' : 'La tuvo ') . SeguimientoUtil::duracion($tenencia[$i]['segundos']), $col_t[0], $col_t[1]);
            }
            if ($s->accion != '') {
                $pdf->SetFont('Arial', 'B', 7.5);
                $pdf->color_texto(array(138, 97, 0));
                $pdf->SetXY($x_caja + $w_caja - 84, $ye);
                $pdf->Cell(80, 4.4, $pdf->t($s->accion), 0, 0, 'R');
            }

            // de -> para
            $yp = $y + 9;
            $lados = array(
                array('DE', $s->de_oficina, $s->nombre_emisor, $s->cargo_emisor,
                    'Enviado el ' . date('d/m/Y', strtotime($s->fecha_emision)) . ' · ' . substr($s->hora_emision, 0, 5), FALSE),
                array('PARA', $s->a_oficina, $s->nombre_receptor, $s->cargo_receptor,
                    $s->fecha_recepcion
                        ? 'Recibido el ' . date('d/m/Y', strtotime($s->fecha_recepcion)) . ' · ' . substr($s->hora_recepcion, 0, 5)
                        : 'Sin recibir desde hace ' . SeguimientoUtil::duracion(max(0, time() - strtotime($s->fecha_emision . ' ' . $s->hora_emision))),
                    !$s->fecha_recepcion),
            );
            foreach ($lados as $k => $l) {
                $xl = $x_caja + 4 + $k * ($w_col + 10);
                $pdf->SetXY($xl, $yp);
                $pdf->SetFont('Arial', 'B', 6);
                $pdf->color_texto(Pdf_Seguimiento::$gris_claro);
                $pdf->Cell($w_col, 3, $l[0], 0, 2);
                $pdf->SetFont('Arial', 'B', 6.5);
                $pdf->color_texto(Pdf_Seguimiento::$azul);
                $pdf->Cell($w_col, 3.3, $pdf->t(mb_strtoupper($l[1], 'UTF-8')), 0, 2);
                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->color_texto(Pdf_Seguimiento::$texto);
                $pdf->Cell($w_col, 4.3, $pdf->t($l[2]), 0, 2);
                $pdf->SetFont('Arial', '', 7);
                $pdf->color_texto(Pdf_Seguimiento::$gris);
                $pdf->Cell($w_col, 3.5, $pdf->t($l[3]), 0, 2);
                $pdf->SetFont('Arial', $l[5] ? 'B' : '', 7);
                $pdf->color_texto($l[5] ? array(178, 107, 0) : array(74, 85, 104));
                $pdf->Cell($w_col, 3.8, $pdf->t($l[4]), 0, 0);
            }
            // flecha
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->color_texto(Pdf_Seguimiento::$gris_claro);
            $pdf->SetXY($x_caja + 4 + $w_col, $yp + 6);
            $pdf->Cell(10, 5, '>', 0, 0, 'C');

            // proveido
            $yv = $yp + 19.5;
            if ($proveido !== '') {
                $pdf->SetFont('Arial', '', 7.5);
                $pdf->color_relleno(Pdf_Seguimiento::$fondo);
                $pdf->RoundedRect($x_caja + 4, $yv, $w_caja - 8, $alto_prov - 1, 1.5, 'F');
                $pdf->color_texto(Pdf_Seguimiento::$texto);
                $pdf->SetXY($x_caja + 5, $yv + 1);
                $pdf->MultiCell($w_caja - 10, 3.8, $proveido, 0, 'L');
                $yv += $alto_prov;
            }
            if ($extras) {
                $pdf->SetFont('Arial', '', 7);
                $pdf->color_texto($justificaciones ? array(180, 35, 24) : array(74, 85, 104));
                $pdf->SetXY($x_caja + 5, $yv);
                $pdf->MultiCell($w_caja - 10, 3.6, $pdf->t(implode('   ·   ', $extras)), 0, 'L');
            }
            $pdf->SetY($y + $alto);
        }

        $pdf->Output('seguimiento_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $hr) . '.pdf', 'I');
        exit;
    }

    public function action_word()
    {

    }

}

