<?php
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Base controller.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Controller
	{
		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Config
		 */
		protected $_config;

		/**
		 * Available routes.
		 * @var array
		 */
		protected $_routing;

		/**
		 * Initialize the controller.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @param array $routing Available routes.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Config $config, array $routing)
		{
			$this->_config = $config;
			$this->_routing = $routing;
		}

		/**
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function dispatch();
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php