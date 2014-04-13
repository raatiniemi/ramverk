<?php
namespace Me\Raatiniemi\Ramverk\Trunk\Action
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class Profile extends Trunk\Action
	{
		public function executeRead(array $parameters=array())
		{
			return 'Success';
		}
	}
}