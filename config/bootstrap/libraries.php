<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * The libraries file is where you configure the various plugins, frameworks, and other libraries
 * to be used by your application, including your application itself. This file also defines some
 * global constants used to tell Lithium where to find your application and support libraries
 * (including Lithium itself). It uses the `Libraries` class to add configurations for the groups of
 * classes used in your app.
 *
 * In Lithium, a _library_ is any collection of classes located in a single base directory, which
 * all share the same class-to-file naming convention, and usually a common class or namespace
 * prefix. While all collections of classes are considered libraries, there are two special types of
 * libraries:
 *
 * - **Applications**: Applications are libraries which follow the organizational conventions that
 *   Lithium defines for applications (see `Libraries::locate()` and `Libraries::paths()`), and
 *   which also include a web-accessible document root (i.e. the `webroot/` folder), and can
 *   dispatch HTTP requests (i.e. through `webroot/index.php`).
 *
 * - **Plugins**: Plugins are libraries which generally follow the same organizational conventions
 *   as applications, but are designed to be used within the context of another application. They
 *   _may_ include a public document root for supporting assets, but this requires a symlink from
 *   `libraries/<plugin-name>/webroot` to `<app-name>/webroot/<plugin-name>` (recommended for
 *   production), or a media filter to load plugin resources (see `/config/bootstrap/media.php`).
 *
 * Note that a library can be designed as both an application and a plugin, but this requires some
 * special considerations in the bootstrap process, such as removing any `require` statements, and
 * conditionally defining the constants below.
 *
 * By default, libraries are stored in the base `/libraries` directory, or in the
 * application-specific `<app-name>/libraries` directory. Libraries can be loaded from either place
 * without additional configuration, but note that if the same library is in both directories, the
 * application-specific `libraries` directory will override the global one.
 *
 * The one exception to this is the _primary_ library, which is an application configured with
 * `'default' => true` (see below); this library uses the `LITHIUM_APP_PATH` constant (also defined
 * below) as its path. Note, however, that any library can be overridden with an arbitrary path by
 * passing the `'path'` key to its configuration. See `Libraries::add()` for more options.
 *
 * @see lithium\core\Libraries
 */

/**
 * This is the path to your application's directory.  It contains all the sub-folders for your
 * application's classes and files.  You don't need to change this unless your webroot folder is
 * stored outside of your app folder.
 */
define('LITHIUM_APP_PATH', dirname(dirname(__DIR__)));
/**
 * This is the path to the class libraries used by your application, and must contain a copy of the
 * Lithium core.  By default, this directory is named `libraries`, and resides in the same
 * directory as your application.  If you use the same libraries in multiple applications, you can
 * set this to a shared path on your server.
 */
//define('LITHIUM_LIBRARY_PATH', '/Library/WebServer/Documents/lithium/framework/libraries');
// define('LITHIUM_LIBRARY_PATH', dirname(LITHIUM_APP_PATH) . '/libraries');
define('LITHIUM_LIBRARY_PATH', LITHIUM_APP_PATH . '/libraries');
/**
 * Locate and load Lithium core library files.  Throws a fatal error if the core can't be found.
 * If your Lithium core directory is named something other than `lithium`, change the string below.
 */
if (!include LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/core/Libraries.php') {
	$message  = "Lithium core could not be found.  Check the value of LITHIUM_LIBRARY_PATH in ";
	$message .= __FILE__ . ".  It should point to the directory containing your ";
	$message .= "/libraries directory.";
	throw new ErrorException($message);
}

use lithium\core\Libraries;

/**
 * Optimize default request cycle by loading common classes.  If you're implementing custom
 * request/response or dispatch classes, you can safely remove these.  Actually, you can safely
 * remove them anyway, they're just there to give slightly you better out-of-the-box performance.
 */
/*
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/core/Object.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/core/StaticObject.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/util/Collection.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/util/collection/Filters.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium/util/Inflector.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/util/String.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/core/Adaptable.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/core/Environment.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/Message.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Message.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Media.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Request.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Response.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Route.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/net/http/Router.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/action/Controller.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/action/Dispatcher.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/action/Request.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/action/Response.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/template/View.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/template/view/Renderer.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/template/view/Compiler.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/template/view/adapter/File.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/storage/Cache.php';
require LITHIUM_LIBRARY_PATH . '/unionofrad/lithiumlithium/storage/cache/adapter/Apc.php';
*/

//var_dump(LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium');exit();
/**
 * Add the Lithium core library.  This sets default paths and initializes the autoloader.  You
 * generally should not need to override any settings.
 */
Libraries::add('lithium', array('path' => LITHIUM_LIBRARY_PATH . '/unionofrad/lithium/lithium'));

/**
 * Add the application.  You can pass a `'path'` key here if this bootstrap file is outside of
 * your main application, but generally you should not need to change any settings.
 * 
 * You should probably change this namespace to something specific for your application.
 */
Libraries::add('app', array('default' => true));

/**
 * Add the Lithium Bootstrap core library.
 * This CAN NOT be placed under config/bootstrap/libraries because
 * this core library would include itself then. That would be bad.
 * 
 * Note: If 'symlinkAssets' is set to true, then li3b_core will
 * NOT include the filter on the Dispatcher that media.php calls.
 * This means all `webroot` directories for libraries will need 
 * to be symlinked to the main application's `webroot` directory.
 * 
 * If using symlinks for static assets, you will gain a little
 * bit of performance. Lithium Bootstrap will work either way
 * and upon library installation (with the Lithium Bootstrap
 * console command), a symlink will be created if `symlinkAssets`
 * is set to true here. Either way, a symlink for `li3b_core` has
 * likely already been added for you if you ran the setup.sh script.
 * So you can access core Lithium Bootstrap assets either way.
 * ie. /li3b_core/css/bootstrap.css OR /li3b_core/css/bootstrap.css
 * 
 * Note: You can change this to false if you don't want to use symlinks.
 * BUT, if you flip this setting back and forth keep in mind that
 * you may have to symlink some directories in the future depending
 * on the setting of this option at the time of library installation.
 */
// Optional config.ini file sets some options.
$uiDefaults = array(
	'navbarTitle' => 'Lithium Bootstrap',
	'adminNavbarTitle' => 'Lithium Bootstrap'
);
$li3Options = array();
if(file_exists(dirname(__DIR__) . '/config.ini')) {
	$li3Options = parse_ini_file(dirname(__DIR__) . '/config.ini', true);
}
$uiOptions = isset($li3Options['ui']) ? $li3Options['ui'] += $uiDefaults:$uiDefaults;

// Optionally set the default timezone from the config file, ex. "America/Los_Angeles"
if(isset($li3Options['general']['timezone'])) {
	date_default_timezone_set($li3Options['general']['timezone']);
}

Libraries::add('li3b_core', array('symlinkAssets' => true, 'navbarTitle' => $uiOptions['navbarTitle'], 'adminNavbarTitle' => $uiOptions['adminNavbarTitle']));
?>