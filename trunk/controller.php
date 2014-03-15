<?php
try {
	// Include the available Action-classes.
	require 'action/Action.class.php';
	require 'action/Index.class.php';
	require 'action/Login.class.php';
	require 'action/Logout.class.php';
	require 'action/Profile.class.php';

	// Include the available View-classes.
	require 'view/View.class.php';
	require 'view/IndexSuccess.class.php';
	require 'view/LogoutSuccess.class.php';
	require 'view/LoginForm.class.php';
	require 'view/LoginSuccess.class.php';
	require 'view/LoginFailure.class.php';
	require 'view/ProfileSuccess.class.php';

	// Retrieve the request URI.
	$uri = isset($_GET['uri']) ? trim($_GET['uri'], '/') : NULL;

	// Require the routing configurations.
	$routes = require 'routes.php';
	foreach($routes as $route) {
		$parameters = array();

		// Build the rexex used to match arguments routes.
		// The key will be used to match the value within the argument container.
		$regex = sprintf(
			'/\{{1}%1$s%2$s\}{1}/i',
			'([a-z]+)\:',			// key,		e.g. id:
			'([\\a-z\+\*\(\)\?]+)'	// value,	e.g. (\d+)
		);

		if(preg_match_all($regex, $route['pattern'], $matches)) {
			// Only attempt to extract the route parameters if we have all of the
			// necessary data, i.e. the matched values, the keys, and the patterns.
			if(isset($matches[0], $matches[1], $matches[2])) {
				$matched = $matches[0];
				$keys = $matches[1];
				$patterns = $matches[2];

				// Iterate through each of the matched parameters.
				foreach($matched as $index => $match) {
					$parameters[] = $keys[$index];

					// Replace the parameter syntax with the actual regex for the parameter.
					// Otherwise, we'll not be able to match the route against the URI.
					$route['pattern'] = str_replace($match, "({$patterns[$index]})", $route['pattern']);
				}
			}
		}

		if(preg_match("#{$route['pattern']}#i", $uri, $matches)) {
			$module['name'] = ucfirst(strtolower($route['module']));
			$action['name'] = ucfirst(strtolower($route['action']));
			$action['params'] = array();

			// If the route have possible parameters we have to extract these
			// to the parameters index for the action.
			if(!empty($parameters)) {
				$data = array();
				foreach($parameters as $index => $name) {
					// The key have to be incremented with one to exclude the
					// matches within the regex. We only want the actual values.
					$key = $index + 1;
					if(array_key_exists($key, $matches)) {
						// If the value exists, assign it to the data container.
						//
						// Depending on the syntax of the regex we might include
						// the URI separators (i.e. slashes), these have to be
						// stripped from the actual value.
						$data[$name] = trim($matches[$key], '/');
					}
				}
				$action['params'] = $data;
			}

			break;
		}
	}

	if(!isset($module) || !isset($action)) {
		// TODO: Initialize the module and action with the 404.
		// This way we can send the 404 page with the requested content type,
		// e.g. pages requested with application/json in the accept header will
		// receive the the 404 with application/json.
		throw new Exception("Page not found");
	}

	// ---- Handle Action

	$method = strtolower($_SERVER['REQUEST_METHOD']) === 'post' ? 'write' : 'read';
	$action['method'] = sprintf('execute%s', ucfirst($method));

	$action['reflection'] = new ReflectionClass($action['name']);
	if(!$action['reflection']->hasMethod($action['method'])) {
		$action['method'] = 'execute';
	}

	$action['instance'] = $action['reflection']->newInstance();

	// Retrieve the view name.
	$view['name'] = sprintf(
		'%s%s',
		$action['name'],
		call_user_func_array(
			array($action['instance'], $action['method']),
			array()
		)
	);

	// ---- Handle View

	$headers = array_change_key_case(getallheaders());

	// Check if the accept header have been supplied, otherwise fallback to "text/html".
	// TODO: Allow for different fallback depending on output and context configuration.
	$accept = isset($headers['accept']) ? $headers['accept'] : 'text/html';
	$accepts = explode(',', $accept);
	unset($accept);

	// TODO: Implement support for quality marker, i.e. q=0.6.
	$view['reflection'] = new \ReflectionClass($view['name']);
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
	echo call_user_func_array(array($view['instance'], $view['method']), array($action['params']));
} catch(Exception $e) {
	echo $e->getMessage();
	exit($e->getCode());
}