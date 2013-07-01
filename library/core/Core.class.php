<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk\Configuration;
	use Net\TheDeveloperBlog\Ramverk\Configuration\Handler;
	use Net\TheDeveloperBlog\Ramverk\Loader;

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
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Loader\Autoload;

		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Container
		 */
		private $_config;

		/**
		 * Configuration handler factory.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Factory
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
		public function __construct(Configuration\Container $config)
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
			$this->_config = $config;

			// Setup default configurations and register the configuration handlers.
			$this->setupDefaults();
			$this->registerConfigurationHandlers();
		}

		/**
		 * Setup default configurations.
		 * If configurations already have been supplied, the previous values will be used.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function setupDefaults()
		{
			// Setup the default directory structure.
			$this->setupDirectories();

			// Get the configuration container.
			$config = $this->getConfig();

			// Setup the default exception template and context.
			$config->set('exception.template', '%directory.core.template%/exception.php');
			$config->set('context', 'web');
		}

		/**
		 * Setup default directory structure.
		 * If directories already have been supplied the previous path will be used.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function setupDirectories()
		{
			// Get the configuration container.
			$config = $this->getConfig();

			// -- Application directory structure.

			// Check that a base application directory have been supplied.
			if(!$config->has('directory.application')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application directory have been supplied.'
				));
			}

			// Setup the default directory structure for the application.
			$config->set('directory.application.cache', '%directory.application%/cache');
			$config->set('directory.application.config', '%directory.application%/config');
			$config->set('directory.application.library', '%directory.application%/library');
			$config->set('directory.application.module', '%directory.application%/module');
			$config->set('directory.application.template', '%directory.application%/template');

			// -- Core directory structure.

			// Check that a base core directory have been supplied.
			if(!$config->has('directory.core')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No core directory have been supplied.'
				));
			}

			// Setup the default directory structure for the core.
			$config->set('directory.core.config', '%directory.core%/config');
			$config->set('directory.core.library', '%directory.core%/library');
			$config->set('directory.core.template', '%directory.core%/template');
		}

		/**
		 * Register the configuration handlers.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 *
		 * @todo Register the rest of the configuration handlers.
		 */
		private function registerConfigurationHandlers()
		{
			// Namespace for the configuration handlers.
			$namespace = 'Net\\TheDeveloperBlog\\Ramverk\\Configuration\\Handler';

			// Register the handlers with the handler factory.
			$factory = $this->getConfigurationHandlerFactory();
			$factory->registerHandler('Autoload', "{$namespace}\\Autoload");
			$factory->registerHandler('Module', "{$namespace}\\Module");
			$factory->registerHandler('Routing', "{$namespace}\\Routing");
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
		public function getConfigurationHandlerFactory()
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
				$context = ucfirst($this->getConfig()->get('context'));
				$namespace = 'Net\\TheDeveloperBlog\\Ramverk';

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
						'Controller for context "%s" do not extend the base controller.',
						$context
					));
				}

				$request['base'] = "{$namespace}\\Request";
				$request['name'] = "{$request['base']}\\{$context}";

				if(!class_exists($request['name'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Request for context "%s" do not exists.',
						$context
					));
				}

				$request['reflection'] = new \ReflectionClass($request['name']);
				if(!$request['reflection']->isSubclassOf($request['base'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Request for context "%s" do not extend the base request.',
						$context
					));
				}

				$filename = '%directory.application.config%/routing.xml';
				$routes = $this->getConfigurationHandlerFactory()->callHandler('Routing', $filename);
				if(empty($routes)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}

				$routing['base'] = "{$namespace}\\Routing";
				$routing['name'] = "{$routing['base']}\\{$context}";

				if(!class_exists($routing['name'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Routing for context "%s" do not exists.',
						$context
					));
				}

				$routing['reflection'] = new \ReflectionClass($routing['name']);
				if(!$routing['reflection']->isSubclassOf($routing['base'])) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'Routing for context "%s" do not extend the base routing.',
						$context
					));
				}

				$routing['arguments'] = array($request['reflection']->newInstance(), $routes);
				$routing['instance'] = $routing['reflection']->newInstanceArgs($routing['arguments']);

				// Temporarily include the core as an argument. Need to be able
				// to merge autoload configurations.
				$controller['arguments'] = array($this, $this->getConfig(), $routing['instance']);
				$this->_controller = $controller['reflection']->newInstanceArgs($controller['arguments']);
			}
			return $this->_controller;
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php