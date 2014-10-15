<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the autoload configuration handler.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Autoload extends \PHPUnit_Framework_TestCase
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

    public function testSimple()
    {
        // Load the simple sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <autoloads>
                    <autoload name="Foo">Bar.php</autoload>
                </autoloads>
            </configuration>'
        );

        $autoload = new Handler\Autoload($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array('Foo' => 'Bar.php')
        );
    }

    public function testGroupWithNamespace()
    {
        // Load the configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <autoloads namespace="Foo">
                    <autoload name="Bar">Baz.php</autoload>
                </autoloads>
            </configuration>'
        );

        $autoload = new Handler\Autoload($this->config->getMock());
        $this->assertEquals(
            $autoload->execute($document),
            array('Foo\\Bar' => 'Baz.php')
        );
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage
     */
    public function testAutoloadWithoutName()
    {
        // Load the configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <autoloads>
                    <autoload foo="Bar">Baz.php</autoload>
                </autoloads>
            </configuration>'
        );

        $autoload = new Handler\Autoload($this->config->getMock());
        $autoload->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage
     */
    public function testAutoloadWithoutValue()
    {
        // Load the configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <autoloads>
                    <autoload name="Foo" />
                </autoloads>
            </configuration>'
        );

        $autoload = new Handler\Autoload($this->config->getMock());
        $autoload->execute($document);
    }
}
// End of file: Autoload.php
// Location: test/library/configuration/handler/Autoload.php
