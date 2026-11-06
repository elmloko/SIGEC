<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PDF de documentos generados por un usuario, por tipo y rango de fechas (print/documentos).
 * Requiere que el controlador haya cargado fpdf17/fpdf y fpdf17/code39 antes de instanciarla.
 * Hoja carta horizontal: 279.4 x 215.9 mm.
 */
class Pdf_Documentos extends Pdf_Seguimiento {

    public $titulo = 'REPORTE DE DOCUMENTOS GENERADOS';

    // columnas: titulo, ancho
    protected $columnas = array(
        array('N°', 8),
        array('Fecha', 20),
        array('Cite', 44),
        array('Hoja de ruta', 28),
        array('Destinatario', 62),
        array('Referencia', 93.4),
    );

    protected function ancho() {
        return $this->w - $this->lMargin - $this->rMargin;
    }

    /**
     * Dibuja el reporte completo.
     * @param object $user   usuario (nombre, cargo)
     * @param string $tipo   nombre del tipo filtrado ('Todos los tipos' si no hay filtro)
     * @param string $desde  Y-m-d
     * @param string $hasta  Y-m-d
     * @param array  $filas  filas de Model_Documentos::reporte_tipo() ordenadas por tipo y fecha
     */
    public function generar($user, $tipo, $desde, $hasta, array $filas) {
        $this->hr = $tipo . ' · del ' . self::fecha($desde) . ' al ' . self::fecha($hasta);
        $this->impreso_por = $user->nombre;
        $this->SetTitle($this->t('Documentos generados - ' . $user->nombre));
        $this->SetMargins(12, 10, 12);
        $this->SetAutoPageBreak(TRUE, 14);
        $this->AliasNbPages();
        $this->AddPage();

        // agrupar por tipo
        $grupos = array();
        foreach ($filas as $f) {
            $grupos[$f['plural']][] = $f;
        }

        $this->datos_usuario($user, $tipo, $desde, $hasta);
        $this->resumen($grupos, count($filas));

        if (count($filas) == 0) {
            $this->sin_documentos();
            return;
        }
        foreach ($grupos as $plural => $docs) {
            if ($this->GetY() + 30 > $this->PageBreakTrigger) {
                $this->AddPage();
            }
            $this->Ln(2);
            $this->titulo_seccion($plural . ' (' . count($docs) . ')');
            $this->cabecera_tabla();
            $n = 1;
            foreach ($docs as $f) {
                $this->fila($n, $f);
                $n++;
            }
        }
    }

    public static function fecha($ymd) {
        $t = strtotime($ymd);
        return $t ? date('d/m/Y', $t) : $ymd;
    }

    protected function datos_usuario($user, $tipo, $desde, $hasta) {
        $m = $this->lMargin;
        $ancho = $this->ancho();
        $y = $this->GetY();
        $this->color_linea(self::$borde);
        $this->SetLineWidth(0.25);
        $this->RoundedRect($m, $y, $ancho, 14, 2.5, 'D');
        $datos = array(
            array('USUARIO', $user->nombre, 75),
            array('CARGO', $user->cargo, 75),
            array('TIPO DE DOCUMENTO', $tipo, 45),
            array('PERIODO', self::fecha($desde) . ' - ' . self::fecha($hasta), $ancho - 195 - 8),
        );
        $x = $m + 4;
        foreach ($datos as $d) {
            $this->SetXY($x, $y + 2.5);
            $this->SetFont('Arial', '', 6.5);
            $this->color_texto(self::$gris);
            $this->Cell($d[2], 3.5, $this->t($d[0]), 0, 2, 'L');
            $this->SetFont('Arial', 'B', 9);
            $this->color_texto(self::$texto);
            $texto = $this->t($d[1]);
            while ($this->GetStringWidth($texto) > $d[2] - 2 && strlen($texto) > 4) {
                $texto = substr($texto, 0, -4) . '...';
            }
            $this->Cell($d[2], 5, $texto, 0, 0, 'L');
            $x += $d[2];
        }
        $this->SetY($y + 18);
    }

    /** Tarjetas: total y cantidad por tipo (hasta 5 tipos; el resto se suma en "Otros"). */
    protected function resumen(array $grupos, $total) {
        $kpis = array(array('Total de documentos', $total, self::$azul_oscuro));
        $cantidades = array();
        foreach ($grupos as $plural => $docs) {
            $cantidades[$plural] = count($docs);
        }
        arsort($cantidades);
        $i = 0;
        $otros = 0;
        foreach ($cantidades as $plural => $c) {
            if ($i < 5 || count($cantidades) == 6) {
                $kpis[] = array($plural, $c, self::$azul);
            } else {
                $otros += $c;
            }
            $i++;
        }
        if ($otros > 0) {
            $kpis[] = array('Otros tipos', $otros, self::$gris);
        }
        $m = $this->lMargin;
        $sep = 4;
        $n = max(count($kpis), 4);
        $w = ($this->ancho() - $sep * ($n - 1)) / $n;
        $y = $this->GetY();
        foreach ($kpis as $i => $k) {
            $x = $m + $i * ($w + $sep);
            $this->color_relleno(self::$fondo);
            $this->RoundedRect($x, $y, $w, 15, 2.5, 'F');
            $this->color_relleno($k[2]);
            $this->Rect($x, $y + 3, 1.2, 9, 'F');
            $this->SetXY($x + 4, $y + 2);
            $this->SetFont('Arial', 'B', 14);
            $this->color_texto($k[2]);
            $this->Cell($w - 6, 7, number_format($k[1], 0, ',', '.'), 0, 2, 'L');
            $this->SetFont('Arial', '', 7);
            $this->color_texto(self::$gris);
            $texto = $this->t($k[0]);
            while ($this->GetStringWidth($texto) > $w - 6 && strlen($texto) > 4) {
                $texto = substr($texto, 0, -4) . '...';
            }
            $this->Cell($w - 6, 4, $texto, 0, 0, 'L');
        }
        $this->SetY($y + 19);
    }

    protected function cabecera_tabla() {
        $this->color_relleno(self::$azul);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 7.5);
        $this->SetX($this->lMargin);
        foreach ($this->columnas as $c) {
            $this->Cell($c[1], 7, $this->t($c[0]), 0, 0, $c[0] == 'N°' ? 'C' : 'L', TRUE);
        }
        $this->Ln();
        $this->color_relleno(self::$amarillo);
        $this->Rect($this->lMargin, $this->GetY(), $this->ancho(), 0.8, 'F');
        $this->SetY($this->GetY() + 0.8);
    }

    protected function fila($n, array $f) {
        $col = $this->columnas;
        $pad = 1.5;
        $lh = 3.6;

        $cite = $this->t($f['cite_original']);
        $nur = $this->t($f['nur'] != '' ? $f['nur'] : '-');
        $dest = $this->t($f['nombre_destinatario']);
        $dest_cargo = $this->t($f['cargo_destinatario']);
        $ref = trim((string) $f['referencia']);
        if (mb_strlen($ref, 'UTF-8') > 400) {
            $ref = mb_substr($ref, 0, 397, 'UTF-8') . '...';
        }
        $ref = $this->t($ref != '' ? $ref : 'Sin referencia');

        // alto de la fila = el de la celda mas alta
        $this->SetFont('Arial', 'B', 7.5);
        $h_cite = $this->alto_texto($col[2][1] - 2 * $pad, $lh, $cite);
        $h_nur = $this->alto_texto($col[3][1] - 2 * $pad, $lh, $nur);
        $h_dest = $this->alto_texto($col[4][1] - 2 * $pad, $lh, $dest);
        $this->SetFont('Arial', '', 6.8);
        $h_dest += $dest_cargo !== '' ? $this->alto_texto($col[4][1] - 2 * $pad, $lh * 0.9, $dest_cargo) : 0;
        $this->SetFont('Arial', '', 7.5);
        $h_ref = $this->alto_texto($col[5][1] - 2 * $pad, $lh, $ref);
        $h = max($h_cite, $h_nur, $h_dest, $h_ref, 2 * $lh) + 2 * $pad;

        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage();
            $this->cabecera_tabla();
        }
        $y = $this->GetY();
        $x = $this->lMargin;

        if ($n % 2 == 0) {
            $this->color_relleno(array(250, 251, 253));
            $this->Rect($x, $y, $this->ancho(), $h, 'F');
        }
        $this->color_linea(self::$borde);
        $this->SetLineWidth(0.2);
        $this->Line($x, $y + $h, $x + $this->ancho(), $y + $h);

        // N°
        $this->SetXY($x, $y + $pad);
        $this->SetFont('Arial', 'B', 8);
        $this->color_texto(self::$gris_claro);
        $this->Cell($col[0][1], $lh, $n, 0, 0, 'C');
        $x += $col[0][1];

        // fecha y hora
        $fc = (string) $f['fecha_creacion'];
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$texto);
        $this->Cell($col[1][1] - 2 * $pad, $lh, self::fecha(substr($fc, 0, 10)), 0, 2, 'L');
        $this->SetFont('Arial', '', 7);
        $this->color_texto(self::$gris);
        $this->Cell($col[1][1] - 2 * $pad, $lh, substr($fc, 11, 5), 0, 0, 'L');
        $x += $col[1][1];

        // cite
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$azul_oscuro);
        $this->MultiCell($col[2][1] - 2 * $pad, $lh, $cite, 0, 'L');
        $x += $col[2][1];

        // hoja de ruta
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$azul);
        $this->MultiCell($col[3][1] - 2 * $pad, $lh, $nur, 0, 'L');
        $x += $col[3][1];

        // destinatario
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$texto);
        $this->MultiCell($col[4][1] - 2 * $pad, $lh, $dest, 0, 'L');
        if ($dest_cargo !== '') {
            $this->SetX($x + $pad);
            $this->SetFont('Arial', '', 6.8);
            $this->color_texto(self::$gris);
            $this->MultiCell($col[4][1] - 2 * $pad, $lh * 0.9, $dest_cargo, 0, 'L');
        }
        $x += $col[4][1];

        // referencia
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', '', 7.5);
        $this->color_texto(self::$texto);
        $this->MultiCell($col[5][1] - 2 * $pad, $lh, $ref, 0, 'L');

        $this->SetY($y + $h);
    }

    protected function sin_documentos() {
        $m = $this->lMargin;
        $y = $this->GetY() + 2;
        $this->color_relleno(self::$fondo);
        $this->RoundedRect($m, $y, $this->ancho(), 18, 3, 'F');
        $this->SetXY($m, $y + 4);
        $this->SetFont('Arial', 'B', 11);
        $this->color_texto(self::$azul_oscuro);
        $this->Cell($this->ancho(), 6, $this->t('No se encontraron documentos'), 0, 2, 'C');
        $this->SetFont('Arial', '', 8);
        $this->color_texto(self::$gris);
        $this->Cell($this->ancho(), 4, $this->t('No generó documentos de este tipo en el periodo seleccionado.'), 0, 0, 'C');
    }

}
