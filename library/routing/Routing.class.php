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
	 * Base functionality for handling routing.
	 *
	 * @package Ramverk
	 * @subpackage Routing
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Routing
	{
		/**
		 * Handles requests.
		 * @var Net\TheDeveloperBlog\Ramverk\Request
		 */
		protected $_request;

		/**
		 * Available routes.
		 * @var array
		 */
		protected $_routes;

		/**
		 * Module name from the parsed route.
		 * @var string
		 */
		protected $_module;

		/**
		 * Action name from the parsed route.
		 * @var string
		 */
		protected $_action;

		/**
		 * Initialize routing handler.
		 * @param Net\TheDeveloperBlog\Ramverk\Request $request Handles requests.
		 * @param array $routes Available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Request $request, array $routes)
		{
			$this->_request = $request;
			$this->_routes = $routes;

			// Initialize route parsing.
			$this->parseRoutes();
		}

		/**
		 * Get the module name from the parsed route.
		 * @return string Module name from the parsed route.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getModule()
		{
			return $this->_module;
		}

		/**
		 * Get the action name from the parsed route.
		 * @return string Action name from the parsed route.
		 */
		public function getAction()
		{
			return $this->_action;
		}

		/**
		 * Parse the available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract protected function parseRoutes();
	}
}
// End of file: Routing.class.php
// Location: library/routing/Routing.class.php