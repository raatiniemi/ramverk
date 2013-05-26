<?php
namespace Net\TheDeveloperBlog\Ramverk\Config\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Handles configuration caching.
	 *
	 * @package Ramverk
	 * @subpackage Config
	 * 
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Cache
	{
		/**
		 * Application profile.
		 * @var string
		 */
		protected $_profile;

		/**
		 * Initialize the configuration cache.
		 * @param string $profile Application profile.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct($profile)
		{
			$this->_profile = $profile;
		}

		/**
		 * Assemble the name for the cache file.
		 * @param string $filename Name of the configuration file.
		 * @return string Generated cache name.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function generateName($filename)
		{
			// Build the name for the cache file in the following format:
			// configurationfile_profile_sha1(filename).php
			return sprintf('%s_%s_%s.php', basename($filename), $this->_profile, sha1($filename));
		}

		/**
		 * Check whether or not the cache file should be updated.
		 * @param string $filename Absolute path to the configuration file.
		 * @param string $cachename Absolute path to the cache file.
		 * @return boolean Whether or not the cache file should be updated.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function isModified($filename, $cachename)
		{
			return !is_readable($cachename) || filemtime($filename) > filemtime($cachename);
		}

		/**
		 * Reads the configuration data from the cache file.
		 * @param string $cachename Absolute path to the cache file.
		 * @return array Array with the configuration data, or NULL.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function read($cachename)
		{
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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function write($cachename, array $data)
		{
			// TODO: Create directory if it don't exists.
			$directory = dirname($cachename);

			if(!is_dir($directory)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'Cache directory "%s" do not exists.',
					$directory
				));
			}

			// Build the content of the cache file. The file should should
			// return an array with the configuration.
			$data = sprintf('<?php return %s;', var_export($data, 1));

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
// Location: library/config/handler/Cache.class.php