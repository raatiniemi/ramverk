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
		use Loader\Autoload;

		/**
		 * Configuration container.
		 * @var Me\Raatiniemi\Ramverk\Configuration\Container
		 */
		private $_config;

		/**
		 * Application context.
		 * @var Me\Raatiniemi\Ramverk\Core\Context
		 */
		private $_context;

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

			// Setup the default directory structure.
			$this->setupDirectories($config);

			// Check if the configuration container have been supplied with an exception template.
			if(!$config->has('exception.template')) {
				// If no exception template have been defined, use the default.
				$template = $config->get('exception.template.default', '%directory.core.template%/exception/plaintext.php');
				$config->set('exception.template', $template);
			}

			// Initialize the core context.
			// TODO: Implement support for custom core context classes?
			$this->_context = new Core\Context($config);
			$this->_config = $config;

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
				throw new Ramverk\Exception('No core directory have been supplied.');
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
				throw new Ramverk\Exception('No application directory have been supplied.');
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
		}

		/**
		 * Retrieve the application context.
		 * @return Me\Raatiniemi\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getContext()
		{
			return $this->_context;
		}

		/**
		 * Retrieve the configuration handler factory, used by the autoload-trait.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfigurationHandlerFactory()
		{
			return $this->getContext()->getConfigurationHandlerFactory();
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php