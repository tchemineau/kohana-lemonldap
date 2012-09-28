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
		$user = $this->_get_lemonldap_user();

		if ($user)
		{
			return $this->complete_login($user->data());
		}

		return FALSE;
	}

}

