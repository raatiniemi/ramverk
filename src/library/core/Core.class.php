<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Configuration;
	use Me\Raatiniemi\Ramverk\Loader;

	/**
	 * Initialize the core functionality.
	 *
	 * @package Ramverk
	 * @subpackage Core
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	final class Core
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Core\Context\Handler;
		use Loader\Autoload;

		/**
		 * Application controller.
		 * @var Me\Raatiniemi\Ramverk\Controller
		 */
		private $_controller;

		/**
		 * Initialize the ramverk core.
		 * @param Me\Raatiniemi\Ramverk\Configuration\Container Configuration container.
		 * @param string $profile Profile for the application, optional.
		 * @param string $context Context for the application, optional.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Configuration\Container $config, $profile=NULL, $context=NULL)
		{
			// Check if the configuration container have been supplied with a profile.
			if(!$config->has('profile')) {
				// If no profile have been defined, check if the profile have been
				// supplied as an argument, otherwise use the default profile.
				$profile = isset($profile) ? $profile : $config->get('profile.default', 'development');
				$config->set('profile', $profile);
			}

			// Check if the configuration container have been supplied with a context.
			if(!$config->has('context')) {
				// If no context have been defined, check if the context have been
				// supplied as an argument, otherwise use the default context.
				$context = isset($context) ? $context : $config->get('context.default', 'web');
				$config->set('context', $context);
			}

			// TODO: Validate the retrieved context name.
			// Since each of the context requires certain classes and
			// configurations we can only allow for specific context names to
			// be used, i.e. 'web', 'console', etc.
			//
			// Howevery, it should be possible to supply additional context
			// names through the application configuration.

			// Check if the configuration container have been supplied with an exception template.
			if(!$config->has('exception.template')) {
				// If no exception template have been defined, use the default.
				$template = $config->get('exception.template.default', '%directory.core.template%/exception/plaintext.php');
				$config->set('exception.template', $template);
			}

			// Setup the default directory structure.
			$this->setupDirectories($config);

			// Initialize the core context.
			// TODO: Implement support for custom core context classes?
			$this->setContext(new Core\Context($config));

			// Register the framework autoload handler.
			$this->setAutoloadFile('%directory.application.config%/autoload.xml');
			spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);

			// Register the configuration handlers.
			$this->registerConfigurationHandlers();
		}

		/**
		 * Setup the default directory structure.
		 *
		 * If directories already have been specified, the directories will not be replaced.
		 *
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 * @todo Implement check for has directory before attempting to write it.
		 */
		private function setupDirectories($config)
		{
			// -- Core directory structure.
			// Check that a base core directory have been supplied.
			if(!$config->has('directory.core')) {
				// TODO: Better exception message, include config name.
				// TODO: Better specify the Exception-object.
				throw new Exception('No core directory have been supplied.');
			}

			// Setup the default directory structure for the core.
			$config->set('directory.core.config', '%directory.core%/config');
			$config->set('directory.core.library', '%directory.core%/library');
			$config->set('directory.core.template', '%directory.core%/template');

			// -- Application directory structure.
			// Check that a base application directory have been supplied.
			if(!$config->has('directory.application')) {
				// TODO: Better exception message, include config name.
				// TODO: Better specify the Exception-object.
				throw new Exception('No application directory have been supplied.');
			}

			// Setup the default directory structure for the application.
			$config->set('directory.application.cache', '%directory.application%/cache');
			$config->set('directory.application.config', '%directory.application%/config');
			$config->set('directory.application.library', '%directory.application%/library');
			$config->set('directory.application.module', '%directory.application%/module');
			$config->set('directory.application.template', '%directory.application%/template');
		}

		/**
		 * Register the configuration handlers.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function registerConfigurationHandlers()
		{
			// Base namespace for the configuration handlers.
			$namespace = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler';

			// Register the handlers with the handler factory.
			$factory = $this->getConfigurationHandlerFactory();
			$factory->registerHandler('Autoload', "{$namespace}\\Autoload");
			$factory->registerHandler('Core', "{$namespace}\\Core");
			$factory->registerHandler('Module', "{$namespace}\\Module");
			$factory->registerHandler('Routing', "{$namespace}\\Routing");
		}

		/**
		 * Retrieve the application controller.
		 * @return Me\Raatiniemi\Ramverk\Controller Application controller.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getController()
		{
			if($this->_controller === NULL) {
				$class = __NAMESPACE__ . '\\Controller';
				if(!class_exists($class)) {
					throw new \Exception('Can not find controller');
				}

				$reflection = new \ReflectionClass($class);
				$this->_controller = $reflection->newInstanceArgs(array($this->getContext()));
			}
			return $this->_controller;
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php