<?php
class cg_cookie
{
	public static $config = array(
		'cookie_prefix' => '',
		'salt' => true,
		'salt_key' => '',
		'salt_agent' => false,
		'salt_ip' => false,
		'domain' => '',
		'path' => '/',
		'secure' => false,
		'httponly' => true
	);

	public static function init($config)
	{
		foreach ($config as $key => $value)
		{
			if (isset(self::$config[$key]))
			{
				self::$config[$key] = $value;
			}
		}
	}

	private static function prefix_name($name)
	{
		if (!empty(self::$config['cookie_prefix']))
		{
			$name = self::$config["cookie_prefix"] . $name;
		}
		return $name;
	}

	public static function set($name, $value, $expire = 0)
	{
		$name = self::prefix_name($name);
		if (self::$config['salt'])
		{
			$value = self::salt($name, $value) . $value;
		}
		return setcookie($name, $value, $expire, self::$config['path'], self::$config['domain'], self::$config['secure'], self::$config['httponly']);
	}

	public static function get($name)
	{
		$name = self::prefix_name($name);
		if (!isset($_COOKIE[$name]))
		{
			return NULL;
		}
		$cookie = $_COOKIE[$name];
		if (!self::$config['salt'])
		{
			return $cookie;
		}
		$hash = substr($cookie, 0, 40);
		$value = substr($cookie, 40);
		if (self::salt($name, $value) === $hash)
		{
			return $value;
		}
		self::delete($name);
		return NULL;
	}

	public static function delete($name)
	{
		$name = self::prefix_name($name);
		unset($_COOKIE[$name]);
		return setcookie($name, false, 0x7fffffff, self::$config['path'], self::$config['domain'], self::$config['secure'], self::$config['httponly']);
	}

	public static function salt($name, $value)
	{
		$s = '';
		if (self::$config['salt_agent'])
		{
			$agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';
			$s .= $agent;
		}
		if (self::$config['salt_ip'])
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			$s .= $ip;
		}
		return sha1($s . $name . $value . self::$config['salt_key']);
	}
}