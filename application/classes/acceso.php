<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Quien puede ver o descargar una hoja de ruta, un documento o un archivo digital.
 *
 * Un usuario ve y descarga aquello en lo que interviene:
 *  - genero algun documento de la hoja de ruta (documentos.id_user),
 *  - la derivo o se la derivaron, como oficial o como copia (seguimiento.derivado_por / derivado_a),
 *  - participa en una hoja agrupada con ella (la principal o una de sus agrupadas),
 *  - con el permiso "ver_oficina": la hoja de ruta paso por su oficina.
 *
 * Lo demas depende de los permisos de su rol (clase Permisos, pantalla admin/roles):
 *  - ver_todo: abre cualquier hoja de ruta o documento;
 *  - descargar_todo: descarga cualquier adjunto, PDF, Word o plantilla;
 *  - registrar_bitacora: esos accesos donde no interviene quedan en la bitacora.
 * Las marcas personales prioridad = 1 o super <> 0 ven y descargan todo (marcas antiguas, se mantienen).
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

    /** ¿El rol del usuario tiene el permiso? (ver Permisos::lista) */
    public static function permiso($user, $clave)
    {
        return $user && Permisos::tiene($user->nivel, $clave);
    }

    /** ¿Ve todas las hojas de ruta y documentos (marca personal o permiso del rol)? */
    public static function ve_todo($user)
    {
        return self::sin_restriccion($user) || self::permiso($user, 'ver_todo');
    }

    /** Solo el permiso del rol (para avisar que entro "como jefe / administrador"). */
    public static function ve_todo_por_rol($user)
    {
        return self::permiso($user, 'ver_todo');
    }

    /** Nombre del rol para la bitacora y los avisos (de la tabla niveles). */
    public static function nombre_rol($user)
    {
        return $user ? Permisos::nombre_nivel($user->nivel) : '';
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

    /** ¿El usuario participa personalmente en la hoja de ruta o en su grupo? */
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

    /** ¿La hoja de ruta (o su grupo) paso por la oficina? (creada, enviada o recibida por ella) */
    public static function oficina_participa($id_oficina, $nur)
    {
        $id_oficina = (int) $id_oficina;
        if ($id_oficina <= 0 || (string) $nur === '') {
            return FALSE;
        }
        $nurs = self::nurs_del_grupo($nur);
        return DB::query(Database::SELECT, 'SELECT 1 FROM seguimiento WHERE nur IN :nurs AND id_de_oficina = :o
                UNION SELECT 1 FROM seguimiento WHERE nur IN :nurs AND id_a_oficina = :o
                UNION SELECT 1 FROM documentos WHERE nur IN :nurs AND id_oficina = :o
                LIMIT 1')
                ->param(':nurs', $nurs)
                ->param(':o', $id_oficina)
                ->execute()->count() > 0;
    }

    /** ¿Interviene? Personalmente o, con el permiso ver_oficina, a traves de su oficina. */
    public static function interviene($user, $nur)
    {
        if (self::participa($user, $nur)) {
            return TRUE;
        }
        return self::permiso($user, 'ver_oficina') && self::oficina_participa($user->id_oficina, $nur);
    }

    /** Acceso por permiso del rol donde no interviene; se anota en la bitacora si el rol lo pide. */
    private static function por_permiso($user, $permiso, $accion, $clave)
    {
        if (!self::permiso($user, $permiso)) {
            return FALSE;
        }
        if (self::permiso($user, 'registrar_bitacora')) {
            self::registrar($user, $accion, $clave);
        }
        return TRUE;
    }

    /** ¿Puede ver la hoja de ruta (seguimiento)? */
    public static function hoja_ruta($user, $nur)
    {
        if (!$user) {
            return FALSE;
        }
        if (self::sin_restriccion($user) || self::interviene($user, $nur)) {
            return TRUE;
        }
        return self::por_permiso($user, 'ver_todo',
                'Abrió como ' . self::nombre_rol($user) . ' la hoja de ruta <b>' . HTML::chars($nur) . '</b>, en la que no interviene', 'hr:' . $nur);
    }

    /** ¿Puede descargar el PDF de la hoja de ruta o de su seguimiento? */
    public static function descarga_hoja_ruta($user, $nur)
    {
        if (!$user) {
            return FALSE;
        }
        if (self::sin_restriccion($user) || self::interviene($user, $nur)) {
            return TRUE;
        }
        return self::por_permiso($user, 'descargar_todo',
                'Descargó como ' . self::nombre_rol($user) . ' el PDF de la hoja de ruta <b>' . HTML::chars($nur) . '</b>, en la que no interviene', 'dhr:' . $nur);
    }

    /** ¿Interviene en el documento? (autor, marca personal o interviene en su hoja de ruta) */
    private static function interviene_documento($user, $documento)
    {
        return (int) $documento->id_user === (int) $user->id
            || self::sin_restriccion($user)
            || ($documento->nur != '' && self::interviene($user, $documento->nur));
    }

    /** ¿Puede ver el documento? */
    public static function documento($user, $documento)
    {
        if (!$user || !$documento || !$documento->loaded()) {
            return FALSE;
        }
        if (self::interviene_documento($user, $documento)) {
            return TRUE;
        }
        return self::por_permiso($user, 'ver_todo',
                'Abrió como ' . self::nombre_rol($user) . ' el documento <b>' . HTML::chars($documento->codigo) . '</b> (hoja de ruta ' . HTML::chars($documento->nur) . '), en el que no interviene', 'doc:' . $documento->id);
    }

    /** ¿Puede descargar el documento (Word, plantilla, Excel) o sus adjuntos? */
    public static function descarga_documento($user, $documento)
    {
        if (!$user || !$documento || !$documento->loaded()) {
            return FALSE;
        }
        if (self::interviene_documento($user, $documento)) {
            return TRUE;
        }
        return self::por_permiso($user, 'descargar_todo',
                'Descargó como ' . self::nombre_rol($user) . ' el documento <b>' . HTML::chars($documento->codigo) . '</b> (hoja de ruta ' . HTML::chars($documento->nur) . '), en el que no interviene', 'ddoc:' . $documento->id);
    }

    /** ¿Puede descargar el archivo digital? Quien lo subio, o quien puede descargar su documento. */
    public static function archivo($user, $archivo)
    {
        if (!$user || !$archivo || !$archivo->loaded()) {
            return FALSE;
        }
        if ((int) $archivo->id_user === (int) $user->id) {
            return TRUE;
        }
        return self::descarga_documento($user, ORM::factory('documentos', (int) $archivo->id_documento));
    }

    /**
     * Condicion SQL para listados y busquedas sobre documentos (alias $alias): solo las hojas de ruta
     * en las que interviene. Devuelve '' cuando el usuario ve todo (marca personal o permiso ver_todo).
     */
    public static function condicion_sql($user, $alias = 'd')
    {
        if (self::ve_todo($user)) {
            return '';
        }
        $u = (int) $user->id;
        $oficina = '';
        if (self::permiso($user, 'ver_oficina') && (int) $user->id_oficina > 0) {
            $o = (int) $user->id_oficina;
            $oficina = "
                    UNION SELECT nur FROM seguimiento WHERE id_de_oficina = $o
                    UNION SELECT nur FROM seguimiento WHERE id_a_oficina = $o
                    UNION SELECT nur FROM documentos WHERE id_oficina = $o";
        }
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
                    UNION SELECT a.padre FROM agrupaciones a INNER JOIN seguimiento s ON s.nur = a.hijo WHERE s.derivado_por = $u$oficina
                ) x))";
    }

    /** Anota en la bitacora (una vez por clave y sesion, para no llenarla al recargar). */
    private static function registrar($user, $accion, $clave)
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
