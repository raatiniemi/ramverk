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
		 */
		public function getHttpHeaders()
		{
			if($this->_headers === NULL) {
				$this->_headers = array_change_key_case(getallheaders());
			}
			return $this->_headers;
		}

		public function getHttpHeader($name)
		{
			$headers = $this->getHttpHeader();

			return isset($headers[$name]) ? $headers[$name] : NULL;
		}

		/**
		 */
		public function getMethod()
		{
			return $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
		}
	}
}
// End of file: Web.class.php
// Location: library/request/Web.class.php