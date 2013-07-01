<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Configuration\Handler
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;
	use Net\TheDeveloperBlog\Ramverk\Configuration;

	/**
	 * Handles configuration parsing.
	 *
	 * @package Ramverk
	 * @subpackage Configuration
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Parser
	{
		/**
		 * Configuration container.
		 * @var Net\TheDeveloperBlog\Ramverk\Configuration\Container
		 */
		protected $_config;

		/**
		 * Application profile.
		 * @var string
		 */
		protected $_profile;

		/**
		 * Application context.
		 * @var string
		 */
		protected $_context;

		/**
		 * Configuration items found that match the current context.
		 * @var array
		 */
		protected $_items;

		/**
		 * Included parent documents, prevents infinite loops.
		 * @var array
		 */
		protected $_parentDocuments;

		/**
		 * Initialize the configuration parser.
		 * @param Net\TheDeveloperBlog\Ramverk\Configuration\Container $config Configuration container.
		 * @param string $profile Application profile.
		 * @param string $context Application context.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(Configuration\Container $config, $profile, $context)
		{
			$this->_config = $config;
			$this->_profile = $profile;
			$this->_context = $context;

			// Initialize variables used while parsing.
			$this->_items = array();
			$this->_parentDocuments = array();
		}

		/**
		 * Execute the document parsing.
		 * @param DOMDocument $document Document to parse.
		 * @return DOMDocument Parsed document.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function execute(\DOMDocument $document)
		{
			$this->parse($document);

			// Loop through each of the saved configuration items and retrieve
			// the child element nodes. Since there're different elements, we
			// can't specify the element name.
			$nodes = array();
			foreach($this->_items as $item) {
				if($item->hasChildNodes()) {
					foreach($item->childNodes as $node) {
						if($node->nodeType === XML_ELEMENT_NODE) {
							$nodes[] = $node;
						}
					}
				}
			}

			// Using reflection because we'd want to give the handler
			// the same object type that the factory want to parse.
			$documentReflection = new \ReflectionClass(get_class($document));

			// Instansiate and load the parent configuration document,
			// with the version and encoding from the first document.
			$parsedDocument = $documentReflection->newInstanceArgs(array(
				$document->version,
				$document->encoding
			));
			$configurationElement = $parsedDocument->createElement('configuration');

			// Rebuild the configuration document. Only keep one configuration
			// item with all of the matched (profile etc.) configurations. This
			// way we won't have to loop through multiple configurations within
			// the handler.
			$parsedDocument->appendChild($configurationElement);

			// Loop through the nodes and import them into the parsed document.
			foreach($nodes as $node) {
				if(($item = $parsedDocument->importNode($node, TRUE)) === FALSE) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Unable to import configuration node in "%s".',
						$document->documentURI
					));
				}

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
		 * @param DOMDocument $document Document to parse.
		 * @return DOMDocument Parsed document.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function parse(\DOMDocument $document)
		{
			// Run some quick validation of the document. Every document has
			// to have a configurations element as root.
			$documentElement = $document->documentElement;
			if($documentElement->tagName !== 'configurations') {
				// TODO: Better specify the Exception-object.
				throw new Ramverk\Exception(
					'Invalid configuration document. Every configuration '.
					'document must have the "configurations" element as root.'
				);
			}

			// Check if the current document has a parent document. If it does,
			// parse that document first. This way we'll be able to override
			// parent configurations from the child configuration.
			//
			// It will put the configuration items in the correct order.
			$this->parseParentDocument($document);

			// Loop through the child nodes of the configurations element.
			// Retrieve the configuration items.
			foreach($documentElement->childNodes as $node) {
				if($node->nodeType === XML_ELEMENT_NODE && $node->localName === 'configuration') {
					// For a configuration item to be included it have to match
					// a few conditions. If neither the profile or context
					// attributes have been defined, the item will be included.
					//
					// If the item have either of the attributes and value
					// matches the respective values, the item will be included.
					//
					// Any other scenario it'll be ignored.
					if($node->hasAttribute('profile')) {
						if($node->getAttribute('profile') !== $this->_profile) {
							continue;
						}
					}
					if($node->hasAttribute('context')) {
						if($node->getAttribute('context') !== $this->_context) {
							continue;
						}
					}
					$this->_items[] = $node;
				}
			}
		}

		/**
		 * Check if the document has a parent document, include and parse it.
		 * @param DOMDocument $document Current document being parsed.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		protected function parseParentDocument(\DOMDocument $document)
		{
			// Check if the current document has a parent document that needs
			// to be included and parsed.
			$documentElement = $document->documentElement;
			if($documentElement->hasAttribute('parent')) {
				$parent = $this->_config->expandDirectives(
					$documentElement->getAttribute('parent')
				);

				// We have to check if the parent document already have been
				// imported, prevents infinite loops.
				//
				// No need to check if the configuration document is attempting
				// to include itself, exception will be thrown next loop.
				if(in_array($parent, $this->_parentDocuments)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Infinite inclusion-loop of parent documents '.
						'detected within the configuration file "%s".',
						$document->documentURI
					));
				}

				// Verify that the parent document actually exists.
				if(!file_exists($parent) || !is_readable($parent)) {
					// TODO: Better specify the Exception-object.
					throw new Ramverk\Exception(sprintf(
						'Parent configuration file "%s" do not exists.',
						$parent
					));
				}
				// Save the parent URI, will prevent infinite loops.
				$this->_parentDocuments[] = $parent;

				// Using reflection because we'd want to give the handler
				// the same object type that the factory want to parse.
				$documentReflection = new \ReflectionClass(get_class($document));

				// Instansiate and load the parent configuration document,
				// with the version and encoding from the first document.
				$parentDocument = $documentReflection->newInstanceArgs(array(
					$document->version,
					$document->encoding
				));
				$parentDocument->load($parent);

				// Parse the parent document and retrieve the items.
				$this->parse($parentDocument);
			}
		}
	}
}
// End of file: Parser.class.php
// Location: library/config/handler/Parser.class.php