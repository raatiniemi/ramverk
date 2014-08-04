<?php
namespace Me\Raatiniemi\Ramverk\Trunk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Configuration as Config;
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
		$config = new Config();

		// To improve performance, use a non-development profile.
		// A development profile is formatted as: 'development(\..*)'
		// E.g. 'development' or 'development.developer'.
		// $config->set('profile', 'production');

		// Set the absolute path for the framework core and the application.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);
		$config->set('directory.application', "{$directory}/trunk", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);
		$controller = $core->getController();
		$controller->dispatch();

		// ---- controller->dispatch() code.

		// Retrieve the configuration handler factory.
		$factory = $controller->getConfigurationHandlerFactory();

		// Setup the base namespace for the framework and the context name.
		// Since the context name will represent certain elements of the
		// structure it has to be formated accordingly, i.e. first letter in
		// uppercase followed by lowercase.
		$namespace['base'] = 'Me\\Raatiniemi\\Ramverk';
		$context = ucfirst(strtolower($config->get('context')));

		$controller->setClass('request', "{$namespace['base']}\\Request\\{$context}");
		$controller->setClass('request.data', "{$namespace['base']}\\Request\\{$context}\\Data");
		$controller->setClass('routing', "{$namespace['base']}\\Routing\\{$context}");

		// TODO: Retrieve the application configuration.
		// The application configuration will contain the application base
		// namespace, key for retrieving the uri, etc.

		// Create new instance for the context based request data container.
		$data = $controller->createInstance('request.data');

		// Create new instance for the context based request.
		$request = $controller->createInstance('request', array($core->getContext(), $data));

		// Retrieve the application specific routing configuration.
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		// Create new instance for the context based routing.
		$routing = $controller->createInstance('routing', array($request, $routes));

		// If a route have been found the 'parse'-method will return 'true'.
		if(!$routing->parse()) {
			// Since no route could be found we have to use the 404 route.
			// This way we can deliver different responses depending on the accepted content-type.
			$routing->setModule($config->get('actions.404_module'));
			$routing->setAction($config->get('actions.404_action'));
		}

		// Now that we've found the right module we can initialize it.
		$controller->initializeModule($routing->getModule());

		// Check whether the module should use namespaces.
		// If namespaces are going to be used, every class have to be located under the namespace.
		if(($shouldNamespace = $config->get('module.namespace.enabled', $config->get('core.namespace.enabled', TRUE)))) {
			// Attempt to retrieve the module specific namespace, if it has
			// been defined, otherwise use the core with the module name as suffix.
			$namespace['module'] = $config->get('module.namespace');
			if(empty($namespace['module']) && $config->has('core.namespace')) {
				$namespace['module'] = "{$config->get('core.namespace')}\\{$routing->getModule()}";
			}
		}

		// Check if namespaces should be used and if we have a module namespace available.
		// If namespaces are used, the action have to be located under the "$module\Action"-namespace.
		$prefix = ($shouldNamespace && isset($namespace['module'])) ? "{$namespace['module']}\\Action\\" : NULL;
		$controller->setClass('action', "{$prefix}{$routing->getAction()}");

		// Retrieve the instance for the requested action.
		$action = $controller->createInstance('action');
		$method = $routing->getActionMethod($controller->getReflection('action'));

		// If the method is 'executeWrite', i.e. the request is post and the
		// action have a write method defined. No need to parse the request
		// data if the request is post but no write method is available.
		if($method === 'executeWrite') {
		}

		// TODO: How should the request data be passed to the action?
		// Should everything be merged with $routing->getParams() or should
		// the data be separated, i.e. URI data and POST data?

		// Execute the action method.
		call_user_func_array(array($action, $method), array($routing->getParams()));
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}

	// End the performace timer and print the results.
	$time['end'] = microtime(true);
	printf('Execution took %f seconds', ($time['end'] - $time['start']));
}