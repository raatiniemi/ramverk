<?php
namespace Me\Raatiniemi\Ramverk\Trunk\Action
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Trunk;

	class Login extends Trunk\Action
	{
		public function executeWrite(array $data=array())
		{
			if(isset($data['username'], $data['password'])) {
				// TODO: Attempt to login...
				$login = TRUE;
				if($login) {
					return 'Success';
				}
			}
			// TODO: Supply the view with a message...
			// $this->setAttribute('error', 'Failed to login due to something');
			return 'Form';
		}

		public function executeRead()
		{
			return 'Form';
		}
	}
}