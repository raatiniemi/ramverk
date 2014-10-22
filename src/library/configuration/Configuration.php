<?php
namespace Me\Raatiniemi\Ramverk {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Container for working with configuration directives.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Configuration {
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Configuration\Utility;

		/**
		 * Stores the configuration items with their values.
		 * @var array
		 */
		private $config;

		/**
		 * Stores the readonly configuration items with their values.
		 * @var array
		 */
		private $readonly;

		/**
		 * Initialize the configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct() {
			$this->config = array();
			$this->readonly = array();
		}

		/**
		 * Set configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $value Value of the directive.
		 * @param boolean $override Override existing directive.
		 * @param boolean $readonly Should the diretive be readonly.
		 * @throws InvalidArgumentException If the type of the name is not a string.
		 * @throws Me\Raatiniemi\Ramverk\Exception If directive is already specified as readonly.
		 * @return boolean True if directive is set, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function set($name, $value, $override = false, $readonly = false) {
			// Verify that the directive is an actual string.
			if(!is_string($name)) {
				throw new \InvalidArgumentException(sprintf(
					'The name of the configuration directive is of type "%s" and not a string',
					gettype($name)
				));
			}

			// Check if the directive already exists as readonly.
			if($this->hasReadonly($name)) {
				throw new Exception(sprintf(
					'Configuration directive "%s" have already been defined '.
					'and is specified as readonly',
					$name
				));
			}

			$returnValue = false;

			// Check if the item already exists, we shouldn't override
			// already existing items unless the override flag is set.
			if(!$this->has($name) || $override === true) {
				$this->config[$name] = $value;

				// If the directive is specified as readonly it should be
				// added to the readonly container.
				if($readonly) {
					$this->readonly[$name] = $value;
				}

				$returnValue = true;
			}
			return $returnValue;
		}

		/**
		 * Get value of configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $default Default value, if directive do not exists.
		 * @return mixed Value of the directive, or the default value.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function get($name, $default = null) {
			return $this->has($name) ? $this->config[$name] : $default;
		}

		/**
		 * Check whether a configuration directive exists.
		 * @param string $name Name of the directive.
		 * @return boolean True if the directive exists, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function has($name) {
			return isset($this->config[$name]);
		}

		/**
		 * Check whether a readonly configuration directive exists.
		 * @param string $name Name of the directive.
		 * @return boolean True if the readonly directive exists, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasReadonly($name) {
			return isset($this->readonly[$name]);
		}

		/**
		 * Import configuration directives and values from an array.
		 * @param array $data Directives to import.
		 * @param boolean $override Override existing directives.
		 * @throws Me\Raatiniemi\Ramverk\Exception If directive is already specified as readonly.
		 * @return boolean True if directives have been imported, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function fromArray(array $data, $override = false) {
			$imported = true;

			foreach($data as $directive => $value) {
				if(!($imported = $this->set($directive, $value, $override))) {
					break;
				}
			}
			return $imported;
		}

		/**
		 * Export the configuration directives with their values.
		 * @return array Configuration directives with their values.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function toArray() {
			return (array)$this->config;
		}

		/**
		 * Expands configuration directives.
		 *
		 * Directive names between precentage signs, e.g. %application.name%
		 * will be replaced with the value of the directive.
		 *
		 * @param string $value String with configuration directives to expand.
		 * @return string String with configuration directives expanded.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function expand($value) {
			return $this->expandDirectives($value);
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Me\Raatiniemi\Ramverk\Configuration Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function getConfig() {
			return $this;
		}
	}
}
// End of file: Configuration.php
// Location: library/configuration/Configuration.php