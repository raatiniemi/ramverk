<?php
namespace Me\Raatiniemi\Ramverk\Configuration {
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	/**
	 * Utilities for working with the configuration container.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	trait Utility {
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
		public function expandDirectives($value) {
			do {
				$oldValue = $value;

				// Attempt to replace the reference of the configuration
				// directive with the actual value.
				$value = preg_replace_callback(
					'/\%([a-z0-9\.]+)\%/i',
					function($matches) {
						// Attempt to retrieve the value for the configuration directive.
						// If no value is found, use the original value.
						return $this->getConfig()->get($matches[1], $matches[0]);
					},
					$value
				);
			} while($oldValue != $value);

			return $value;
		}

		/**
		 * Get the configuration container.
		 *
		 * Since the expand directives method needs the configuration container
		 * the implementing class has to supply the getConfig-method.
		 *
		 * By using a method instead of direct property access enable us to do
		 * initial setup and redirect to another container.
		 *
		 * @return Me\Raatiniemi\Ramverk\Configuration Configuration container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		abstract protected function getConfig();
	}
}
// End of file: Utility.php
// Location: library/configuration/Utility.php