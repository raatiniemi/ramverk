<?php
namespace Net\TheDeveloperBlog\Ramverk\Controller
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Controller for web requests.
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
		public function dispatch()
		{
			echo '<pre>';
			var_dump($this->_routing);
			echo '</pre>';
		}
	}
}
// End of file: Web.class.php
// Location: library/controller/Web.class.php