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
    private $class = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Factory';

    // Stores the mock builder object for the Configuration-class.
    private $config;

    // Stores the mock builder object for the Cache-class.
    private $cache;

    // Stores the mock builder object for the Parser-class.
    private $parser;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration')
            ->disableOriginalConstructor();

        $this->cache = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Cache')
            ->disableOriginalConstructor();

        $this->parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->disableOriginalConstructor();
    }

    public function tearDown()
    {
        $this->config = null;
        $this->cache = null;
        $this->parser = null;
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage Configuration directory "/var/www" do not exists.
     */
    public function testCallHandlerWithoutValidPath()
    {
        $factory = $this->getMockBuilder($this->class)
            ->disableOriginalConstructor()
            ->setMethods(array('isDir'))
            ->getMock();

        $factory->expects($this->once())
            ->method('isDir')
            ->with('/var/www')
            ->willReturn(false);

        $factory->callHandler('foo', '/var/www/configuration.xml');
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage The specified configuration file "configuration.xml" do not exists.
     */
    public function testCallHandlerWithoutReadableConfigurationFile()
    {
        $factory = $this->getMockBuilder($this->class)
            ->disableOriginalConstructor()
            ->setMethods(array('isDir', 'isReadable'))
            ->getMock();

        $factory->expects($this->once())
            ->method('isDir')
            ->with('/var/www')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('isReadable')
            ->with('/var/www/configuration.xml')
            ->willReturn(false);

        $factory->callHandler('foo', '/var/www/configuration.xml');
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage The specified configuration file "configuration.xml" do not exists.
     */
    public function testCallHandlerWithoutRegularFileAsConfigurationFile()
    {
        $factory = $this->getMockBuilder($this->class)
            ->disableOriginalConstructor()
            ->setMethods(array('isDir', 'isReadable', 'isFile'))
            ->getMock();

        $factory->expects($this->once())
            ->method('isDir')
            ->with('/var/www')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('isReadable')
            ->with('/var/www/configuration.xml')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('isReadable')
            ->with('/var/www/configuration.xml')
            ->willReturn(false);

        $factory->callHandler('foo', '/var/www/configuration.xml');
    }

    public function testCallHandlerWithoutModifiedConfigurationFile()
    {
        $cache = $this->cache->setMethods(array('generateName', 'isModified', 'read'))
            ->getMock();

        $cache->expects($this->once())
            ->method('generateName')
            ->with('/var/www/configuration.xml')
            ->willReturn('cache.php');

        $cache->expects($this->once())
            ->method('isModified')
            ->with('/var/www/configuration.xml', '')
            ->willReturn(false);

        $factory = $this->getMockBuilder($this->class)
            ->setConstructorArgs(
                array(
                    $this->config->getMock(),
                    $cache,
                    $this->parser->getMock()
                )
            )
            ->setMethods(array('expandDirectives', 'isDir', 'isReadable', 'isFile'))
            ->getMock();

        $factory->expects($this->at(0))
            ->method('expandDirectives')
            ->with('/var/www/configuration.xml')
            ->willReturn('/var/www/configuration.xml');

        $factory->expects($this->once())
            ->method('isDir')
            ->with('/var/www')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('isReadable')
            ->with('/var/www/configuration.xml')
            ->willReturn(true);

        $factory->expects($this->once())
            ->method('isFile')
            ->with('/var/www/configuration.xml')
            ->willReturn(true);

        $factory->expects($this->at(1))
            ->method('expandDirectives')
            ->with('%directory.application.cache%/cache.php')
            ->willReturn('/var/www/cache.php');

        $factory->callHandler('foo', '/var/www/configuration.xml');
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage The configuration handler "foobar" do not exists.
     */
    public function testLoadNonExistingHandler()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $reflection = new \ReflectionClass($this->class);

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
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $reflection = new \ReflectionClass($this->class);

        $loadHandler = $reflection->getMethod('loadHandler');
        $loadHandler->setAccessible(true);
        $loadHandler->invokeArgs($factory, array(get_class($factory)));
    }

    public function testLoadHandler()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );
        $handler = 'Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Core';

        $reflection = new \ReflectionClass($this->class);

        $loadHandler = $reflection->getMethod('loadHandler');
        $loadHandler->setAccessible(true);
        $this->assertNull($loadHandler->invokeArgs($factory, array($handler)));
    }

    public function testHasRegisteredHandler()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $reflection = new \ReflectionClass($this->class);

        $availableHandlers = $reflection->getProperty('availableHandlers');
        $availableHandlers->setAccessible(true);
        $availableHandlers->setValue($factory, array('foo' => 'bar'));

        $this->assertTrue($factory->hasHandler('foo'));
    }

    public function testHasNoneRegisteredHandler()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $this->assertFalse($factory->hasHandler('foo'));
    }

    public function testHasInstansiatedHandlers()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $reflection = new \ReflectionClass($this->class);

        $handlers = $reflection->getProperty('handlers');
        $handlers->setAccessible(true);
        $handlers->setValue($factory, array('foo' => 'bar'));

        $isInstansiated = $reflection->getMethod('isInstansiated');
        $isInstansiated->setAccessible(true);
        $this->assertTrue($isInstansiated->invokeArgs($factory, array('foo')));
    }

    public function testNoHasInstansiatedHandlers()
    {
        $factory = new Handler\Factory(
            $this->config->getMock(),
            $this->cache->getMock(),
            $this->parser->getMock()
        );

        $reflection = new \ReflectionClass($this->class);

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
        $factory = $this->getMockBuilder($this->class)
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
        $factory = $this->getMockBuilder($this->class)
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
