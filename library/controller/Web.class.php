<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Controller
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Functionality for the web based controller.
	 *
	 * @package Ramverk
	 * @subpackage Controller
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Web extends Ramverk\Controller
	{
		/**
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function dispatch()
		{
			$this->initializeModule();

			if($this->_config->get('enabled')) {
				// TODO: Handle enabled modules.
			} else {
				// TODO: Handle disabled modules.
			}
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php