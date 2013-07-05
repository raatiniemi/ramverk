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
	use Net\TheDeveloperBlog\Ramverk\Configuration;
	use Net\TheDeveloperBlog\Ramverk\Configuration\Handler;
	use Net\TheDeveloperBlog\Ramverk\Loader;

	/**
	 * Initialize the core functionality.
	 *
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
		 * Application context.
		 * @var Net\TheDeveloperBlog\Ramverk\Core\Context
		 */
		private $_context;

		/**
		 * Initialize the ramverk core.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Configuration\Container $config, $profile=NULL, $context=NULL)
		{
			// Check if the application profile is set, otherwise use the default.
			$profile = $profile !== NULL ? $profile : $config->get('profile.default', 'development');
			$config->set('profile', $profile);

			// Check if the application context is set, otherwise use the default.
			$context = $context !== NULL ? $context : $config->get('context.default', 'web');
			$config->set('context', $context);

			// Set the default exception template. If the template already have
			// been specified, the specified template will not be replaced.
			$config->set('exception.template', '%directory.core.template%/exception.php');
			$this->_config = $config;

			// TODO: Instansiate the context.
			$this->_context = new Core\Context($this->getConfig());

			// Register the autoloader for the framework core.
			$this->_autoloadFile = '%directory.application.config%/autoload.xml';
			spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);

			// Setup the default directories and register the configuration handlers.
			$this->setupDirectories($config);
			$this->registerConfigurationHandlers();
		}

		/**
		 * Setup the default directory structure.
		 * If directories already have been specified, the directories will not be replaced.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function setupDirectories($config)
		{
			// -- Core directory structure.
			// Check that a base core directory have been supplied.
			if(!$config->has('directory.core')) {
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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		private function registerConfigurationHandlers()
		{
			// Base namespace for the configuration handlers.
			$namespace = 'Net\\TheDeveloperBlog\\Ramverk\\Configuration\\Handler';

			// Register the handlers with the handler factory.
			$factory = $this->getConfigurationHandlerFactory();
			$factory->registerHandler('Autoload', "{$namespace}\\Autoload");
			$factory->registerHandler('Module', "{$namespace}\\Module");
			$factory->registerHandler('Routing', "{$namespace}\\Routing");
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this->_config;
		}

		/**
		 * Retrieve the application context.
		 * @return Net\TheDeveloperBlog\Ramverk\Core\Context Application context.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getContext()
		{
			return $this->_context;
		}

		/**
		 * Retrieve the configuration handler factory, used by the autoload-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfigurationHandlerFactory()
		{
			return $this->getContext()->getConfigurationHandlerFactory();
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php