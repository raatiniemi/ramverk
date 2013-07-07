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
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Functionality for handling web based requests.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Ramverk\Request
	{
		/**
		 * Initialize the routing request.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function initialize()
		{
			$uri = isset($_GET['uri']) ? $_GET['uri'] : '';
			$route = $this->_routing->parseRoutes($uri);

			if(empty($route)) {
				// TODO: Handle 404 requests.
			} else {
				$this->_module = $route['module'];
				$this->_action = $route['action'];
			}
		}
	}
}
// End of file: Web.class.php
// Location: library/request/Web.class.php