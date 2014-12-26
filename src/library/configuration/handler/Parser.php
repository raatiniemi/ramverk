<?php
namespace Me\Raatiniemi\Ramverk\Configuration\Handler;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;
use Me\Raatiniemi\Ramverk\Configuration;
use Me\Raatiniemi\Ramverk\Data\Dom;
use Me\Raatiniemi\Ramverk\Utility;

/**
 * Handles parsing of data from configuration handlers.
 *
 * @package Ramverk
 * @subpackage Configuration
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Parser
{
    // +------------------------------------------------------------------+
    // | Trait use-directives.                                            |
    // +------------------------------------------------------------------+
    use Utility\Filesystem;

    /**
     * Configuration container.
     * @var Me\Raatiniemi\Ramverk\Configuration\Container
     */
    private $config;

    /**
     * Profile for the application.
     * @var string
     */
    private $profile;

    /**
     * Context for the application.
     * @var string
     */
    private $context;

    /**
     * Configuration items found that match current context and profile.
     * @var array
     */
    protected $items;

    /**
     * Included parent documents, prevents infinite loops.
     * @var array
     */
    protected $parentDocuments;

    /**
     * Initialize the parser for configuration handlers.
     * @param Me\Raatiniemi\Ramverk\Configuration $config Configuration container.
     * @param string $profile Profile for the application.
     * @param string $context Context for the application.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function __construct(Ramverk\Configuration $config, $profile, $context)
    {
        $this->config = $config;
        $this->profile = $profile;
        $this->context = $context;
    }

    /**
     * Execute parsing of the configuration document.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document Configuration document to parse.
     * @return Me\Raatiniemi\Ramverk\Data\Dom\Document Parsed configuration document.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function execute(Dom\Document $document)
    {
        // Initialize/reset variables used while parsing.
        $this->items = array();
        $this->parentDocuments = array();

        // Initialize the document parsing.
        $this->parse($document);

        // Using reflections to give the handler the same object type
        // as the factory gave the parser.
        $documentReflection = new \ReflectionClass(get_class($document));

        // Instansiate the document with the encoding and version.
        $parsedDocument = $documentReflection->newInstanceArgs(array(
            $document->version,
            $document->encoding
        ));

        // Create the root node for the parsed document.
        $configurationElement = $parsedDocument->createElement('configuration');
        $parsedDocument->appendChild($configurationElement);

        // Loop through each of the saved configuration items and retrieve
        // their child elements, if available. Since there are different
        // element names, we cant specify the element name.
        $nodes = array();
        foreach ($this->items as $item) {
            if ($item->hasChildNodes()) {
                foreach ($item->childNodes as $node) {
                    if ($node->nodeType === XML_ELEMENT_NODE) {
                        $nodes[] = $node;
                    }
                }
            }
        }

        // Loop though the nodes and import them to the parsed configuration
        // document. Only the items that matched the profile and context of
        // the application will be available for import.
        foreach ($nodes as $node) {
            if (($item = $parsedDocument->importNode($node, true)) === false) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'Unable to import configuration node in "%s".',
                    $document->documentURI
                ));
            }

            // TODO: Handle DOM exceptions.
            // DOM_NO_MODIFICATION_ALLOWED_ERR
            // Raised if this node is readonly or if the previous
            // parent of the node being inserted is readonly.
            //
            // DOM_HIERARCHY_REQUEST_ERR
            // Raised if this node is of a type that does not allow
            // children of the type of the newnode node, or if the
            // node to append is one of this node's ancestors or
            // this node itself.
            //
            // DOM_WRONG_DOCUMENT_ERR
            // Raised if newnode was created from a different document
            // than the one that created this node.

            $configurationElement->appendChild($item);
        }

        return $parsedDocument;
    }

    /**
     * Parse the configuration document.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document Document to parse.
     * @return Me\Raatiniemi\Ramverk\Data\Dom\Document Parsed document.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function parse(Dom\Document $document)
    {
        // Run some quick validation of the document. Every valid document
        // have a configurations element as root.
        $documentElement = $document->documentElement;
        if ($documentElement->tagName !== 'configurations') {
            // TODO: Better specify the Exception-object.
            throw new Ramverk\Exception(
                'Invalid configuration document. Every configuration '.
                'document must have the "configurations" element as root.'
            );
        }

        // Check if the document has a parent document. If it does, we'd
        // want to process that document first. This way the configuration
        // items will be placed in the right order and we'll be able to
        // override parent configurations within the child configuration.
        $this->parseParentDocument($document);

        // Loop through each of the child nodes for the configurations element.
        // We'd only want to retrieve the configuration nodes.
        foreach ($documentElement->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE && $node->localName === 'configuration') {
                // There are a few conditions that the item have to match
                // in order for it to be included. First of, if neither the
                // profile or context attributes have been defined, the
                // item will be included (these are consider general items).
                //
                // If the item have either of the attributes and their
                // values matches the respective values, the item will be
                // included. Any other scenario the item will be ignored.
                if ($node->hasAttribute('profile')) {
                    if ($node->getAttribute('profile') !== $this->getProfile()) {
                        continue;
                    }
                }

                if ($node->hasAttribute('context')) {
                    if ($node->getAttribute('context') !== $this->getContext()) {
                        continue;
                    }
                }

                $this->items[] = $node;
            }
        }
    }

    /**
     * Initialize parsing of parent document, if available.
     * @param Me\Raatiniemi\Ramverk\Data\Dom\Document $document Current document being parsed.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function parseParentDocument(Dom\Document $document)
    {
        // Check if the document has a parent document.
        $documentElement = $document->documentElement;
        if ($documentElement->hasAttribute('parent')) {
            // Attempt to expand the parent document URI.
            $parent = Configuration\Utility::expand(
                $this->getConfig(),
                $documentElement->getAttribute('parent')
            );

            // To prevent infinite loops of parent document we haven to
            // check if the new parent document already have been included.
            if (in_array($parent, $this->parentDocuments)) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'Infinite inclusion-loop of parent documents '.
                    'detected within the configuration file "%s".',
                    $document->documentURI
                ));
            }

            // Check that the parent document actually exists, and is readable.
            if (!$this->isReadable($parent) || !$this->isFile($parent)) {
                // TODO: Better specify the Exception-object.
                throw new Ramverk\Exception(sprintf(
                    'Parent configuration file "%s" do not exists.',
                    $parent
                ));
            }
            // Save the parent URI, prevents infinite loops.
            $this->parentDocuments[] = $parent;

            // Using reflections to give the handler the same object type
            // as the factory gave the parser.
            $documentReflection = new \ReflectionClass(get_class($document));

            // Instansiate the document with the encoding and version. And,
            // load the parent document.
            $parentDocument = $documentReflection->newInstanceArgs(array(
                $document->version,
                $document->encoding
            ));
            $parentDocument->load($parent);

            // Parse the parent document and retrieve the items.
            $this->parse($parentDocument);
        }
    }

    /**
     * Retrieve the configuration container.
     * @return Me\Raatiniemi\Ramverk\Configuration\Container Configuration container.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * Retrieve the application profile.
     * @return string Application profile.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function getProfile()
    {
        return $this->profile;
    }

    /**
     * Retrieve the application context.
     * @return string Application context.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    protected function getContext()
    {
        return $this->context;
    }
}
// End of file: Parser.php
// Location: library/configuration/handler/Parser.php
