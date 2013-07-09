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
	 * Base for handling controller actions.
	 *
	 * @package Ramverk
	 * @subpackage Action
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	abstract class Action
	{
		/**
		 * Execute the action.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		abstract public function execute();
	}
}
// End of file: Action.class.php
// Location: library/action/Action.class.php