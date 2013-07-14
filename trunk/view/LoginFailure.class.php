<?php
	class LoginFailure extends View
	{
		public function executeHtml()
		{
			echo __METHOD__;
		}

		public function executeJson()
		{
			echo json_encode(array(
				'status' => 'failed',
				'method' => __METHOD__
			));
		}
	}