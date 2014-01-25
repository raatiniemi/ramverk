<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Functionality for dispatching actions.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Controller
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Loader\Autoload;

		protected $_context;

		public function __construct(Core\Context $context)
		{
			$this->_context = $context;
		}

		// TODO: Move to Routing class.
		protected $_route;

		public function dispatch()
		{
			$factory = $this->getConfigurationHandlerFactory();
			$routes = $factory->callHandler('Routing', '%directory.application.config%/routing.xml');

			// TODO: Within the Routing class include these methods,
			// getModule/getAction with ucfirst and strtolower convertion.
			// method should be handled within the controller dispatch.
			$uri = isset($_GET['uri']) ? $_GET['uri'] : '';
			foreach($routes as $route) {
				if(preg_match("#{$route['pattern']}#", $uri)) {
					$this->_route = $route;
					break;
				}
			}

			// If no route have been found.
			// TODO: check against the routing class, hasRoute() which should return a boolean.
			if($this->_route === NULL) {
				throw new Exception('#404 - No route have been found.');
			}

			// Retrieve the absolute path for the module directory.
			$config = array();
			$config['directory'] = $this->getConfig()->expandDirectives('%directory.application.module%');
			$config['directory'] = sprintf('%s/%s', $config['directory'], ucfirst($this->_route['module']));

			// Verify that the module directory do exists.
			if(!is_dir($config['directory'])) {
				// TODO: Write exception message.
				// TODO: Better specify the exception object.
				throw new Exception('');
			}

			// Check if the module configuration exists. If it does, then we
			// need to retrieve the configuration data.
			$module = sprintf('%s/config/module.xml', $config['directory']);
			if(file_exists($module)) {
				$config = array_merge($config, $factory->callHandler('Module', $module));
			}

			// Check if the module autoload configuration exists. If it does,
			// then we need to register the controllers autoloader.
			$autoload = sprintf('%s/config/autoload.xml', $config['directory']);
			if(file_exists($autoload)) {
				// Register the controllers autoloader.
				$this->setAutoloadFile($autoload);
				spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);
			}

			$method = strtolower($_SERVER['REQUEST_METHOD']) === 'post' ? 'write' : 'read';
			$action['method'] = sprintf('execute%s', ucfirst($method));

			// If we have a module namespace available, we have to prepend it to
			// the action class, otherwise we won't find the class.
			$action['name'] = $action['class'] = $route['action'];
			if(isset($config['namespace'])) {
				$action['class'] = sprintf('%s\\Action\\%s', $config['namespace'], ucfirst(strtolower($action['class'])));
			}

			// Checking that the action class have the specified method, otherwise
			// fallback to the default execute method.
			$action['reflection'] = new \ReflectionClass($action['class']);
			if(!$action['reflection']->hasMethod($action['method'])) {
				$action['method'] = 'execute';
			}

			// Instansiate the action class.
			// TODO: Do we need to pass any arguments to the action constructor?
			$action['instance'] = $action['reflection']->newInstance();

			// Execute the action method, and retrieve the view name.
			$view['name'] = call_user_func_array(array($action['instance'], $action['method']), array());
			if(!isset($view['name']) || !is_string($view['name'])) {
				// The action method have to return a name.
				// TODO: Write exception message.
				// TODO: Better specify the exception object.
				throw new Exception('');
			}

			// If we have a module namespace available, we have to prepend it to
			// the view class, otherwise we won't find the class.
			$view['name'] = $view['class'] = sprintf('%s%s', $action['name'], $view['name']);
			if(isset($config['namespace'])) {
				$view['class'] = sprintf('%s\\View\\%s', $config['namespace'], ucfirst(strtolower($view['class'])));
			}

			// TODO: Initialize the view.
		}

		protected function getConfig()
		{
			return $this->_context->getConfig();
		}

		protected function getConfigurationHandlerFactory()
		{
			return $this->_context->getConfigurationHandlerFactory();
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php