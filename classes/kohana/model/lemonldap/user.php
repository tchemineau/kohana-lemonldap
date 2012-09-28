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
	protected $_map = array();

	/**
	 * Loads Lemonldap configuration and create the user from HTTP headers.
	 *
	 * @return  void
	 */
	public function __construct ()
	{
		parent::__construct();

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

		$this->_map = $config['mapping']['user'];
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
		$headers = getallheaders();

		foreach ($this->_map as $key => $header)
		{
			if (isset($headers[$header]))
			{
				$data[$key] = $headers[$header];
			}
		}

		return $user->data($data);
	}

}

