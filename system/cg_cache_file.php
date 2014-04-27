<?php

/**
 * file cache class
 */
class cg_cache_file
{
	private $cache_dir = '';

	/**
	 * construct function
	 * @param  array $cache_config   file cache config array
	 */
	public function __construct($cache_config)
	{
		if (!empty($cache_config['cache_dir']))
		{
			$this->cache_dir = $cache_config['cache_dir'];
		}
		if (empty($this->cache_dir))
		{
			$this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "cache";
		}
		$this->cache_dir = rtrim($this->cache_dir, '/\\');
		$this->cache_dir .= DIRECTORY_SEPARATOR;
		if (!is_dir($this->cache_dir))
		{
			$result = mkdir($this->cache_dir, 0777);
			if ($result === false)
			{
				$this->error_log('File Cache Error: ', $this->cache_dir . ' is not writalbe!');
			}
		}
	}

	/**
	 * get cache data
	 *
	 * @param string $key    cache key, convert to filename
	 * @return mixed
	 */
	public function get($key)
	{
		$filename = $this->key2path($key);
		if (!file_exists($filename))
		{
			return false;
		}
		$data = unserialize(file_get_contents($filename));
		if ($data !== false && ($data["_expire_time_"] > time()))
		{
			return $data["_content_"];
		}
		else
		{
			unlink($filename);
			return false;
		}
	}

	/**
	 * set cache data
	 *
	 * @param string   $key    cache_key or filename
	 * @param mixed    $value  cache data
	 * @param integer  cache   expire time, default 5s
	 * @return bool
	 */
	public function set($key, $value, $expire = 5)
	{
		$filename = $this->key2path($key);
		if ($expire == 0)
		{
			$expire = 86400 * 30;
		}
		$data["_expire_time_"] = time() + $expire;
		$data["_content_"] = $value;
		return file_put_contents($filename, serialize($data));
	}

	/**
	 * create cache file directory
	 * @param string $key cache_key
	 * @example key0: index_userlist -> /index/u/userlist.php
	 * @example key1: user_abc   -> /user/a/abc.php
	 * @example key2: user_198   -> /user/0/198.php
	 * @example key3: user_13188 -> /user/13/13188.php
	 * @example key4: user_list_13188 -> /user/list/13/13188.php
	 * @example key5: user_123_13188 -> /user/123/13/13188.php
	 * @example key6: user -> /user.php
	 */
	private function key2path($key)
	{
		$cache_dir = $this->cache_dir;
		$arr_dir = explode('_', $key);
		if (sizeof($arr_dir) == 1)
		{
			return $cache_dir . $key . '.php';
		}
		$last = array_pop($arr_dir);

		if (is_numeric($last) && intval($last) == $last)
		{
			$arr_dir[] = intval(intval($last) / 1000);
		}
		else
		{
			$arr_dir[] = $last[0];
		}
		$filename = $last . '.php';
		foreach ($arr_dir as $dir)
		{
			$cache_dir .= $dir . DIRECTORY_SEPARATOR;
			if (!is_dir($cache_dir))
			{
				mkdir($cache_dir, 0777);
			}
		}
		return $cache_dir . $filename;
	}

	/**
	 * get cache stats
	 *
	 * @return array  file list array
	 */
	public function stats()
	{
		$data = array();
		$dp = opendir($this->cache_dir);
		while (($file = readdir($dp)) !== false)
		{
			$data[] = $file;
		}
		return $data;
	}

	/**
	 * clear cache data
	 * @param string $key cache key
	 */
	public function delete($key)
	{
		$filename = $this->key2path($key);
		return unlink($filename);
	}

	/**
	 * clear all cache data
	 */
	public function flush()
	{
		$this->flush_dir($this->cache_dir);
		return true;
	}

	/**
	 * unlink files in dir
	 * @param string $dir  dir to unlink
	 */
	public function flush_dir($dir)
	{
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false)
		{
			if (is_file($dir . $file))
			{
				unlink($dir . $file);
			}
			elseif (is_dir($dir . $file))
			{
				$this->flush_dir($dir . $file . '/');
			}
		}
		return true;
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

	private function error_log($error, $descr)
	{
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			die('error: file cache dir is not writalbe!');
			return;
		}
		$s = date("[d-M-Y H:i:s]") . ' PHP Notice:  ' . $error . $descr . "\r\n";
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $s);
		fclose($fp);
	}
}




