<?php
namespace Me\Raatiniemi\Ramverk\Test;

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

// Setup the ramverk include path.
$path = realpath(__DIR__ . '/..');
set_include_path($path . PATH_SEPARATOR . get_include_path());

require_once 'src/library/utility/File.php';
require_once 'src/library/utility/Filesystem.php';

require_once 'src/library/configuration/Utility.php';
require_once 'src/library/configuration/Configuration.php';
require_once 'src/library/configuration/handler/Handler.php';
require_once 'src/library/configuration/handler/Autoload.php';
require_once 'src/library/configuration/handler/Core.php';
require_once 'src/library/configuration/handler/Factory.php';
require_once 'src/library/configuration/handler/Module.php';
require_once 'src/library/configuration/handler/Parser.php';
require_once 'src/library/configuration/handler/Routing.php';
require_once 'src/library/configuration/handler/Cache.php';

require_once 'src/library/data/dom/utility/Value.php';
require_once 'src/library/data/dom/Attribute.php';
require_once 'src/library/data/dom/Document.php';
require_once 'src/library/data/dom/Element.php';
require_once 'src/library/data/dom/Node.php';

require_once 'src/library/exception/Exception.php';

// End of file: bootstrap.php
// Location: test/bootstrap.php
