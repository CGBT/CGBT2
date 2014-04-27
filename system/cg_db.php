<?php
class cg_db
{
	private $db_config;
	private $mysqli_master;
	private $mysqli_slave;
	private $current_mysqli;
	private $use_slave;
	private $use_slave_flag;
	private $error_log = '';
	private static $query_count = 0;
	private static $query_sql = array();
	private static $reconnect_times = 0;
	private static $connect_time = array();
	private static $query_time = array();
	private $charset = 'utf8';

	public function __construct($db_confg)
	{
		$this->db_config = $db_confg;
		if (isset($this->db_config['error_log']))
		{
			$this->error_log = $db_confg['error_log'];
		}
		if (!isset($this->db_config['master']))
		{
			echo "db config error";
			$this->txt_error_log('db config error: ', 'master db config not defined');
			exit();
		}
		if (!isset($this->db_config['use_slave']) || $this->db_config['use_slave'] === false)
		{
			$this->use_slave = false;
		}
		else
		{
			$array = array_diff_assoc($this->db_config['master'], $this->db_config['slave']);
			if (empty($array))
			{
				$this->use_slave = false;
			}
			else
			{
				$this->use_slave = true;
			}
		}
		$dict_charset = array(
			'gbk',
			'latin1',
			'utf8',
			'gb2312'
		);
		if (isset($this->db_config['charset']) && in_array($this->db_config['charset'], $dict_charset))
		{
			$this->charset = $this->db_config['charset'];
		}
	}
	//modified by @hulupiao 2014.01.13
	private function microtime_float()
	{
		return microtime(true);
	}

	public function push_slave_status()
	{
		$this->use_slave_flag = $this->use_slave;
		$this->use_slave = false;
	}

	public function pop_slave_status()
	{
		$this->use_slave = $this->use_slave_flag;
	}

	private function connect($type = 'master')
	{
		if ($type != 'master' && $type != 'slave')
		{
			echo "db params error";
			$this->txt_error_log(mysqli_connect_error(), "Server Info: connect db params error");
			exit();
		}
		$db_config = $this->db_config[$type];
		$db_config_name = $this->db_config['name'];
		if ($type == 'slave' && isset($this->db_config['slave'][0]))
		{
			$count = count($this->db_config['slave']);
			$rand = mt_rand(0, $count - 1);
			$db_config = $this->db_config['slave'][$rand];
		}
		$start_time = $this->microtime_float();
		@$this->{'mysqli_' . $type} = new mysqli($db_config['host'], $db_config['username'], $db_config['passwd'], $db_config['dbname'], $db_config['port']);
		$end_time = $this->microtime_float();
		self::$connect_time[] = $end_time - $start_time;
		if (mysqli_connect_errno())
		{
			echo "$db_config_name $type db connect error";
			$this->txt_error_log(mysqli_connect_error(), "Server Info: $db_config[host]:$db_config[port],$db_config[username]/$db_config[passwd],$db_config[dbname]");
			exit();
		}
		$this->set_charset();
	}

	private function set_charset()
	{
		if (is_object($this->mysqli_master))
		{
			$this->mysqli_master->set_charset($this->charset);
		}
		if (is_object($this->mysqli_slave))
		{
			$this->mysqli_slave->set_charset($this->charset);
		}
	}

	public function insert($table, $arr_fields)
	{
		$sql = "insert into $table ";
		foreach ($arr_fields as $key => $value)
		{
			$arr_fields[$key] = $this->real_escape_string($value);
		}
		$fields = implode("`, `", array_keys($arr_fields));
		$values = implode("', '", $arr_fields);
		$sql .= "(`$fields`) values ('$values')";
		$result = $this->query($sql);
		if ($result === false)
		{
			return false;
		}
		else
		{
			return $this->insert_id();
		}
	}

	public function update($table, $arr_fields, $where)
	{
		$sql = "update $table set ";
		$fields = array();
		foreach ($arr_fields as $key => $value)
		{
			$fields[] = "`$key` = '" . $this->real_escape_string($value) . "'";
		}
		$sql .= implode(", ", $fields);
		if (!empty($where))
		{
			$sql .= " where $where ";
		}
		$result = $this->query($sql);
		if ($result === false)
		{
			return false;
		}
		return true;
	}

	public static function escape_mysql_wildcards($s)
	{
		return str_replace(array(
			"%",
			"_"
		), array(
			"\\%",
			"\\_"
		), $s);
	}

	public function get_value($sql)
	{
		$result = $this->query($sql);
		if ($result === false)
		{
			return false;
		}
		$row = $result->fetch_row();
		$result->close();
		if ($this->charset == 'gbk')
		{
			return funcs::gbk2utf8($row[0]);
		}		
		return $row[0];		
	}

	public function get_count($sql)
	{
		return intval($this->get_value($sql));
	}

	public function get_cols($sql)
	{
		$result = $this->query($sql);
		if ($result === false)
		{
			return array();
		}
		$data = array();
		while(($row = $result->fetch_array(MYSQLI_NUM)) !== NULL)
		{
			$data[] = $row[0];
		}
		$result->close();
		if ($this->charset == 'gbk')
		{
			return funcs::gbk2utf8($data);
		}
		return $data;				
	}

	/**
	 * @param  string $sql
	 * @return mixed array
	 */
	public function get_rows($sql)
	{
		$result = $this->query($sql);
		if ($result === false)
		{
			return array();
		}
		$rows = array();
		while(($row = $result->fetch_assoc()) !== NULL)
		{
			$rows[] = $row;
		}
		$result->close();
		if ($this->charset == 'gbk')
		{
			return funcs::gbk2utf8($rows);
		}
		return $rows;
	}

	/**
	 * @param  string  $sql
	 * @return mixed   array
	 */
	public function get_row($sql)
	{
		$result = $this->query($sql);
		if ($result === false)
		{
			return array();
		}
		$row = $result->fetch_assoc();
		if ($this->charset == 'gbk')
		{
			return funcs::gbk2utf8($row);
		}
		return $row;
	}

	public function query($sql)
	{
		if ($this->charset == 'gbk')
		{
			$sql = funcs::utf82gbk($sql);
		}
		$start_time = $this->microtime_float();
		self::$query_count++;
		self::$query_sql[] = $sql;
		if ($this->use_slave && $this->is_select_sql($sql))
		{
			$result = $this->mysqli_slave()->query($sql);
			$this->current_mysqli = 'mysqli_slave';
		}
		else
		{
			$result = $this->mysqli_master()->query($sql);
			$this->current_mysqli = 'mysqli_master';
		}
		$end_time = $this->microtime_float();
		self::$query_time[] = $end_time - $start_time;
		if ($result === false)
		{
			if ($this->current_mysqli()->errno == 2006 && self::$reconnect_times < 5)
			{
				$this->ping();
				self::$reconnect_times++;
				return $this->query($sql);
			}
			$this->sql_error_log($sql);
			return false;
		}
		return $result;
	}

	public function insert_id()
	{
		return $this->mysqli_master->insert_id;
	}

	//@todo no use
	public function multi_query($sql)
	{
		$this->query_count++;
		$this->query_sql[] = $sql;
		$result = $this->mysqli_master->multi_query($sql);
		if ($result === false)
		{
			$this->sql_error_log($sql);
			return false;
		}
		return true;
	}

	public function ping()
	{
		if (!$this->current_mysqli()->ping())
		{
			if ($this->current_mysqli == 'mysqli_master')
			{
				$this->connect('master');
			}
			elseif ($this->current_mysqli == 'mysqli_slave')
			{
				$this->connect('slave');
			}
		}
	}

	public function get_db_stat_data()
	{
		return array(
			'query_count' => self::$query_count,
			'query_sql' => self::$query_sql,
			'reconnect_times' => self::$reconnect_times,
			'query_time' => self::$query_time,
			'connect_time' => self::$connect_time
		);
	}

	public function get_query_count()
	{
		return self::$query_count;
	}

	public function get_query_sql()
	{
		return self::$query_sql;
	}

	public function get_reconnect_times()
	{
		return self::$reconnect_times;
	}

	public function get_query_time()
	{
		return self::$query_time;
	}

	private function mysqli_master()
	{
		if (!is_object($this->mysqli_master))
		{
			$this->connect('master');
		}
		return $this->mysqli_master;
	}

	private function mysqli_slave()
	{
		if (!is_object($this->mysqli_slave))
		{
			$this->connect('slave');
		}
		return $this->mysqli_slave;
	}

	private function current_mysqli()
	{
		if ($this->current_mysqli == 'mysqli_master' || !$this->use_slave)
		{
			return $this->mysqli_master();
		}
		else
		{
			return $this->mysqli_slave();
		}
	}

	public function real_escape_string($string)
	{
		return $this->current_mysqli()->real_escape_string($string);
	}

	private function sql_error_log($error_sql)
	{
		if (cg_config::is_debug())
		{
			echo '<hr />' . $error_sql;
			exit();
		}
		$ts = time();
		$error = $this->current_mysqli()->error;
		$error_descr = $this->real_escape_string($error);
		$sql = $this->real_escape_string($error_sql);
		$sql = "insert into sql_error_log (error_sql, error_descr, createtime) values ('$sql', '$error_descr', '$ts')";
		$result = $this->mysqli_master()->query($sql);
		if ($result === false)
		{
			$this->txt_error_log($error_sql, $error);
		}
	}

	private function txt_error_log($error_sql, $error)
	{
		if (!is_writable($this->error_log))
		{
			$this->error_log = ini_get("error_log");
			if (empty($this->error_log))
			{
				$this->error_log = "error_log.txt";
			}
		}
		if (is_writable($this->error_log))
		{
			$s = date("[d-M-Y H:i:s]") . " SQL Error: " . $error_sql . ",\t" . $error . "\r\n";
			$fp = fopen($this->error_log, 'a');
			fwrite($fp, $s);
			fclose($fp);
		}
	}

	private function is_select_sql($sql)
	{
		return strtolower(substr(ltrim($sql), 0, 6)) == 'select';
	}
}
