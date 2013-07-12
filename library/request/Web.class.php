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
		 * Retrieved request HTTP headers.
		 * @var array
		 */
		protected $_headers;

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

		/**
		 * Retrieve all the HTTP headers.
		 * @return array All request HTTP headers.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getHttpHeaders()
		{
			if($this->_headers === NULL) {
				// TODO: Handle getallheaders on non-apache platforms.
				$this->_headers = array_change_key_case(getallheaders());
			}
			return $this->_headers;
		}

		/**
		 * Retrieve a HTTP header.
		 * @param string $name Name of the HTTP header.
		 * @return string Value for the HTTP header, or NULL.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getHttpHeader($name)
		{
			// Have to convert the name to lowercase, otherwise we'll encounter case issues.
			$name = strtolower($name);
			$headers = $this->getHttpHeader();

			// Check if the header exists, otherwise return NULL.
			return isset($headers[$name]) ? $headers[$name] : NULL;
		}

		/**
		 * Retrieve the HTTP method.
		 * @return string HTTP method used with the request.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getMethod()
		{
			return $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
		}
	}
}
// End of file: Web.class.php
// Location: library/request/Web.class.php