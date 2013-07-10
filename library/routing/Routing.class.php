<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Base functionality for handling request routing.
	 *
	 * @package Ramverk
	 * @subpackage Routing
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Routing
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
		 * @return array Route matching the Request URI.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 * @todo Implement support for prepending additional routes.
		 * @todo Handle retrieval of the URI arguments.
		 * @todo Handle content type based routes.
		 */
		public function parseRoutes($uri)
		{
			$route = array();

			foreach($this->_routes as $availableRoute) {
				if(preg_match("#({$availableRoute['pattern']})#i", $uri)) {
					$route = $availableRoute;
				}
			}

			return $route;
		}
	}
}
// End of file: Routing.class.php
// Location: library/routing/Routing.class.php