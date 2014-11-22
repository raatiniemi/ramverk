<?php
namespace Me\Raatiniemi\Ramverk;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

// Version controll, since we are using traits and other 5.4.0 features the
// minimum required version is 5.4.0.
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    echo 'The minimum PHP version requirement for Ramverk is PHP 5.4.0.' . PHP_EOL;
    echo 'You are running version: ' . PHP_VERSION . PHP_EOL;
    exit;
}

// Setup the include path for the library.
$path = __DIR__ . '/library/';
set_include_path($path . PATH_SEPARATOR . get_include_path());

// Require the absolute core classes needed to bootstrap the framework.
// With these included the autoload functionality can handle the rest.
require 'configuration/Utility.php';
require 'configuration/Configuration.php';

// Include framework utilities, needed by configuration, etc.
require 'utility/File.php';
require 'utility/Filesystem.php';

// Include the configuration handlers required by the core.
require 'configuration/handler/Cache.php';
require 'configuration/handler/Parser.php';
require 'configuration/handler/Factory.php';
require 'configuration/handler/Handler.php';
require 'configuration/handler/Autoload.php';

// Include the extended DOM functionality.
require 'data/dom/utility/Value.php';
require 'data/dom/Attribute.php';
require 'data/dom/Document.php';
require 'data/dom/Element.php';
require 'data/dom/Node.php';

// Include exceptions.
require 'exception/Exception.php';

// End of file: ramverk.php
// Location: ramverk.php
