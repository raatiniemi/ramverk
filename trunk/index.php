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

		// TODO: Retrieve the application configuration.
		// The application configuration will contain the application base
		// namespace, key for retrieving the uri, etc.

		// Create new instance for the context based request data container.
		$class['rd'] = "{$namespace['base']}\\Request\\{$context}\\Data";
		$reflection['rd'] = new \ReflectionClass($class['rd']);
		$rd = $reflection['rd']->newInstance();

		// Create new instance for the context based request.
		$class['rq'] = "{$namespace['base']}\\Request\\{$context}";
		$reflection['rq'] = new \ReflectionClass($class['rq']);
		$rq = $reflection['rq']->newInstanceArgs(array($core->getContext(), $rd));

		// Retrieve the application specific routing configuration.
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		// Create new instance for the context based routing.
		$class['ro'] = "{$namespace['base']}\\Routing\\{$context}";
		$reflection['ro'] = new \ReflectionClass($class['ro']);
		$ro = $reflection['ro']->newInstanceArgs(array($rq, $routes));

		var_dump($ro->parse());
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}