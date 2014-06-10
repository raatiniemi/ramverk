<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	// Version controll, since we are using traits and other 5.4.0 features the
	// minimum required version is 5.4.0.
	if(version_compare(PHP_VERSION, '5.4.0', '<')) {
		echo 'The minimum PHP version requirement for Ramverk is PHP 5.4.0.' . PHP_EOL;
		echo 'You are running version: ' . PHP_VERSION . PHP_EOL;
		exit;
	}

	// Setup the include path for the library.
	$path = __DIR__ . '/library/';
	set_include_path($path . PATH_SEPARATOR . get_include_path());

	// Require the absolute core classes needed to bootstrap the framework.
	// With these included the autoload functionality can handle the rest.
	require 'loader/Autoload.trait.php';
	require 'configuration/Utility.trait.php';
	require 'core/context/Handler.trait.php';
	require 'core/Core.class.php';
	require 'core/Context.class.php';

	// Include the configuration container with its core dependencies.
	require 'data/Container.class.php';
	require 'configuration/Container.class.php';

	// Include the configuration handlers required by the core.
	require 'configuration/handler/Cache.class.php';
	require 'configuration/handler/Parser.class.php';
	require 'configuration/handler/Factory.class.php';
	require 'configuration/handler/Handler.class.php';
	require 'configuration/handler/Autoload.class.php';

	// Include the extended DOM functionality.
	require 'data/dom/utility/Value.trait.php';
	require 'data/dom/Document.class.php';
	require 'data/dom/Element.class.php';
	require 'data/dom/Node.class.php';

	// Include exceptions.
	require 'exception/Exception.class.php';
}
// End of file: ramverk.php
// Location: ramverk.php