<?php
try {
	// Include the available Action-classes.
	require 'action/Action.class.php';
	require 'action/Index.class.php';
	require 'action/Login.class.php';
	require 'action/Logout.class.php';

	// Include the available View-classes.
	require 'view/View.class.php';
	require 'view/IndexSuccess.class.php';
	require 'view/LogoutSuccess.class.php';
	require 'view/LoginForm.class.php';
	require 'view/LoginSuccess.class.php';
	require 'view/LoginFailure.class.php';

	// Retrieve the request URI.
	$uri = isset($_GET['uri']) ? trim($_GET['uri'], '/') : NULL;

	// Require the routing configurations.
	$routes = require 'routes.php';
	foreach($routes as $route) {
		if(preg_match("#{$route['pattern']}#i", $uri)) {
			$module['name'] = ucfirst(strtolower($route['module']));
			$action['name'] = ucfirst(strtolower($route['action']));

			break;
		}
	}

	if(!isset($module) || !isset($action)) {
		// TODO: Initialize the module and action with the 404.
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
	echo call_user_func_array(array($view['instance'], $view['method']), array());
} catch(Exception $e) {
	echo $e->getMessage();
	exit($e->getCode());
}