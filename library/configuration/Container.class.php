<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by TheDeveloperBlog.          |
// | Copyright (c) 2013, Authors.                                             |
// | Copyright (c) 2013, TheDeveloperBlog.                                    |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
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
	class Container extends Data\Container implements IUtility
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Utility;

		/**
		 * Set configuration directive.
		 * @param string $name Name of the directive.
		 * @param mixed $value Value of the directive.
		 * @return boolean True if directive is set, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function set($name, $value)
		{
			return $this->setValueWithIndex($name, $value);
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

			if($this->hasIndex($name)) {
				$value = $this->getValueByIndex($name);
			}

			return $value;
		}

		/**
		 * Retrieve the configuration container, used by Utility-trait.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig()
		{
			return $this;
		}
	}
}
// End of file: Config.class.php
// Location: library/config/Config.class.php