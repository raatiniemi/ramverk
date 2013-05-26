<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Container for configuration.
	 * 
	 * @package Ramverk
	 * @subpackage Config
	 * 
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Config
	{
		/**
		 * Stores the configurations.
		 * @var array
		 */
		private $_config;

		/**
		 * Initialize the configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct()
		{
			$this->_config = array();
		}

		/**
		 * Set configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $value Value for the directive.
		 * @return boolean True if has been set, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>>
		 */
		public function set($name, $value)
		{
			// Check that the directive do not already exists.
			if(!$this->has($name)) {
				$this->_config[$name] = $value;

				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Get value of configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $default Default value.
		 * @return mixed Configuration directive value or default value.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>>
		 */
		public function get($name, $default=NULL)
		{
			return $this->has($name) ? $this->_config[$name] : $default;
		}

		/**
		 * Check whether a configuration directive exists or not.
		 * @param string $name Name of the directive.
		 * @return boolean True if directive exists, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>>
		 */
		public function has($name)
		{
			return array_key_exists($name, $this->_config)
				&& isset($this->_config[$name]);
		}

		/**
		 * Expand configuration directives.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function expandDirectives($value)
		{
			do {
				$oldValue = $value;

				$value = preg_replace_callback(
					'/\%([a-z0-9\.]+)\%/i',
					array($this, 'expandDirectivesCallback'),
					$value
				);
			} while($oldValue != $value);

			return $value;
		}

		/**
		 * Callback function for the expand directives-method.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 *
		 * @todo Refactor when using PHP 5.4.
		 * Unable to use $this within anonymous functions before PHP 5.4.
		 */
		protected function expandDirectivesCallback($matches)
		{
			return $this->get($matches[1], $matches[0]);
		}
	}
}
// End of file: Config.class.php
// Location: library/config/Config.class.php