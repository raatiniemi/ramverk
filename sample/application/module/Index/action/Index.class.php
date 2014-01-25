<?php
namespace Me\Raatiniemi\Ramverk\Sample\Application\Index\Action
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Sample\Application;

	/**
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Index extends Application\Action
	{
		public function executeRead()
		{
			return 'Success';
		}
	}
}
// End of file: Action.class.php
// Location: sample/application/module/Index/action/Index.class.php