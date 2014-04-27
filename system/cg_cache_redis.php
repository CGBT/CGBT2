<?php

/**
 * redis cache class
 *
 */
class cg_cache_redis
{
	private $redis;
	private $key_prefix = '';
	private static $get_keys = array();
	private static $set_keys = array();
	private static $del_keys = array();
	private static $cache_time = 0;
	private static $get_keys_time = array();

	/**
	 * cg cache redis
	 *
	 * @param array $redis_config   redis config array
	 */
	public function __construct($redis_config)
	{
		if (!empty($redis_config['key_prefix']))
		{
			$this->key_prefix = $redis_config['key_prefix'] . '_';
		}
		if (!isset($redis_config['server'][0]['host']))
		{
			unset($redis_config['server']);
			$redis_config['server'][0] = array(
				"host" => "127.0.0.1",
				"port" => 6379,
				"status" => true
			);
		}
		$start_time = $this->microtime_float();
		$this->redis = new Redis();
		$result = $this->redis->connect($redis_config['server'][0]['host'], $redis_config['server'][0]['port']);
		$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		if (!$result)
		{
			$this->error_log('connect error');
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
		$start_time = $this->microtime_float();
		if (is_array($key))
		{
			$olddata = array();
			foreach ($key as $k => $v)
			{
				self::$get_keys[] = $this->key_prefix . $v;
				$olddata[$this->key_prefix . $v] = $this->redis->get($this->key_prefix . $v);
			}
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
			return $this->redis->get($key);
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
		$key = $this->key_prefix . $key;
		if ($expire > 0)
		{
			$result = $this->redis->setex($key, $expire, $value);
		}
		else
		{
			$result = $this->redis->set($key, $value);
		}
		if ($result === false)
		{
			$this->error_log('setkey:' . $key);
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
		return $this->redis->getStats();
	}

	/**
	 * delete cache data
	 * @param string $key cache key
	 */
	public function delete($key)
	{
		$key = $this->key_prefix . $key;
		self::$del_keys[] = $key;
		$result = $this->redis->delete($key);
		if ($result === false)
		{
			$this->error_log('deletekey:' . $key);
		}
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

	private function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	private function error_log($key)
	{
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			$php_error_log = "error_log.txt";
		}
		$s = date("[d-M-Y H:i:s]") . " PHP Notice: redis Failure: $key \r\n";
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $s);
		fclose($fp);
	}
}


