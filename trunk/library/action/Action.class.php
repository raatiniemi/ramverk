<?php
namespace Me\Raatiniemi\Ramverk\Trunk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	class Action extends Ramverk\Action
	{
		public function execute()
		{
			throw new Exception('Method is not available');
		}
	}
}