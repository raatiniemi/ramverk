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
		 * @param string $value String with configuration directives to expand.
		 * @throws Net\TheDeveloperBlog\Ramverk\Exception If object do not implement Net\TheDeveloperBlog\Ramverk\Configuration\IUtility
		 * @return string String with configuration directives expanded.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function expandDirectives($value)
		{
			// We have to check that the object context do implements the
			// IUtility-interface (which ensures that the requires methods exists).
			if(!$this instanceof IUtility) {
				throw new Ramverk\Exception(sprintf(
					'Class "%s" do not implement the "%s\\IUtility" interface.',
					get_class($this), __NAMESPACE__
				));
			}

			do {
				$oldValue = $value;

				// Attempt to replace the reference of the configuration
				// directive with the actual value.
				$value = preg_replace_callback(
					'/\%([a-z0-9\.]+)\%/i',
					array($this, function($matches) {
						return $this->getConfig()->get($matches[1], $matches[0]);
					}),
					$value
				);
			} while($oldValue != $value);

			return $value;
		}
	}
}
// End of file: Utility.trait.php
// Location: library/configuration/Utility.trait.php