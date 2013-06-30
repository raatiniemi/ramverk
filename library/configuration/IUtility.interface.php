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

	/**
	 * Interface for working with the configuration container.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	interface IUtility
	{
		/**
		 * Get the configuration container.
		 * @return Net\TheDeveloperBlog\Ramverk\Configuration\Container Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getConfig();
	}
}
// End of file: Config.class.php
// Location: library/config/Config.class.php