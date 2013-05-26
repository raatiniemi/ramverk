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
		private $_factory;

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
			// Register the framework autoload-method.
			spl_autoload_register(array($this, 'autoload'), TRUE, TRUE);

			// TODO: Register the exception handler.
			// TODO: Register the error handler.

			// ---- Application

			// Verify that the application profile have been supplied.
			if(!$config->has('profile')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application profile have been supplied. Add the '.
					'"%s"-directive to the configuration container.',
					'profile'
				));
			}

			// Verify that the application directory have been supplied.
			if(!$config->has('directory.application')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No application directory have been supplised. Add the '.
					'"%s"-directive to the configuration container.',
					'directory.application'
				));
			}

			// Setup the default paths for the application.
			$config->set('directory.application.cache', '%directory.application%/cache');
			$config->set('directory.application.config', '%directory.application%/config');
			$config->set('directory.application.library', '%directory.application%/library');
			$config->set('directory.application.module', '%directory.application%/module');
			$config->set('directory.application.template', '%directory.application%/template');

			// ---- Core

			// Verify that the core directory have been supplied.
			if(!$config->has('directory.core')) {
				// TODO: Better specify the Exception-object.
				throw new Exception(sprintf(
					'No core directory have been supplied. Add the '.
					'"%s"-directive to the configuration container.',
					'directory.core'
				));
			}

			// Setup the default paths for the core.
			$config->set('directory.core.config', '%directory.core%/config');
			$config->set('directory.core.library', '%directory.core%/library');
			$config->set('directory.core.template', '%directory.core%/template');

			// Setup the default configurations, if already supplied these
			// won't override the others.
			$config->set('context', 'web');
			$config->set('exception.template', '%directory.core.template%/exception.php');

			// Retrieve the application profile and context.
			$profile = $config->get('profile');
			$context = $config->get('context');

			// Instansiate the cache and parser for the factory.
			$cache = new Handler\Cache($profile);
			$parser = new Handler\Parser($config, $profile, $context);

			// Instansiate the configuration handler factory.
			$factory = new Handler\Factory($config, $cache, $parser);
			$this->_factory = $factory;

			// Register the available configuration handlers.
			$handlerNamespace = 'Net\\TheDeveloperBlog\\Ramverk\\Config\\Handler';
			$factory->registerHandler('Autoload', "{$handlerNamespace}\\Autoload");
			$factory->registerHandler('Routing', "{$handlerNamespace}\\Routing");
			// TODO: Register more handlers.
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
				$filename = '%directory.application.config%/autoload.xml';

				// Attempt to load the application autoload classes.
				$autoloads = $this->_factory->callHandler('Autoload', $filename);
				if(empty($autoloads)) {
					$filename = '%directory.core.config%/autoload.xml';

					// Attempt to load the core autoload classes.
					$autoloads = $this->_factory->callHandler('Autoload', $filename);
					if(empty($autoloads)) {
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

		public function getController()
		{
			if($this->_controller === NULL) {
				$filename = '%directory.application.config%/routing.xml';

				// TODO: Instansiate the controller with routing configuration etc.
				$routing = $this->_factory->callHandler('Routing', $filename);
				if(empty($routing)) {
					throw new Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}
			}
			return $this->_controller;
		}
	}
}
// End of file: Core.class.php
// Location: library/core/Core.class.php