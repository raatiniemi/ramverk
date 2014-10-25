<?php
namespace Me\Raatiniemi\Ramverk\Trunk\User\View;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Trunk;

class ProfileSuccess extends Trunk\View
{
    public function executeHtml()
    {
        return $this->setupHtml();
    }

    public function executeJson(array $params = array())
    {
        return json_encode(array(
            'status' => 'success',
            'method' => __METHOD__,
            'parameters' => $params
        ));
    }
}
