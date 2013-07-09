<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Controller
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Functionality for the web based controller.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Ramverk\Controller
	{
		/**
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 * @todo Handle disabled modules.
		 */
		public function dispatch()
		{
			$moduleName = ucfirst(strtolower($this->_request->getModule()));
			$actionName = ucfirst(strtolower($this->_request->getAction()));

			$directory = $this->expandDirectives("%directory.application.module%/{$moduleName}");
			if(!is_dir($directory)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception("Module \"{$moduleName}\" do not exists.");
			}

			$autoload = "{$directory}/config/autoload.xml";
			if(file_exists($autoload)) {
				$this->_autoloadFile = $autoload;
				spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);
			}

			$config = new Configuration\Container();

			$module = "{$directory}/config/module.xml";
			if(file_exists($module)) {
				$items = $this->getConfigurationHandlerFactory()->callHandler('Module', $module);
				$config->import($items);
			}

			// TODO: Handle fallback namespace from the application.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.
			$namespace = $config->get('namespace');

			if($namespace !== NULL) {
				$actionName = "{$namespace}\\{$moduleName}\\Action\\{$actionName}";
			}

			$action['reflection'] = new \ReflectionClass($actionName);
			$action['action'] = $this->_request->getMethod() === 'POST' ? 'Write' : 'Read';

			// TODO: Handle content types.
			$action['methods'][] = sprintf('execute%s', $action['action']);
			$action['methods'][] = $action['method'] = 'execute';

			foreach($action['methods'] as $method) {
				if($action['reflection']->hasMethod($method)) {
					$action['method'] = $method;
					break;
				}
			}

			// TODO: Pass arguments to the action constructor.
			$action['instance'] = $action['reflection']->newInstance();

			// Retrieve the view name from the action.
			$viewName = call_user_func_array(array($action['instance'], $action['method']), array());

			if($namespace !== NULL) {
				$viewName = "{$namespace}\\{$moduleName}\\View\\{$viewName}";
			}

			$view['reflection'] = new \ReflectionClass($viewName);

			// TODO: Handle content types.
			$view['methods'][] = $view['method'] = 'execute';

			foreach($view['methods'] as $method) {
				if($view['reflection']->hasMethod($method)) {
					$view['method'] = $method;
					break;
				}
			}

			// TODO: Pass arguments to the view constructor.
			// TODO: Load template.
			$view['instance'] = $view['reflection']->newInstance();
			call_user_func_array(array($view['instance'], $view['method']), array());
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php