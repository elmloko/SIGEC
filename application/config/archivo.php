<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Ruta base donde se guardan y desde donde se sirven los adjuntos de documentos.
 * Antes estaba repetida (hardcodeada) en documento.php, download.php y download2.php;
 * ahora vive en un solo lugar para poder ajustarla por entorno sin tocar código.
 *
 * En Windows (entorno local de desarrollo, XAMPP) no existe /backup/backup_sigec/...,
 * así que en ese entorno los adjuntos se leen/escriben en vivo por SFTP contra el
 * servidor real (ver 'remote'); en Linux (producción) se accede al disco local tal cual.
 */
return array(
    'path' => (DIRECTORY_SEPARATOR === '\\')
        ? 'C:/xampp/sigec_archivo'
        : '/backup/backup_sigec/sigec/archivo',
    'remote' => array(
        // Solo se usa cuando DIRECTORY_SEPARATOR === '\\' (Windows/local).
        'enabled' => TRUE,
        'host' => '172.65.10.203',
        'port' => 22,
        'user' => 'root',
        // La contraseña vive aparte porque ese archivo no se versiona (ver .gitignore).
        'password' => Kohana::$config->load('archivo_secret')->get('password'),
        'remote_path' => '/backup/backup_sigec/sigec/archivo',
    ),
);
