<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Calculos sobre los pasos de una hoja de ruta (resultado de Model_Seguimiento::seguimiento()),
 * compartidos por la pantalla de seguimiento (hojaruta/seguimiento) y su PDF (print/seguimiento).
 */
class SeguimientoUtil {

    /** Indice del paso donde esta ahora: el ultimo oficial abierto (no recibido o pendiente); NULL si no hay. */
    public static function paso_actual(array $pasos) {
        $actual = NULL;
        foreach ($pasos as $i => $s) {
            if ((int) $s->oficial > 0 && in_array((int) $s->id_estado, array(1, 2), TRUE)) {
                $actual = $i;
            }
        }
        return $actual;
    }

    /** Indice del ultimo paso oficial; NULL si no hay. */
    public static function ultimo_oficial(array $pasos) {
        $ultimo = NULL;
        foreach ($pasos as $i => $s) {
            if ((int) $s->oficial > 0) {
                $ultimo = $i;
            }
        }
        return $ultimo;
    }

    /**
     * Cuanto tiempo tuvo cada receptor la hoja de ruta: desde que la recibio hasta que la derivo
     * (el siguiente paso que envio esa persona). Si sigue pendiente, cuenta hasta ahora.
     * Devuelve array(indice => array('segundos' => n, 'dias' => n, 'en_curso' => bool)).
     */
    public static function tenencia(array $pasos) {
        $tenencia = array();
        $n = count($pasos);
        foreach ($pasos as $i => $s) {
            if (!$s->fecha_recepcion) {
                continue;
            }
            $recibido = strtotime($s->fecha_recepcion . ' ' . $s->hora_recepcion);
            $hasta = NULL;
            for ($j = $i + 1; $j < $n; $j++) {
                $envio = strtotime($pasos[$j]->fecha_emision . ' ' . $pasos[$j]->hora_emision);
                if ((int) $pasos[$j]->derivado_por === (int) $s->derivado_a && $envio >= $recibido) {
                    $hasta = $envio;
                    break;
                }
            }
            if ($hasta !== NULL || (int) $s->id_estado === 2) {
                $segundos = max(0, ($hasta !== NULL ? $hasta : time()) - $recibido);
                $tenencia[$i] = array('segundos' => $segundos, 'dias' => (int) floor($segundos / 86400), 'en_curso' => $hasta === NULL);
            }
        }
        return $tenencia;
    }

    /** Duracion legible: "34 min", "16 h", "1 día", "7 días". */
    public static function duracion($segundos) {
        if ($segundos < 3600) {
            return max(1, (int) round($segundos / 60)) . ' min';
        }
        if ($segundos < 86400) {
            return (int) round($segundos / 3600) . ' h';
        }
        $d = (int) floor($segundos / 86400);
        return $d == 1 ? '1 día' : $d . ' días';
    }

    /** Dias completos entre dos fechas (la segunda por defecto: ahora). */
    public static function dias_entre($desde, $hasta = NULL) {
        $a = strtotime($desde);
        $b = $hasta ? strtotime($hasta) : time();
        return $a ? max(0, (int) floor(($b - $a) / 86400)) : NULL;
    }

    /** Oficinas por las que paso la hoja de ruta (pasos oficiales, sin repetir consecutivas). */
    public static function ruta(array $pasos) {
        $ruta = array();
        foreach ($pasos as $i => $s) {
            if ((int) $s->oficial <= 0) {
                continue;
            }
            if (!$ruta) {
                $ruta[] = array('oficina' => $s->de_oficina, 'persona' => $s->nombre_emisor, 'paso' => NULL);
            }
            $ultima = end($ruta);
            if ($ultima['oficina'] !== $s->a_oficina || $ultima['persona'] !== $s->nombre_receptor) {
                $ruta[] = array('oficina' => $s->a_oficina, 'persona' => $s->nombre_receptor, 'paso' => $i);
            }
        }
        return $ruta;
    }

}
