<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Permisos por rol (nivel), administrables en Configuraciones > Roles y permisos (admin/roles).
 *
 * Se guardan en la tabla nivel_permisos (id_nivel, permiso, valor). Lo que no esta en la tabla toma el
 * valor por defecto de $defecto, que reproduce como funcionaba el sistema antes de existir la pantalla;
 * si la tabla aun no existe, el sistema sigue funcionando con esos valores.
 *
 * Las marcas personales prioridad = 1 / super <> 0 siguen viendo todo (ver Acceso::sin_restriccion).
 */
class Permisos {

    /** Permisos administrables: clave => [titulo, explicacion, grupo]. */
    public static function lista()
    {
        return array(
            'ver_todo' => array('Ver todo',
                'Abre el seguimiento, el detalle y la vista de cualquier hoja de ruta o documento, aunque no intervenga en ella.', 'Hojas de ruta y documentos'),
            'descargar_todo' => array('Descargar todo',
                'Descarga los adjuntos, el PDF de la hoja de ruta, el Word y las plantillas de cualquier documento, aunque no intervenga.', 'Hojas de ruta y documentos'),
            'ver_oficina' => array('Ver y descargar lo de su oficina',
                'Abre y descarga las hojas de ruta que pasaron por su oficina (creadas, enviadas o recibidas por ella), aunque la persona no intervenga.', 'Hojas de ruta y documentos'),
            'registrar_bitacora' => array('Registrar en la bitácora',
                'Cuando abre o descarga algo en lo que no interviene, queda anotado en la bitácora (una vez por hoja de ruta o documento en cada sesión).', 'Hojas de ruta y documentos'),
            'borradores_ajenos' => array('Ver borradores ajenos en la búsqueda',
                'En la búsqueda aparecen, bloqueados, los documentos de otras personas que aún no se derivaron. Si se quita, solo aparecen los ya derivados.', 'Búsqueda y exportación'),
            'exportar_excel' => array('Exportar el seguimiento a Excel',
                'Puede exportar pasos del seguimiento a Excel. Sin "Ver todo", solo los pasos que derivó o recibió.', 'Búsqueda y exportación'),
            'ver_reportes' => array('Reportes del sistema',
                'Pestaña Reportes (Centro de indicadores) y los datos de pendientes, documentos y bitácora de cualquier usuario u oficina.', 'Pestañas especiales'),
            'mis_indicadores' => array('Mis indicadores',
                'Pestaña con el tablero personal de la persona (sus propios números).', 'Pestañas especiales'),
        );
    }

    /** Valores por defecto (= comportamiento previo a la pantalla). '*' aplica a los niveles no listados. */
    private static $defecto = array(
        5 => array('ver_todo' => 1, 'descargar_todo' => 1, 'registrar_bitacora' => 1, 'borradores_ajenos' => 1, 'exportar_excel' => 1, 'ver_reportes' => 1),
        3 => array('ver_todo' => 1, 'descargar_todo' => 1, 'registrar_bitacora' => 1, 'borradores_ajenos' => 1, 'exportar_excel' => 1, 'mis_indicadores' => 1),
        2 => array('borradores_ajenos' => 1, 'exportar_excel' => 1, 'mis_indicadores' => 1),
        '*' => array('borradores_ajenos' => 1, 'exportar_excel' => 1),
    );

    /** Valores guardados por nivel (una consulta por nivel y peticion). */
    private static $cache = array();

    public static function defecto($nivel, $clave)
    {
        $nivel = (int) $nivel;
        $base = isset(self::$defecto[$nivel]) ? self::$defecto[$nivel] : self::$defecto['*'];
        return !empty($base[$clave]);
    }

    private static function guardados($nivel)
    {
        $nivel = (int) $nivel;
        if (!array_key_exists($nivel, self::$cache)) {
            self::$cache[$nivel] = array();
            try {
                $filas = DB::query(Database::SELECT, 'SELECT permiso, valor FROM nivel_permisos WHERE id_nivel = :n')
                        ->param(':n', $nivel)
                        ->execute();
                foreach ($filas as $f) {
                    self::$cache[$nivel][$f['permiso']] = (int) $f['valor'] === 1;
                }
            } catch (Database_Exception $e) {
                // la tabla aun no existe: se usan los valores por defecto
            }
        }
        return self::$cache[$nivel];
    }

    /** ¿El nivel tiene el permiso? */
    public static function tiene($nivel, $clave)
    {
        $guardados = self::guardados($nivel);
        return array_key_exists($clave, $guardados) ? $guardados[$clave] : self::defecto($nivel, $clave);
    }

    /** ¿Existe la tabla nivel_permisos? (para avisar en la pantalla) */
    public static function tabla_lista()
    {
        try {
            return DB::query(Database::SELECT, "SHOW TABLES LIKE 'nivel_permisos'")->execute()->count() > 0;
        } catch (Database_Exception $e) {
            return FALSE;
        }
    }

    /** Guarda los permisos de un nivel: $valores = [clave => bool] (las claves no enviadas quedan en FALSE). */
    public static function guardar($nivel, array $valores, $id_usuario)
    {
        $nivel = (int) $nivel;
        foreach (array_keys(self::lista()) as $clave) {
            DB::query(Database::INSERT, 'INSERT INTO nivel_permisos (id_nivel, permiso, valor, actualizado, id_usuario)
                    VALUES (:n, :p, :v, NOW(), :u)
                    ON DUPLICATE KEY UPDATE valor = VALUES(valor), actualizado = VALUES(actualizado), id_usuario = VALUES(id_usuario)')
                    ->param(':n', $nivel)
                    ->param(':p', $clave)
                    ->param(':v', empty($valores[$clave]) ? 0 : 1)
                    ->param(':u', (int) $id_usuario)
                    ->execute();
        }
        unset(self::$cache[$nivel]);
    }

    /** Nombre del rol (tabla niveles), en minusculas para usarlo dentro de una frase. */
    public static function nombre_nivel($nivel)
    {
        static $nombres = NULL;
        if ($nombres === NULL) {
            $nombres = array();
            foreach (DB::query(Database::SELECT, 'SELECT id, nivel FROM niveles')->execute() as $f) {
                $nombres[(int) $f['id']] = $f['nivel'];
            }
        }
        $nivel = (int) $nivel;
        return isset($nombres[$nivel]) ? mb_strtolower($nombres[$nivel], 'UTF-8') : 'nivel ' . $nivel;
    }
}
