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

		$classes = array();
		$classes['request'] = "{$namespace['base']}\\Request\\{$context}";
		$classes['data'] = "{$namespace['base']}\\Request\\{$context}\\Data";
		$classes['routing'] = "{$namespace['base']}\\Routing\\{$context}";

		$reflection = array();
		function createReflectionInstance($name, array $arguments=array()) {
			global $reflection, $classes;

			if(!isset($reflection[$name])) {
				if(!isset($classes[$name])) {
					throw new \Exception(sprintf(
						'Class for name "%s" is not registered',
						$name
					));
				}
				$class = $classes[$name];

				if(!class_exists($class)) {
					throw new \Exception(sprintf(
						'Class "%s" can not be found',
						$name
					));
				}

				$reflection[$name] = new \ReflectionClass($class);
			}

			return $reflection[$name]->newInstanceArgs($arguments);
		}

		// TODO: Retrieve the application configuration.
		// The application configuration will contain the application base
		// namespace, key for retrieving the uri, etc.

		// Create new instance for the context based request data container.
		$data = createReflectionInstance('data');

		// Create new instance for the context based request.
		$request = createReflectionInstance('request', array($core->getContext(), $data));

		// Retrieve the application specific routing configuration.
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		// Create new instance for the context based routing.
		$routing = createReflectionInstance('routing', array($request, $routes));

		// If a route have been found the 'parse'-method will return 'true'.
		if(!$routing->parse()) {
			// TODO: No route have been found, handle it.
			// Initialize the module and action with the 404. This way we can
			// send the 404 page with the requested content type, e.g. pages
			// requested with application/json in the accept header will
			// receive the the 404 with application/json.
			throw new \Exception('Page not found');
		}

		// -- setupModule code

		// Setup the directory structure for the module.
		// TODO: Check that the module base directory actually exists.
		$config->set('directory.module', "%directory.application.module%/{$routing->getModule()}");
		$config->set('directory.module.action', '%directory.module%/action');
		$config->set('directory.module.config', '%directory.module%/config');
		$config->set('directory.module.view', '%directory.module%/view');

		var_dump($config->export());
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}

	// End the performace timer and print the results.
	$time['end'] = microtime(true);
	printf('Execution took %f seconds', ($time['end'] - $time['start']));
}