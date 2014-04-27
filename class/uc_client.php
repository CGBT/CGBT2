<?php
define('UC_CLIENT_VERSION', '1.5.0');
define('UC_CLIENT_RELEASE', '20090121');
class uc_client
{
	private $uc_api;
	private $uc_key;
	private $uc_appid;
	private $uc_ip;
	private $db_config_name;
	private $table_prefix;
	private $charset;

	/**
	 * @return uc_client
	 */
	public function __construct()
	{
		$forums_config = cg::config()->config['forums'];
		$uc_config = cg::config()->config['forums']['uc_config'];
		$this->db_config_name = $uc_config['db_config_name'];
		$this->charset = cg::config()->config['db'][$this->db_config_name]['charset'];

		if ($this->db_config_name == $forums_config['db_config_name'])
		{
			$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'] . 'ucenter_';
		}
		else
		{
			$this->table_prefix = cg::config()->config['db'][$this->db_config_name]['table_prefix'];
		}

		$this->uc_api = $uc_config['uc_api'];
		$this->uc_key = $uc_config['uc_key'];
		$this->uc_appid = $uc_config['uc_appid'];
		$this->uc_ip = $uc_config['uc_ip'];
	}

	/**
	 *
	 * @param string $username username,utf8
	 * @param string $password password
	 *
	 * @return array
	 */
	public function check_login($username, $password)
	{
		$table = $this->table_prefix . 'members';
		if (strtolower($this->charset) == 'gbk')
		{
			$username = funcs::utf82gbk($username);
		}
		$data = array();
		$sql = "select * from $table where username = '$username' limit 1";
		$user = cg::db($this->db_config_name)->get_row($sql);

		if (empty($user))
		{
			$data["result"] = -1;
		}
		else
		{
			$passhash = md5(md5($password) . $user['salt']);
			if ($passhash != $user['password'])
			{
				$data["result"] = -2;
			}
			else
			{
				$data["result"] = 0;
				$data["email"] = $user['email'];
				$data["uid"] = $user['uid'];
				$data['salt'] = $user['salt'];
			}
		}
		return $data;
	}

	public function uc_pm_send($fromuid, $msgto, $subject, $message, $instantly = 1, $replypmid = 0, $isusername = 0, $type = 0)
	{
		//define('UC_API_FUNC', UC_CONNECT == 'mysql' ? 'uc_api_mysql' : 'uc_api_post');
		//$func = 'uc_api_post';
		if ($instantly)
		{
			$replypmid = @is_numeric($replypmid) ? $replypmid : 0;
			$this->uc_api_post('pm', 'sendpm', array(
				'fromuid' => $fromuid,
				'msgto' => $msgto,
				'subject' => $subject,
				'message' => $message,
				'replypmid' => $replypmid,
				'isusername' => $isusername,
				'type' => $type
			));
		}
		else
		{
			$fromuid = intval($fromuid);
			$subject = rawurlencode($subject);
			$msgto = rawurlencode($msgto);
			$message = rawurlencode($message);
			$replypmid = @is_numeric($replypmid) ? $replypmid : 0;
			$replyadd = $replypmid ? "&pmid=$replypmid&do=reply" : '';
			$apiurl = $this->uc_api_url('pm_client', 'send', "uid=$fromuid", "&msgto=$msgto&subject=$subject&message=$message$replyadd");
			@header("Expires: 0");
			@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
			@header("location: " . $apiurl);
		}
	}

	public function uc_pm_checknew($uid, $more = 0)
	{
		return $this->uc_api_post('pm', 'check_newpm', array(
			'uid' => $uid,
			'more' => $more
		));
	}

	public function uc_api_url($module, $action, $arg = '', $extra = '')
	{
		$url = $this->uc_api . '/index.php?' . $this->uc_api_requestdata($module, $action, $arg, $extra);
		return $url;
	}

	public function uc_api_requestdata($module, $action, $arg = '', $extra = '')
	{
		$input = $this->uc_api_input($arg);
		$post = "m=$module&a=$action&inajax=2&release=" . UC_CLIENT_RELEASE . "&input=$input&appid=" . $this->uc_appid . $extra;
		return $post;
	}

	public function uc_api_input($data)
	{
		$s = urlencode($this->uc_authcode($data . '&agent=' . md5($_SERVER['HTTP_USER_AGENT']) . "&time=" . time(), 'ENCODE', $this->uc_key));
		return $s;
	}

	public function uc_syn_logout()
	{
		$return = $this->uc_api_post('user', 'synlogout', array());
		return $return;
	}

	public function uc_syn_login($uid)
	{
		$uid = intval($uid);
		$return = $this->uc_api_post('user', 'synlogin', array(
			'uid' => $uid
		));
		return $return;
	}

	private function uc_stripslashes($string)
	{
		!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
		if (MAGIC_QUOTES_GPC)
		{
			return stripslashes($string);
		}
		else
		{
			return $string;
		}
	}

	private function uc_api_post($module, $action, $arg = array())
	{
		$s = $sep = '';
		foreach ($arg as $k => $v)
		{
			$k = urlencode($k);
			if (is_array($v))
			{
				$s2 = $sep2 = '';
				foreach ($v as $k2 => $v2)
				{
					$k2 = urlencode($k2);
					$s2 .= "$sep2{$k}[$k2]=" . urlencode($this->uc_stripslashes($v2));
					$sep2 = '&';
				}
				$s .= $sep . $s2;
			}
			else
			{
				$s .= "$sep$k=" . urlencode($this->uc_stripslashes($v));
			}
			$sep = '&';
		}
		$postdata = $this->uc_api_requestdata($module, $action, $s);
		return $this->uc_fopen2($this->uc_api . '/index.php', 500000, $postdata, '', TRUE, $this->uc_ip, 20);
	}

	private function uc_fopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
	{
		$__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
		if ($__times__ > 2)
		{
			return '';
		}
		$url .= (strpos($url, '?') === FALSE ? '?' : '&') . "__times__=$__times__";
		return $this->uc_fopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block);
	}

	public function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
	{
		$ckey_length = 4;

		$key = md5($key ? $key : $this->uc_key);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) .
		 $string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++)
		{
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++)
		{
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if ($operation == 'DECODE')
		{
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
			{
				return substr($result, 26);
			}
			else
			{
				return '';
			}
		}
		else
		{
			return $keyc . str_replace('=', '', base64_encode($result));
		}
	}

	private function uc_fopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE)
	{
		$return = '';
		$matches = parse_url($url);
		!isset($matches['host']) && $matches['host'] = '';
		!isset($matches['path']) && $matches['path'] = '';
		!isset($matches['query']) && $matches['query'] = '';
		!isset($matches['port']) && $matches['port'] = '';
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;
		if ($post)
		{
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= 'Content-Length: ' . strlen($post) . "\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		}
		else
		{
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			//$out .= "Referer: $boardurl\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if (!$fp)
		{
			return '';
		}
		else
		{
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if (!$status['timed_out'])
			{
				while(!feof($fp))
				{
					if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n"))
					{
						break;
					}
				}

				$stop = false;
				while(!feof($fp) && !$stop)
				{
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$return .= $data;
					if ($limit)
					{
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			@fclose($fp);
			return $return;
		}
	}

	public function uc_xml_unserialize(&$xml, $isnormal = FALSE)
	{
		cg::load_class('uc_client/lib/xml.class');
		$params = func_get_args();
		return call_user_func_array('xml_unserialize', $params);
	}

	public function uc_xml_serialize($arr, $htmlon = FALSE, $isnormal = FALSE, $level = 1)
	{
		cg::load_class('uc_client/lib/xml.class');
		$params = func_get_args();
		return call_user_func_array('xml_serialize', $params);
	}
}
