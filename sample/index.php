<?php
namespace Me\Raatiniemi\Ramverk\Sample;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

try {
    // Enable full error reporting.
    error_reporting(E_ALL);

    // Require the framework bootstrap file, the autoload functionality
    // within the framework will handle the inclusion of the other files.
    $directory = realpath(__DIR__ . '/..');
    require "{$directory}/src/ramverk.php";

    // Setup the basic application directory configurations.
    $config = new Ramverk\Configuration();

    // To improve performance, use a non-development profile.
    // A development profile is formatted as: 'development(\..*)'
    // E.g. 'development' or 'development.developer'.
    // $config->set('profile', 'production');

    // Set the absolute path for the framework core and the application.
    $config->set('directory.core', "{$directory}/src", false, true);
    $config->set('directory.application', "{$directory}/sample/application", false, true);

    // Initialize the framework core.
    $core = new Ramverk\Core($config);
    $core->getController()->dispatch();
} catch (\Exception $e) {
    // Render thrown exceptions with the specified template.
    Ramverk\Exception::render($e, $config);
}
// End of file: index.php
// Location: sample/index.php
