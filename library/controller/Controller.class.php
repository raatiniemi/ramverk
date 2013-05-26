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
		 * Delegate the controller.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function delegate();
	}
}
// End of file: Controller.class.php
// Location: library/controller/Controller.class.php