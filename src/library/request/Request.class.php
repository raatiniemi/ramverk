<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Core;

	abstract class Request
	{
		// +------------------------------------------------------------------+
		// | Trait use-directives.                                            |
		// +------------------------------------------------------------------+
		use Core\Context\Handler;

		/**
		 * Constant for write requests.
		 */
		const Write = 'Write';

		/**
		 * Constant for read requests.
		 */
		const Read = 'Read';

		/**
		 * Stores the request uri.
		 * @var string
		 */
		private $_ru;

		/**
		 * Stores the request method.
		 * @var string
		 */
		private $_rm;

		/**
		 * Stores the request data.
		 * @var Me\Raatiniemi\Ramverk\Request\Data
		 */
		private $_rd;

		/**
		 * Initialize the request object.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @param Me\Raatiniemi\Ramverk\Request\Data $rd Request data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Core\Context $ct, Request\Data $rd)
		{
			$this->setContext($ct);
			$this->_rd = $rd;

			// Process the raw request data, save what is neccessary and discard
			// everything else. Handles content type specific data.
			$this->processRawData();
		}

		abstract protected function processRawData();

		/**
		 * Set the request URI.
		 * @param string $ru Request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setRequestUri($ru)
		{
			return $this->_ru = $ru;
		}

		/**
		 * Retrieve the request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getRequestUri()
		{
			return $this->_ru;
		}

		/**
		 * Set the request method.
		 * @param string $rm Request method.
		 * @return string Request method.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setRequestMethod($rm)
		{
			// Attempt to adjust the format for the request method. The method
			// should be formated as `Write` or `Read`.
			if(!empty($rm) && is_string($rm)) {
				$rm = ucfirst(strtolower($rm));
			} else {
				// If the request method is either empty or not a string we
				// have to avoid fatal errors by reverting the method.
				$rm = Request::Read;
			}

			// Check that the request method is one of the accepted values,
			// otherwise fallback to the default one.
			$rm = in_array($rm, array(Request::Write, Request::Read)) ? $rm : Request::Read;
			return $this->_rm = $rm;
		}

		/**
		 * Retrieve the request method.
		 * @return string Request method.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getRequestMethod()
		{
			return $this->_rm;
		}

		protected function getRequestData()
		{
			return $this->_rd;
		}

		// TODO: Implement better support for request data.
	}
}
// End of file: Request.class.php
// Location: library/request/Request.class.php