<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Handles routing.
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
		 * Initialize routing handler.
		 * @param array $routes Available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(array $routes)
		{
			$this->_routes = $routes;

			// Initialize route parsing.
			$this->parseRoutes();
		}

		/**
		 * Parse the available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function parseRoutes()
		{
		}
	}
}
// End of file: Routing.class.php
// Location: library/routing/Routing.class.php