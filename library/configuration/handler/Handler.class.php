<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Base functionality for configuration handlers.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Handler
	{
		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Container
		 */
		protected $_config;

		/**
		 * Initialize the configuration handler.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Container $config)
		{
			$this->_config = $config;
		}

		/**
		 * Execute the configuration handler.
		 * @param DOMDocument $document XML document with configuration data.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function execute(\DOMDocument $document);
	}
}
// End of file: Handler.class.php
// Location: library/configuration/handler/Handler.class.php