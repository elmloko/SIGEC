<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Quien puede ver una hoja de ruta, un documento o un archivo digital.
 *
 * Un usuario ve solo aquello en lo que participa:
 *  - genero algun documento de la hoja de ruta (documentos.id_user),
 *  - la derivo o se la derivaron, como oficial o como copia (seguimiento.derivado_por / derivado_a),
 *  - participa en una hoja agrupada con ella (la principal o una de sus agrupadas).
 *
 * Excepciones:
 *  - prioridad = 1 o super <> 0: ven todo (marcas antiguas, se mantienen).
 *  - administrador (nivel 5) y jefe de oficina (nivel 3): ven todo, pero cada acceso donde no
 *    participan queda en la bitacora.
 */
class Acceso {

    const NIVEL_ADMIN = 5;
    const NIVEL_JEFE = 3;

    /** ¿Ve todo sin restriccion? (prioridad / super). */
    public static function sin_restriccion($user)
    {
        return $user && ((int) $user->prioridad === 1 || (int) $user->super !== 0);
    }

    public static function es_admin($user)
    {
        return $user && (int) $user->nivel === self::NIVEL_ADMIN;
    }

    public static function es_jefe($user)
    {
        return $user && (int) $user->nivel === self::NIVEL_JEFE;
    }

    /** ¿Su rol le deja ver todo (administrador o jefe de oficina)? Esos accesos se anotan en la bitacora. */
    public static function ve_todo_por_rol($user)
    {
        return self::es_admin($user) || self::es_jefe($user);
    }

    /** Nombre del rol para la bitacora y los avisos. */
    public static function nombre_rol($user)
    {
        return self::es_jefe($user) ? 'jefe de oficina' : 'administrador';
    }

    /** Hojas de ruta enlazadas por agrupacion (la principal y sus agrupadas), incluida la propia. */
    public static function nurs_del_grupo($nur)
    {
        $nurs = array((string) $nur);
        $filas = DB::query(Database::SELECT, 'SELECT padre AS nur FROM agrupaciones WHERE hijo = :nur
                UNION SELECT hijo FROM agrupaciones WHERE padre = :nur')
                ->param(':nur', (string) $nur)
                ->execute();
        foreach ($filas as $f) {
            $nurs[] = (string) $f['nur'];
        }
        return array_values(array_unique($nurs));
    }

    /** ¿El usuario participa (sin excepciones) en la hoja de ruta o en su grupo? */
    public static function participa($user, $nur)
    {
        if (!$user || (string) $nur === '') {
            return FALSE;
        }
        $nurs = self::nurs_del_grupo($nur);
        return DB::query(Database::SELECT, 'SELECT 1 FROM seguimiento WHERE nur IN :nurs AND derivado_a = :u
                UNION SELECT 1 FROM seguimiento WHERE nur IN :nurs AND derivado_por = :u
                UNION SELECT 1 FROM documentos WHERE nur IN :nurs AND id_user = :u
                LIMIT 1')
                ->param(':nurs', $nurs)
                ->param(':u', (int) $user->id)
                ->execute()->count() > 0;
    }

    /**
     * ¿Puede ver la hoja de ruta? El acceso del administrador donde no participa se anota en la bitacora
     * (una vez por hoja de ruta y sesion, para no llenarla al recargar).
     */
    public static function hoja_ruta($user, $nur)
    {
        if (!$user) {
            return FALSE;
        }
        if (self::sin_restriccion($user) || self::participa($user, $nur)) {
            return TRUE;
        }
        if (self::ve_todo_por_rol($user)) {
            self::registrar_admin($user, 'Abrió como ' . self::nombre_rol($user) . ' la hoja de ruta <b>' . HTML::chars($nur) . '</b>, en la que no interviene', 'hr:' . $nur);
            return TRUE;
        }
        return FALSE;
    }

    /** ¿Puede ver el documento? El autor siempre; si no, quien puede ver su hoja de ruta. */
    public static function documento($user, $documento)
    {
        if (!$user || !$documento || !$documento->loaded()) {
            return FALSE;
        }
        if ((int) $documento->id_user === (int) $user->id || self::sin_restriccion($user)) {
            return TRUE;
        }
        if ($documento->nur != '' && self::participa($user, $documento->nur)) {
            return TRUE;
        }
        if (self::ve_todo_por_rol($user)) {
            self::registrar_admin($user, 'Abrió como ' . self::nombre_rol($user) . ' el documento <b>' . HTML::chars($documento->codigo) . '</b> (hoja de ruta ' . HTML::chars($documento->nur) . '), en el que no interviene', 'doc:' . $documento->id);
            return TRUE;
        }
        return FALSE;
    }

    /** ¿Puede descargar el archivo digital? Quien lo subio, o quien puede ver su documento. */
    public static function archivo($user, $archivo)
    {
        if (!$user || !$archivo || !$archivo->loaded()) {
            return FALSE;
        }
        if ((int) $archivo->id_user === (int) $user->id) {
            return TRUE;
        }
        return self::documento($user, ORM::factory('documentos', (int) $archivo->id_documento));
    }

    /**
     * Condicion SQL para listados y busquedas sobre documentos (alias $alias): solo las hojas de ruta
     * en las que participa. Devuelve '' cuando el usuario ve todo (prioridad/super/administrador/jefe).
     */
    public static function condicion_sql($user, $alias = 'd')
    {
        if (self::sin_restriccion($user) || self::ve_todo_por_rol($user)) {
            return '';
        }
        $u = (int) $user->id;
        // toda la union va dentro de una tabla derivada: MariaDB la calcula una sola vez
        // (con la union suelta dentro del IN se evaluaba fila por fila: ~9 s contra ~0,5 s medido en produccion)
        // nur vacio = documento sin hoja de ruta (borrador): solo lo ve su autor; sin esta guarda, cualquier
        // borrador propio hacia "coincidir" todos los borradores ajenos (nur '' IN (... '' ...))
        return "($alias.id_user = $u OR $alias.nur <> '' AND $alias.nur IN (SELECT x.nur FROM (
                    SELECT nur FROM seguimiento WHERE derivado_a = $u
                    UNION SELECT nur FROM seguimiento WHERE derivado_por = $u
                    UNION SELECT nur FROM documentos WHERE id_user = $u
                    UNION SELECT a.hijo FROM agrupaciones a INNER JOIN seguimiento s ON s.nur = a.padre WHERE s.derivado_a = $u
                    UNION SELECT a.hijo FROM agrupaciones a INNER JOIN seguimiento s ON s.nur = a.padre WHERE s.derivado_por = $u
                    UNION SELECT a.padre FROM agrupaciones a INNER JOIN seguimiento s ON s.nur = a.hijo WHERE s.derivado_a = $u
                    UNION SELECT a.padre FROM agrupaciones a INNER JOIN seguimiento s ON s.nur = a.hijo WHERE s.derivado_por = $u
                ) x))";
    }

    private static function registrar_admin($user, $accion, $clave)
    {
        $session = Session::instance();
        $vistos = (array) $session->get('acceso_admin_registrado', array());
        if (isset($vistos[$clave])) {
            return;
        }
        $vistos[$clave] = 1;
        $session->set('acceso_admin_registrado', $vistos);
        $vitacora = ORM::factory('vitacora');
        $vitacora->id_entidad = $user->id_entidad;
        $vitacora->id_usuario = $user->id;
        $vitacora->fecha_hora = date('Y-m-d H:i:s');
        $vitacora->accion_realizada = $accion;
        $vitacora->ip_usuario = Request::$client_ip;
        $vitacora->save();
    }

    /** Vista de "acceso restringido" con HTTP 403, para las pantallas con plantilla. */
    public static function vista_denegado($response, $codigo = '')
    {
        if ($response) {
            $response->status(403);
        }
        return View::factory('no_access')->set('codigo', $codigo);
    }
}
