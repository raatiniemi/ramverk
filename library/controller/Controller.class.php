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
	use Net\TheDeveloperBlog\Ramverk\Configuration;

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
		 * Application core.
		 * @var Net\TheDeveloperBlog\Ramverk\Core
		 */
		protected $_core;

		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Container
		 */
		protected $_config;

		/**
		 * Initialize the controller.
		 * @param Net\TheDeveloperBlog\Ramverk\Core $core Application core.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Core $core, Configuration\Container $config)
		{
			$this->_core = $core;
			$this->_config = $config;
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