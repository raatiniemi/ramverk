<?php
namespace Me\Raatiniemi\Ramverk\Trunk\View
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class LoginForm extends Trunk\View
	{
		public function executeHtml()
		{
			return $this->setupHtml(array(), NULL);
		}
	}
}