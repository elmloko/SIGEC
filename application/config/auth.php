<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'       => 'ORM',
	'hash_method'  => 'sha256',
	'hash_key'     => '2, 4, 6, 7, 9, 15, 20, 23, 25, 30',
	'lifetime'     => 1209600,
	'session_key'  => 'session_native', //'auth_user'

	// Username/password combinations for the Auth File driver
	'users' => array(
		'ealcon' => 'b6c56905f53fbea5b1acb6f28d4e8d61940b7c99c80b5e765e9762fc069f13f9',
	),

);
