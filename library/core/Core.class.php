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
			// Register the autoload-method.
			spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);

			// TODO: Register the default exception handler.
			// TODO: Register the default error handler.

			if(!$config->has('profile')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application profile have been supplied.'
				));
			}
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
			if(!$this->getConfig()->has('directory.application')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application directory have been supplied.'
				));
			}

			// Setup the default application directory structure.
			$this->getConfig()->set('directory.application.cache', '%directory.application%/cache');
			$this->getConfig()->set('directory.application.config', '%directory.application%/config');
			$this->getConfig()->set('directory.application.library', '%directory.application%/library');
			$this->getConfig()->set('directory.application.module', '%directory.application%/module');
			$this->getConfig()->set('directory.application.template', '%directory.application%/template');

			if(!$this->getConfig()->has('directory.core')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No core directory have been supplied.'
				));
			}

			// Setup the default core directory structure.
			$this->getConfig()->set('directory.core.config', '%directory.core%/config');
			$this->getConfig()->set('directory.core.library', '%directory.core%/library');
			$this->getConfig()->set('directory.core.template', '%directory.core%/template');
		}

		/**
		 * Registers the configuration handlers.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 *
		 * @todo Register the rest of the configuration handlers.
		 */
		private function registerConfigurationHandlers()
		{
			$factory = $this->getHandlerFactory();

			$namespace = 'Net\\TheDeveloperBlog\\Ramverk\\Config\\Handler';
			$factory->registerHandler('Autoload', "{$namespace}\\Autoload");
			$factory->registerHandler('Routing', "{$namespace}\\Routing");
		}

		/**
		 * Handle autoloading of library classes.
		 * @param string $name Name of class to autoload, with namespace.
		 * @throws Net\TheDeveloperBlog\Ramverk\Exception If no autoload items are available.
		 * @return boolean True if class was loaded, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function autoload($name)
		{
			// Check if the classes available for autoloading have been loaded.
			if($this->_autoloads === NULL) {
				$factory = $this->getHandlerFactory();

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
		 * Get the configuration container.
		 * @return Net\TheDeveloperBlog\Ramverk\Config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this->_config;
		}

		/**
		 * Get the configuration handler factory, instansiate it if necessary.
		 * @return Net\TheDeveloperBlog\Ramverk\Config\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getHandlerFactory()
		{
			if($this->_handlerFactory === NULL) {
				// Retrieve the application profile and context.
				$profile = $this->getConfig()->get('profile');
				$context = $this->getConfig()->get('context');

				// Instansiate the cache and parser for the factory.
				$cache = new Handler\Cache($profile);
				$parser = new Handler\Parser($this->_config, $profile, $context);

				// Instansiate the configuration handler factory.
				$this->_handlerFactory = new Handler\Factory($this->_config, $cache, $parser);
			}
			return $this->_handlerFactory;
		}

		public function getController()
		{
			if($this->_controller === NULL) {
				$filename = '%directory.application.config%/routing.xml';

				$routing = $this->_handlerFactory->callHandler('Routing', $filename);
				if(empty($routing)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}

				$base = 'Net\\TheDeveloperBlog\\Ramverk\\Controller';
				$context = ucfirst($this->_config->get('context'));
				$controller = "{$base}\\{$context}";

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
						'Context controller "%s" do not extend the base controller.',
						$controller
					));
				}

				$arguments = array($this->_config, $routing);
				$this->_controller = $reflection->newInstanceArgs($arguments);
			}
			return $this->_controller;
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php