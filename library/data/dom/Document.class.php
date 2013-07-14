<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Data\Dom
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * DOMDocument.
	 *
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
	 */
	class Document extends \DOMDocument
	{
		/**
		 * List of replacement classes for PHP DOM functionality.
		 * @var array
		 */
		protected $_classes = array(
			'DOMDocument' => 'Document',
			'DOMElement' => 'Element',
			'DOMNode' => 'Node'
		);

		/**
		 * Initialize the DOM document.
		 * @param string $version XML version.
		 * @param string $encoding XML encoding.
		 * @see DOMDocument::__construct()
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct($version='1.0', $encoding='UTF-8')
		{
			parent::__construct($version, $encoding);

			// Register all of the DOM classes with the extended functionality.
			foreach($this->_classes as $dom => $extended) {
				$this->registerNodeClass($dom, __NAMESPACE__ . "\\{$extended}");
			}
		}
	}
}
// End of file: Document.class.php
// Location: library/data/dom/Document.class.php