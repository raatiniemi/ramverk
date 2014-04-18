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

		$config = $core->getContext()->getConfig();

		// TODO: Implement support for context aware request, response, and routing.

		$request = new Request\Web();
		$routing = new Routing\Web($request, $routes);
		$route = $routing->parse();

		// TODO: Do not return the route, instead use $routing->hasRoute();
		if(!isset($route['module']) || !isset($route['action'])) {
			// TODO: Initialize the module and action with the 404.
			// This way we can send the 404 page with the requested content type,
			// e.g. pages requested with application/json in the accept header will
			// receive the the 404 with application/json.
			throw new \Exception('Page not found');
		}

		$config->set('directory.module', "%directory.application.module%/{$route['module']}");
		if(!is_dir($config->expandDirectives('%directory.module%'))) {
			throw new \Exception('Module do not exists');
		}

		$config->set('directory.module.config', '%directory.module%/config');
		$module = $config->expandDirectives('%directory.module.config%/module.xml');
		if(is_readable($module)) {
			// TODO: Prevent overrides and configuration directive collisions.
			$config->import($factory->callHandler('Module', $module));
		}

		// ---- Handle Action

		$action['name'] = $action['class'] = $route['action'];
		if($config->has('namespace')) {
			$action['class'] = sprintf('%s\\Action\\%s', $config->get('namespace'), $action['class']);
		}

		$action['reflection'] = new \ReflectionClass($action['class']);
		$action['method'] = $routing->getActionMethod($action['reflection']);

		// TODO: Arguments to action?
		$action['instance'] = $action['reflection']->newInstance();

		$action['arguments'] = array();
		if($action['method'] === 'executeWrite') {
			$validate = require 'validate.php';

			if(array_key_exists($route['module'], $validate)) {
				$module['validate'] = $validate[$route['module']];
				if(is_array($module['validate']) && array_key_exists($route['action'], $module['validate'])) {
					$action['validate'] = $module['validate'][$route['action']];

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
		$view['name'] = call_user_func_array(array($action['instance'], $action['method']), $action['arguments']);
		if(!isset($view['name']) || !is_string($view['name'])) {
			// The action method have to return a name.
			// TODO: Write exception message.
			// TODO: Better specify the exception object.
			throw new Exception('');
		}

		// ---- Handle View

		$response = new Response\Web();
		$accepts = $response->getAccept();

		$view['name'] = $view['class'] = sprintf('%s%s', $action['name'], ucfirst(strtolower($view['name'])));
		if($config->has('namespace')) {
			$view['class'] = sprintf('%s\\View\\%s', $config->get('namespace'), $view['class']);
		}
		$view['reflection'] = new \ReflectionClass($view['class']);
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