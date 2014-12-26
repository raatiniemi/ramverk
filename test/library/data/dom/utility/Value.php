<?php
namespace Me\Raatiniemi\Ramverk\Test\Data\Dom\Utility;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

/**
 * Unit test case for handling value retrieval for DOM nodes/elements.
 *
 * @package Ramverk
 * @subpackage Data
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Value extends \PHPUnit_Framework_TestCase
{
    private $trait = 'Me\\Raatiniemi\\Ramverk\\Data\\Dom\\Utility\\Value';

    public function testGetValueWithNullValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = null;

        $this->assertNull($mock->getValue());
    }

    public function testGetValueWithEmptyValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = '';

        $this->assertNull($mock->getValue());
    }

    public function testHasValueWithNullValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = null;

        $this->assertFalse($mock->hasValue());
    }

    public function testHasValueWithEmptyValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = '';

        $this->assertFalse($mock->hasValue());
    }

    public function testHasValueWithNumericValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = 5.3;

        $this->assertTrue($mock->hasValue());
    }

    public function testHasValueWithZeroValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = 0.0;

        $this->assertTrue($mock->hasValue());
    }

    public function testHasValueWithStringValue()
    {
        $mock = $this->getMockForTrait($this->trait);
        $mock->nodeValue = 'foobar';

        $this->assertTrue($mock->hasValue());
    }

    public function testGetAttributeDefaultValue()
    {
        $mock = $this->getMockForTrait(
            $this->trait,
            array(),
            '',
            true,
            true,
            true,
            array('hasAttribute')
        );

        $mock->expects($this->once())
            ->method('hasAttribute')
            ->willReturn(false);

        $this->assertFalse($mock->getAttribute('foobar', false));
    }

    public function testTypecastBooleanTrue()
    {
        $mock = $this->getMockForTrait($this->trait);

        $method = new \ReflectionMethod($mock, 'handleTypecast');
        $method->setAccessible(true);

        $this->assertEquals(true, $method->invoke($mock, 'true'));
    }

    public function testTypecastBooleanFalse()
    {
        $mock = $this->getMockForTrait($this->trait);

        $method = new \ReflectionMethod($mock, 'handleTypecast');
        $method->setAccessible(true);

        $this->assertEquals(false, $method->invoke($mock, 'false'));
    }

    public function testTypecastInteger()
    {
        $mock = $this->getMockForTrait($this->trait);

        $method = new \ReflectionMethod($mock, 'handleTypecast');
        $method->setAccessible(true);

        $this->assertEquals(35, $method->invoke($mock, '35'));
    }

    public function testTypecastDecimal()
    {
        $mock = $this->getMockForTrait($this->trait);

        $method = new \ReflectionMethod($mock, 'handleTypecast');
        $method->setAccessible(true);

        $this->assertEquals(315.313, $method->invoke($mock, '315.313'));
    }
}
// End of file: Value.php
// Location: test/library/data/dom/utility/Value.php
