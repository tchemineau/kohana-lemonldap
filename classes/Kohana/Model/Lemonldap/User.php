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
	 * Lemonldap user mapping
	 *
	 * @var array
	 */
	public static $map = array();

	/**
	 * Loads Lemonldap configuration and create the user from HTTP headers.
	 *
	 * @return  void
	 */
	public function __construct ()
	{
                $config = Kohana::$config->load('lemonldap');

                if (!isset($config['mapping']))
                {
                        $config['mapping'] = array();
                }
                if (!isset($config['mapping']['user']))
                {
                        $config['mapping']['user'] = array(
                                'username' => 'HTTP_AUTH_USER'
                        );
                }

		self::$map = $config['mapping']['user'];
	}

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
		$user = new self();
		$data = array();

		foreach (self::$map as $key => $header)
		{
			if (isset($_SERVER[$header]))
			{
				$data[$key] = $_SERVER[$header];
			}
		}

		if (sizeof($data) == 0)
		{
			return FALSE;
		}

		return $user->data($data);
	}

}

