<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the routing configuration handler.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Routing extends \PHPUnit_Framework_TestCase
{
    // Stores the mock builder object for the Configuration-class.
    private $config;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration');
    }

    public function tearDown()
    {
        $this->config = null;
    }

    public function testSimpleRoute()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" pattern="^$" module="bar" action="baz" />
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array(
                array(
                    'name' => 'foo',
                    'pattern' => '^$',
                    'module' => 'bar',
                    'action' => 'baz'
                )
            )
        );
    }

    public function testEmptySection()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array()
        );
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testRouteWithoutName()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route pattern="^$" module="bar" action="baz" />
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $autoload->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testRouteWithoutPattern()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" module="bar" action="baz" />
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $autoload->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testRouteWithoutModule()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" pattern="^$" action="baz" />
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $autoload->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testRouteWithoutAction()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" pattern="^$" module="bar" />
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $autoload->execute($document);
    }

    public function testNestedRoute()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" pattern="^bar" module="baz">
                        <route name=".qux" pattern="/{id:\d+}$" action="quux" />
                    </route>
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array(
                array(
                    'name' => 'foo.qux',
                    'pattern' => '^bar/{id:\d+}$',
                    'module' => 'baz',
                    'action' => 'quux'
                )
            )
        );
    }

    public function testNestedRoutes()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <routes>
                    <route name="foo" pattern="^bar" module="baz">
                        <route name=".qux" pattern="/{id:\d+}$" action="quux" />
                        <route name=".corge" pattern="/{name:\w+}$" action="grault" />
                    </route>
                    <route name="waldo" module="fred">
                        <route name=".xyzzy" pattern="^{id:\d+}$" action="thud" />
                    </route>
                </routes>
            </configuration>'
        );

        $autoload = new Handler\Routing($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array(
                array(
                    'name' => 'foo.qux',
                    'pattern' => '^bar/{id:\d+}$',
                    'module' => 'baz',
                    'action' => 'quux'
                ),
                array(
                    'name' => 'foo.corge',
                    'pattern' => '^bar/{name:\w+}$',
                    'module' => 'baz',
                    'action' => 'grault'
                ),
                array(
                    'name' => 'waldo.xyzzy',
                    'pattern' => '^{id:\d+}$',
                    'module' => 'fred',
                    'action' => 'thud'
                )
            )
        );
    }
}
// End of file: Routing.php
// Location: test/library/configuration/handler/Routing.php
