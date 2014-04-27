<?php

/**
 * memcache cache class
 *
 */
class cg_cache_memcache
{
	private $memcache;
	private $key_prefix = '';
	private static $get_keys = array();
	private static $set_keys = array();
	private static $del_keys = array();
	private static $cache_time = 0;
	private static $get_keys_time = array();

	/**
	 * cg cache memcache
	 *
	 * @param array $memcache_config   memcache config array
	 */
	public function __construct($memcache_config)
	{
		if (isset($memcache_config['key_prefix']))
		{
			$this->key_prefix = $memcache_config['key_prefix'] . '_';
		}
		if (!isset($memcache_config['server'][0]['host']))
		{
			unset($memcached_config['server']);
			$memcache_config['server'][0] = array(
				"host" => "127.0.0.1",
				"port" => "11211",
				"weight" => 1,
				"status" => true
			);
		}
		$persistent = false;
		$timeout = 1;
		$retry_interval = 15;

		$failure_callback = 'cg_cache_memcache::memcache_failure_callback';
		$this->memcache = new Memcache();

		$start_time = $this->microtime_float();
		foreach ($memcache_config['server'] as $server)
		{
			if ($server['status'] == false)
			{
				$retry_interval = -1;
			}
			$result = $this->memcache->addServer($server["host"], $server["port"], $persistent, $server["weight"], $timeout, $retry_interval, $server["status"], $failure_callback);
		}
		$end_time = $this->microtime_float();
		self::$cache_time = $end_time - $start_time;
	}

	public function keys()
	{
		return array(
			'get' => self::$get_keys,
			'set' => self::$set_keys,
			'del' => self::$del_keys,
			'time' => self::$cache_time,
			'get_keys_time' => self::$get_keys_time
		);
	}

	/**
	 * get cache data
	 *
	 * @param string $key  cache key
	 * @return mixed
	 */
	public function get($key)
	{
		if (empty($key))
		{
			return false;
		}
		$start_time = $this->microtime_float();
		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$key[$k] = $this->key_prefix . $v;
			}
			self::$get_keys[] = $key;
			$olddata = $this->memcache->get($key);
			$newdata = array();
			foreach ((array)$olddata as $key => $value)
			{
				$newkey = substr($key, strlen($this->key_prefix));
				$newdata[$newkey] = $value;
			}
			unset($olddata);
			$end_time = $this->microtime_float();
			self::$get_keys_time[] = $end_time - $start_time;
			return $newdata;
		}
		else
		{
			$key = $this->key_prefix . $key;
			self::$get_keys[] = $key;
			$end_time = $this->microtime_float();
			self::$get_keys_time[] = $end_time - $start_time;
			return $this->memcache->get($key);
		}
	}

	/**
	 * set cache data
	 *
	 * @param string  $key   cache_key or filename
	 * @param mixed   $value cache data
	 * @param integer cache  expire time, default 0s
	 * @return bool
	 */
	public function set($key, $value, $expire = 0)
	{
		if (empty($value) || empty($key))
		{
			return false;
		}
		$key = $this->key_prefix . $key;
		$result = $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $expire);
		if ($result === false || stripos($key, 'cgbt_peers') !== false)
		{
			//$this->error_log("memcache set error: key: $key " . json_encode($value));
		}
		self::$set_keys[] = $key;
		return $result;
	}

	/**
	 * get cache stats
	 *
	 * @return mixed
	 */
	public function stats()
	{
		return $this->memcache->getStats();
	}

	/**
	 * delete cache data
	 * @param string $key cache key
	 */
	public function delete($key)
	{
		if (empty($key))
		{
			return false;
		}
		$key = $this->key_prefix . $key;
		self::$del_keys[] = $key;
		$this->memcache->delete($key);
	}

	/**
	 * flush all data
	 */
	public function flush()
	{
		$this->memcache->flush();
	}

	//overloading
	public function __call($method, $params)
	{
		switch ($method)
		{
			case "write":
				list($key, $value, $expire) = $params;
				return $this->set($key, $value, $expire);
				break;
			case "read":
				list($key) = $params;
				return $this->get($key);
				break;
			case "clear":
			case "del":
				list($key) = $params;
				return $this->delete($key);
				break;
			case "stat":
				return $this->stats();
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * when memcahce failure ,then call this
	 * note: static method
	 */
	public static function memcache_failure_callback()
	{
		$args = func_get_args();
		$message = date("[d-M-Y H:i:s]") . " PHP Notice:  memcached ";
		$message .= $args[0] . ":" . $args[1] . " " . $args[4] . " error! " . $args[3];
		$message .= "\r\n";
		//static method
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			$php_error_log = "error_log.txt";
		}
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $message);
		fclose($fp);
	}

	private function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	private function error_log($message)
	{
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			$php_error_log = "error_log.txt";
		}
		$s = date("[d-M-Y H:i:s]") . ' PHP Notice: Memcache Failure ' . $message . "\r\n";
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $s);
		fclose($fp);
	}
}


