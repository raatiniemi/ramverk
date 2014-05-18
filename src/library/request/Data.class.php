<?php
namespace Me\Raatiniemi\Ramverk\Request
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

	abstract class Data
	{
		private $_data;

		private $_raw;

		public function __construct()
		{
			$this->_data = array();
			$this->_raw = array();
		}

		public function setRaw(array $data)
		{
			return $this->_raw = $data;
		}
	}
}
// End of file: Data.class.php
// Location: library/request/Data.class.php