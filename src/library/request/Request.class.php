<?php
namespace Me\Raatiniemi\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+
	use Me\Raatiniemi\Ramverk\Core;

	/**
	 * Generic functionality for handling incoming requests.
	 *
	 * @package Ramverk
	 * @subpackage Request
	 *
	 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
	 * @copyright (c) 2014, Authors
	 *
	 * @abstract
	 */
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
		 * Initialize the request.
		 * @param Me\Raatiniemi\Ramverk\Core\Context $ct Application context.
		 * @param Me\Raatiniemi\Ramverk\Request\Data $rd Request data.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function __construct(Core\Context $ct, Request\Data $rd)
		{
			$this->setContext($ct);
			$this->setData($rd);

			// Process the raw request data, save what is neccessary and discard
			// everything else. Handles content type specific data.
			$this->processRawData();
		}

		/**
		 * Processes the incoming raw data and populate the request data container.
		 *
		 * The incoming raw data is dependant on the application context, e.g.
		 * the web context handles the data differently than the console context.
		 *
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 * @abstract
		 */
		abstract protected function processRawData();

		/**
		 * Set the request URI.
		 * @param string $ru Request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setUri($ru)
		{
			return $this->_ru = $ru;
		}

		/**
		 * Retrieve the request URI.
		 * @return string Request URI.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		public function getUri()
		{
			return $this->_ru;
		}

		/**
		 * Set the request method.
		 * @param string $rm Request method.
		 * @return string Request method.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function setMethod($rm)
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
		public function getMethod()
		{
			return $this->_rm;
		}

		/**
		 * Set the request data container.
		 * @param Me\Raatiniemi\Ramverk\Request\Data $rd Request data container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		private function setData(Request\Data $rd)
		{
			$this->_rd = $rd;
		}

		/**
		 * Retrieve the request data container.
		 * @return Me\Raatiniemi\Ramverk\Request\Data Request data container.
		 * @author Tobias Raatiniemi <raatiniemi@gmail.com>
		 */
		protected function getData()
		{
			return $this->_rd;
		}
	}
}
// End of file: Request.class.php
// Location: library/request/Request.class.php