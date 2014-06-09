<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	/**
	 * Handles caching of configuration data from handlers.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 *
	 * @todo Migrate most of the functionality to a more generic caching class.
	 */
	class Cache
	{
		/**
		 * Profile for the application.
		 * @var string
		 */
		protected $_profile;

		/**
		 * Context for the application.
		 * @var string
		 */
		protected $_context;

		/**
		 * Initialize the cache for configuration handlers.
		 * @param string $profile Profile for the application.
		 * @param string $context Context for the application.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct($profile, $context)
		{
			$this->_profile = $profile;
			$this->_context = $context;
		}

		/**
		 * Assemble the name for the cache file.
		 * @param string $filename Name of the configuration file.
		 * @return string Generated cache name.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function generateName($filename)
		{
			// Build the name for the cache file in the following format:
			// configurationfile_profile_context_sha1(filename).php
			return sprintf(
				'%s_%s_%s_%s.php',
				basename($filename),
				$this->_profile,
				$this->_context,
				sha1($filename)
			);
		}

		/**
		 * Check whether the cache file should be updated.
		 * @param string $filename Absolute path to the configuration file.
		 * @param string $cachename Absolute path to the cache file.
		 * @return boolean True if the cache file should be updated, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function isModified($filename, $cachename)
		{
			return !is_readable($cachename) || filemtime($filename) > filemtime($cachename);
		}

		/**
		 * Read data from the cache file.
		 * @param string $cachename Absolute path to the cache file.
		 * @return array Array with the configuration data, or NULL.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function read($cachename)
		{
			// Before attempting to read the cache file we have to check that
			// it exists and is readable (permission issues).
			if(is_readable($cachename)) {
				$data = require($cachename);

				// All of the cached configuration data is stored as an array.
				// Hence, if we get back anything else something is wrong.
				if(!is_array($data)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The cached configuration file "%s" did not return '.
						'valid configuration data.',
						basename($cachename)
					));
				}

				return $data;
			}
			return NULL;
		}

		/**
		 * Write the configuration data to the cache file.
		 * @param string $cachename Absolute path to the cache file.
		 * @param array $data Configuration data to be cached.
		 * @return boolean True if caching was successful, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function write($cachename, array $data)
		{
			$directory = dirname($cachename);

			// Check if the cache directory exists. If it doesn't
			// attempt to create it.
			if(!is_dir($directory)) {
				if(!mkdir($directory, 0777, TRUE)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Cache directory "%s" do not exists and can not be created.',
						$directory
					));
				}
			}

			// Build the content of the cache file. The file should should
			// return an array with the configuration.
			$data = sprintf('<?php return %s;', var_export($data, 1));

			// TODO: Check if $cachename is writable.
			// The file_put_contents function returns the amount of bytes
			// written and false on failure.
			if(file_put_contents($cachename, $data) === FALSE) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'Unable to write data to cache file "%s".',
					$cachename
				));
			}

			return TRUE;
		}
	}
}
// End of file: Cache.class.php
// Location: library/configuration/handler/Cache.class.php