<?php
namespace Me\Raatiniemi\Ramverk\Trunk\Error\Action;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Trunk;

class NotFound extends Trunk\Action
{
    public function execute()
    {
        return 'Success';
    }
}
