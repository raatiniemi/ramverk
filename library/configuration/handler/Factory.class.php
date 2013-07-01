<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Factory for instansiating configuration handlers.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Factory
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Configuration\Utility;

		/**
		 * Stores the available configuration handlers.
		 * @var array
		 */
		protected $_availableHandlers = array();

		/**
		 * Stores instansiated configuration handlers.
		 * @var array
		 */
		protected $_handlers = array();

		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration
		 */
		protected $_config;

		/**
		 * Caching for configuration handler data.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Cache
		 */
		protected $_cache;

		/**
		 * Parser for configuration handlers.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Parser
		 */
		protected $_parser;

		/**
		 * Initialize the configuration handler factory.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Cache $cache Caching for configuration handler data.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Handler\Parser $parser Parser for configuration handlers.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Configuration\Container $config, Cache $cache, Parser $parser)
		{
			$this->_config = $config;
			$this->_cache = $cache;
			$this->_parser = $parser;
		}

		/**
		 * Call the configuration handler.
		 * @param string $name Name of the configuration handler.
		 * @return array Parsed configuration from the handler.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function callHandler($name, $filename)
		{
			// Get the absolute path for the configuration file.
			$filename = $this->expandDirectives($filename);

			// Check that the configuration directory exists.
			if(!is_dir(dirname($filename))) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'Configuration "%s" directory do not exists.',
					dirname($filename)
				));
			}

			// Check that the configuration file do exists and is readable.
			if(!file_exists($filename) || !is_readable($filename)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'The specified configuration file "%s" do not exists.',
					basename($filename)
				));
			}

			// Generate the cachename, and prepend the absolute cache directory.
			$cachename = $this->expandDirectives(sprintf(
				'%s/%s',
				'%directory.application.cache%',
				$this->_cache->generateName($filename)
			));

			// Check if the configuration file is available from the cache.
			// There's no need to load and instansiate the configuration handler
			// if we don't really need to.
			if($this->_cache->isModified($filename, $cachename)) {
				// Check if the specified handler is available.
				if(!$this->hasHandler($name)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The configuration handler "%s" is not registered.',
						$name
					));
				}

				// Retrieve the class name for the configuration handler.
				// The class name also includes the full namespace.
				$handler = $this->_availableHandlers[$name];

				// Check if the handler have been instansiated/loaded. There's
				// no need to instansiate a handler more than once.
				if(!$this->isInstansiated($handler)) {
					$this->loadHandler($handler);
				}

				// Retrieve the handler-instance.
				$instance = $this->_handlers[$handler];

				// Setup the arguments for the handlers execute-method.
				$document = new \DOMDocument('1.0', 'UTF-8');
				$document->load($filename);

				// Parse the configuration document. Parsing includes inclusion
				// of possible parent documents, retrieval of configuration
				// connected to the correct application profile and context.
				$document = $this->_parser->execute($document);
				$arguments = array($document, $this->_config);

				// Call the execute-method on the handler. The execute-method will
				// retrieve all of the configuration data within one array.
				$data = call_user_func_array(array($instance, 'execute'), $arguments);
				if(!is_array($data)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The configuration handler "%s" did not return an array.',
						$handler
					));
				}

				// Cache the retrieved configuration data.
				$this->_cache->write($cachename, $data);
			} else {
				// Retrieve the cached configuration data.
				$data = $this->_cache->read($cachename);
			}

			// Return the retrieved configuration data.
			return $data;
		}

		/**
		 * Instansiates/loads the configuration handler.
		 * @param string $handler Handlers full class name, with namespace.
		 * @return void Handlers are stored within the handlers array.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function loadHandler($handler)
		{
			// Check that the handler class actually exists. If it isn't loaded
			// yet the autoload functionality will attempt to include it.
			if(!class_exists($handler)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'The configuration handler "%s" do not exists.',
					$handler
				));
			}

			// Seems as though the handler class exists. We need to check
			// a few things before instansiating the class.
			$reflection = new \ReflectionClass($handler);

			// The handler have to implement the IHandler interface.
			$interface = __NAMESPACE__ . '\\IHandler';
			if(!$reflection->implementsInterface($interface)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'The configuration handler "%s" do not implement the '.
					'"%s"-interface.',
					$handler,
					$interface
				));
			}

			// Everything seems good to go, instansiate the handler.
			// TODO: Do the constructor need anything?
			$this->_handlers[$handler] = $reflection->newInstance();
		}

		/**
		 * Check if specified configuration handler is registered.
		 * @param string $name Name of the handler.
		 * @return boolean True if handler is registered, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function hasHandler($name)
		{
			return array_key_exists($name, $this->_availableHandlers)
				&& isset($this->_availableHandlers[$name]);
		}

		/**
		 * Check if the specified configuration handler is instansiated/loaded.
		 * @param string $handler Handlers full class name, with namespace.
		 * @return boolean True if handler is instansiated, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function isInstansiated($handler)
		{
			return array_key_exists($handler, $this->_handlers)
				&& isset($this->_handlers[$handler]);
		}

		/**
		 * Register configuration handler.
		 * @param string $name Name of the handler, used with callHandler.
		 * @param string $class Handlers full class name, with namespace.
		 * @throws Net\TheDeveloperBlog\Ramverk\Exception If handler is already registered.
		 * @return boolean True if handler is successfully registered.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function registerHandler($name, $class)
		{
			// Check that the configuration handler have not already been
			// registered. We don't want to override existing handlers.
			if($this->hasHandler($name)) {
				throw new Ramverk\Exception(sprintf(
					'The configuration handler "%s" have already been registered.',
					$name
				));
			}

			// Register the configuration handler.
			$this->_availableHandlers[$name] = $class;
			return TRUE;
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
	}
}
// End of file: Factory.class.php
// Location: library/configuration/handler/Factory.class.php