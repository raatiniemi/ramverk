<?php
namespace Me\Raatiniemi\Ramverk\Trunk\User\View
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class LoginFailure extends Trunk\View
	{
		public function executeHtml()
		{
			echo __METHOD__;
		}

		public function executeJson()
		{
			echo json_encode(array(
				'status' => 'failed',
				'method' => __METHOD__
			));
		}
	}
}