<?php
namespace Me\Raatiniemi\Ramverk\Test\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk\Configuration\Handler;
use Me\Raatiniemi\Ramverk\Data\Dom;

/**
 * Unit test case for the configuration parser.
 *
 * @package Ramverk
 * @subpackage Test
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Parser extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInitializationWithoutConfiguration()
    {
        new Handler\Parser(null, null, null);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInitializationWithIncorrectConfigurationObject()
    {
        new Handler\Parser(new \stdClass, null, null);
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage Invalid configuration document. Every configuration document must have the "configurations" element as root.
     */
    public function testDocumentWithoutRootConfigurationsItem()
    {
        $parser = new Handler\Parser($this->config->getMock(), 'profile', 'context');

        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML('<foo><bar baz="quux" /></foo>');

        $reflection = new \ReflectionClass(get_class($parser));

        $parse = $reflection->getMethod('parse');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }

    public function testBasicDocument()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations>'.
            '<configuration>'.
            '<foo>'.
            '<bar baz="quux" />'.
            '</foo>'.
            '</configuration>'.
            '</configurations>'
        );

        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('parseParentDocument'))
            ->getMock();

        $parser->expects($this->once())
            ->method('parseParentDocument')
            ->with($document);

        $parsedDocument = $parser->execute($document);

        $this->assertTrue($parsedDocument instanceof Dom\Document);
        $this->assertEquals(
            $parsedDocument->saveXML(),
            "<?xml version=\"1.0\"?>\n".
            '<configuration>'.
            '<foo>'.
            '<bar baz="quux"/>'.
            '</foo>'.
            "</configuration>\n"
        );
    }

    public function testDocumentWithMultipleGroups()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations>'.
            '<configuration>'.
            '<foo>'.
            '<bar baz="quux" />'.
            '</foo>'.
            '</configuration>'.
            '<configuration>'.
            '<bar>'.
            '<baz quux="" />'.
            '</bar>'.
            '</configuration>'.
            '</configurations>'
        );


        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('parseParentDocument'))
            ->getMock();

        $parser->expects($this->once())
            ->method('parseParentDocument')
            ->with($document);

        $parsedDocument = $parser->execute($document);

        $this->assertTrue($parsedDocument instanceof Dom\Document);
        $this->assertEquals(
            $parsedDocument->saveXML(),
            "<?xml version=\"1.0\"?>\n".
            '<configuration>'.
            '<foo>'.
            '<bar baz="quux"/>'.
            '</foo>'.
            '<bar>'.
            '<baz quux=""/>'.
            '</bar>'.
            "</configuration>\n"
        );
    }

    public function testDocumentWithMultipleProfiles()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations>'.
            '<configuration profile="foobar">'.
            '<foo>'.
            '<bar baz="quux" />'.
            '</foo>'.
            '</configuration>'.
            '<configuration>'.
            '<bar>'.
            '<baz quux="" />'.
            '</bar>'.
            '</configuration>'.
            '<configuration profile="profile">'.
            '<baz>'.
            '<quux />'.
            '</baz>'.
            '</configuration>'.
            '</configurations>'
        );

        $parser = new Handler\Parser($this->config->getMock(), 'profile', 'context');
        $parsedDocument = $parser->execute($document);

        $this->assertTrue($parsedDocument instanceof Dom\Document);
        $this->assertEquals(
            $parsedDocument->saveXML(),
            "<?xml version=\"1.0\"?>\n".
            '<configuration>'.
            '<bar>'.
            '<baz quux=""/>'.
            '</bar>'.
            '<baz>'.
            '<quux/>'.
            '</baz>'.
            "</configuration>\n"
        );
    }

    public function testDocumentWithMultipleContext()
    {
        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations>'.
            '<configuration context="foobar">'.
            '<foo>'.
            '<bar baz="quux" />'.
            '</foo>'.
            '</configuration>'.
            '<configuration>'.
            '<bar>'.
            '<baz quux="" />'.
            '</bar>'.
            '</configuration>'.
            '<configuration context="context">'.
            '<baz>'.
            '<quux />'.
            '</baz>'.
            '</configuration>'.
            '</configurations>'
        );

        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('parseParentDocument'))
            ->getMock();

        $parser->expects($this->once())
            ->method('parseParentDocument')
            ->with($document);

        $parsedDocument = $parser->execute($document);

        $this->assertTrue($parsedDocument instanceof Dom\Document);
        $this->assertEquals(
            $parsedDocument->saveXML(),
            "<?xml version=\"1.0\"?>\n".
            '<configuration>'.
            '<bar>'.
            '<baz quux=""/>'.
            '</bar>'.
            '<baz>'.
            '<quux/>'.
            '</baz>'.
            "</configuration>\n"
        );
    }

    public function testParseDocumentWithoutParent()
    {
        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setMethods(array('expandDirectives'))
            ->disableOriginalConstructor()
            ->getMock();

        // If the `expandDirectives`-method is called we've detected a parent document.
        $parser->expects($this->exactly(0))
            ->method('expandDirectives');

        $document = new Dom\Document();
        $document->loadXML(
            '<configuration>'.
            '</configuration>'
        );

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser');

        $parse = $reflection->getMethod('parseParentDocument');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage /^Infinite inclusion-loop of parent documents detected within the configuration file \"([\w\/\\])+\"\.$/
     */
    public function testParentDocumentWithInfiniteInclusionLoop()
    {
        $parser = new Handler\Parser($this->config->getMock(), 'profile', 'context');

        $document = new Dom\Document();
        $document->loadXML(
            '<configuration parent="foobar">'.
            '</configuration>'
        );

        $reflection = new \ReflectionClass('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser');

        $parents = $reflection->getProperty('parentDocuments');
        $parents->setAccessible(true);
        $parents->setValue($parser, array('foobar'));

        $parse = $reflection->getMethod('parseParentDocument');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage Parent configuration file "foobar" do not exists.
     */
    public function testDocumentWithoutReadableParentDocument()
    {
        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('isReadable'))
            ->getMock();

        $parser->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(false);

        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations parent="foobar">'.
            '<configuration>'.
            '<bar />'.
            '</configuration>'.
            '</configurations>'
        );

        $reflection = new \ReflectionClass(get_class($parser));

        $parents = $reflection->getProperty('parentDocuments');
        $parents->setAccessible(true);
        $parents->setValue($parser, array());

        $parse = $reflection->getMethod('parseParentDocument');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }

    /**
     * @expectedException Me\Raatiniemi\Ramverk\Exception
     * @expectedExceptionMessage Parent configuration file "foobar" do not exists.
     */
    public function testDocumentWithoutRegularFileAsParentDocument()
    {
        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('isReadable', 'isFile'))
            ->getMock();

        $parser->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(true);

        $parser->expects($this->once())
            ->method('isFile')
            ->with('foobar')
            ->willReturn(false);

        // Load the sample configuration to the document.
        $document = new Dom\Document();
        $document->loadXML(
            '<configurations parent="foobar">'.
            '<configuration>'.
            '<bar />'.
            '</configuration>'.
            '</configurations>'
        );

        $reflection = new \ReflectionClass(get_class($parser));

        $parents = $reflection->getProperty('parentDocuments');
        $parents->setAccessible(true);
        $parents->setValue($parser, array());

        $parse = $reflection->getMethod('parseParentDocument');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }

    /**
     * @note
     * Since I havn't figured out a way to properly mock a new reflection
     * instance from within the `parseParentDocument`-method the actual test
     * here is that the `parse`-method is called once without any errors.
     */
    public function testParseParentDocument()
    {
        $parser = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Configuration\\Handler\\Parser')
            ->setConstructorArgs(array($this->config->getMock(), 'profile', 'context'))
            ->setMethods(array('isReadable', 'isFile', 'parse'))
            ->getMock();

        $parser->expects($this->once())
            ->method('isReadable')
            ->with('foobar')
            ->willReturn(true);

        $parser->expects($this->once())
            ->method('isFile')
            ->with('foobar')
            ->willReturn(true);

        $parser->expects($this->once())
            ->method('parse')
            ->willReturn(null);

        // Have to mock the `load`-method, otherwise it'll trigger an error
        // when the parent document URI don't exists.
        $document = $this->getMockBuilder('Me\\Raatiniemi\\Ramverk\\Data\\Dom\\Document')
            ->setMethods(array('load'))
            ->getMock();

        // Load the sample configuration to the document.
        $document->loadXML(
            '<configurations parent="foobar">'.
            '<configuration>'.
            '<bar />'.
            '</configuration>'.
            '</configurations>'
        );

        $reflection = new \ReflectionClass(get_class($parser));

        $parents = $reflection->getProperty('parentDocuments');
        $parents->setAccessible(true);
        $parents->setValue($parser, array());

        $parse = $reflection->getMethod('parseParentDocument');
        $parse->setAccessible(true);
        $parse->invokeArgs($parser, array($document));
    }
}
// End of file: Parser.php
// Location: test/library/configuration/handler/Parser.php
