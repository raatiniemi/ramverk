<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	/**
	 * Handles caching of the configuration data from configuration handlers.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Cache {
		/**
		 * Profile name for the application.
		 * @var string
		 */
		private $profile;

		/**
		 * Context name for the application.
		 * @var string
		 */
		private $context;

		/**
		 * Initialize the cache for configuration handlers.
		 * @param string $profile Profile name for the application.
		 * @param string $context Context name for the application.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct($profile, $context) {
			// Verify that the supplied application profile is valid.
			if(!is_string($profile) || empty($profile)) {
				throw new \InvalidArgumentException(
					'Profile given to configuration handler cache is invalid'
				);
			}

			// Verify that the supplied application context is valid.
			// TODO: Validate against the available context names.
			if(!is_string($context) || empty($context)) {
				throw new \InvalidArgumentException(
					'Application context given to configuration handler cache is invalid'
				);
			}

			$this->profile = $profile;
			$this->context = $context;
		}

		/**
		 * Assemble the name for the cache file.
		 * @param SplFileInfo $filename Configuration file.
		 * @return string Generated cache name.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function generateName(\SplFileInfo $filename) {
			// Build the name for the cache file in the following format:
			// configurationfile_profile_context_sha1(filename).php
			return sprintf(
				'%s_%s_%s_%s.php',
				basename($filename->getBasename()),
				$this->profile,
				$this->context,
				sha1($filename->getPathname())
			);
		}

		/**
		 * Check whether the cache file should be updated.
		 * @param SplFileInfo $filename Configuration file.
		 * @param SplFileInfo $cachename Cache file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If configuration file do not exists or is not readable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file do not exists but is not a regular file.
		 * @return boolean True if the cache file should be updated, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function isModified(\SplFileInfo $filename, \SplFileInfo $cachename) {
			// Verify that the configuration file exists and is readable.
			if(!$filename->isFile() || !$filename->isReadable()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception();
			}

			// If the cache file exists, it also have to be a file.
			if($cachename->isReadable() && !$cachename->isFile()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception();
			}

			// True will be returned if the cache file do not exists, or if the
			// configuration file has been modified after the cache file was modified.
			return !$cachename->isReadable() || $filename->getMTime() > $cachename->getMTime();
		}

		/**
		 * Read data from the cache file.
		 * @param SplFileInfo $cachename Cache file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file exists but is not a regular file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cached data is not an array.
		 * @return array Array with the configuration data, or null.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function read(\SplFileInfo $cachename) {
			$data = null;

			// Before attempting to read the cache file we have to check that
			// it exists and is readable (permission issues).
			if($cachename->isReadable()) {
				//
				if(!$cachename->isFile()) {
					// TODO: Write exception message.
					throw new Ramverk\Exception();
				}

				//
				$data = require($cachename->getPathname());

				// All of the cached configuration data is stored as an array.
				// Hence, if we get back anything else something is wrong.
				if(!is_array($data)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The cached configuration file "%s" did not return '.
						'valid configuration data',
						$cachename->getBasename()
					));
				}
			}
			return $data;
		}

		/**
		 * Write the configuration data to the cache file.
		 * @param SplFileInfo $cachename Cache file.
		 * @param array $data Configuration data to be cached.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache directory do not exists and can not be created.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache directory is not writable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file exists but is either not a regular file or is writable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If write cache data to file fails.
		 * @return boolean True if caching was successful, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function write(\SplFileInfo $cachename, array $data) {
			// Check if the cache directory exists. If it doesn't
			// attempt to create it.
			$directory = $cachename->getPathInfo();
			if(!$directory->isDir()) {
				if(!$directory->isWritable() || !mkdir($directory->getRealPath(), 0777, true)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Cache directory "%s" do not exists and can not be created',
						$directory->getRealPath()
					));
				}
			}

			// Now that the directory exists, we need to check that we have
			// permission to actually write the data to the cache file.

			if(!$directory->isWritable()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception;
			}

			if($cachename->isReadable() && (!$cachename->isFile() || !$cachename->isWritable())) {
				// TODO: Write exception message.
				throw new Ramverk\Exception;
			}

			// Build the content of the cache file. The file should should
			// return an array with the configuration.
			$data = sprintf('<?php return %s;', var_export($data, 1));

			// The file_put_contents function returns the amount of bytes
			// written or false on failure.
			$file = $cachename->openFile();
			if($file->fwrite($data) === null) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'Unable to write data to cache file "%s"',
					$cachename->getBasename()
				));
			}
			return true;
		}
	}
}
// End of file: Cache.class.php
// Location: library/configuration/handler/Cache.class.php