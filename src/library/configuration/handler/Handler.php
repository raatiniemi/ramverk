<?php
namespace Me\Raatiniemi\Ramverk\Configuration;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Base functionality for configuration handlers.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
abstract class Handler
{
    /**
     * Configuration container.
     * @var Me\Raatiniemi\Ramverk\Configuration\Container
     */
    private $config;

    /**
     * Initialize the configuration handler.
     * @param Me\Raatiniemi\Ramverk\Configuration\Container $config Configuration container.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function __construct(Ramverk\Configuration $config)
    {
        $this->config = $config;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Execute the configuration handler.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document XML document with configuration data.
     * @return array Retrieved configuration data.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    abstract public function execute(Dom\Document $document);
}
// End of file: Handler.php
// Location: library/configuration/handler/Handler.php
