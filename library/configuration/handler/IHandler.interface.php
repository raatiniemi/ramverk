<?php
namespace Net\TheDeveloperBlog\Ramverk\Config\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Interface for configuration handlers.
	 *
	 * @package Ramverk
	 * @subpackage Config
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	interface IHandler
	{
		/**
		 * Execute the configuration handler.
		 * @param DOMDocument $document XML document with configuration data.
		 * @param Net\TheDeveloperBlog\Ramverk\Config $config Configuration container.
		 * @return array Retrieved configuration data.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(\DOMDocument $document, Ramverk\Config $config);
	}
}
// End of file: IHandler.interface.php
// Location: library/config/handler/IHandler.interface.php