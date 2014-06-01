<?php
namespace Me\Raatiniemi\Ramverk\Request
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	/**
	 * Generic functionality for handling request data.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2014, Authors
	 *
	 * @abstract
	 */
	abstract class Data
	{
		/**
		 * @var array
		 */
		private $_data;

		/**
		 * Raw request data.
		 *
		 * The raw request data should not be accessible from within the
		 * application. The application validation configuration have to
		 * specify which data should be accessible.
		 * @var array
		 */
		private $_raw;

		/**
		 * Initialize the data containers.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct()
		{
			$this->_data = array();
			$this->_raw = array();
		}

		/**
		 * Set the raw data from the incoming request.
		 * @param array $data Raw request data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function setRaw(array $data)
		{
			return $this->_raw = $data;
		}
	}
}
// End of file: Data.class.php
// Location: library/request/Data.class.php