<?php

require ('fpdf.php');

class PDF extends FPDF {
    var $footer=true;
    var $widths;
    var $aligns;
    var $nombre;
    function Header() {
        //Logo
        $this->Image('assets/img/escudo.png', 10, 8, 22);
        //Arial bold 15
        $this->SetFont('Arial', '', 10);
        //Move to the right
        $this->Cell(55);
        //Title
        $this->Cell(90, 5, 'ESTADO PLURINACIONAL DE BOLIVIA', 'B', 0, 'C');
        $this->Image('assets/img/logo.png', 180, 8, 18);
        $this->Ln(6);
        $this->Cell(55);
        $this->SetFont('Arial', '', 12);
        $this->Cell(90, 5, 'EMPRESA ESTATAL DE TRANSPORTE POR CABLE MI TELEFERICO', 0, 0, 'C');
        //Line break
        $this->Ln(15);
    }

    //Page footer
    function Footer() {
        //Position at 1.5 cm from bottom
        //$this->SetY(-45);
        //Arial italic 8        
        $y = $this->GetY();
        if ($this->footer) {
            $this->SetY(-22);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(15, 5, '', 0, FALSE, 'L');
            $this->Cell(75, 5, utf8_decode('ENTREGADO POR'), 'T', FALSE, 'C');
            $this->Cell(15, 5, '', 0, FALSE, 'L');
            $this->Cell(75, 5, utf8_decode('RECIBIDO POR'), 'T', FALSE, 'C');
            $this->Ln(8);
        } else {
            $this->SetY(-15);
        }

        $this->SetFont('Arial', 'I', 7);
        //Page number        
        // $this->Image('assets/img/quinua.png', 140, 258, 60);
        $this->Cell(150, 5, 'Fecha: ' . date('d/m/Y'), 0, 0, 'L');
        $this->Cell(45, 5, 'Pag.' . $this->PageNo(), 0, 0, 'R');
        $this->Ln();
        $this->Cell(195, 5, utf8_decode('Av. Mariscal Santa Cruz, Edif. Centro de Comunicaciones La Paz 5to Piso - Telefonos: (591)-(2)-2119999-2156600 - www.oopp.gob.bo'), 'T', 0, 'C');
    }

    function SetWidths($w) {
        //Set the array of column widths 
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments 
        $this->aligns = $a;
    }

    function fill($f) {
        //juego de arreglos de relleno
        $this->fill = $f;
    }

    function Row($data) {
        //Calculate the height of the row 
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed 
        $this->CheckPageBreak($h);
        //Draw the cells of the row 
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position 
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border 
            $this->Rect($x, $y, $w, $h);
            //Print the text 
            $this->MultiCell($w, 5, $data[$i], 'LTR', $a);
            //Put the position to the right of the cell 
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line 
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately 
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take 
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l+=$cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

}
