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

		// To improve performance, use a non-development profile.
		// A development profile is formatted as: 'development(\..*)'
		// E.g. 'development' or 'development.developer'.
		// $config->set('profile', 'production');

		// Set the absolute path for the framework core and the application.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);
		$config->set('directory.application', "{$directory}/trunk", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);

		// ---- controller->dispatch() code.

		$controller = $core->getController();

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
			// TODO: No route have been found, handle it.
			// Initialize the module and action with the 404. This way we can
			// send the 404 page with the requested content type, e.g. pages
			// requested with application/json in the accept header will
			// receive the the 404 with application/json.
			throw new \Exception('Page not found');
		}

		// Setup the directory structure for the module.
		// TODO: Check that the module base directory actually exists.
		$config->set('directory.module', "%directory.application.module%/{$routing->getModule()}");
		$config->set('directory.module.action', '%directory.module%/action');
		$config->set('directory.module.config', '%directory.module%/config');
		$config->set('directory.module.view', '%directory.module%/view');

		// Check if module specific configuration is available.
		$module = $config->expandDirectives('%directory.module.config%/module.xml');
		if(is_readable($module)) {
			$config->import($factory->callHandler('Module', $module));
		}

		// TODO: Check if application should use namespaces.
		// If the application should use namespaces there are a couple of requirements.
		// 1.	The action, view, and model (etc.) have to be located with
		// 		their respective namespaces. These are predefined namespaces.
		// 2.	Coming soon...
		// $useNamespace = $config->get('application.namespace.enabled', true);

		// Attempt to retrieve the module namespace, with fallback to the
		// application wide namespace, if any is available.
		$namespace['module'] = $config->get('module.namespace', $config->get('application.namespace'));
		$controller->setClass('action', "{$namespace['module']}\\Action\\{$routing->getAction()}");

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