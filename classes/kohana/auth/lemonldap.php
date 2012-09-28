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
		if (!isset($lmconfig['debug']))
		{
			$lmconfig['debug'] = false;
		}

		$this->_config = $lmconfig;
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
				file_put_contents('/tmp/lemonldap.log', 'REMOTE_ADDR='.$_SERVER['REMOTE_ADDR']."\n");
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
				file_put_contents('/tmp/lemonldap.log', 'TOKEN_NAME='.$header.', TOKEN_VALUE='.$value."\n");
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
			file_put_contents('/tmp/lemonldap.log', 'USER='.var_export($user,true)."\n");
		}

		// Authenticate
		if ($user !== false)
		{
			return $this->complete_login($user->data());
		}

		return FALSE;
	}

}

