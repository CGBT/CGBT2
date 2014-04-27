<?php
class forums_discuz_model extends base_model
{
	private $table_prefix;
	private $charset;

	public function __construct($db_config_id = 'discuz')
	{
		$this->db_config_id = $db_config_id;
		$this->table_prefix = cg::config()->config['forums']['table_prefix'];
		$this->charset = cg::config()->config['db'][$db_config_id]['charset'];
	}

	public function get_thread_list($fid, $limit = 20, $displayorder = -1, $timelimit = 0)
	{
		$function = "_" . __FUNCTION__;
		$params = array(
			$fid,
			$limit,
			$displayorder,
			$timelimit
		);
		$args = func_get_args();
		$cache_key = 'forums_thread_' . implode('_', $args);
		return $this->get_cache_data($function, $cache_key, $params, 1800);
	}

	protected function _get_thread_list($params)
	{
		list($fid, $limit, $displayorder, $timelimit) = $params;
		//$displayorder = "0,1,2,3";
		$timestamp = time() - $timelimit;
		$table = $this->table_prefix . "threads";
		$sql = "select * from $table where fid = '$fid' and closed = 0 ";
		if ($displayorder > -1)
		{
			$sql .= " and displayorder in ($displayorder) ";
		}
		if ($timelimit > 0)
		{
			$sql .= " and dateline > '$timestamp' ";
		}
		$sql .= " order by tid desc limit $limit";
		$rows = $this->db()->get_rows($sql);
		if ($this->charset == "gbk")
		{
			cg::load_class('funcs.php');
			return funcs::gbk2utf8($rows);
		}
		return $rows;
	}

	public function get_posts_count($tid)
	{
		$table = $this->table_prefix . "posts";
		$sql = "select count(1) from $table where tid = '$tid'";
		return $this->db()->get_count($sql);
	}

	public function get_posts($tid, $pagesize = 20, $page = 1)
	{
		$table = $this->table_prefix . "posts";
		$start = ($page - 1) * $pagesize;
		$end = $pagesize * $page;
		$sql = "select * from $table where tid = '$tid' order by pid limit $start, $end";
		$rows = $this->db->get_rows($sql);
		if ($this->charset != "utf8" && $rows)
		{
			cg::load_class('funcs.php');
			return funcs::gbk2utf8($rows);
		}
		return $rows;
	}

	/**
	 *
	 * @param integer $uid
	 * @param string $username
	 * @return array
	 */
	public function get_user_info($uid, $username)
	{
		$table = $this->table_prefix . "members";
		$sql = "select * from $table where ";
		if (intval($uid) > 0)
		{
			$sql .= " uid = '$uid'";
		}
		else
		{
			if ($this->charset == "gbk")
			{
				cg::load_class('funcs.php');
				$username = funcs::utf82gbk($username);
			}
			$sql .= " username = '$username'";
		}
		$row = $this->db()->get_row($sql);
		if ($this->charset == 'gbk')
		{
			cg::load_class('funcs.php');
			return funcs::gbk2utf8($row);
		}
		return $row;
	}

	public function new_post($uid, $username, $subject, $message, $fid, $tid, $is_utf8 = true)
	{
		$table_posts = $this->table_prefix . "posts";
		$table_forums = $this->table_prefix . "forums";

		if ($is_utf8 && $this->charset == "gbk")
		{
			cg::load_class('funcs.php');
			$username = funcs::utf82gbk($username);
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
		}

		$timestamp = time();
		$arr_fields = array();
		$arr_fields['fid'] = $fid;
		$arr_fields['tid'] = $tid;
		$arr_fields['first'] = 1;
		$arr_fields['authorid'] = $uid;
		$arr_fields['author'] = $username;
		$arr_fields['subject'] = $subject;
		$arr_fields['message'] = $message;
		$arr_fields['dateline'] = $timestamp;
		$pid = $this->db()->insert($table_posts, $arr_fields);

		$lastpost = "$tid\t$subject\t$timestamp\t$username";
		$lastpost = $this->db()->real_escape_string($lastpost);
		$sql = "update $table_forums set
				lastpost = '$lastpost',
				threads = threads + 1,
				posts = posts + 1,
				todayposts = todayposts + 1
				where fid = '$fid'";
		$this->db()->query($sql);
		return $pid;
	}

	/**
	 *
	 * @param integer  $uid
	 * @param string   $username
	 * @param string   $subject
	 * @param string   $message
	 * @param integer  $fid
	 * @return integer $tid
	 */
	public function new_thread($uid, $username, $subject, $message, $fid)
	{
		$table_threads = $this->table_prefix . "threads";

		$timestamp = time();
		$is_utf8 = true;
		if ($this->charset == "gbk")
		{
			$is_utf8 = false;
			cg::load_class('funcs.php');
			$username = funcs::utf82gbk($username);
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
		}
		$arr_fields = array();
		$arr_fields['fid'] = $fid;
		$arr_fields['author'] = $username;
		$arr_fields['authorid'] = $uid;
		$arr_fields['subject'] = $subject;
		$arr_fields['dateline'] = $timestamp;
		$arr_fields['lastpost'] = $timestamp;
		$arr_fields['lastposter'] = $username;
		$tid = $this->db()->insert($table_threads, $arr_fields);

		$this->new_post($fid, $tid, $uid, $username, $subject, $message, $is_utf8);

		return $tid;
	}

	public function send_msg($fromuid, $touser, $subject, $message)
	{
		cg::load_class('uc_client/client.php');
		if ($this->charset == 'gbk')
		{
			cg::load_class('funcs.php');
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
			$touser = funcs::utf82gbk($touser);
		}
		uc_pm_send($fromuid, $touser, $subject, $message, 1, 0, 1);
	}

	public function check_new_msg($uid)
	{
		cg::load_class('uc_client/client.php');
		return intval(uc_pm_checknew($uid));
	}

	public function synlogout()
	{
		cg::load_class('uc_client/client.php');
		return uc_user_synlogout();
	}

	/**
	 * @param integer $uid
	 * @return string
	 */
	public function synlogin($uid)
	{
		return uc_user_synlogin($uid);
	}

	/**
	 * forums user login check
	 *
	 * @param string $username
	 * @param string $password
	 * @return array
	 */
	public function check_login($username, $password)
	{
		cg::load_class('uc_client/client.php');
		if ($this->charset == 'gbk')
		{
			cg::load_class('funcs.php');
			$username = funcs::utf82gbk($username);
		}
		$result = uc_user_login($username, $password);
		list($uid, $username, $password, $email) = $result;
		$data = array();
		if ($uid == -1)
		{
			$data['result'] = -1;
		}
		elseif ($uid == -2)
		{
			$data['result'] = -2;
		}
		elseif ($uid > 0)
		{
			$data['result'] = true;
			$data['user']['email'] = $email;
			$data['user']['uid'] = $uid;
		}
		return $data;
	}

	public function update_user($field, $value, $uid, $username)
	{
		$table = $this->table_prefix . "members";
		$sql = "update $table set $field = '$value' where ";
		if (intval($uid) > 0)
		{
			$sql .= " uid = '$uid' limit 1";
		}
		else
		{
			if ($this->charset == "gbk")
			{
				cg::load_class('funcs.php');
				$username = funcs::utf82gbk($username);
			}
			$sql .= " username = '$username' limit 1";
		}
		return $this->db()->query($sql);
	}

}