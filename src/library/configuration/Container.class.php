<?php
namespace Me\Raatiniemi\Ramverk\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;
	use Me\Raatiniemi\Ramverk\Data;

	/**
	 * Container for working with configuration directives.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Container extends Data\Container
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Utility;

		/**
		 * Container for readonly directives.
		 * @var array
		 */
		protected $_readonly;

		/**
		 * Initialize the container within inital values.
		 * @param array $container Initial values for the container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 * @todo Implement support for initial readonly container.
		 */
		public function __construct(array $container=array())
		{
			parent::__construct($container);

			// Initialize the readonly container.
			$this->_readonly = array();
		}

		/**
		 * Set configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $value Value of the directive.
		 * @param boolean $override Override existing directive.
		 * @param boolean $readonly Should the diretive be readonly.
		 * @return boolean True if directive is set, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function set($name, $value, $override=FALSE, $readonly=FALSE)
		{
			// Check if the directive already exists as readonly.
			if($this->hasReadonly($name)) {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(sprintf(
					'Configuration directive "%s" already exists and is readonly.',
					$name
				));
			}

			// Attempt to set the configuration directive. If the directive is
			// set the return value will be TRUE, otherwise it's FALSE.
			$returnValue = parent::set($name, $value, $override);

			// If the configuration directive have been set and the directive
			// is specified as readonly it should be added to the readonly container.
			if($returnValue && $readonly) {
				$this->_readonly[$name] = $value;
			}

			return $returnValue;
		}

		/**
		 * Get value of configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $default Default value, if directive do not exists.
		 * @return mixed Directive or default value.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function get($name, $default=NULL)
		{
			return $this->has($name) ? parent::get($name) : $default;
		}

		/**
		 * Check whether a readonly configuration directive exists or not.
		 * @param string $name Name of the directive.
		 * @return boolean True if the readonly directive exists, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasReadonly($name)
		{
			return array_key_exists($name, $this->_readonly)
				&& isset($this->_readonly[$name]);
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfig()
		{
			return $this;
		}
	}
}
// End of file: Container.class.php
// Location: library/configuration/Container.class.php