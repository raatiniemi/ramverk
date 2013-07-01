<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Request\Routing
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk\Request;

	/**
	 * Functionality for handling web based request routing.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Request\Routing
	{
		/**
		 * Parse the available routes.
		 * @param string $uri Request URI.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function parseRoutes($uri)
		{
			// TODO: Parse routing.
		}
	}
}
// End of file: Web.class.php
// Location: library/request/routing/Web.class.php