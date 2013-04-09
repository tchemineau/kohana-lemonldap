<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Lemonldap class
 *
 * @package   Kohana/Lemonldap
 * @author    Thomas Chemineau - thomas.chemineau@gmail.com
 * @copyright (c) 2007-2012 Thomas Chemineau
 * @copyright (c) 2007-2012 Kohana Team
 */
class Kohana_Lemonldap
{

	/**
	 * Get an authentication instance
	 *
	 * @return Auth_Lemonldap
	 */
	public static function get_auth_instance ()
	{
		return new Auth_Lemonldap(Kohana::$config->load('auth'));

	}

}

