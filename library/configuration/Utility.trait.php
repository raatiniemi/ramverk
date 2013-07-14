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

	/**
	 * Utilities for working with the configuration container.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	trait Utility
	{
		/**
		 * Expands configuration directives.
		 * Directive names between precentage signs, e.g. %application.name%
		 * will be replaced with the value of the directive.
		 * @param string $value String with configuration directives to expand.
		 * @return string String with configuration directives expanded.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function expandDirectives($value)
		{
			do {
				$oldValue = $value;

				// Attempt to replace the reference of the configuration
				// directive with the actual value.
				$value = preg_replace_callback(
					'/\%([a-z0-9\.]+)\%/i',
					function($matches) {
						return $this->getConfig()->get($matches[1], $matches[0]);
					},
					$value
				);
			} while($oldValue != $value);

			return $value;
		}

		/**
		 * Get the configuration container.
		 * Since the expand directives method needs the configuration container
		 * the implementing class has to supply the getConfig-method.
		 *
		 * By using a method instead of direct property access enable us to do
		 * initial setup and redirect to another container.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function getConfig();
	}
}
// End of file: Utility.trait.php
// Location: library/configuration/Utility.trait.php