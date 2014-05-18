<?php
namespace Me\Raatiniemi\Ramverk\Request\Web
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Request;

	class Data extends Request\Data
	{
		private $_headers;

		public function getHeaders()
		{
			// We should only attempt to parse the HTTP headers once.
			if($this->_headers === NULL) {
				$headers = array();

				// Not all of the platforms have the `getallheaders` function
				// available, so if we don't have it available we have to
				// manually parse the headers from the `$_SERVER` array.
				if(!function_exists('getallheaders')) {
					foreach($_SERVER as $index => $value) {
						// Check if the index matches the predefined format for
						// HTTP headers. If the index is an HTTP header the index
						// should begin with the name `http_`.
						if(stripos($index, 'http_') === 0) {
							// Convert the index formatting from e.g:
							// `HTTP_CONTENT_TYPE` to `CONTENT-TYPE`
							$index = str_replace('_', '-', substr($index, 5));
							$headers[$index] = $value;
						}
					}
				} else {
					$headers = getallheaders();
				}

				// We have to change the case for the keys to only lowercase, this
				// will ease the extraction of header values.
				$this->_headers = array_change_key_case($headers);
			}
			return $this->_headers;
		}

		public function getHeader($name, $default=NULL)
		{
			$headers = $this->getHeaders();

			$value = $default;
			$name = strtolower($name);
			if(array_key_exists($name, $headers)) {
				$value = $headers[$name];
			}

			return $value;
		}
	}
}
// End of file: Data.class.php
// Location: library/request/web/Data.class.php