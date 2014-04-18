<?php
namespace Me\Raatiniemi\Ramverk\Trunk\User\View
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class LogoutSuccess extends Trunk\View
	{
		public function executeHtml()
		{
			return $this->setupHtml(array('hej'));
		}

		public function executeJson()
		{
			return json_encode(array(
				'status' => 'success',
				'method' => __METHOD__
			));
		}
	}
}