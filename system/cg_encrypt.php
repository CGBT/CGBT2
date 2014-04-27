<?php
if (!defined('MCRYPT_RIJNDAEL_128'))
{
	define('MCRYPT_RIJNDAEL_128', 'MCRYPT_RIJNDAEL_128');
}
if (!defined('MCRYPT_MODE_NOFB'))
{
	define('MCRYPT_MODE_NOFB', 'MCRYPT_MODE_NOFB');
}
class cg_enctypt
{
	private static $key = '';
	private static $rand;
	private static $cipher = MCRYPT_RIJNDAEL_128;
	private static $mode = MCRYPT_MODE_NOFB;

	public static function init($key)
	{
		self::$key = $key;
	}

	public static function encrypt($data, $key = '')
	{
		if ($key === '')
		{
			$key = self::$key;
		}
		if (!function_exists('mcrypt_encrypt'))
		{
			return self::encrypt2($data, $key);
		}
		$iv_size = mcrypt_get_iv_size(self::$cipher, self::$mode);
		self::get_rand();
		$iv = mcrypt_create_iv($iv_size, self::$rand);
		$data = base64_encode($data);
		$data = mcrypt_encrypt(self::$cipher, $key, $data, self::$mode, $iv);
		return base64_encode($iv . $data);
	}

	public static function decrypt($data, $key = '')
	{
		if ($key === '')
		{
			$key = self::$key;
		}
		if (!function_exists('mcrypt_encrypt'))
		{
			return self::decrypt2($data, $key);
		}

		$data = base64_decode($data, true);
		if (!$data)
		{
			return false;
		}
		$iv_size = mcrypt_get_iv_size(self::$cipher, self::$mode);
		$iv = substr($data, 0, $iv_size);
		if ($iv_size !== strlen($iv))
		{
			return false;
		}
		$data = substr($data, $iv_size);
		$data = mcrypt_decrypt(self::$cipher, $key, $data, self::$mode, $iv);
		$data = rtrim($data, "\0");
		return base64_decode($data);
	}

	private static function get_rand()
	{
		$is_windows = (DIRECTORY_SEPARATOR === '\\');
		if (self::$rand === NULL)
		{
			if ($is_windows)
			{
				self::$rand = MCRYPT_RAND;
			}
			else
			{
				if (defined('MCRYPT_DEV_URANDOM'))
				{
					self::$rand = MCRYPT_DEV_URANDOM;
				}
				elseif (defined('MCRYPT_DEV_RANDOM'))
				{
					//@todo maybe slow
					self::$rand = MCRYPT_DEV_RANDOM;
				}
				else
				{
					self::$rand = MCRYPT_RAND;
				}
			}
		}
		if (self::$rand === MCRYPT_RAND)
		{
			mt_srand();
		}
	}

	private static function encrypt2($data, $key)
	{
		$result = '';
		for($i = 1; $i <= strlen($data); $i++)
		{
			$char = substr($data, $i - 1, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}

	private static function decrypt2($data, $key)
	{
		$result = '';
		$data = base64_decode($data);
		for($i = 1; $i <= strlen($data); $i++)
		{
			$char = substr($data, $i - 1, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
}

