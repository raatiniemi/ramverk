<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Request
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Base functionality for handling request routing.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Routing
	{
		/**
		 * Available routes.
		 * @var array
		 */
		protected $_routes;

		/**
		 * Initialize the request routing.
		 * @param array $routes Available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(array $routes)
		{
			$this->_routes = $routes;
		}

		/**
		 * Parse the available routes.
		 * @param string $uri Request URI.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function parseRoutes($uri);
	}
}
// End of file: Routing.class.php
// Location: library/request/routing/Routing.class.php