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
	use Net\TheDeveloperBlog\Ramverk\Request;

	/**
	 * Base functionality for handling requests.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Request
	{
		/**
		 * Handles request routing.
		 * @var Net\TheDeveloperBlog\Ramverk\Request\Routing
		 */
		protected $_routing;

		/**
		 * Module name for the request.
		 * @var string
		 */
		protected $_module;

		/**
		 * Action name for the request.
		 * @var string
		 */
		protected $_action;

		/**
		 * Initialize the request.
		 * @param Net\TheDeveloperBlog\Ramverk\Request\Routing $routing Handles request routing.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Request\Routing $routing)
		{
			$this->_routing = $routing;

			$this->initialize();
		}

		/**
		 * Initialize the routing request.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract protected function initialize();

		/**
		 * Get the module name for the request.
		 * @return string Module name for the request.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getModule()
		{
			return $this->_module;
		}

		/**
		 * Get the action name for the request
		 * @return string Action name for the request
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getAction()
		{
			return $this->_action;
		}
	}
}
// End of file: Request.class.php
// Location: library/request/Request.class.php