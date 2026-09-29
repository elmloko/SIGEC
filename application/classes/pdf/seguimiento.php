<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PDF del seguimiento de una hoja de ruta (print/seguimiento), con el estilo de Correos de Bolivia.
 * Requiere que el controlador haya cargado fpdf17/fpdf y fpdf17/code39 antes de instanciarla.
 * Hoja carta horizontal: 279.4 x 215.9 mm.
 */
class Pdf_Seguimiento extends PDF_Code39 {

    public $hr = '';
    public $impreso_por = '';
    public $logo = '';

    // paleta
    public static $azul = array(26, 84, 154);
    public static $azul_oscuro = array(18, 62, 115);
    public static $azul_suave = array(234, 241, 249);
    public static $amarillo = array(254, 203, 52);
    public static $amarillo_suave = array(255, 247, 221);
    public static $gris = array(122, 133, 148);
    public static $gris_claro = array(154, 164, 178);
    public static $borde = array(220, 227, 236);
    public static $fondo = array(243, 245, 248);
    public static $texto = array(45, 55, 72);

    // estado -> array(fondo, texto)
    public static $estados = array(
        1 => array(array(255, 243, 220), array(178, 107, 0)),
        2 => array(array(234, 241, 249), array(26, 84, 154)),
        4 => array(array(230, 244, 236), array(34, 117, 71)),
        6 => array(array(241, 236, 250), array(91, 60, 153)),
        10 => array(array(238, 242, 247), array(74, 85, 104)),
        11 => array(array(253, 232, 232), array(180, 35, 24)),
    );

    /** Texto UTF-8 a la codificacion de las fuentes de FPDF (cp1252). */
    public function t($texto) {
        $r = @iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $texto);
        return $r === FALSE ? utf8_decode((string) $texto) : $r;
    }

    public function color_texto(array $c) {
        $this->SetTextColor($c[0], $c[1], $c[2]);
    }

    public function color_relleno(array $c) {
        $this->SetFillColor($c[0], $c[1], $c[2]);
    }

    public function color_linea(array $c) {
        $this->SetDrawColor($c[0], $c[1], $c[2]);
    }

    function Header() {
        $m = $this->lMargin;
        $ancho = $this->w - $this->lMargin - $this->rMargin;
        if ($this->logo && file_exists($this->logo)) {
            $this->Image($this->logo, $m, 8, 0, 13);
        }
        $this->SetXY($m + 40, 8.5);
        $this->SetFont('Arial', 'B', 13);
        $this->color_texto(self::$azul_oscuro);
        $this->Cell($ancho - 80, 6, $this->t('SEGUIMIENTO DE HOJA DE RUTA'), 0, 2, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->color_texto(self::$azul);
        $this->Cell($ancho - 80, 6, $this->t($this->hr), 0, 0, 'C');
        $this->SetXY($m + $ancho - 50, 9);
        $this->SetFont('Arial', '', 7.5);
        $this->color_texto(self::$gris);
        $this->Cell(50, 4, $this->t('Sistema de Gestión de Correspondencia'), 0, 2, 'R');
        $this->Cell(50, 4, $this->t('Correos de Bolivia'), 0, 2, 'R');
        // franja amarilla
        $this->color_relleno(self::$amarillo);
        $this->Rect($m, 23.5, $ancho, 1.2, 'F');
        $this->SetY(28);
    }

    function Footer() {
        $m = $this->lMargin;
        $ancho = $this->w - $this->lMargin - $this->rMargin;
        $this->SetY(-11);
        $this->color_linea(self::$borde);
        $this->SetLineWidth(0.2);
        $this->Line($m, $this->GetY(), $m + $ancho, $this->GetY());
        $this->SetY(-9.5);
        $this->SetFont('Arial', '', 7);
        $this->color_texto(self::$gris);
        $impreso = 'Impreso el ' . date('d/m/Y H:i') . ($this->impreso_por ? ' por ' . $this->impreso_por : '');
        $this->Cell($ancho / 2, 4, $this->t('SIGEC · ' . $impreso), 0, 0, 'L');
        $this->Cell($ancho / 2, 4, $this->t('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }

    /** Rectangulo con esquinas redondeadas. $estilo: 'D' borde, 'F' relleno, 'DF' ambos. */
    public function RoundedRect($x, $y, $w, $h, $r, $estilo = '') {
        $k = $this->k;
        $hp = $this->h;
        $op = $estilo == 'F' ? 'f' : (($estilo == 'FD' || $estilo == 'DF') ? 'B' : 'S');
        $arc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_arco($xc + $r * $arc, $yc - $r, $xc + $r, $yc - $r * $arc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_arco($xc + $r, $yc + $r * $arc, $xc + $r * $arc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_arco($xc - $r * $arc, $yc + $r, $xc - $r, $yc + $r * $arc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_arco($xc - $r, $yc - $r * $arc, $xc - $r * $arc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    protected function _arco($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

    /** Etiqueta redondeada en ($x,$y); devuelve el ancho usado. */
    public function etiqueta($x, $y, $texto, array $fondo, array $color, $tam = 6.5) {
        $this->SetFont('Arial', 'B', $tam);
        $texto = $this->t(mb_strtoupper($texto, 'UTF-8'));
        $w = $this->GetStringWidth($texto) + 4;
        $this->color_relleno($fondo);
        $this->RoundedRect($x, $y, $w, 4.4, 1.5, 'F');
        $this->color_texto($color);
        $this->SetXY($x, $y + 0.2);
        $this->Cell($w, 4, $texto, 0, 0, 'C');
        return $w;
    }

    /** Titulo de seccion con barra lateral amarilla. */
    public function titulo_seccion($texto) {
        $m = $this->lMargin;
        $this->color_relleno(self::$amarillo);
        $this->Rect($m, $this->GetY() + 0.5, 1.4, 5, 'F');
        $this->SetX($m + 3);
        $this->SetFont('Arial', 'B', 10);
        $this->color_texto(self::$azul_oscuro);
        $this->Cell(0, 6, $this->t($texto), 0, 1, 'L');
        $this->Ln(1);
    }

    /** Alto que ocupa un texto con MultiCell de ancho $w e interlineado $h (con la fuente actual). */
    public function alto_texto($w, $h, $texto) {
        return $this->NbLines($w, $texto) * $h;
    }

}
