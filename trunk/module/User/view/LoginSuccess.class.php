<?php
namespace Me\Raatiniemi\Ramverk\Trunk\User\View
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class LoginSuccess extends Trunk\View
	{
		public function executeHtml()
		{
			echo __METHOD__;
		}

		public function executeJson()
		{
			echo json_encode(array(
				'status' => 'success',
				'method' => __METHOD__
			));
		}
	}
}