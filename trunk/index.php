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

		// Absolute path for the core framework.
		$config->set('directory.core', "{$directory}/src", FALSE, TRUE);

		// Absolute path for the application.
		$config->set('directory.application', "{$directory}/trunk", FALSE, TRUE);

		// Initialize the framework core.
		$core = new Ramverk\Core($config);

		// ---- Controller code.

		$factory = $core->getConfigurationHandlerFactory();
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		// TODO: Implement support for context aware request, response, and routing.

		$request = new Request\Web();
		$routing = new Routing\Web($request, $routes);
		$route = $routing->parse();

		if(!isset($route['module']) || !isset($route['action'])) {
			// TODO: Initialize the module and action with the 404.
			// This way we can send the 404 page with the requested content type,
			// e.g. pages requested with application/json in the accept header will
			// receive the the 404 with application/json.
			throw new \Exception("Page not found");
		}

		// ---- Handle Action

		// TODO: Handle application/module namespace.
		$action['reflection'] = new \ReflectionClass("Me\\Raatiniemi\\Ramverk\\Trunk\\Action\\{$route['action']}");
		$action['method'] = $routing->getActionMethod($action['reflection']);

		$action['instance'] = $action['reflection']->newInstance();

		$action['arguments'] = array();
		if($action['method'] === 'executeWrite') {
			$validate = require 'validate.php';

			if(array_key_exists($module['name'], $validate)) {
				$module['validate'] = $validate[$module['name']];
				if(array_key_exists($action['name'], $module['validate'])) {
					$action['validate'] = $module['validate'][$action['name']];

					$data = $request->getRequestRawData();
					if(!empty($data)) {
						foreach($data as $index => $value) {
							// If the request data index do not exists within the validation
							// for the defined module/action then it should be removed from
							// the request data.
							//
							// The same goes for values that do not match the supplied regex.
							if(!array_key_exists($index, $action['validate'])) {
								unset($data[$index]);
							} else {
								$regex = isset($action['validate'][$index]['regex']) ? $action['validate'][$index]['regex'] : NULL;
								if(empty($regex) || !preg_match($regex, $value)) {
									unset($data[$index]);
								}
							}
						}

						// We can assign the remaining data to the action arguments.
						$action['arguments'] = $data;
					}
				}
			}
		}

		// Retrieve the view name.
		$view['name'] = sprintf(
			'%s%s',
			$route['action'],
			call_user_func_array(
				array($action['instance'], $action['method']),
				array($action['arguments'])
			)
		);

		// ---- Handle View

		$response = new Response\Web();
		$accepts = $response->getAccept();

		// TODO: Handle application/module namespace.
		$view['reflection'] = new \ReflectionClass("Me\\Raatiniemi\\Ramverk\\Trunk\\View\\{$view['name']}");
		foreach($accepts as $accept) {
			$accept = strtolower($accept);

			switch($accept) {
				case 'application/json':
					$type = 'json';
					break;
				case 'text/html':
				default:
					$type = 'html';
					break;
			}

			$method = sprintf('execute%s', ucfirst(strtolower($type)));
			if($view['reflection']->hasMethod($method)) {
				// Send the content-type header back with the correct content type.
				header("Content-type: {$accept}");
				$view['method'] = $method;
				break;
			}
		}

		// If no method have been found for the view, use the default one.
		if(!isset($view['method'])) {
			$view['method'] = 'execute';
		}

		$view['instance'] = $view['reflection']->newInstance();
		echo call_user_func_array(array($view['instance'], $view['method']), array($route['params']));
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}