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
	abstract class Controller
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
			$config->set('module.name', ucfirst(strtolower($this->_request->getModule())));

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
		 */
		abstract public function dispatch();
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php