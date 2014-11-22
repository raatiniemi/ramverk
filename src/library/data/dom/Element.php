<?php
namespace Me\Raatiniemi\Ramverk\Data\Dom;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
use Me\Raatiniemi\Ramverk;

/**
 * @package Ramverk
 * @subpackage Data
 *
 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
 * @copyright (c) 2013-2014, Authors
 */
class Element extends \DOMElement
{
    // +------------------------------------------------------------------+
    // | Trait use-directives.                                            |
    // +------------------------------------------------------------------+
    use Utility\Value;

    /**
     * Retrieve the name of the element.
     * @return string Name of the element.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getName()
    {
        return $this->nodeName;
    }

    /**
     * Retrieve child elements with specified name.
     * @param string $name Name of the children to retrieve.
     * @return DOMNodeList List with the DOM nodes.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getChildren($name)
    {
        // Query for child elements with the specified name.
        $query = sprintf('child::*[local-name() = "%s"]', $name);

        return $this->ownerDocument->getXPath()->query($query, $this);
    }

    /**
     * Retrieve child elements with specified name.
     * @param string $name Name of the children to retrieve.
     * @return DOMNodeList List with the DOM nodes.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function get($name)
    {
        return $this->getChildren($name);
    }

    /**
     * Retrieve child element with specified name.
     * @param string $name Name of the child to retrieve.
     * @return DOMElement DOM element, or null if no element is found.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function getChild($name)
    {
        // Query for child element with the specified name.
        $query = sprintf('child::*[local-name() = "%s"][1]', $name);
        $node = $this->ownerDocument->getXPath()->query($query, $this);

        // Retrieve the first element from the DOMNodeList. If the index do
        // not exists, e.g. the DOMNodeList is empty, the `item`-method
        // will return null.
        return $node->item(0);
    }

    /**
     * Check whether the element have direct child elements with specified name.
     * @param string $name Name of child elements to check.
     * @return boolean True if child elements exists, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function hasChildren($name)
    {
        $query = sprintf('count(child::*[local-name() = "%s"])', $name);

        return $this->ownerDocument->getXPath()->evaluate($query, $this) > 0;
    }

    /**
     * Check whether the element have direct child elements with specified name.
     * @param string $name Name of child elements to check.
     * @return boolean True if child elements exists, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function has($name)
    {
        return $this->hasChildren($name);
    }

    /**
     * Check whether the element have direct child element with specified name.
     * @param string $name Name of child element to check.
     * @return boolean True if child element exists, otherwise false.
     * @author Tobias Raatiniemi <raatiniemi@gmail.com>
     */
    public function hasChild($name)
    {
        return $this->hasChildren($name);
    }
}
// End of file: Element.php
// Location: library/data/dom/Element.php
