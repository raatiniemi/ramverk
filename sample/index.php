<?php
namespace Me\Raatiniemi\Ramverk\Sample
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	try {
		// Require the framework bootstrap file, the autoload functionality
		// within the framework will handle the inclusion of the other files.
		$directory = realpath(__DIR__ . '/..');
		require "{$directory}/src/ramverk.php";

		// Setup the basic application configurations. There're three directives
		// that need to be defined.
		$config = new Ramverk\Configuration\Container();

		// 'profile'
		// Profile for the application. For example, useful when separating configuration
		// between different profiles, e.g. development and production.
		$config->set('profile', 'development', TRUE, TRUE);

		// 'directory.core'
		// Absolute path for the core framework directory.
		$config->set('directory.core', "{$directory}/src");

		// 'directory.application'
		// Absolute path for the application directory.
		$config->set('directory.application', "{$directory}/sample/application");
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}
// End of file: index.php
// Location: sample/index.php