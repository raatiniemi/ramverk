<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration as Config;

/**
 * Unit test case for the configuration utility.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Utility extends \PHPUnit_Framework_TestCase
{
    public function testExpandDirective()
    {
        $config = new Config();
        $config->set('foo', 'bar');

        $value = Config\Utility::expand($config, '%foo%');
        $this->assertEquals($value, 'bar');
    }

    public function testExpandMultipleDirectives()
    {
        $config = new Config();
        $config->set('foo', 'bar');
        $config->set('baz', 'qux');

        $value = Config\Utility::expand($config, '%foo%%baz%');
        $this->assertEquals($value, 'barqux');
    }

    public function testExpandDirectiveWithoutDirectives()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, '%foo%');
        $this->assertEquals($value, '%foo%');
    }

    public function testExpandDirectiveWithoutReference()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, 'foo');
        $this->assertEquals($value, 'foo');
    }

    public function testExpandDirectiveWithNullValue()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, null);
        $this->assertNull($value);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value with configuration directives is of type "integer" and not a string.
     */
    public function testExpandDirectiveWithIntegerValue()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, 1);
        $this->assertEquals($value, 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value with configuration directives is of type "double" and not a string.
     */
    public function testExpandDirectiveWithFloatValue()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, 1.2);
        $this->assertEquals($value, 1.2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value with configuration directives is of type "object" and not a string.
     */
    public function testExpandDirectiveWithObjectValue()
    {
        $config = new Config();

        $value = Config\Utility::expand($config, new \stdClass);
        $this->assertEquals($value, new \stdClass);
    }
}
// End of file: Utility.php
// Location: test/library/configuration/Utility.php
