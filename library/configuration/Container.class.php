<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Data;

	/**
	 * Container for working with configuration directives.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
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
			$returnValue = $this->setItem($name, $value, $override);

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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function get($name, $default=NULL)
		{
			$value = $default;

			if($this->hasItem($name)) {
				$value = $this->getItem($name);
			}

			return $value;
		}

		/**
		 * Check whether a configuration directive exists or not.
		 * @param string $name Name of the directive.
		 * @return boolean True if the directive exists, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function has($name)
		{
			return $this->hasItem($name);
		}

		/**
		 * Check whether a readonly configuration directive exists or not.
		 * @param string $name Name of the directive.
		 * @return boolean True if the readonly directive exists, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function hasReadonly($name)
		{
			return array_key_exists($name, $this->_readonly)
				&& isset($this->_readonly[$name]);
		}

		/**
		 * Import directives to the container.
		 * @param array $items Directives to import.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function import(array $items)
		{
			$this->importItems($items);
		}

		/**
		 * Export directives from the container.
		 * @return array Directives stored within the container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function export()
		{
			return $this->exportItems();
		}

		/**
		 * Get the configuration container, used by Utility-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this;
		}
	}
}
// End of file: Container.class.php
// Location: library/configuration/Container.class.php