<?php
class users_model extends base_model
{

	public function __construct()
	{
		parent::__construct();
		$this->table = $this->table('users');
		$this->pk = 'uid';
	}

	/**
	 *
	 * @return users_model
	 */
	public static function get_instance()
	{
		static $instance;
		$name = __CLASS__;
		if (!isset($instance[$name]))
		{
			$instance[$name] = new $name();
		}
		return $instance[$name];
	}

	public function get_uid_by_passkey($passkey)
	{
		$function_name = '_' . __FUNCTION__;
		$cache_key = "passkey2uid_$passkey";
		return $this->get_cache_data($function_name, $cache_key, $passkey);
	}

	protected function _get_uid_by_passkey($passkey)
	{
		$passkey = $this->addslashes_array($passkey);
		$sql = "select uid from $this->table where passkey = '$passkey' limit 1";
		return $this->db()->get_value($sql);
	}

	public function get_uid_by_username($username, $use_cache = true)
	{
		$username = trim($username);
		if (strlen($username) >= 30 || strpos($username, ' '))
		{
			return false;
		}
		if (!preg_match('/^[a-z0-9@\_]+$/i', $username))
		{
			return false;
		}
		if ($use_cache) //@todo remove, use in login submit
		{
			$function_name = '_' . __FUNCTION__;
			$cache_key = "username2uid_$username";
			return $this->get_cache_data($function_name, $cache_key, $username);
		}
		else
		{
			return $this->_get_uid_by_username($username);
		}
	}

	protected function _get_uid_by_username($username)
	{
		$username = $this->addslashes_array($username);
		$sql = "select uid from $this->table where username = '$username' limit 1";
		return $this->db()->get_value($sql);
	}


	/**
	 * 根据邮箱取用户信息
	 * @param  email $email email
	 * @return array list
	 */
	public function get_by_email($email)
	{
		if (empty($email))
		{
			return NULL;
		}
		$email = $this->db()->real_escape_string($email);
		$sql = "select * from $this->table where email='$email'";
		return $this->db()->get_rows($sql);
	}

	/**
	 * 根据注册IP取用户信息
	 * @param  ip $regip 注册IP
	 * @return array list
	 */
	public function get_by_ip($regip)
	{
		if (empty($regip))
		{
			return NULL;
		}
		$regip = $this->db()->real_escape_string($regip);
		$sql = "select * from $this->table where regip='$regip'";
		return $this->db()->get_rows($sql);
	}

	/**
	 * 取BT用户总数
	 * @return int BT用户总数
	 */
	public function get_count()
	{
		$sql = "select count(*) from $this->table";
		return $this->db()->get_count($sql);
	}

	/**
	 * 根据取用户UID范围取用户列表
	 * @param  int $start_uid UID>=起始值
	 * @param  int $end_uid   UID<结束值
	 * @return array            用户列表
	 */
	public function get_list_by_uid($start_uid, $end_uid)
	{
		if (!is_numeric($start_uid) || !is_numeric($end_uid))
		{
			throw new Exception('参数类型不对');
			exit();
		}
		$sql = "select * from $this->table where uid >= $start_uid && uid < $end_uid order by uid desc";
		return $this->db()->get_rows($sql);
	}
}