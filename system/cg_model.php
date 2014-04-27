<?php
class cg_model
{
	protected $db_config_name = '';
	protected $table_prefix = '';
	protected $table = '';
	protected $pk = '';
	public $use_cache = true;
	private $use_cache_flag = true;
	protected $cache_type = '';
	public $timestamp;
	public static $sleep_times = 0;

	public function __construct()
	{
		$this->timestamp = time();
		$this->cache_type = cg::config()->config['cache']['cache_type'];
		if (DIRECTORY_SEPARATOR == '\\')
		{
			$this->cache_type = 'memcache';
		}
	}

	public function push_cache_status()
	{
		$this->use_cache_flag = $this->use_cache;
		$this->use_cache = false;
	}

	public function pop_cache_status()
	{
		$this->use_cache = true;
	}

	final public function get_rows_by_ids($ids)
	{
		$rows = array();
		foreach ($ids as $id)
		{
			$rows[] = $this->find($id);
		}
		return $rows;
	}

	final public function get_rows_by_sql($sql)
	{
		return $this->db()->get_rows($sql);
	}

	final public function get_row($where = '', $orderby = '')
	{
		$rows = $this->get_rows($where, $orderby, '1');
		if (isset($rows[0]))
		{
			return $rows[0];
		}
		else
		{
			return array();
		}
	}

	final public function get_rows($where = '', $orderby = '', $limit = '')
	{
		$sql = "select * from $this->table ";
		if (!empty($where))
		{
			$sql .= " where $where ";
		}
		if (!empty($orderby))
		{
			$sql .= " order by $orderby";
		}
		if (!empty($limit))
		{
			$sql .= " limit $limit";
		}
		return $this->db()->get_rows($sql);
	}

	final public function count($where = '', $use_slave = false)
	{
		//@todo use cache
		if (!$use_slave)
		{
			$this->db()->push_slave_status();
		}
		$sql = "select count(1) from $this->table";
		if (!empty($where))
		{
			$sql .= " where $where";
		}
		$count = $this->db()->get_count($sql);
		$this->db()->pop_slave_status();
		return $count;
	}

	final public function exists($pk_id, $use_slave = false)
	{
		//@todo use cache
		if (!$use_slave)
		{
			$this->db()->push_slave_status();
		}
		$sql = "select count(1) from $this->table where `$this->pk` = '$pk_id'";
		$count = $this->db()->get_count($sql);
		$this->db()->pop_slave_status();
		return $count;
	}

	final public function find($pk_id, $use_cache = true, $cache_time = 0)
	{
		$pk_id = intval($pk_id);
		$cache_key = $this->table . '_' . $pk_id;
		if ($use_cache)
		{
			return $this->get_cache_data('_find', $cache_key, $pk_id, $cache_time);
		}
		else
		{
			$data = $this->_find($pk_id);
			$this->cache()->set($cache_key, $data, $cache_time);
			return $data;
		}
	}

	final public function _find($pk_id)
	{
		$sql = "select * from $this->table where `$this->pk` = '$pk_id' limit 1";
		return $this->db()->get_row($sql);
	}

	final public function insert($arr_fields)
	{
		$data = array();
		$columns = $this->get_columns();
		foreach ($columns as $field_name => $column)
		{
			if (isset($arr_fields[$field_name]))
			{
				$data[$field_name] = $arr_fields[$field_name];
			}
			/*
			else
			{
				$data[$field_name] = $column['default'] === null ? '' : $column['default'];
			}
			*/
		}
		if (empty($data))
		{
			return;
		}
		unset($arr_fields);

		$insert_id = $this->db()->insert($this->table, $data);
		if ($insert_id > 0)
		{
			//$data[$this->pk] = $insert_id;
				//$this->cache()->set($this->table . '_' . $insert_id, $data);
			/*
			$this->db()->use_slave = false;
			$this->find($insert_id, false);
			$this->db()->use_slave = true;
			*/
		}
		return $insert_id;
	}

	final public function update($arr_fields, $pk_id, $update_cache = true)
	{
		$data = array();
		$columns = $this->get_columns();
		if (empty($columns))
		{
			$this->error_log('empty columns' . $this->table);
		}
		foreach ($columns as $field_name => $column)
		{
			if (isset($arr_fields[$field_name]))
			{
				$data[$field_name] = $arr_fields[$field_name];
			}
		}
		if (empty($data))
		{
			return;
		}
		unset($arr_fields);
		$pk_id = intval($pk_id);
		$result = $this->db()->update($this->table, $data, '`' . $this->pk . "` = '$pk_id'");
		if ($update_cache)
		{
			$cache_data = $this->find($pk_id);
			if (empty($cache_data) || !is_array($cache_data))
			{
				$cache_data = $this->_find($pk_id);
			}
			if (!empty($this->pk))
			{
				$cache_data[$this->pk] = $pk_id;
			}
			$new_data = array_merge((array)$cache_data, (array)$data);
			foreach ($columns as $field_name => $column)
			{
				if (!isset($new_data[$field_name]))
				{
					$this->cache()->delete($this->table . '_' . $pk_id);
					return;
				}
			}
			$this->cache()->set($this->table . '_' . $pk_id, $new_data);
		}
		return $result;
	}

	final public function delete($pk_id, $update_cache = true)
	{
		$pk_id = intval($pk_id);
		$sql = "delete from $this->table where `$this->pk` = '$pk_id'";
		$result = $this->db()->query($sql);
		if ($update_cache)
		{
			$this->delete_cache($pk_id);
		}
		return $result;
	}

	final public function delete_cache($pk_id)
	{
		$this->cache($this->cache_type)->delete($this->table . '_' . $pk_id);
	}

	/**
	 * @return cg_db
	 */
	final public function db()
	{
		return cg::db($this->db_config_name);
	}

	/**
	 *
	 * @param string $cache_type
	 * return cg_cache_memcache
	 */
	final public function cache($cache_type = '')
	{
		if (empty($cache_type))
		{
			$cache_type = $this->cache_type;
		}
		return cg::cache($cache_type);
	}

	final public function get_columns()
	{
		$cache_key = $this->table . '_columns';
		$data = $this->get_cache_data('_' . __FUNCTION__, $cache_key, '', 3600);
		if (empty($data))
		{
			$this->error_log($cache_key);
		}
		return $data;
	}

	private function error_log($message)
	{
		$php_error_log = ini_get("error_log");
		if ($php_error_log == '')
		{
			$php_error_log = "error_log.txt";
		}
		$s = date("[d-M-Y H:i:s]") . ' PHP Notice: get_columns Failure ' . $message . "\r\n";
		$fp = fopen($php_error_log, 'a');
		fwrite($fp, $s);
		fclose($fp);
	}

	final public function _get_columns()
	{
		$data = array();
		$sql = "desc " . $this->table;
		$rows = $this->db()->get_rows($sql);
		foreach ($rows as $key => $row)
		{
			$data[$row['Field']] = array_change_key_case($row, CASE_LOWER);
		}
		return $data;
	}

	/**
	 * 获取缓存数据
	 *
	 * @param string	$method         获取数据的函数名
	 * @param string	$cache_key	           缓存key
	 * @param array     $params         函数参数
	 * @param int		$cache_time	          缓存时间
	 * @param string    $cache_type     缓存类型
	 * @return mixed
	 */
	final public function get_cache_data($method, $cache_key, $params = '', $cache_time = 0, $cache_type = '')
	{
		$data = false;
		$get_data_ing = '__GET_DATA_ING__';
		if (empty($cache_type))
		{
			$cache_type = $this->cache_type;
		}
		if (DIRECTORY_SEPARATOR == '\\')
		{
			$cache_type = 'memcache';
		}
		if ($this->use_cache)
		{
			$data = $this->cache($cache_type)->get($cache_key);
			if ($data === $get_data_ing)
			{
				$got_data = 0;
				for($i = 0; $i < 5; $i++)
				{
					self::$sleep_times++;
					usleep(200000);
					$data = $this->cache($cache_type)->get($cache_key);
					if ($data !== $get_data_ing)
					{
						$got_data = 1;
						break;
					}
				}
				if (!$got_data)
				{
					$data = false;
				}
			}
		}
		if ($data === false)
		{
			if ($this->use_cache)
			{
				$this->cache($cache_type)->set($cache_key, $get_data_ing, 1);
			}

			$data = $this->$method($params);

			if (empty($data))
			{
				$this->cache($cache_type)->set($cache_key, $data, 120);
			}
			else
			{
				$this->cache($cache_type)->set($cache_key, $data, $cache_time);
			}
		}
		return $data;
	}

	/**
	 * only for $_GET,$_POST,$_COOKIE
	 * @param mixed $string
	 * @return mixed
	 */
	final public function addslashes_array($string)
	{
		if (get_magic_quotes_gpc())
		{
			return $string;
		}
		if (is_array($string))
		{
			foreach ($string as $key => $value)
			{
				$string[$key] = $this->addslashes_array($value);
			}
		}
		else
		{
			$string = addslashes($string);
		}
		return $string;
	}
}
