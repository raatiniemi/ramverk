<?php
namespace Me\Raatiniemi\Ramverk\Trunk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Request;
	use Me\Raatiniemi\Ramverk\Response;
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
		$config = new Configuration\Container();

		// Set the absolute path for the framework core and the application.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);
		$config->set('directory.application', "{$directory}/trunk", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);

		// ---- controller->dispatch() code.

		// Retrieve the configuration container and the configuration handler factory.
		$config = $core->getContext()->getConfig();
		$factory = $core->getConfigurationHandlerFactory();

		// Setup the base namespace for the framework and the context name.
		// Since the context name will represent certain elements of the
		// structure it has to be formated accordingly, i.e. first letter in
		// uppercase followed by lowercase.
		$namespace['base'] = 'Me\\Raatiniemi\\Ramverk';
		$context = ucfirst(strtolower($config->get('context')));

		$reflection = array();
		$newInstance = function($name, $class, array $arguments=array()) {
			global $reflection;

			if(!isset($reflection[$name])) {
				$reflection[$name] = new \ReflectionClass($class);
			}

			return $reflection[$name]->newInstanceArgs($arguments);
		};

		// TODO: Retrieve the application configuration.
		// The application configuration will contain the application base
		// namespace, key for retrieving the uri, etc.

		// Create new instance for the context based request data container.
		$class['rd'] = "{$namespace['base']}\\Request\\{$context}\\Data";
		$rd = $newInstance('rd', $class['rd']);

		// Create new instance for the context based request.
		$class['rq'] = "{$namespace['base']}\\Request\\{$context}";
		$rq = $newInstance('rq', $class['rq'], array($core->getContext(), $rd));

		// Retrieve the application specific routing configuration.
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		// Create new instance for the context based routing.
		$class['ro'] = "{$namespace['base']}\\Routing\\{$context}";
		$ro = $newInstance('ro', $class['ro'], array($rq, $routes));

		// If a route have been found the 'parse'-method will return 'true'.
		if(!$ro->parse()) {
			// TODO: No route have been found, handle it.
		}
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}

	// End the performace timer and print the results.
	$time['end'] = microtime(true);
	printf('Execution took %f seconds', ($time['end'] - $time['start']));
}