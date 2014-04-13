<?php
namespace Me\Raatiniemi\Ramverk\Utility\Http
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk;

	trait Header
	{
		private $_headers;

		public function getHeaders()
		{
			if($this->_headers === NULL) {
				// Retrieve the HTTP headers. We have to change the case for the keys
				// to only lowercase, this will ease the extraction of header values.
				$this->_headers = function_exists('getallheaders') ? getallheaders() : array();
				$this->_headers = array_change_key_case($this->_headers);
			}
			return $this->_headers;
		}

		public function getHeader($name, $default=NULL)
		{
			$value = $default;
			$name = strtolower($name);
			if(array_key_exists($name, $this->getHeaders())) {
				$value = $this->getHeaders()[$name];
			}
			return $value;
		}
	}
}
// End of file: Web.class.php
// Location: library/request/Web.class.php