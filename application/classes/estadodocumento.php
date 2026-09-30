<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Estado de envio de un documento (original o respuesta) segun el seguimiento de su hoja de ruta.
 *
 * El campo documentos.estado solo se actualiza en el documento original; las respuestas quedan siempre en 0.
 * Por eso se calcula asi: el documento viaja con la primera derivacion que hace su autor en esa hoja de ruta
 * despues de crearlo. Ese "paso" puede tener varios destinatarios (oficial y copias):
 *  - derivado: existe esa derivacion
 *  - recibido: alguno de los destinatarios de ese paso ya la recibio (estado distinto de 1 = "No recibido")
 * Una vez recibido, el documento y sus archivos digitales ya no se modifican.
 */
class EstadoDocumento {

    public static function de($documento)
    {
        $r = array('derivado' => FALSE, 'recibido' => FALSE, 'paso' => NULL, 'fecha' => NULL, 'destinos' => 0, 'sin_recibir' => 0);
        if (!$documento || !$documento->loaded() || $documento->nur == '') {
            return $r;
        }
        $primera = DB::query(Database::SELECT, 'SELECT id, id_seguimiento, fecha_emision FROM seguimiento
                WHERE nur = :nur AND derivado_por = :autor AND fecha_emision >= :creado
                ORDER BY id LIMIT 1')
                ->param(':nur', (string) $documento->nur)
                ->param(':autor', (int) $documento->id_user)
                ->param(':creado', (string) $documento->fecha_creacion)
                ->execute()->current();
        if (!$primera) {
            return $r;
        }
        $paso = DB::query(Database::SELECT, 'SELECT COUNT(*) AS destinos, SUM(estado <> 1) AS recibidos FROM seguimiento
                WHERE nur = :nur AND derivado_por = :autor AND id_seguimiento = :paso AND fecha_emision >= :creado')
                ->param(':nur', (string) $documento->nur)
                ->param(':autor', (int) $documento->id_user)
                ->param(':paso', (int) $primera['id_seguimiento'])
                ->param(':creado', (string) $documento->fecha_creacion)
                ->execute()->current();
        $r['derivado'] = TRUE;
        $r['paso'] = (int) $primera['id_seguimiento'];
        $r['fecha'] = $primera['fecha_emision'];
        $r['destinos'] = (int) $paso['destinos'];
        $r['recibido'] = (int) $paso['recibidos'] > 0;
        // destinatarios que todavia no lo recibieron (copias que aun se pueden cancelar)
        $r['sin_recibir'] = (int) $paso['destinos'] - (int) $paso['recibidos'];
        return $r;
    }

}
