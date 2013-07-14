<?php
return array(
	array(
		'module' => 'Index',
		'action' => 'Index',
		'pattern' => '^$'
	),
	array(
		'module' => 'User',
		'action' => 'Login',
		'pattern' => '^user/login$'
	),
	array(
		'module' => 'User',
		'action' => 'Logout',
		'pattern' => '^user/logout$'
	)
);