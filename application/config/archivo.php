<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Ruta base donde se guardan y desde donde se sirven los adjuntos de documentos.
 * Antes estaba repetida (hardcodeada) en documento.php, download.php y download2.php;
 * ahora vive en un solo lugar para poder ajustarla por entorno sin tocar código.
 */
return array(
    'path' => '/backup/backup_sigec/sigec/archivo',
);
