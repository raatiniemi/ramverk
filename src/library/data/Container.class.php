<?php
namespace Me\Raatiniemi\Ramverk\Data
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Container for working with KeyValue based items.
	 *
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2013-2014, Authors
	 */
	class Container
	{
		/**
		 * Contained items.
		 * @var array
		 */
		protected $_container;

		/**
		 * Initialize the container within inital values.
		 * @param array $container Initial values for the container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(array $container=array())
		{
			$this->_container = $container;
		}

		/**
		 * Set container item.
		 * @param string $name Name of container item.
		 * @param mixed $value Value of container item.
		 * @param boolean $override Override existing container item.
		 * @return boolean True if item have been set, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setItem($name, $value, $override=FALSE)
		{
			// Check if the item already exists, we shouldn't override
			// already existing items.
			if(!$this->hasItem($name) || $override === TRUE) {
				$this->_container[$name] = $value;

				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Get container item.
		 * @param string $name Name of the container item.
		 * @return mixed Value of container item or NULL if item do not exists.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getItem($name)
		{
			return $this->hasItem($name) ? $this->_container[$name] : NULL;
		}

		/**
		 * Check whether a container item exists.
		 * @param string $name Name of the container item.
		 * @return boolean True if the item exists, otherwise false.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function hasItem($name)
		{
			return array_key_exists($name, $this->_container)
				&& isset($this->_container[$name]);
		}

		/**
		 * Import items to the container.
		 * @param array $items Items to import.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function importItems(array $items)
		{
			foreach($items as $name => $item) {
				$this->setItem($name, $item);
			}
		}

		/**
		 * Export items from the container.
		 * @return array Items stored within the container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function exportItems()
		{
			return $this->_container;
		}
	}
}
// End of file: Container.class.php
// Location: library/data/Container.class.php