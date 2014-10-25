<?php
namespace Me\Raatiniemi\Ramverk\Trunk\User\Action;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Trunk;

class Login extends Trunk\Action
{
    public function executeWrite(array $parameters = array())
    {
        if (isset($parameters['username'], $parameters['password'])) {
            // TODO: Attempt to login...
            $login = true;
            if ($login) {
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
