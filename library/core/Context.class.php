<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Core
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;
	use Net\TheDeveloperBlog\Ramverk\Configuration\Handler;
	use Net\TheDeveloperBlog\Ramverk\Controller;

	/**
	 * Handles the context for the application.
	 *
	 * @package Ramverk
	 * @subpackage Core
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Context
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Configuration\Utility;

		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Container.
		 */
		protected $_config;

		/**
		 * Configuration handler factory.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Factory
		 */
		protected $_configurationHandlerFactory;

		/**
		 * Application context controller.
		 * @var Net\TheDeveloperBlog\Ramverk\Controller
		 */
		protected $_controller;

		/**
		 * Initialize the application context.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Configuration\Container $config)
		{
			$this->_config = $config;
		}

		/**
		 * Retrieve configuration container.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration Container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this->_config;
		}

		/**
		 * Retrieve the factory for configuration handlers.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfigurationHandlerFactory()
		{
			// Check if the configuration handler factory already have been instansiated.
			if($this->_configurationHandlerFactory === NULL) {
				// Retrieve the application profile and context.
				$profile = $this->getConfig()->get('profile');
				$context = $this->getConfig()->get('context');

				// When application is running with a development profile the
				// cache should be cleared every request.
				if(preg_match('/(development(\.[a-z0-9]+)?)/i', $profile)) {
					$directory = $this->getConfig()->get('directory.application.cache');
					$directory = $this->expandDirectives($directory);

					// Loop though each of the files within the cache directory.
					foreach(new \DirectoryIterator($directory) as $file) {
						// Dot files (i.e. ".." and ".") should not be included.
						if($file->isDot()) {
							continue;
						}

						// Check that the path name match the expected patterns,
						// we don't want to remove e.g. the .gitignore file.
						if(preg_match('/([a-z0-9\.\_]+)\.php/i', $file->getPathname())) {
							unlink($file->getPathname());
						}
					}
				}

				// Instansiate the cache and parser for the factory.
				$cache = new Handler\Cache($profile, $context);
				$parser = new Handler\Parser($this->getConfig(), $profile, $context);

				// Instansiate the configuration handler factory.
				$this->_configurationHandlerFactory = new Handler\Factory($this->getConfig(), $cache, $parser);
			}
			return $this->_configurationHandlerFactory;
		}

		/**
		 * Retrieve the context controller.
		 * @return Net\TheDeveloperBlog\Ramverk\Controller Context controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getController()
		{
			// Check if the context controller already have been instansiated.
			if($this->_controller === NULL) {
				$context = ucfirst($this->getConfig()->get('context'));
				$namespace = 'Net\\TheDeveloperBlog\\Ramverk';

				// -- Routing
				$routing['base'] = "{$namespace}\\Request\\Routing";
				$routing['name'] = "{$routing['base']}\\{$context}";

				if(!class_exists($routing['name'])) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Routing for context "%s" do not exists.',
						$context
					));
				}

				$routing['reflection'] = new \ReflectionClass($routing['name']);
				if(!$routing['reflection']->isSubclassOf($routing['base'])) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Routing for context "%s" do not extend "%s".',
						$context,
						$routing['base']
					));
				}

				// Retrieve the routing configuration.
				$filename = '%directory.application.config%/routing.xml';
				$routes = $this->getConfigurationHandlerFactory()->callHandler('Routing', $filename);
				if(empty($routes)) {
					// TODO: Better specify the Exception-object.
					throw new Exception('No routing configuration have been specified.');
				}

				$routing['instance'] = $routing['reflection']->newInstanceArgs($routes);

				// -- Request
				$request['base'] = "{$namespace}\\Request";
				$request['name'] = "{$request['base']}\\{$context}";

				if(!class_exists($request['name'])) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Request for context "%s" do not exists.',
						$context
					));
				}

				$request['reflection'] = new \ReflectionClass($request['name']);
				if(!$request['reflection']->isSubclassOf($request['base'])) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Request for context "%s" do not extend "%s".',
						$context,
						$request['base']
					));
				}

				$request['instance'] = $request['reflection']->newInstance();

				// -- Controller
				$controller['base'] = "{$namespace}\\Controller";
				$controller['name'] = "{$controller['base']}\\{$context}";

				if(!class_exists($controller['name'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Controller for context "%s" do not exists.',
						$context
					));
				}

				$controller['reflection'] = new \ReflectionClass($controller['name']);
				if(!$controller['reflection']->isSubclassOf($controller['base'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Controller for context "%s" do not extend "%s".',
						$context,
						$controller['base']
					));
				}

				$arguments = array($this, $request['instance']);
				$this->_controller = $controller['reflection']->newInstanceArgs($arguments);
			}
			return $this->_controller;
		}
	}
}
// End of file: Context.class.php
// Location: library/core/Context.class.php