<?php
	class LoginSuccess extends View
	{
		public function executeHtml()
		{
			echo __METHOD__;
		}

		public function executeJson()
		{
			echo json_encode(array(
				'status' => 'success',
				'method' => __METHOD__
			));
		}
	}