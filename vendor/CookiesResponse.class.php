<?php

class CookiesResponse
{

	private $_error;

	private $_cookies;

	public function __construct ( $error, $cookies )
	{
		$this->_error = $this->__set('error', $error);
		$this->_cookies = $this->__set('cookies', $cookies);
	}

	public function __get( $property )
	{
		$value = null;

		switch ($property)
		{
			case 'cookies':
				$value = $this->getCookies();
				break;

			case 'error':
				$value = $this->getError();
				break;
		}

		return $value;
	}

	public function __set( $property, $value )
	{
		switch ($property)
		{
			case 'cookies':
				$tmp = (array) $value;
				foreach ($tmp as $cookieName => $cookieValue)
				{
					$tmp[$cookieName] = (string) $cookieValue;
				}
				$this->_cookies = $tmp;
				break;

			case 'error':
				$this->_error = $value;
				break;
		}
	}

	public function getCookies ()
	{
		return $this->_cookies;
	}

	public function getError ()
	{
		return $this->_error;
	}
}

