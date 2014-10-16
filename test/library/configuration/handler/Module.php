<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the module configuration handler.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Module extends \PHPUnit_Framework_TestCase
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

    public function testBasicDocument()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <settings>
                    <setting name="foo">Bar</setting>
                    <setting name="baz">Quux</setting>
                </settings>
            </configuration>'
        );

        $module = new Handler\Module($this->config->getMock());
        $this->assertEquals(
            $module->execute($document),
            array(
                'module.foo' => 'Bar',
                'module.baz' => 'Quux'
            )
        );
    }

    public function testEmptyDocument()
    {
        $document = new Dom\Document();

        $module = new Handler\Module($this->config->getMock());
        $this->assertEquals($module->execute($document), array());
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSettingWithoutName()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <settings>
                    <setting>Foo</setting>
                </settings>
            </configuration>'
        );

        $module = new Handler\Module($this->config->getMock());
        $module->execute($document);
    }

    public function testMultipleSections()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <settings>
                    <setting name="foo">Bar</setting>
                </settings>
                <settings>
                    <setting name="baz">Quux</setting>
                </settings>
            </configuration>'
        );

        $module = new Handler\Module($this->config->getMock());
        $this->assertEquals(
            $module->execute($document),
            array(
                'module.foo' => 'Bar',
                'module.baz' => 'Quux'
            )
        );
    }

    public function testSettingWithCapitalizedName()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <settings>
                    <setting name="FOO">Bar</setting>
                </settings>
            </configuration>'
        );

        $module = new Handler\Module($this->config->getMock());
        $this->assertEquals(
            $module->execute($document),
            array('module.foo' => 'Bar')
        );
    }
}
// End of file: Module.php
// Location: test/library/configuration/handler/Module.php
