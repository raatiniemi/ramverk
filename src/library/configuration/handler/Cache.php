<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Utility;

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
		use Utility\Filesystem;

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
		 * @param Me\Raatiniemi\Ramverk\Utility\File $file Configuration file.
		 * @return string Generated cache name.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function generateName(Utility\File $file) {
			// Build the name for the cache file in the following format:
			// configurationfile_profile_context_sha1(filename).php
			return sprintf(
				'%s_%s_%s_%s.php',
				basename($file->getBasename()),
				$this->profile,
				$this->context,
				sha1($file->getPathname())
			);
		}

		/**
		 * Check whether the cache file should be updated.
		 * @param Me\Raatiniemi\Ramverk\Utility\File $file Configuration file.
		 * @param Me\Raatiniemi\Ramverk\Utility\File $cache Cache file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If configuration file do not exists or is not readable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file do exists but is not a regular file.
		 * @return boolean True if the cache file should be updated, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function isModified(Utility\File $file, Utility\File $cache) {
			// Verify that the configuration file exists and is readable.
			if(!$file->isFile() || !$file->isReadable()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception();
			}

			// If the cache file exists, it also have to be a file.
			if($cache->isReadable() && !$cache->isFile()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception();
			}

			// True will be returned if the cache file do not exists, or if the
			// configuration file has been modified after the cache file was modified.
			return !$cache->isReadable() || $file->getMTime() > $cache->getMTime();
		}

		/**
		 * Read data from the cache file.
		 * @param Me\Raatiniemi\Ramverk\Utility\File $file Cache file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file exists but is not a regular file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cached data is not an array.
		 * @return array Array with the configuration data, or null if file do not exists.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function read(Utility\File $file) {
			$data = null;

			// Before attempting to read the cache file we have to check that
			// it exists and is readable (permission issues).
			if($file->isReadable()) {
				//
				if(!$file->isFile()) {
					// TODO: Write exception message.
					throw new Ramverk\Exception();
				}

				// Import the cached configuration data.
				$data = $this->import($file);

				// All of the cached configuration data is stored as an array.
				// Hence, if we get back anything else something is wrong.
				if(!is_array($data)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'The cached configuration file "%s" did not return '.
						'valid configuration data',
						$file->getBasename()
					));
				}
			}
			return $data;
		}

		/**
		 * Import the configuration data from cache file.
		 * This method exists primarily for unit testing since we can't
		 * override or mock the require keyword.
		 * @param Me\Raatiniemi\Ramverk\Utility\File $file Cache file.
		 * @return array Cached configuration data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 * @codeCoverageIgnore
		 */
		protected function import(Utility\File $file) {
			return require($file->getPathname());
		}

		/**
		 * Write the configuration data to the cache file.
		 * @param Me\Raatiniemi\Ramverk\Utility\File $file Cache file.
		 * @param array $data Configuration data to be cached.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache directory is a file.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache directory do not exists and can not be created.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache directory is not writable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If cache file exists but is either not a regular file or is writable.
		 * @throws Me\Raatiniemi\Ramverk\Exception If write data to cache file fails.
		 * @return boolean True if caching was successful, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function write(Utility\File $file, array $data) {
			// Check if the cache directory exists. If it doesn't
			// attempt to create it.
			$directory = $file->getPathInfo();
			if(!$directory->isDir()) {
				// If the directory path is not an actual directory, it can be a file.
				if($directory->isFile()) {
					// TODO: Write exception message.
					throw new Ramverk\Exception();
				}

				// Attempt to create the cache directory.
				if(!$this->makeDirectory($directory->getRealPath(), 0777, true)) {
					throw new Ramverk\Exception(sprintf(
						'Cache directory "%s" do not exists and can not be created',
						$directory->getRealPath()
					));
				}
			}

			// The directory seems to exists, but can we write to it?
			if(!$directory->isWritable()) {
				// TODO: Write exception message.
				throw new Ramverk\Exception;
			}

			// If the file is readable, it also have to be an actual file and writable.
			// Otherwise, we can't update the cache data.
			if($file->isReadable() && (!$file->isFile() || !$file->isWritable())) {
				// TODO: Write exception message.
				throw new Ramverk\Exception;
			}

			// Build the content of the cache file. The file should should
			// return an array with the configuration.
			$data = sprintf('<?php return %s;', var_export($data, 1));

			// The `write`-method returns the number of bytes written,
			// or `null` on failure.
			if($file->write($data) === null) {
				throw new Ramverk\Exception(sprintf(
					'Unable to write data to cache file "%s"',
					$file->getBasename()
				));
			}
			return true;
		}
	}
}
// End of file: Cache.php
// Location: library/configuration/handler/Cache.php