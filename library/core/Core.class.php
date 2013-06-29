<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk\Config\Handler;

	/**
	 * @package Ramverk
	 * @subpackage Core
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	final class Core
	{
		/**
		 * Classes available for autoloading.
		 * @var array
		 */
		private $_autoloads;

		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Config
		 */
		private $_config;

		/**
		 * Configuration handler factory.
		 * @var Net\TheDeveloperBlog\Ramverk\Config\Handler\Factory
		 */
		private $_handlerFactory;

		/**
		 * Application controller.
		 * @var Net\TheDeveloperBlog\Ramverk\Controller
		 */
		private $_controller;

		/**
		 * Initialize the ramverk core.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Config $config)
		{
			// Register and prepend the method for handling class autoloading.
			spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);

			// TODO: Register the default exception handler.
			// $this->exception
			// TODO: Register the default error handler.
			// $this->error

			// Verify that the application profile have been supplied.
			if(!$config->has('profile')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application profile have been supplied.'
				));
			}

			// Setup default configurations. If these already have been
			// supplied they won't be rewritten.
			$config->set('exception.template', '%directory.core.template%/exception.php');
			$config->set('context', 'web');
			$this->_config = $config;

			$this->setupDirectories();
			$this->registerConfigurationHandlers();
		}

		/**
		 * Setup the default directory structure for both application and core.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function setupDirectories()
		{
			$config = $this->getConfig();

			// Verify that we have the base path for the application directory.
			if(!$config->has('directory.application')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application directory have been supplied.'
				));
			}

			// Setup the default application directory structure.
			$config->set('directory.application.cache', '%directory.application%/cache');
			$config->set('directory.application.config', '%directory.application%/config');
			$config->set('directory.application.library', '%directory.application%/library');
			$config->set('directory.application.module', '%directory.application%/module');
			$config->set('directory.application.template', '%directory.application%/template');

			// Verify that we have the base path for the framework directory.
			if(!$config->has('directory.core')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No core directory have been supplied.'
				));
			}

			// Setup the default core directory structure.
			$config->set('directory.core.config', '%directory.core%/config');
			$config->set('directory.core.library', '%directory.core%/library');
			$config->set('directory.core.template', '%directory.core%/template');
		}

		/**
		 * Registers the configuration handlers.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function registerConfigurationHandlers()
		{
			$namespace = 'Net\\TheDeveloperBlog\\Ramverk\\Config\\Handler';

			// TODO: Register the rest of the configuration handlers.
			$factory = $this->getHandlerFactory();
			$factory->registerHandler('Autoload', "{$namespace}\\Autoload");
			$factory->registerHandler('Routing', "{$namespace}\\Routing");
		}

		/**
		 * Handle autoloading of classes within the library.
		 * @param string $name Name of class to autoload, with namespace.
		 * @throws Net\TheDeveloperBlog\Ramverk\Exception If no autoload items are available.
		 * @return boolean True if class was loaded, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function autoload($name)
		{
			// Check if the autoload classes have been loaded.
			if($this->_autoloads === NULL) {
				$factory = $this->getHandlerFactory();

				// First attempt to load the applications autoload configurations.
				// If that fails, load the framework autoload configuration.
				$autoloads = $factory->callHandler('Autoload', '%directory.application.config%/autoload.xml');
				if(empty($autoloads)) {
					$autoloads = $factory->callHandler('Autoload', '%directory.core.config%/autoload.xml');
					if(empty($autoloads)) {
						// TODO: Better specify the Exception-object.
						throw new Exception(sprintf(
							'The configuration file "%s" returned an empty array.',
							$filename
						));
					}
				}
				$this->_autoloads = $autoloads;
			}

			// Due to the use of namespaces, the separator have to be dubbled
			// to be able to match the array key.
			$name = str_replace('\\', '\\\\', $name);

			// Check if the class is available within the list of autoloaded
			// classes. If it exists, include it and return true.
			if(isset($this->_autoloads[$name])) {
				require $this->_autoloads[$name];

				return TRUE;
			}

			// If it don't exists we can only return false. Since we're
			// prepending the autoloader we can't throw an exception since it
			// might prevent another autoloader of finding the file.
			return FALSE;
		}

		/**
		 * Retrieve the configuration container.
		 * @return Net\TheDeveloperBlog\Ramverk\Config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this->_config;
		}

		/**
		 * Retrieve the configuration handler factory.
		 * @return Net\TheDeveloperBlog\Ramverk\Config\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getHandlerFactory()
		{
			// Check if the handler factory already have been instansiated. If
			// the handler factory has yet not been instansiated, instansiate it.
			if($this->_handlerFactory === NULL) {
				// Retrieve the application profile and context.
				$profile = $this->getConfig()->get('profile');
				$context = $this->getConfig()->get('context');

				// Instansiate the cache and parser for the factory.
				$cache = new Handler\Cache($profile, $context);
				$parser = new Handler\Parser($this->getConfig(), $profile, $context);

				// Instansiate the configuration handler factory.
				$this->_handlerFactory = new Handler\Factory($this->getConfig(), $cache, $parser);
			}
			return $this->_handlerFactory;
		}

		/**
		 * Retrieve the context controller.
		 * @return Net\TheDeveloperBlog\Ramverk\Controller Context controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getController()
		{
			// Check if the controller already have been instansiated. If the
			// controller has yet not been instansiated, instansiate it.
			if($this->_controller === NULL) {
				// Retrieve the information for the context Controller.
				$base = 'Net\\TheDeveloperBlog\\Ramverk\\Controller';
				$context = ucfirst($this->getConfig()->get('context'));
				$controller = "{$base}\\{$context}";

				// Verify that the context Controller actually exists before
				// attempting to instantiating it.
				if(!class_exists($controller)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Controller for context "%s" do not exists.',
						$context
					));
				}

				$reflection = new \ReflectionClass($controller);
				if(!$reflection->isSubclassOf($base)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Controller for context "%s" do not extend the base controller.',
						$context
					));
				}

				// Load the configuration routing.
				$filename = '%directory.application.config%/routing.xml';
				$routes = $this->getHandlerFactory()->callHandler('Routing', $filename);
				if(empty($routes)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}

				// Instansiate the routing with the available routes.
				$routing = new Routing($routes);

				// Create a new controller instance with real arguments.
				$arguments = array($this->getConfig(), $routing);
				$this->_controller = $reflection->newInstanceArgs($arguments);
			}
			return $this->_controller;
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php