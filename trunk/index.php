<?php
namespace Me\Raatiniemi\Ramverk\Trunk;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration as Config;
use Me\Raatiniemi\Ramverk\Request;
use Me\Raatiniemi\Ramverk\Routing;

// Start the performace timer.
$time['start'] = microtime(true);

try {
    // Enable full error reporting.
    error_reporting(E_ALL);

    // Require the framework bootstrap file, the autoload functionality
    // within the framework will handle the inclusion of the other files.
    $directory = realpath(__DIR__ . '/..');
    require "{$directory}/src/ramverk.php";

    // Setup the basic application directory configurations.
    $config = new Config();

    // To improve performance, use a non-development profile.
    // A development profile is formatted as: 'development(\..*)'
    // E.g. 'development' or 'development.developer'.
    // $config->set('profile', 'production');

    // Set the absolute path for the framework core and the application.
    $config->set('directory.core', "{$directory}/src", false, true);
    $config->set('directory.application', "{$directory}/trunk", false, true);

    // Initialize the framework core.
    $core = new Ramverk\Core($config);
    $controller = $core->getController();
    $controller->dispatch();

    // ---- controller->dispatch() code.

    // Retrieve the configuration handler factory.
    $factory = $controller->getConfigurationHandlerFactory();

} catch (\Exception $e) {
    // Render thrown exceptions with the specified template.
    Ramverk\Exception::render($e, $config);
}

// End the performace timer and print the results.
$time['end'] = microtime(true);
printf('Execution took %f seconds', ($time['end'] - $time['start']));
