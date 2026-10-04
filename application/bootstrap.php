<?php

defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}
-
        /**
         * Set the default time zone.
         *
         * @see  http://kohanaframework.org/guide/using.configuration
         * @see  http://php.net/timezones
         */
        date_default_timezone_set('America/La_Paz');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'es_ES.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Cookies de sesion y de "recordarme":
 *  - httponly: el JavaScript de la pagina no puede leerlas (si alguien inyecta un script, no roba la sesion)
 *  - secure: solo viajan por HTTPS, cuando el sitio se abre por HTTPS (por HTTP no se marca, o no habria login)
 *  - la sesion solo acepta ids creados por el servidor y solo por cookie (no por la URL)
 */
Cookie::$httponly = TRUE;
Cookie::$secure = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
    || strtolower((string) (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : '')) === 'https';
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_strict_mode', 1);

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
//Kohana::$environment = isset($_SERVER['KOHANA_ENV'])?constant('Kohana::'. strtoupper($_SERVER['KOHANA_ENV'])):Kohana::PRODUCTION;
Kohana::$environment = Kohana::PRODUCTION;

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
    'base_url' => '/',
    'index_file' => FALSE,
    //'index_file'   =>Kohana::$environment ===Kohana::PRODUCTION,
    'errors' => Kohana::$environment !== Kohana::PRODUCTION,
    //'profile'        => Kohana::$environment !==Kohana::PRODUCTION,
    'caching' => Kohana::$environment === Kohana::PRODUCTION,
));

Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    'auth' => MODPATH . 'auth', // Basic authentication
    'cache' => MODPATH . 'cache', // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database' => MODPATH . 'database', // Database access
    'image' => MODPATH . 'image', // Image manipulation
    'orm' => MODPATH . 'orm', // Object Relationship Mapping
    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'email' => MODPATH . 'email', // email send
));

/* //incluimos phpword
  if ($path = Kohana::find_file('libraries', ''))
  {
  ini_set('include_path',
  ini_get('include_path').PATH_SEPARATOR.dirname(dirname($path)));
  require_once 'myClass.php';
  }


  /**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */


Route::set('soap', 'soap(/<controller>(/<action>(/<id>)))')
    ->defaults(array(
        'directory' => 'soap',
        'controller' => 'main',
        'action' => 'index',
    ));
Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'directory' => 'admin',
            'controller' => 'main',
            'action' => 'index',
        ));
Route::set('archivero', 'archivero(/<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'directory' => 'archivero',
            'controller' => 'principal',
            'action' => 'index',
        ));
Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'dashboard',
            'action' => 'index',
        ));

set_exception_handler(array('Exceptionhandler', 'handle'));
