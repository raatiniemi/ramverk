<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Data
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Container for working with KeyValue based items.
	 *
	 * @package Ramverk
	 * @subpackage Data
	 *
	 * @copyright (c) 2013, Authors
	 * @copyright (c) 2013, The Developer Blog
	 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
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
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function __construct(array $container=array())
		{
			$this->_container = $container;
		}

		/**
		 * Set container item.
		 * @param string $name Name of container item.
		 * @param mixed $value Value of container item.
		 * @return boolean True if item have been set, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function setItem($name, $value)
		{
			// Check if the item already exists, we shouldn't override
			// already existing items.
			if(!$this->hasItem($name)) {
				$this->_container[$name] = $value;

				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Get container item.
		 * @param string $name Name of the container item.
		 * @return mixed Value of container item or NULL if item do not exists.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getItem($name)
		{
			return $this->hasItem($name) ? $this->_container[$name] : NULL;
		}

		/**
		 * Check whether a container item exists.
		 * @param string $name Name of the container item.
		 * @return boolean True if the item exists, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function hasItem($name)
		{
			return array_key_exists($name, $this->_container)
				&& isset($this->_container[$name]);
		}

		/**
		 * Import items to the container.
		 * @param array $items Items to import.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function importItems(array $items)
		{
			foreach($item as $name => $item) {
				$this->setItem($name, $item);
			}
		}
	}
}
// End of file: Container.class.php
// Location: library/data/Container.class.php