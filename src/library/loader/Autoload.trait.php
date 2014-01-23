<?php
namespace Me\Raatiniemi\Ramverk\Loader
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	/**
	 * Handles autoloading of classes, interfaces and traits.
	 *
	 * @package Ramverk
	 * @subpackage Loader
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
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
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
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
				if(($filename = $this->getAutoloadFile()) === NULL) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'No autoload file have been supplied for class "%s".',
						get_class($this)
					));
				}

				// Retrieve the configuration data from the file.
				$autoload = $factory->callHandler('Autoload', $filename);
				if(empty($autoload)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The configuration file "%s" returned an empty array.',
						$filename
					));
				}

				// Set the autoload.
				$this->_autoload = $autoload;
			}

			// Check if the class exists within our autoload configurations.
			if(isset($this->_autoload[$name])) {
				// Before attempting to include the file, we have to check that
				// it actually exists otherwise we'll get errors.
				if(!file_exists($this->_autoload[$name])) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Class "%s" is specified within autoload '.
						'configuration but do not exists.',
						$name
					));
				}

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
			//
			// Do not trigger a new autoload-chain with the controll.
			return class_exists($name, FALSE);
		}

		/**
		 * Set the path for the autoload file.
		 * @param string $file Path to the autoload file.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setAutoloadFile($file)
		{
			$this->_autoloadFile = $file;
		}

		/**
		 * Get the path for the autoload file.
		 * @return string Path to the autoload file.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getAutoloadFile()
		{
			return $this->_autoloadFile;
		}

		/**
		 * Retrieve the configuration handler factory.
		 * Since the autoload method needs the configuration handler factory
		 * the implementing class has to supply the getConfigurationHandlerFactory-method.
		 * @return Me\Raatiniemi\Ramverk\Config\Handler\Factory Factory for configuration handlers.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		abstract public function getConfigurationHandlerFactory();
	}
}
// End of file: Autoload.trait.php
// Location: library/loader/Autoload.trait.php