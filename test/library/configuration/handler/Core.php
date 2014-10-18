<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the core configuration handler.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Core extends \PHPUnit_Framework_TestCase
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

    public function testDefaultSystemActions()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                        <action>Index</action>
                    </system_action>
                    <system_action name="404">
                        <module>Error</module>
                        <action>NotFound</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $this->assertEquals(
            $core->execute($document),
            array(
                'actions.default_module' => 'Index',
                'actions.default_action' => 'Index',
                'actions.404_module' => 'Error',
                'actions.404_action' => 'NotFound'
            )
        );
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testWithoutSystemActions()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testWithoutDefaultSystemAction()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="404">
                        <module>Error</module>
                        <action>NotFound</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testWithout404SystemAction()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                        <action>Index</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSystemActionWithoutName()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action>
                        <module>Error</module>
                        <action>NotFound</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSystemActionWithoutModule()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <action>Index</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSystemActionWithoutModuleValue()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module></module>
                        <action>Index</action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSystemActionWithoutAction()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     */
    public function testSystemActionWithoutActionValue()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                        <action></action>
                    </system_action>
                </system_actions>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }

    public function testBasicSettings()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                        <action>Index</action>
                    </system_action>
                    <system_action name="404">
                        <module>Error</module>
                        <action>NotFound</action>
                    </system_action>
                </system_actions>
                <settings>
                    <setting name="Foo">Bar</setting>
                </settings>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $this->assertEquals(
            $core->execute($document),
            array(
                'actions.default_module' => 'Index',
                'actions.default_action' => 'Index',
                'actions.404_module' => 'Error',
                'actions.404_action' => 'NotFound',
                'core.foo' => 'Bar'
            )
        );
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
                <system_actions>
                    <system_action name="default">
                        <module>Index</module>
                        <action>Index</action>
                    </system_action>
                    <system_action name="404">
                        <module>Error</module>
                        <action>NotFound</action>
                    </system_action>
                </system_actions>
                <settings>
                    <setting>Foo</setting>
                </settings>
            </configuration>'
        );

        $core = new Handler\Core($this->config->getMock());
        $core->execute($document);
    }
}
// End of file: Core.php
// Location: test/library/configuration/handler/Core.php
