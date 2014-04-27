<?php
class forums_discuzx_model extends base_model
{
	public $gbk_charset = false;
	public $charset;

	public function __construct()
	{
		$forums_config = cg::config()->config['forums'];
		$this->db_config_name = $forums_config['db_config_name'];
		$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'];
		$this->charset = cg::config()->config['db'][$this->db_config_name]['charset'];

		if (strtolower($this->charset) == 'gbk')
		{
			$this->gbk_charset = true;
			cg::load_class('funcs');
		}
	}

	/**
	 *
	 * @return forums_discuzx_model
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

	/**
	 * forums user login check
	 *
	 * @param string $username
	 * @param string $password
	 * @return array
	 */
	public function check_login($username, $password)
	{
		$username = $this->db()->real_escape_string($username);
		if ($this->gbk_charset)
		{
			$username = funcs::utf82gbk($username);
		}
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		return $uc_client->check_login($username, $password);
	}

	public function pm_send($from_uid, $msgto, $subject, $message, $isusername)
	{
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		//public function uc_pm_send($fromuid, $msgto, $subject, $message, $instantly = 1, $replypmid = 0, $isusername = 0, $type = 0)
		$uc_client->uc_pm_send($from_uid, $msgto, $subject, $message, 1, 0, $isusername);
	}

	public function notice_send($from_uid, $from_username, $to_uid, $note)
	{
		$arr_fields = array(
			'uid' => $to_uid,
			'type' => 'post',
			'new' => '1',
			'authorid' => $from_uid,
			'author' => $from_username,
			'note' => $note,
			'dateline' => time(),
			'from_id' => 1,
			'from_idtype' => 'at',
			'from_num' => 1,
			'category' => 1
		);
		$this->db()->insert('pre_home_notification', $arr_fields);
	}

	public function pm_checknew($uid, $more = 0)
	{
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		return $uc_client->uc_pm_checknew($uid, $more);
	}

	/**
	 * @param integer $uid
	 * @return string
	 */
	public function synlogin($uid)
	{
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		return $uc_client->uc_syn_login($uid);
	}

	public function synlogout()
	{
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		return $uc_client->uc_syn_logout();
	}

	public function get_thread_count($fid, $displayorder)
	{
		$table = $this->table_prefix . "forum_thread";
		$sql = "select count(1) from $table where fid = '$fid' ";
		if ($displayorder != -1)
		{
			$sql .= " and displayorder in ($displayorder) ";
		}
		return $this->db()->get_count($sql);
	}

	public function get_thread_list($fid, $page, $pagesize = 20, $displayorder = -1, $timelimit = 0)
	{
		$function = "_" . __FUNCTION__;
		$params = array(
			$fid,
			$page,
			$pagesize,
			$displayorder,
			$timelimit
		);
		$args = func_get_args();
		$cache_key = 'forums_thread_' . implode('_', $args);
		$this->use_cache = false;
		return $this->get_cache_data($function, $cache_key, $params, 1800);
	}

	protected function _get_thread_list($params)
	{
		list($fid, $page, $pagesize, $displayorder, $timelimit) = $params;
		//$displayorder = "0,1,2,3";
		$timestamp = time() - $timelimit;
		$table = $this->table_prefix . "forum_thread";
		$sql = "select tid from $table where fid = '$fid' ";
		if ($displayorder != -1)
		{
			$sql .= " and displayorder in ($displayorder) ";
		}
		if ($timelimit > 0)
		{
			$sql .= " and dateline > '$timestamp' ";
		}
		$start = ($page - 1) * $pagesize;
		$sql .= " order by tid desc limit $start, $pagesize";
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
		$table = $this->table_prefix . "forum_post";
		$sql = "select count(1) from $table where tid = '$tid'";
		return $this->db()->get_count($sql);
	}

	public function get_posts($tid, $page = 1, $pagesize = 20)
	{
		$table = $this->table_prefix . "forum_post";
		$start = ($page - 1) * $pagesize;
		$sql = "select * from $table where tid = '$tid' and invisible = 0 order by pid limit $start, $pagesize";
		$rows = $this->db()->get_rows($sql);
		if ($this->charset != "utf8" && $rows)
		{
			return funcs::gbk2utf8($rows);
		}
		return $rows;
	}

	public function get_first_post($tid)
	{
		$table = $this->table_prefix . "forum_post";
		$sql = "select * from $table where tid = '$tid' and first = 1 limit 1";
		$row = $this->db()->get_row($sql);
		if ($this->charset != "utf8" && $row)
		{
			cg::load_class('funcs.php');
			return funcs::gbk2utf8($row);
		}
		return $row;
	}

	/**
	 *
	 * @param integer $uid
	 * @param string $username
	 * @return array
	 */
	public function get_user_info($username)
	{
		$username = $this->db()->real_escape_string($username);
		if ($this->charset == "gbk")
		{
			$username = funcs::utf82gbk($username);
		}
		$sql = "select emailstatus, posts, a.regdate, groupid, a.email,b.* from {$this->table_prefix}ucenter_members a
		        inner join {$this->table_prefix}common_member_count b on a.uid=b.uid
		        inner join {$this->table_prefix}common_member       c on a.uid=c.uid
		        where a.username='$username' limit 1";
		$row = $this->db()->get_row($sql);
		if ($this->charset == 'gbk')
		{
			return funcs::gbk2utf8($row);
		}
		return $row;
	}

	public function new_post($fid, $tid, $uid, $username, $subject, $message, $is_utf8 = true, $first = 1)
	{
		$table_id = $this->table_prefix . "forum_post_tableid";
		$arr_fields['pid'] = '';
		$pid = $this->db()->insert($table_id, $arr_fields);

		$table_posts = $this->table_prefix . "forum_post";
		$table_forums = $this->table_prefix . "forum_forum";

		if ($is_utf8 && $this->charset == "gbk")
		{
			$username = funcs::utf82gbk($username);
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
		}

		$timestamp = time();
		$arr_fields = array();
		$arr_fields['pid'] = $pid;
		$arr_fields['fid'] = $fid;
		$arr_fields['tid'] = $tid;
		$arr_fields['first'] = $first;
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

	public function delete_thread($tid)
	{
		$table_threads = $this->table_prefix . "forum_thread";
		$sql = "update $table_threads set displayorder = '-1' where tid = '$tid'";
		$this->db()->query($sql);

		$table_posts = $this->table_prefix . "forum_post";
		$sql = "update $table_posts set invisible = -1 where tid = '$tid'";
		$this->db()->query($sql);

		/*
		$sql = "delete from $table_threads where tid='$tid'";
		$this->db()->query($sql);
		$sql = "delete from $table_posts where tid='$tid'";
		$this->db()->query($sql);
		*/
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
		$table_threads = $this->table_prefix . "forum_thread";

		$timestamp = time();
		$is_utf8 = true;
		if ($this->charset == "gbk")
		{
			$is_utf8 = false;
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
		$arr_fields['status'] = '32'; //回帖提醒
		$tid = $this->db()->insert($table_threads, $arr_fields);

		$this->new_post($fid, $tid, $uid, $username, $subject, $message, $is_utf8);

		return $tid;
	}

	public function update_thread($tid, $uid, $username, $subject, $message, $fid)
	{
		$table_threads = $this->table_prefix . "forum_thread";

		$timestamp = time();
		$is_utf8 = true;
		if ($this->charset == "gbk")
		{
			$is_utf8 = false;
			$username = funcs::utf82gbk($username);
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
		}
		$arr_fields = array();
		$arr_fields['fid'] = $fid;
		$arr_fields['author'] = $username;
		$arr_fields['authorid'] = $uid;
		$arr_fields['subject'] = $subject;
		$arr_fields['lastpost'] = $timestamp;
		$arr_fields['lastposter'] = $username;
		$arr_fields['status'] = '32'; //回帖提醒
		$this->db()->update($table_threads, $arr_fields, "tid='$tid'");

		$this->update_post($fid, $tid, $uid, $username, $subject, $message, $is_utf8);

		return $tid;
	}

	public function update_post_content($pid, $content)
	{
		$table_posts = $this->table_prefix . "forum_post";
		$arr_fields['message'] = $content;
		$this->db()->update($table_posts, $arr_fields, "pid='$pid'");
	}

	public function update_post($fid, $tid, $uid, $username, $subject, $message, $is_utf8 = true)
	{
		$table_posts = $this->table_prefix . "forum_post";
		$table_forums = $this->table_prefix . "forum_forum";

		$sql = "select pid from $table_posts where tid='$tid' and first = 1 ";
		$pid = $this->db()->get_value($sql);

		if ($is_utf8 && $this->charset == "gbk")
		{
			$username = funcs::utf82gbk($username);
			$subject = funcs::utf82gbk($subject);
			$message = funcs::utf82gbk($message);
		}

		$timestamp = time();
		$arr_fields = array();
		$arr_fields['fid'] = $fid;
		$arr_fields['authorid'] = $uid;
		$arr_fields['author'] = $username;
		$arr_fields['subject'] = $subject;
		$arr_fields['message'] = $message;

		$pid = $this->db()->update($table_posts, $arr_fields, "pid='$pid'");

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

	public function get_lastest_thread_replies()
	{
		$table_threads = $this->table_prefix . "forum_thread";
		$start_time = $this->timestamp - 86400 * 10;
		$sql = "select tid,replies,lastpost from $table_threads where lastpost > '$start_time'";
		return $this->db()->get_rows($sql);
	}

	public function add_credits($field, $count, $uid)
	{
		$table = $this->table_prefix . 'common_member_count';
		$sql = "update $table set $field = $field + $count where uid = '$uid'";
		$this->db()->query($sql);
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

	public function new_notification($forums_uid)
	{
		$table = $this->table_prefix . "home_notification";
		$sql = "select count(1) from $table where uid = '$forums_uid' and new = '1'";
		return $this->db()->get_count($sql);
	}

	public function new_msg($forums_uid)
	{
		cg::load_class('uc_client');
		$uc_client = new uc_client();
		return $uc_client->uc_pm_checknew($forums_uid);
	}

	public function get_attach($aid)
	{
		$cache_key = 'forums_attach_' . $aid;
		$function = '_' . __FUNCTION__;
		return $this->get_cache_data($function, $cache_key, $aid, 86400 * 7);
	}

	public function _get_attach($aid)
	{
		$sql = "select * from pre_forum_attachment where aid = '$aid'";
		$row = $this->db()->get_row($sql);
		$tableid = $row['tableid'];
		$table = "pre_forum_attachment_$tableid";
		$sql = "select * from $table where aid = '$aid'";
		return $this->db()->get_row($sql);
	}
}