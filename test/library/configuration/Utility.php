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

        $this->assertEquals(Config\Utility::expand($config, '%foo%'), 'bar');
    }

    public function testExpandDirectives()
    {
        $config = new Config();
        $config->set('foo', 'bar');
        $config->set('baz', 'qux');

        $this->assertEquals(Config\Utility::expand($config, '%foo%%baz%'), 'barqux');
    }

    public function testExpandDirectiveWithoutDirectives()
    {
        $config = new Config();

        $this->assertEquals(Config\Utility::expand($config, '%foo%'), '%foo%');
    }

    public function testExpandDirectiveWithoutReference()
    {
        $config = new Config();

        $this->assertEquals(Config\Utility::expand($config, 'foo'), 'foo');
    }
}
// End of file: Utility.php
// Location: test/library/configuration/Utility.php
