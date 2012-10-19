<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Lemonldap auth driver.
 * This auth driver does not support roles nor autologin.
 *
 * @package   Kohana/Lemonldap
 * @author    Thomas Chemineau - thomas.chemineau@gmail.com
 * @copyright (c) 2007-2012 Thomas Chemineau
 * @copyright (c) 2007-2012 Kohana Team
 * @license   http://kohanaframework.org/license
 */
class Kohana_Auth_Lemonldap extends Auth
{

	/**
	 * Lemonldap configuration.
	 *
	 * @var array
	 */
	private $_lmconfig = null;

	/**
	 * Loads Lemonldap configuration.
	 *
	 * @param   array   $config   Lemonldap auth configuration
	 * @return  void
	 */
	public function __construct ( $config = array() )
	{
		parent::__construct($config);

		$lmconfig = Kohana::$config->load('lemonldap');

		if (!isset($lmconfig['security']))
		{
			$lmconfig['security'] = array();
		}
		if (!isset($lmconfig['security']['server_ip']))
		{
			$lmconfig['security']['server_ip'] = '127.0.0.1';
		}
		if (!isset($lmconfig['security']['token_header']))
		{
			$lmconfig['security']['token_header'] = false;
		}
		if (!isset($lmconfig['security']['token_value']))
		{
			$lmconfig['security']['token_value'] = false;
		}
		if (!isset($lmconfig['service']))
		{
			$lmconfig['service'] = array();
		}
		if (!isset($lmconfig['service']['wsdl']))
		{
			$lmconfig['service']['wsdl'] = false;
		}
		if (!isset($lmconfig['service']['sessionid_header']))
		{
			$lmconfig['service']['sessionid_header'] = false;
		}
		if (!isset($lmconfig['service']['cookie_domain']))
		{
			$lmconfig['service']['cookie_domain'] = false;
		}
		if (!isset($lmconfig['service']['cookie_name']))
		{
			$lmconfig['service']['cookie_name'] = 'lemonldap';
		}
		if (!isset($lmconfig['sso_header']))
		{
			$lmconfig['sso_header'] = false;
		}
		if (!isset($lmconfig['debug']))
		{
			$lmconfig['debug'] = false;
		}

		$this->_lmconfig = $lmconfig;
	}

	/**
	 * Most of the time, it is not possible to retrieve a password
	 * from a user into a LDAP directory.
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password ( $password )
	{
		return FALSE;
	}

	/**
	 * Check if SSO is activated by verifying the SSO header.
	 *
	 * @return boolean
	 */
	public static function is_sso ()
	{
		// Get configuration
		$config = $this->_lmconfig;
		$debug = $config['debug'];

		return $config['sso_header'] !== false && isset($_SERVER[$config['sso_header']);
	}

	/**
	 * Login user through a SOAP request to the Lemonldap::NG server
	 *
	 * @param   string   $username  Username to log in
	 * @param   string   $password  Password to check against
	 * @return  boolean
	 */
	public function login_with_soap ( $username, $password )
	{
		// Get configuration
		$config = $this->_lmconfig;
		$debug = $config['debug'];

		// Check WSDL ressource
		if ($config['service']['wsdl'] === false)
		{
			if ($debug)
			{
				self::_trace('NO WSDL');
			}
			return FALSE;
		}

		// Check HTTP header
		if ($config['service']['sessionid_header'] === false)
		{
			if ($debug)
			{
				self::_trace('NO SESSIONID HEADER');
			}
			return FALSE;
		}

		// Check SSO domain
		if ($config['service']['cookie_domain'] === false)
		{
			if ($debug)
			{
				self::_trace('NO COOKIE DOMAIN FOUND');
			}
			return FALSE;
		}

		// Get session id
		$sessionid = $_SERVER[$config['service']['sessionid_header']];

		// Store the success of the operation
		$success = false;

		try
		{
			// Configure SOAP request
			$service_maps = array('GetCookieResponse' => 'CookiesResponse');
			$service_opts = array('trace' => 1, 'classmap' => $service_maps);

			// Instanciate the SOAP instance
			$service = new SoapClient($config['service']['wsdl'], $service_opts);

			// Authenticate the user via SOAP
			$result = $service->getCookies($username, $password, $sessionid);

			// Get the result. If there are some cookies which values are
			// not 0, then authentication should be OK.
			$r_error   = $result->getError();
			$r_cookies = $result->getCookies();

			// Cookie name and domain
			$cookie_name = $config['service']['cookie_name'];
			$cookie_domain = $config['service']['cookie_domain'];

			if ($debug)
			{
				self::_trace('ERROR = '.var_export($r_error,true));
				self::_trace('COOKIES = '.var_export($r_cookies,true));
			}

			// The lemonldap cookie should contains the SSO session id
			// of the corresponding user. It must be the same as the one
			// found into HTTP headers.
			if (isset($r_cookies[$cookie_name]) && strlen($r_cookies[$cookie_name]) > 1 && $sessionid == $r_cookies[$cookie_name])
			{
				$success = true;
			}
			else if ($debug)
			{
				self::_trace('NO LEMONLDAP COOKIE FOUND ('.$sessionid.')');
			}

			// To force Handler to refresh its header, send an update cookie.
			$success &= setcookie(
				$cookie_name.'update',			// Name
				time(),					// Value
				0,					// Expire
				'/',					// Path
				$cookie_domain,				// Domain
				false,					// Secure
				false);					// HttpOnly
		}

		// If an error occurs, trace it
		catch (SoapFault $exception)
		{
			if ($debug)
			{
				$error = $service->__getLastRequest()."\n".$service->__getLastResponse();
				self::_trace("SOAP ERROR:\n".$error);
			}
		}

		return $success;
	}

	/**
	 * Check to see if the user is logged in, and if $role is set,
	 * has all roles.
	 *
	 * @param   mixed   $role
	 * @return  boolean
	 */
	public function logged_in ( $role = NULL )
	{
		return false;
	}

	/**
	 * Most of the time, it is not possible to retrieve a password
	 * from a user into a LDAP directory.
	 *
	 * @param   mixed   username
	 * @return  null
	 */
	public function password ( $username )
	{
		return null;
	}

	/**
	 * Trace
	 *
	 * @param   string   $message
	 */
	protected static function _trace ( $message )
	{
		file_put_contents('/tmp/lemonldap.log', $message."\n", FILE_APPEND);
	}

	/**
	 * Get Lemonldap user.
	 *
	 * @param   string   $username
	 * @param   Model_Lemonldap_User
	 */
	protected function _get_lemonldap_user ()
	{
		return Model::factory('Lemonldap_User')->get();
	}

	/**
	 * Authenticate a user against Lemonldap::NG
	 *
	 * @param   string   $username
	 * @param   string   $password
	 * @param   boolean  $remember   Enable autologin (no password check)
	 * @return  boolean
	 */
	protected function _login ( $username = null, $password = null, $remember = true )
	{
		// Is debug
		$config = $this->_lmconfig;
		$debug = $config['debug'];

		// Check remote IP address
		if ($config['security']['server_ip'] !== false)
		{
			if ($debug)
			{
				self::_trace('REMOTE_ADDR='.$_SERVER['REMOTE_ADDR']);
			}
			if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != $config['security']['server_ip'])
			{
				return FALSE;
			}
		}

		// Check remote token
		if ($config['security']['token_header'] !== false && $config['security']['token_value'] !== false)
		{
			$header = $config['security']['token_header'];
			$value = $config['security']['token_value'];

			if ($debug)
			{
				self::_trace('TOKEN_NAME='.$header.', TOKEN_VALUE='.$value);
			}
			if (isset($_SERVER[$header]) && $_SERVER[$header] != $value)
			{
				return FALSE;
			}
		}

		// Retrieve user
		$user = $this->_get_lemonldap_user();

		// Trace user information
		if ($debug)
		{
			self::_trace('USER='.var_export($user,true));
		}

		// Authenticate
		if ($user !== false)
		{
			return $this->complete_login($user->data());
		}

		return FALSE;
	}

}

