<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by TheDeveloperBlog.          |
// | Copyright (c) 2013, Authors.                                             |
// | Copyright (c) 2013, TheDeveloperBlog.                                    |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk\Data
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Net\TheDeveloperBlog\Ramverk;

	/**
	 * Container for working with index => value based items.
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
		 * Contained values, stored in a index => value fashion.
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
		 * Set value with container index.
		 * @param string $index Container index.
		 * @param mixed $value Container value.
		 * @return boolean True if index have been set, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function setValueWithIndex($index, $value)
		{
			// Check if the index already exists, we can't override
			// already existing indexes/values.
			if(!$this->hasIndex($index)) {
				$this->_container[$index] = $value;

				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Get value of container index.
		 * @param string $index Container index.
		 * @return mixed|NULL Container value or NULL if index do not exists.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function getValueByIndex($index)
		{
			return $this->hasIndex($index) ? $this->_container[$index] : NULL;
		}

		/**
		 * Check whether a container index exists or not.
		 * @param string $index Container index.
		 * @return boolean True if index exists, otherwise false.
		 * @author Tobias Raatiniemi <me@thedeveloperblog.net>
		 */
		public function hasIndex($index)
		{
			return array_key_exists($index, $this->_container)
				&& isset($this->_container[$index]);
		}
	}
}
// End of file: Container.class.php
// Location: library/data/Container.class.php