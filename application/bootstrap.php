<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Asia/Manila');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

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
 * Set the production status by the server ip address.
 */
define('IN_PRODUCTION', false);

/**
 * Set generic salt for application wide hashing
 */
define('GENERIC_SALT', 'dJkrTa12s9as200d0783dss');

/**
 * Defines the version of the application
 */
define('APP_VERSION', '0.0.1');

//-- Configuration and initialization -----------------------------------------

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
	'base_url' 		=> '/',
	'index_file' 	=> FALSE,
	'profile'  		=> ! IN_PRODUCTION,
	'caching'    	=> IN_PRODUCTION
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	// 'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	// 'image'      => MODPATH.'image',      // Image manipulation
	// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	// 'oauth'      => MODPATH.'oauth',      // OAuth authentication
	// 'pagination' => MODPATH.'pagination', // Paging of results
	'unittest'   => MODPATH.'unittest',   // Unit testing
	'userguide'  => MODPATH.'userguide',  // User guide and API documentation
));

/**
 * Translated user guide
 */
Route::set('manual', 'manual(/<language>(/<page>))', array(
		'page' => '.+',
	))
	->defaults(array(
		'controller' => 'manual',
		'action'     => 'docs',
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
	));

/**
 * Execute the main request using PATH_INFO. If no URI source is specified,
 * the URI will be automatically detected.
 */
try
{
	if ( ! defined('SUPPRESS_REQUEST'))
	{
		// Attempt to execute the response
		$request = Request::instance()->execute();
	
		// Display the request response.
		echo $request->send_headers()->response;
	}
}
catch (Exception $e)
{
	if ( ! IN_PRODUCTION)
	{
		// Just re-throw the exception
		throw $e;
	}

	// Log the error
	Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));

	// Create new request for serving error pages
	$request = null;

	// 404 errors are usually thrown as ReflectionException or 
	// Kohana_Request_Exception when a controller/action is not
	// found or a route is not set for a specific request
	if ($e instanceof ReflectionException OR $e instanceof Kohana_Request_Exception)
	{
		// Create a 404 response
		$request = Request::factory('error/404')->execute();

		// insert the requested page to the error reponse
		$uri = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/';
		$page = array('{KOHANA_REQUESTED_PAGE}' => URL::site("/$uri", true));
		$request->response = strtr((string) $request->response, $page);
	}
	else
	{
		// create a 500 response
		$request = Request::factory('error/500')->execute();
	}

	echo $request->send_headers()->response;
}
