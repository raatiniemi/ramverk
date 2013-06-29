<?php
namespace Net\TheDeveloperBlog\Ramverk\Routing
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Handles web based routing.
	 *
	 * @package Ramverk
	 * @subpackage Routing
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Ramverk\Routing
	{
		/**
		 * Parse the available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function parseRoutes()
		{
			// Retrieve the request URI.
			$uri = $this->_request->getRequestURI();

			// Loop through each of the available routes and check if the
			// routes' pattern match the request URI.
			foreach($this->_routes as $route) {
				if(preg_match('#('.$route['pattern'].')#', $uri)) {
					// TODO: Implement support for prepending other routes.
					// TODO: Handle retrieval of arguments from the URI.

					// When we find the we're looking for, retrieve the name
					// of the module and action.
					$this->_module = $route['module'];
					$this->_action = $route['action'];

					// Since we've already found which route we were looking
					// for we can break free from the foreach-loop.
					break;
				}
			}

			// TODO: How to handle 404 pages.
		}
	}
}
// End of file: Web.class.php
// Location: library/routing/Web.class.php