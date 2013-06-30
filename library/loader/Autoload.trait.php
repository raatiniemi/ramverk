<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by TheDeveloperBlog.          |
// | Copyright (c) 2013, Authors.                                             |
// | Copyright (c) 2013, TheDeveloperBlog.                                    |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Loader
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Handles autoloading of classes, interfaces and traits.
	 *
	 * @package Ramverk
	 * @subpackage Loader
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	trait Autoload
	{
		/**
		 * List of files with possible autoload configurations.
		 * @var array
		 */
		private $_autoloadFile;

		/**
		 * Items available for autoloading.
		 * @var array
		 */
		private $_autoload;

		/**
		 * Handles autoloading of classes, interfaces and traits.
		 * @param string $name Name of the item to autoload, with namespace.
		 * @return boolean True if item was loaded, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function autoload($name)
		{
			// Check if we've already loaded the autoload configurations.
			if($this->_autoload === NULL) {
				// Get the configuration handler factory. The method is an
				// abstract method that is forced to be implemented in the
				// class that is using this trait.
				$factory = $this->getConfigurationHandlerFactory();

				// Get the autoload configuration file.
				if(($filename = $this->_autoloadFile) === NULL) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'No autoload file have been supplied for class "%s".',
						get_class($this)
					));
				}

				// Retrieve the configuration data from the file.
				$autoload = $factory->callHandler('Autoload', $filename);
				if(empty($autoload)) {
					// TODO: Better specify the Exception-object.
					throw new Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}

				// Set the autoload.
				$this->_autoload = $autoload;
			}

			// Due to the use of namespaces, the separator have to be dubbled
			// to be able to match the array index.
			$name = str_replace('\\', '\\\\', $name);

			//
			if(isset($this->_autoload[$name])) {
				// Since autoload is not triggered unless the class do not
				// exists there is no need for require_once, or other checks.
				require $this->_autoload[$name];
			}

			// If we do not find the class there's nothing we can do. If we
			// throw an exception or trigger an error we might prevent other
			// autoloaders of finding the item.
			//
			// E.g. if the module autoloader do not finds the item because it
			// is located within the core autoload items.
			return class_exists($name, FALSE);
		}

		/**
		 * Retrieve the configuration handler factory.
		 * @return Net\TheDeveloperBlog\Ramverk\Config\Handler\Factory Configuration handler factory.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function getConfigurationHandlerFactory();
	}
}
// End of file: Autoload.trait.php
// Location: library/loader/Autoload.trait.php