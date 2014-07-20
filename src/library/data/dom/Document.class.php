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
		 * List of replacement classes for PHP DOM functionality.
		 * @var array
		 */
		protected $_classes = array(
			'DOMAttr' => 'Attribute',
			'DOMDocument' => 'Document',
			'DOMElement' => 'Element',
			'DOMNode' => 'Node'
		);

		protected $_xpath;

		/**
		 * Initialize the DOM document.
		 * @param string $version XML version.
		 * @param string $encoding XML encoding.
		 * @see DOMDocument::__construct()
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct($version='1.0', $encoding='UTF-8')
		{
			parent::__construct($version, $encoding);

			// Register all of the DOM classes with the extended functionality.
			foreach($this->_classes as $dom => $extended) {
				$this->registerNodeClass($dom, __NAMESPACE__ . "\\{$extended}");
			}

			$this->_xpath = new \DOMXPath($this);
		}

		/**
		 * Retrieve the XPath.
		 * @return DOMXPath
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getXPath()
		{
			return $this->_xpath;
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