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
	use Net\TheDeveloperBlog\Ramverk\Data;

	/**
	 * Container for working with configurations.
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
		 * Set configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $value Value of the directive.
		 * @param boolean $override Override existing directive.
		 * @return boolean True if directive is set, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function set($name, $value, $override=FALSE)
		{
			return $this->setItem($name, $value, $override);
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