<?php
namespace Me\Raatiniemi\Ramverk\Sample
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration;

	try {
		// Enable full error reporting.
		error_reporting(E_ALL);

		// Require the framework bootstrap file, the autoload functionality
		// within the framework will handle the inclusion of the other files.
		$directory = realpath(__DIR__ . '/..');
		require "{$directory}/src/ramverk.php";

		// Setup the basic application directory configurations.
		$config = new Configuration\Container();

		// Absolute path for the core framework.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);

		// Absolute path for the application.
		$config->set('directory.application', "{$directory}/sample/application", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);
		$core->getContext()->getController()->dispatch();
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}
// End of file: index.php
// Location: sample/index.php