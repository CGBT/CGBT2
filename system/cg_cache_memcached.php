<?php

/**
 * memcached cache class
 *
 */
class cg_cache_memcached
{
	private $memcached;
	private $key_prefix = '';
	private static $get_keys = array();
	private static $set_keys = array();
	private static $del_keys = array();
	private static $cache_time = 0;
	private static $get_keys_time = array();

	/**
	 * cg cache memcached
	 *
	 * @param array $memcached_config   memcached config array
	 */
	public function __construct($memcached_config)
	{
		if (!empty($memcached_config['key_prefix']))
		{
			$this->key_prefix = $memcached_config['key_prefix'] . '_';
		}
		if (!isset($memcached_config['server'][0]['host']))
		{
			unset($memcached_config['server']);
			$memcached_config['server'][0] = array(
				"host" => "127.0.0.1",
				"port" => 11211,
				"weight" => 1,
				"status" => true
			);
		}
		$this->memcached = new Memcached();
		$serverlist = $this->memcached->getServerList();
		if (empty($serverlist))
		{
			$this->memcached->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
			$this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
			$this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			$this->memcached->setOption(Memcached::OPT_COMPRESSION, true);
			//$this->memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 400);
			foreach ($memcached_config['server'] as $key => $s)
			{
				if ($s['status'] == false)
				{
					unset($memcached_config['server'][$key]);
				}
				unset($memcached_config['server'][$key]['status']);
			}
			$start_time = $this->microtime_float();
			$result = $this->memcached->addServers($memcached_config['server']);
			if (!$result)
			{
				$this->error_log('add_server');
			}
			$end_time = $this->microtime_float();
			self::$cache_time = $end_time - $start_time;
		}
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
			foreach ($key as $k => $v)
			{
				$key[$k] = $this->key_prefix . $v;
			}
			self::$get_keys[] = $key;
			$null = null;
			$olddata = $this->memcached->getMulti($key, $null, Memcached::GET_PRESERVE_ORDER);
			if ($this->memcached->getResultCode() === Memcached::RES_SUCCESS)
			{
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
				//$this->error_log('getkey:'.$key);
				return false;
			}
		}
		else
		{
			$key = $this->key_prefix . $key;
			self::$get_keys[] = $key;
			$data = $this->memcached->get($key);
			$end_time = $this->microtime_float();
			self::$get_keys_time[] = $end_time - $start_time;
			if ($this->memcached->getResultCode() === Memcached::RES_SUCCESS)
			{
				return $data;
			}
			else
			{
				//$this->error_log('getkey'.$key);
				return false;
			}
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
		$result = $this->memcached->set($key, $value, $expire);
		if ($result === false)
		{
			$this->error_log('setkey:'.$key);
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
		return $this->memcached->getStats();
	}

	/**
	 * delete cache data
	 * @param string $key cache key
	 */
	public function delete($key)
	{
		$key = $this->key_prefix . $key;
		self::$del_keys[] = $key;
		$result = $this->memcached->delete($key);
		if ($result === false)
		{
			//$this->error_log('deletekey:'.$key);
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

	/**
	 * when memcahce failure ,then call this
	 * note: static method
	 */
	public static function memcached_failure_callback()
	{
		$args = func_get_args();
		$message = date("[d-M-Y H:i:s]") . " PHP Notice:  memcachedd ";
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

	private function error_log($key)
	{
		$error_code = $this->memcached->getResultCode();
		$message = $this->memcached->getResultMessage();
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			$php_error_log = "error_log.txt";
		}
		$s = date("[d-M-Y H:i:s]") . " PHP Notice: memcached Failure, error_code: $error_code, key $key, $message . \r\n";
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $s);
		fclose($fp);
	}
}


