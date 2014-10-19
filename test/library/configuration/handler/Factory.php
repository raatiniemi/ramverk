<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the configuration handler factory.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Factory extends \PHPUnit_Framework_TestCase
{
    // Stores the mock builder object for the Configuration-class.
    private $config;

    // Stores the mock builder object for the Cache-class.
    private $cache;

    // Stores the mock builder object for the Parser-class.
    private $parser;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Cache')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function tearDown()
    {
        $this->config = null;
        $this->cache = null;
        $this->parser = null;
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage The configuration handler "foobar" do not exists.
     */
    public function testLoadNonExistingHandler()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $loadHandler = $reflection->getMethod('loadHandler');
        $loadHandler->setAccessible(true);
        $loadHandler->invokeArgs($factory, array('foobar'));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage /^The configuration handler \"([\w\\]+)\" do not extend the base configuration handler\.$/
     */
    public function testLoadHandlerWithIncorrectSuperClass()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $loadHandler = $reflection->getMethod('loadHandler');
        $loadHandler->setAccessible(true);
        $loadHandler->invokeArgs($factory, array(get_class($factory)));
    }

    public function testLoadHandler()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);
        $handler = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Core';

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $loadHandler = $reflection->getMethod('loadHandler');
        $loadHandler->setAccessible(true);
        $this->assertNull($loadHandler->invokeArgs($factory, array($handler)));
    }

    public function testHasRegisteredHandler()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $availableHandlers = $reflection->getProperty('availableHandlers');
        $availableHandlers->setAccessible(true);
        $availableHandlers->setValue($factory, array('foo' => 'bar'));

        $this->assertTrue($factory->hasHandler('foo'));
    }

    public function testHasNoneRegisteredHandler()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);
        $this->assertFalse($factory->hasHandler('foo'));
    }

    public function testHasInstansiatedHandlers()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $handlers = $reflection->getProperty('handlers');
        $handlers->setAccessible(true);
        $handlers->setValue($factory, array('foo' => 'bar'));

        $isInstansiated = $reflection->getMethod('isInstansiated');
        $isInstansiated->setAccessible(true);
        $this->assertTrue($isInstansiated->invokeArgs($factory, array('foo')));
    }

    public function testNoHasInstansiatedHandlers()
    {
        $factory = new Handler\Factory($this->config, $this->cache, $this->parser);

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory');

        $isInstansiated = $reflection->getMethod('isInstansiated');
        $isInstansiated->setAccessible(true);
        $this->assertFalse($isInstansiated->invokeArgs($factory, array('foo')));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage /^The configuration handler \"\w+\" have already been registered\.$/
     */
    public function testAlreadyRegisteredHandler()
    {
        $factory = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('hasHandler'))
            ->getMock();

        $factory->expects($this->once())
            ->method('hasHandler')
            ->with('foo')
            ->willReturn(true);

        $factory->registerHandler('foo', 'bar');
    }

    public function testRegisterHandler()
    {
        $factory = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('hasHandler'))
            ->getMock();

        $factory->expects($this->once())
            ->method('hasHandler')
            ->with('foo')
            ->willReturn(false);

        $this->assertTrue($factory->registerHandler('foo', 'bar'));
    }
}
// End of file: Factory.php
// Location: test/library/configuration/handler/Factory.php
