<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Base functionality for the controller.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Controller
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Configuration\Utility;
		use Loader\Autoload;

		/**
		 * Context for the application.
		 * @var Net\TheDeveloperBlog\Ramverk\Core\Context
		 */
		protected $_context;

		/**
		 * Handles requests.
		 * @var Net\TheDeveloperBlog\Ramverk\Request
		 */
		protected $_request;

		/**
		 * Initialize the controller.
		 * @param Net\TheDeveloperBlog\Ramverk\Core\Context $context Context for the application
		 * @param Net\TheDeveloperBlog\Ramverk\Request $request Handles requests.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Core\Context $context, Request $request)
		{
			$this->_context = $context;
			$this->_request = $request;
		}

		/**
		 * Retrieve the application context.
		 * @return Net\TheDeveloperBlog\Ramverk\Net\TheDeveloperBlog\Ramverk\Core\Context
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getContext()
		{
			return $this->_context;
		}

		/**
		 * Retrieve the request object.
		 * @return Net\TheDeveloperBlog\Ramverk\Net\TheDeveloperBlog\Ramverk\Request
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getRequest()
		{
			return $this->_request;
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this->_context->getConfig();
		}

		/**
		 * Retrieve the configuration handler factory, used by Autoload-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Config\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfigurationHandlerFactory()
		{
			return $this->_context->getConfigurationHandlerFactory();
		}

		/**
		 * Initialize the module.
		 * @throws Net\TheDeveloperBlog\Ramverk\Exception If module do not exists.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Module configuration.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function initializeModule()
		{
			// Initialize the configuration container for the module.
			$config = new Configuration\Container();
			$config->set('module.name', ucfirst(strtolower($this->getRequest()->getModule())));

			// Check that the module directory actually exists.
			$directory = $this->expandDirectives("%directory.application.module%/{$config->get('module.name')}");
			if(!is_dir($directory)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf('Module "%s" do not exists.', $config->get('module.name')));
			}

			// Setup the module autoload configuration, if available.
			$autoload = "{$directory}/config/autoload.xml";
			if(file_exists($autoload)) {
				$this->_autoloadFile = $autoload;
				spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);
			}

			// Retrieve the module configuration, if available.
			$module = "{$directory}/config/module.xml";
			if(file_exists($module)) {
				$items = $this->getConfigurationHandlerFactory()->callHandler('Module', $module);
				$config->import($items);
			}

			return $config;
		}

		/**
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 * @todo Handle disabled modules.
		 */
		public function dispatch()
		{
			$config = $this->initializeModule();

			$config->set('action.name', ucfirst(strtolower($this->getRequest()->getAction())));

			// TODO: Implement support for namespace fallback.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.

			$config->set('action.class', $config->get('action.name'));
			if($config->has('module.namespace')) {
				$config->set('action.class', $config->expandDirectives('%module.namespace%\\%module.name%\\Action\\%action.name%'), TRUE);
			}

			$action['reflection'] = new \ReflectionClass($config->get('action.class'));
			$action['action'] = $this->getRequest()->getMethod() === 'POST' ? 'Write' : 'Read';

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

			$config->set('view.name', sprintf(
				'%s%s',
				$config->get('action.name'),
				call_user_func_array(array($action['instance'], $action['method']), array())
			));

			// TODO: Implement support for namespace fallback.
			// If no namespace have been supplied for the specific module, the
			// application namespace should be used. If no namespace have been
			// supplied for the application, global namespace should be used.

			$config->set('view.class', $config->get('view.name'));
			if($config->has('module.namespace')) {
				$config->set('view.class', $config->expandDirectives('%module.namespace%\\%module.name%\\View\\%view.name%'), TRUE);
			}

			$view['reflection'] = new \ReflectionClass($config->get('view.class'));

			// TODO: Implement support for content types.
			// TODO: Handle content type from routing.
			$headers = $this->getRequest()->getHttpHeaders();
			if(isset($headers['content-type'])) {
				$headers['content-type'] = strtolower($headers['content-type']);

				switch($headers['content-type']) {
					case 'application/json':
						$contentType = 'json';
						break;
					case 'text/html':
					default:
						$contentType = 'html';
						break;
				}

				$view['methods'][] = sprintf('execute%s', ucfirst(strtolower($contentType)));
			}
			$view['methods'][] = $view['method'] = 'execute';

			foreach($view['methods'] as $method) {
				if($view['reflection']->hasMethod($method)) {
					$view['method'] = $method;
					break;
				}
			}

			// TODO: Pass arguments to the view constructor.
			$view['instance'] = $view['reflection']->newInstance();
			call_user_func_array(array($view['instance'], $view['method']), array());

			// TODO: Handle loading of template for content type HTML.
		}
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php