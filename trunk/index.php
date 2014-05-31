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

		/**
		 * TODO: Figure out a way to loosen the coupling between controller and request, reponse, and routing.
		 * The controller needs to be testable, hence the mentioned objects needs to
		 * be injected to the controller and not instansiated within the controller.
		 *
		 * The action and view (and later model) will most likley have to be instansiated
		 * within the controller, since that is basically the purpose of the controller dispatch.
		 *
		 * TODO: Implement support for request and response data.
		 * The request data will be headers, post and get data, json data, etc. Based on the
		 * request data different arguments should be sent to the action. The action in turn
		 * will talk to the model and retrieve the response data.
		 *
		 * The reponse will need access to the request data aswell, since the accept headers
		 * will be located there and those values will determind which content type to send
		 * back to the user.
		 */

		/*
			array(13) {
				["profile"]=>"development"
				["context"]=>"web"

				["exception.template"]=>"%directory.core.template%/exception/plaintext.php"

				["directory.core"]=>"/var/www/ramverk/src"
				["directory.application"]=>"/var/www/ramverk/trunk"
				["directory.core.config"]=>"%directory.core%/config"
				["directory.core.library"]=>"%directory.core%/library"
				["directory.core.template"]=>"%directory.core%/template"
				["directory.application.cache"]=>"%directory.application%/cache"
				["directory.application.config"]=>"%directory.application%/config"
				["directory.application.library"]=>"%directory.application%/library"
				["directory.application.module"]=>"%directory.application%/module"
				["directory.application.template"]=>"%directory.application%/template"
			}
		*/

		// Retrieve the configuration container and the configuration handler factory.
		$config = $core->getContext()->getConfig();
		$factory = $core->getConfigurationHandlerFactory();


		$namespace['base'] = 'Me\\Raatiniemi\\Ramverk';
		$context = ucfirst(strtolower($config->get('context')));

		// Define the context based request, routing, and response classes with their namespaces.
		$class['rq'] = "{$namespace['base']}\\Request\\{$context}";
		$class['rd'] = "{$namespace['base']}\\Request\\{$context}\\Data";
		$class['routing'] = "{$namespace['base']}\\Routing\\{$context}";
		$class['response'] = "{$namespace['base']}\\Response\\{$context}";

		// Verify that each of the context based classes exists.
		foreach($class as $key => $value) {
			if(!class_exists($value)) {
				throw new \Exception("Context based '{$key}'-class do not exists");
			}
		}
		$reflection['rd'] = new \ReflectionClass($class['rd']);
		$rd = $reflection['rd']->newInstance();

		// TODO: Send arguments to request constructor?
		$reflection['rq'] = new \ReflectionClass($class['rq']);
		$rq = $reflection['rq']->newInstanceArgs(array($core->getContext(), $rd));

		// Retrieve the routing configuration.
		$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

		$reflection['routing'] = new \ReflectionClass($class['routing']);
		$routing = $reflection['routing']->newInstanceArgs(array($rq, $routes));
		$routing->parse();

		if(!$routing->hasRoute()) {
			// TODO: Initialize the module and action with the 404.
			// This way we can send the 404 page with the requested content type,
			// e.g. pages requested with application/json in the accept header will
			// receive the the 404 with application/json.
			throw new \Exception('Page not found');
		}

		$config->set('directory.module', "%directory.application.module%/{$routing->getModule()}");
		if(!is_dir($config->expandDirectives('%directory.module%'))) {
			throw new \Exception('Module do not exists');
		}

		// TODO: Better handling.
		$config->set('directory.module.config', '%directory.module%/config');
		$module = $config->expandDirectives('%directory.module.config%/module.xml');
		if(is_readable($module)) {
			// TODO: Prevent overrides and configuration directive collisions.
			$config->import($factory->callHandler('Module', $module));
		}

		// ---- Handle Action

		$name['action'] = $class['action'] = $routing->getAction();
		if($config->has('namespace')) {
			// TODO: Add support for custom action namespace.
			$class['action'] = sprintf('%s\\Action\\%s', $config->get('namespace'), $class['action']);
		}

		// TODO: Send arguments to action constructor?
		$reflection['action'] = new \ReflectionClass($class['action']);
		$action = $reflection['action']->newInstance();

		// TODO: Is the reflection really necessary?
		$method['action'] = $routing->getActionMethod($reflection['action']);

		// TODO: Migrate the request data handling.
		$arguments['action'] = array();
		if($method['action'] === 'executeWrite') {
			$validate = require 'validate.php';

			if(array_key_exists($routing->getModule(), $validate)) {
				$validate['module'] = $validate[$routing->getModule()];
				if(is_array($validate['module']) && array_key_exists($routing->getAction(), $validate['module'])) {
					$validate['action'] = $validate['module'][$routing->getAction()];

					$data = $rq->getRequestRawData();
					if(!empty($data)) {
						foreach($data as $index => $value) {
							// If the request data index do not exists within the validation
							// for the defined module/action then it should be removed from
							// the request data.
							//
							// The same goes for values that do not match the supplied regex.
							if(!array_key_exists($index, $validate['action'])) {
								unset($data[$index]);
							} else {
								$regex = isset($validate['action'][$index]['regex']) ? $validate['action'][$index]['regex'] : NULL;
								if(empty($regex) || !preg_match($regex, $value)) {
									unset($data[$index]);
								}
							}
						}

						// We can assign the remaining data to the action arguments.
						$arguments['action'] = $data;
					}
				}
			}
		}

		// Retrieve the view name.
		$name['view'] = call_user_func_array(array($action, $method['action']), $arguments['action']);
		if(!isset($name['view']) || !is_string($name['view'])) {
			// The action method have to return a name.
			// TODO: Write exception message.
			// TODO: Better specify the exception object.
			throw new \Exception('');
		}

		// ---- Handle View

		// TODO: Handle headers within the reponse.
		exit;

		$reflection['response'] = new \ReflectionClass($class['response']);
		$response = $reflection['response']->newInstance();

		// TODO: Accept will only be availabe within the Web context.
		$accepts = $response->getAccept();

		$name['view'] = $class['view'] = sprintf('%s%s', $name['action'], ucfirst(strtolower($name['view'])));
		if($config->has('namespace')) {
			// TODO: Add support for custom view namespace.
			$class['view'] = sprintf('%s\\View\\%s', $config->get('namespace'), $class['view']);
		}
		$reflection['view'] = new \ReflectionClass($class['view']);
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

			// TODO: Better handling for headers, add support for redirections etc.
			$method['view'] = sprintf('execute%s', ucfirst(strtolower($type)));
			if($reflection['view']->hasMethod($method['view'])) {
				// Send the content-type header back with the correct content type.
				header("Content-type: {$accept}");
				break;
			}
		}

		// If no method have been found for the view, use the default one.
		if(!isset($method['view'])) {
			$method['view'] = 'execute';
		}

		// TODO: Send arguments to view constructor?
		$view = $reflection['view']->newInstance();
		echo call_user_func_array(array($view, $method['view']), array());
	} catch(\Exception $e) {
		// Render thrown exceptions with the specified template.
		Ramverk\Exception::render($e, $config);
	}
}