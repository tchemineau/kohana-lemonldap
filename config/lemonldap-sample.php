<?php defined('SYSPATH') or die('No direct script access.');

return array
(

	// Activate debug
	'debug' => false,

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

	// SOAP parameters
	'service' => array (

		// URL to the WSDL file of Lemonldap::NG server
		'wsdl' => 'http://auth.example.com/portal.wsdl',

		// Cookie domain
		'cookie_domain' => '.example.com',

		// Cookie name
		'cookie_name' => 'lemonldap',

	),

	// Name of the HTTP header that specify the user SSO session id.
	// This header have to be configured into the Lemonldap::NG manager.
	'sessionid_header' => 'HTTP_SSO_SESSIONID',

	// Header used to check if SSO is enabled or not.
	// Only used by the Lemonldap_Auth::is_sso() function.
	'sso_header' => 'HTTP_SSO',

);

