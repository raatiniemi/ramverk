<?php
namespace Me\Raatiniemi\Ramverk\Trunk;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

class Action extends Ramverk\Action
{
    public function execute()
    {
        throw new Ramverk\Exception('Method is not available');
    }
}
