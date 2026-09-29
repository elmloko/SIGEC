<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PDF de correspondencia pendiente de un usuario (print/pendientes), con el estilo de Correos de Bolivia.
 * Requiere que el controlador haya cargado fpdf17/fpdf y fpdf17/code39 antes de instanciarla.
 * Hoja carta horizontal: 279.4 x 215.9 mm.
 */
class Pdf_Pendientes extends Pdf_Seguimiento {

    public $titulo = 'CORRESPONDENCIA PENDIENTE';

    // columnas: titulo, ancho
    protected $columnas = array(
        array('N°', 8),
        array('Hoja de ruta', 30),
        array('Documento y referencia', 80),
        array('Remitente', 46),
        array('Acción / proveído', 48),
        array('Recibido', 24),
        array('En bandeja', 19.4),
    );

    /** Dias en bandeja -> array(fondo, texto) */
    public static function color_dias($dias) {
        if ($dias > 7) {
            return array(array(253, 232, 232), array(180, 35, 24));
        }
        if ($dias >= 3) {
            return array(array(255, 243, 220), array(178, 107, 0));
        }
        return array(array(230, 244, 236), array(34, 117, 71));
    }

    /**
     * Dibuja el reporte completo.
     * @param object $user  usuario (nombre, cargo, email)
     * @param array  $filas filas de Model_Hojasruta::pendientes()
     */
    public function generar($user, array $filas) {
        $this->hr = $user->nombre;
        $this->impreso_por = $user->nombre;
        $this->SetTitle($this->t('Correspondencia pendiente - ' . $user->nombre));
        $this->SetMargins(12, 10, 12);
        $this->SetAutoPageBreak(TRUE, 14);
        $this->AliasNbPages();
        $this->AddPage();

        // la mas antigua primero: es la que mas urge atender
        usort($filas, function ($a, $b) {
            return (int) $b['dias_ahora'] - (int) $a['dias_ahora'];
        });

        $this->datos_usuario($user);
        $this->indicadores($filas);
        $this->titulo_seccion('Detalle (de la más antigua a la más reciente)');

        if (count($filas) == 0) {
            $this->sin_pendientes();
            return;
        }
        $this->cabecera_tabla();
        $n = 1;
        foreach ($filas as $f) {
            $this->fila($n, $f);
            $n++;
        }
        $this->leyenda();
    }

    protected function ancho() {
        return $this->w - $this->lMargin - $this->rMargin;
    }

    protected function datos_usuario($user) {
        $m = $this->lMargin;
        $ancho = $this->ancho();
        $y = $this->GetY();
        $this->color_linea(self::$borde);
        $this->SetLineWidth(0.25);
        $this->RoundedRect($m, $y, $ancho, 14, 2.5, 'D');
        $datos = array(
            array('USUARIO', $user->nombre, 80),
            array('CARGO', $user->cargo, 85),
            array('CORREO', $user->email, 55),
            array('FECHA DE CORTE', date('d/m/Y H:i'), $ancho - 220 - 8),
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

    protected function indicadores(array $filas) {
        $total = count($filas);
        $oficiales = 0;
        $urgentes = 0;
        $mas7 = 0;
        $suma = 0;
        foreach ($filas as $f) {
            $oficiales += $f['oficial'] == 'Oficial' ? 1 : 0;
            $urgentes += (int) $f['prioridad'] === 1 ? 1 : 0;
            $mas7 += (int) $f['dias_ahora'] > 7 ? 1 : 0;
            $suma += (int) $f['dias_ahora'];
        }
        $kpis = array(
            array('Pendientes', $total, self::$azul),
            array('Oficiales', $oficiales, self::$azul),
            array('Copias', $total - $oficiales, self::$gris),
            array('Urgentes', $urgentes, $urgentes > 0 ? array(180, 35, 24) : self::$gris),
            array('Más de 7 días', $mas7, $mas7 > 0 ? array(180, 35, 24) : self::$gris),
            array('Promedio en bandeja', $total ? round($suma / $total) . ' d' : '-', self::$azul_oscuro),
        );
        $m = $this->lMargin;
        $sep = 4;
        $w = ($this->ancho() - $sep * (count($kpis) - 1)) / count($kpis);
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
            $this->Cell($w - 6, 7, $this->t($k[1]), 0, 2, 'L');
            $this->SetFont('Arial', '', 7);
            $this->color_texto(self::$gris);
            $this->Cell($w - 6, 4, $this->t($k[0]), 0, 0, 'L');
        }
        $this->SetY($y + 20);
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
        $oficial = $f['oficial'] == 'Oficial';
        $urgente = (int) $f['prioridad'] === 1;
        $dias = (int) $f['dias_ahora'];

        // textos (ya en cp1252)
        $cite = $this->t($f['cite_original']);
        $ref = $this->t(trim($f['referencia']) != '' ? trim($f['referencia']) : 'Sin referencia');
        $rem = $this->t($f['nombre_emisor']);
        $rem_cargo = $this->t($f['cargo_emisor']);
        $accion = $this->t($f['accion']);
        $proveido = trim((string) $f['proveido']);
        if (mb_strlen($proveido, 'UTF-8') > 160) {
            $proveido = mb_substr($proveido, 0, 157, 'UTF-8') . '...';
        }
        $proveido = $this->t($proveido);

        // alto de la fila = el de la celda mas alta
        $this->SetFont('Arial', 'B', 7.5);
        $h_doc = $this->alto_texto($col[2][1] - 2 * $pad, $lh, $cite);
        $h_rem = $this->alto_texto($col[3][1] - 2 * $pad, $lh, $rem);
        $h_acc = $this->alto_texto($col[4][1] - 2 * $pad, $lh, $accion);
        $this->SetFont('Arial', '', 7.5);
        $h_doc += $this->alto_texto($col[2][1] - 2 * $pad, $lh, $ref);
        $this->SetFont('Arial', '', 6.8);
        $h_rem += $this->alto_texto($col[3][1] - 2 * $pad, $lh * 0.9, $rem_cargo);
        $h_acc += $proveido !== '' ? $this->alto_texto($col[4][1] - 2 * $pad, $lh * 0.9, $proveido) : 0;
        $h = max($h_doc, $h_rem, $h_acc, $urgente ? 13 : 11.5) + 2 * $pad;

        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage();
            $this->cabecera_tabla();
        }
        $y = $this->GetY();
        $x = $this->lMargin;

        // fondo alterno y linea inferior
        if ($n % 2 == 0) {
            $this->color_relleno(array(250, 251, 253));
            $this->Rect($x, $y, $this->ancho(), $h, 'F');
        }
        if ($dias > 7) {
            // marca lateral roja para las atrasadas
            $this->SetFillColor(211, 47, 47);
            $this->Rect($x, $y, 0.9, $h, 'F');
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

        // hoja de ruta + tipo
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 8);
        $this->color_texto(self::$azul);
        $this->MultiCell($col[1][1] - 2 * $pad, $lh, $this->t($f['nur']), 0, 'L');
        $ty = $this->GetY() + 0.8;
        $this->etiqueta($x + $pad, $ty, $oficial ? 'Oficial' : 'Copia',
            $oficial ? self::$azul_suave : self::$fondo, $oficial ? self::$azul : self::$gris, 5.8);
        if ($urgente) {
            $this->etiqueta($x + $pad, $ty + 5.2, 'Urgente', array(253, 232, 232), array(180, 35, 24), 5.8);
        }
        $x += $col[1][1];

        // documento: cite + referencia
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$azul_oscuro);
        $this->MultiCell($col[2][1] - 2 * $pad, $lh, $cite, 0, 'L');
        $this->SetX($x + $pad);
        $this->SetFont('Arial', '', 7.5);
        $this->color_texto(self::$texto);
        $this->MultiCell($col[2][1] - 2 * $pad, $lh, $ref, 0, 'L');
        $x += $col[2][1];

        // remitente
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$texto);
        $this->MultiCell($col[3][1] - 2 * $pad, $lh, $rem, 0, 'L');
        $this->SetX($x + $pad);
        $this->SetFont('Arial', '', 6.8);
        $this->color_texto(self::$gris);
        $this->MultiCell($col[3][1] - 2 * $pad, $lh * 0.9, $rem_cargo, 0, 'L');
        $x += $col[3][1];

        // accion + proveido
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$azul_oscuro);
        $this->MultiCell($col[4][1] - 2 * $pad, $lh, $accion, 0, 'L');
        if ($proveido !== '') {
            $this->SetX($x + $pad);
            $this->SetFont('Arial', 'I', 6.8);
            $this->color_texto(self::$gris);
            $this->MultiCell($col[4][1] - 2 * $pad, $lh * 0.9, $proveido, 0, 'L');
        }
        $x += $col[4][1];

        // recibido: fecha, hora y cuanto tardo en recibirse
        $fr = $f['fecha_recepcion'];
        $this->SetXY($x + $pad, $y + $pad);
        $this->SetFont('Arial', 'B', 7.5);
        $this->color_texto(self::$texto);
        $this->Cell($col[5][1] - 2 * $pad, $lh, substr($fr, 0, 10), 0, 2, 'L');
        $this->SetFont('Arial', '', 7);
        $this->color_texto(self::$gris);
        $this->Cell($col[5][1] - 2 * $pad, $lh, substr($fr, 11, 5), 0, 2, 'L');
        $dr = (int) $f['dias_recepcion'];
        $this->SetFont('Arial', '', 6.3);
        $this->Cell($col[5][1] - 2 * $pad, $lh, $this->t($dr == 0 ? 'recibido el mismo día' : 'recibido en ' . $dr . ($dr == 1 ? ' día' : ' días')), 0, 0, 'L');
        $x += $col[5][1];

        // dias en bandeja
        $c = self::color_dias($dias);
        $cw = $col[6][1] - 2 * $pad;
        $this->color_relleno($c[0]);
        $this->RoundedRect($x + $pad, $y + $pad, $cw, 10, 2, "F");
        $this->color_texto($c[1]);
        $this->SetXY($x + $pad, $y + $pad + 0.6);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($cw, 5.5, $dias, 0, 2, 'C');
        $this->SetFont('Arial', '', 6.3);
        $this->Cell($cw, 3.5, $this->t($dias == 1 ? 'día' : 'días'), 0, 0, 'C');

        $this->SetY($y + $h);
    }

    protected function sin_pendientes() {
        $m = $this->lMargin;
        $y = $this->GetY() + 2;
        $this->color_relleno(array(230, 244, 236));
        $this->RoundedRect($m, $y, $this->ancho(), 18, 3, 'F');
        $this->SetXY($m, $y + 4);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(34, 117, 71);
        $this->Cell($this->ancho(), 6, $this->t('No tiene correspondencia pendiente'), 0, 2, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell($this->ancho(), 4, $this->t('Todas las hojas de ruta recibidas ya fueron derivadas o archivadas.'), 0, 0, 'C');
    }

    protected function leyenda() {
        if ($this->GetY() + 8 > $this->PageBreakTrigger) {
            $this->AddPage();
        }
        $this->Ln(3);
        $x = $this->lMargin;
        $y = $this->GetY();
        $this->SetFont('Arial', '', 6.8);
        $this->color_texto(self::$gris);
        $this->SetXY($x, $y);
        $this->Cell(30, 4.4, $this->t('Días en bandeja:'), 0, 0, 'L');
        $x += 24;
        foreach (array(array(0, '0 a 2 días'), array(3, '3 a 7 días'), array(8, 'más de 7 días (atrasada)')) as $l) {
            $c = self::color_dias($l[0]);
            $x += $this->etiqueta($x, $y, $l[1], $c[0], $c[1], 5.8) + 2;
        }
        $this->SetXY($x + 4, $y);
        $this->SetFont('Arial', '', 6.8);
        $this->color_texto(self::$gris);
        $this->Cell(0, 4.4, $this->t('Oficial: debe atenderla y derivarla · Copia: solo para conocimiento'), 0, 0, 'L');
    }

}
