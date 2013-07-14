<?php
	class Login extends Action
	{
		public function executeWrite()
		{
			// Success or Failure.
			return 'Success';
		}

		public function executeRead()
		{
			return 'Form';
		}
	}