<?php
namespace Me\Raatiniemi\Ramverk\Trunk\Action
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class Logout extends Trunk\Action
	{
		public function executeRead()
		{
			return 'Success';
		}
	}
}