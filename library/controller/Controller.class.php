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
	 * Base functionality for the controller.
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
		 * Dispatch the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function dispatch();
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php