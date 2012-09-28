<?php defined('SYSPATH') or die('No direct script access.');

return array
(

	// Security parameters
	'security' => array (

		// IP address of the Lemonldap::NG server. The IP address into
		// the HTTP header named REMOTE_IP will be verified before
		// authenticating the user.
		// Set FALSE to disable this check
		'server_ip' => '127.0.0.1',

		// Name of the HTTP header that specify the security token to verify the
		// identity of the request
		// Set FALSE to disable this check
		'token_header' => 'HTTP_AUTH_TOKEN',

		// Token value
		// Only verify if token_header_name is specified
		'token_value' => '123456',

	),

	// Here, define mapping between internal variables and HTTP headers. Add your own
	// internal variables which will be stored in session and could be retrieve by
	// calling $auth->get_user()
	'mapping' => array (

		// Default values for user mapping
		'user' => array (

			'username' => 'HTTP_AUTH_USER',
			'mail' => 'HTTP_AUTH_MAIL',

		),

	),

);

