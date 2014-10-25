<?php
namespace Me\Raatiniemi\Ramverk\Trunk\Index\View;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Trunk;

class IndexSuccess extends Trunk\View
{
    public function executeHtml()
    {
        return $this->setupHtml();
    }

    public function executeJson()
    {
        return json_encode(array(
            'status' => 'success',
            'method' => __METHOD__
        ));
    }
}
