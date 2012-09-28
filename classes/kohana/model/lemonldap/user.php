<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Lemonldap user model.
 *
 * @package   Kohana/Lemonldap
 * @author    Thomas Chemineau - thomas.chemineau@gmail.com
 * @copyright (c) 2012 Thomas Chemineau
 * @license   http://kohanaframework.org/license
 */
class Kohana_Model_Lemonldap_User extends Model
{

	/**
	 * Lemonldap user data.
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Get or set user data.
	 *
	 * @param   array   $data
	 * @return  array
	 */
	public function data ( $data = null )
	{
		if (is_null($data))
		{
			return $this->_data;
		}

		$this->_data = $data;
		return $this;
	}

	/**
	 * Get the user.
	 *
	 * @return Model_Lemonldap_User
	 */
	public static function get ()
	{
		return new self();
	}

}

