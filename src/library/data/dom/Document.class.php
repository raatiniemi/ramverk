<?php
namespace Me\Raatiniemi\Ramverk\Data\Dom
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Document extends \DOMDocument
	{
		/**
		 * Class extensions for PHP DOM classes.
		 * @var array
		 */
		private $classes = array(
			'DOMAttr' => 'Attribute',
			'DOMDocument' => 'Document',
			'DOMElement' => 'Element',
			'DOMNode' => 'Node'
		);

		/**
		 * XPath-handler for the document.
		 * @var DOMXPath
		 */
		private $xpath;

		/**
		 * Initialize the DOM document with version and encoding.
		 * @param string $version XML version.
		 * @param string $encoding XML encoding.
		 * @see DOMDocument::__construct()
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct($version = '1.0', $encoding = 'UTF-8')
		{
			parent::__construct($version, $encoding);

			// Iterate through the class extensions and register them.
			foreach($this->classes as $base => $extended) {
				$this->registerNodeClass($base, __NAMESPACE__ . "\\{$extended}");
			}
		}

		/**
		 * Retrieve the document XPath-handler.
		 * @return DOMXPath XPath-handler.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getXPath()
		{
			// Check if the XPath-handler have been correctly initialized.
			if($this->xpath === null || !($this->xpath instanceof \DOMXPath)) {
				// Initialize the XPath-handler with the document.
				$this->xpath = new \DOMXPath($this);
			}
			return $this->xpath;
		}

		/**
		 * Retrieve the configuration elements from the document.
		 * @return array Array with configuration elements.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getConfigurationElements()
		{
			$nodes = array();

			// Iterate through the child nodes and retrieve the configuration elements.
			foreach($this->childNodes as $node) {
				if($node->nodeType === XML_ELEMENT_NODE && $node->localName === 'configuration') {
					$nodes[] = $node;
				}
			}
			return $nodes;
		}
	}
}
// End of file: Document.class.php
// Location: library/data/dom/Document.class.php