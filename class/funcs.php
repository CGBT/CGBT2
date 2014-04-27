<?php
class funcs
{

	public static function check_peer_connectable($ip, $port)
	{
		if (empty($ip) || $port < 1 || $port > 65535)
		{
			return false;
		}
		if (strpos($ip, ':') !== false)
		{
			$ip = '[' . $ip . ']';
		}
		$fp = @fsockopen($ip, $port, $errno, $errstr, 2);
		if ($fp)
		{
			@fclose($fp);
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function check_ext_loaded($ext)
	{
		$all_exts = get_loaded_extensions();
		if (in_array($ext, $all_exts) !== false)
		{
			return true;
		}
		return false;
	}

	public static function create_hidden_ip($ip)
	{
		$is_ipv6 = strpos($ip, ':') !== false;
		$ip = str_replace('.', ':', $ip);
		$arr_ip = funcs::explode($ip, ':');
		array_pop($arr_ip);
		array_pop($arr_ip);
		$ip = implode(":", $arr_ip) . ':*';
		if (!$is_ipv6)
		{
			$ip = str_replace(':', '.', $ip);
		}
		return $ip;
	}

	public static function recursive_mkdir($dst_dir, $dst_filename)
	{
		$dirs = explode('/', $dst_filename);
		$dir = $dst_dir . '/';
		for($i = 0; $i < count($dirs) - 1; $i++)
		{
			$dir .= $dirs[$i] . '/';
			if (!is_dir($dir))
			{
				$ret = mkdir($dir, 0777);
				if (!$ret)
				{
					return false;
				}
			}
		}
		return true;
	}

	public static function ubb2html($sUBB)
	{
		global $emotPath, $cnum, $arrcode, $bUbb2htmlFunctionInit;
		$sHtml = $sUBB;
		$cnum = 0;
		$arrcode = array();
		$emotPath = '../xheditor_emot/'; //表情根路径



		if (!$bUbb2htmlFunctionInit)
		{

			function saveCodeArea($match)
			{
				global $cnum, $arrcode;
				$cnum++;
				$arrcode[$cnum] = $match[0];
				return "[\tubbcodeplace_" . $cnum . "\t]";
			}
		}
		$sHtml = preg_replace_callback('/\[code\s*(?:=\s*((?:(?!")[\s\S])+?)(?:"[\s\S]*?)?)?\]([\s\S]*?)\[\/code\]/i', 'saveCodeArea', $sHtml);

		//$sHtml = preg_replace("/&/", '&amp;', $sHtml);
		$sHtml = preg_replace("/</", '&lt;', $sHtml);
		$sHtml = preg_replace("/>/", '&gt;', $sHtml);
		$sHtml = preg_replace("/\r?\n/", '<br />', $sHtml);

		$sHtml = preg_replace("/\[(\/?)(b|u|i|s|sup|sub)\]/i", '<$1$2>', $sHtml);
		$sHtml = preg_replace('/\[color\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i', '<span style="color:$1;">', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getSizeName($match)
			{
				$arrSize = array(
					'10px',
					'13px',
					'16px',
					'18px',
					'24px',
					'32px',
					'48px'
				);
				if (preg_match("/^\d+$/", $match[1]))
				{
					$match[1] = isset($arrSize[$match[1] - 1]) ? $arrSize[$match[1] - 1] : '';
				}
				if (empty($match[1]))
				{
					return '';
				}
				return '<span style="font-size:' . $match[1] . ';">';
			}
		}
		$sHtml = preg_replace_callback('/\[size\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i', 'getSizeName', $sHtml);
		$sHtml = preg_replace('/\[font\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i', '<span style="font-family:$1;">', $sHtml);
		$sHtml = preg_replace('/\[back\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/i', '<span style="background-color:$1;">', $sHtml);
		$sHtml = preg_replace("/\[\/(color|size|font|back)\]/i", '</span>', $sHtml);

		for($i = 0; $i < 3; $i++)
			$sHtml = preg_replace('/\[align\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\](((?!\[align(?:\s+[^\]]+)?\])[\s\S])*?)\[\/align\]/', '<p align="$1">$2</p>', $sHtml);
		$sHtml = preg_replace('/\[img\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/i', '<img src="$1" alt="" />', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getImg($match)
			{
				$alt = $match[1];
				$p1 = $match[2];
				$p2 = $match[3];
				$p3 = $match[4];
				$src = $match[5];
				$a = $p3 ? $p3 : (!is_numeric($p1) ? $p1 : '');
				return '<img src="' . $src . '" alt="' . $alt . '"' . (is_numeric($p1) ? ' width="' . $p1 . '"' : '') . (is_numeric($p2) ? ' height="' . $p2 . '"' : '') .
				 ($a ? ' align="' . $a . '"' : '') . ' />';
			}
		}
		$sHtml = preg_replace_callback('/\[img\s*=([^,\]]*)(?:\s*,\s*(\d*%?)\s*,\s*(\d*%?)\s*)?(?:,?\s*(\w+))?\s*\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*)?\s*\[\/img\]/i', 'getImg', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getEmot($match)
			{
				global $emotPath;
				$arr = split(',', $match[1]);
				if (!isset($arr[1]))
				{
					$arr[1] = $arr[0];
					$arr[0] = 'default';
				}
				$path = $emotPath . $arr[0] . '/' . $arr[1] . '.gif';
				return '<img src="' . $path . '" alt="' . $arr[1] . '" />';
			}
		}
		$sHtml = preg_replace_callback('/\[emot\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\/\]/i', 'getEmot', $sHtml);
		$sHtml = preg_replace('/\[url\]\s*(((?!")[\s\S])*?)(?:"[\s\S]*?)?\s*\[\/url\]/i', '<a href="$1">$1</a>', $sHtml);
		$sHtml = preg_replace('/\[url\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]*?)\s*\[\/url\]/i', '<a href="$1">$2</a>', $sHtml);
		$sHtml = preg_replace('/\[email\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/email\]/i', '<a href="mailto:$1">$1</a>', $sHtml);
		$sHtml = preg_replace('/\[email\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]+?)\s*\[\/email\]/i', '<a href="mailto:$1">$2</a>', $sHtml);
		$sHtml = preg_replace("/\[quote\]/i", '<blockquote>', $sHtml);
		$sHtml = preg_replace("/\[\/quote\]/i", '</blockquote>', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getFlash($match)
			{
				$w = $match[1];
				$h = $match[2];
				$url = $match[3];
				if (!$w)
					$w = 480;
				if (!$h)
					$h = 400;
				return '<embed type="application/x-shockwave-flash" src="' . $url . '" wmode="opaque" quality="high" bgcolor="#ffffff" menu="false" play="true" loop="true" width="' .
				 $w . '" height="' . $h . '" />';
			}
		}
		$sHtml = preg_replace_callback('/\[flash\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/flash\]/i', 'getFlash', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getMedia($match)
			{
				$w = $match[1];
				$h = $match[2];
				$play = $match[3];
				$url = $match[4];
				if (!$w)
					$w = 480;
				if (!$h)
					$h = 400;
				return '<embed type="application/x-mplayer2" src="' . $url . '" enablecontextmenu="false" autostart="' . ($play == '1' ? 'true' : 'false') . '" width="' . $w .
				 '" height="' . $h . '" />';
			}
		}
		$sHtml = preg_replace_callback('/\[media\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/media\]/i', 'getMedia', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getTable($match)
			{
				return '<table' . (isset($match[1]) ? ' width="' . $match[1] . '"' : '') . (isset($match[2]) ? ' bgcolor="' . $match[2] . '"' : '') . '>';
			}
		}
		$sHtml = preg_replace_callback('/\[table\s*(?:=(\d{1,4}%?)\s*(?:,\s*([^\]"]+)(?:"[^\]]*?)?)?)?\s*\]/i', 'getTable', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getTR($match)
			{
				return '<tr' . (isset($match[1]) ? ' bgcolor="' . $match[1] . '"' : '') . '>';
			}
		}
		$sHtml = preg_replace_callback('/\[tr\s*(?:=(\s*[^\]"]+))?(?:"[^\]]*?)?\s*\]/i', 'getTR', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getTD($match)
			{
				$col = isset($match[1]) ? $match[1] : 0;
				$row = isset($match[2]) ? $match[2] : 0;
				$w = isset($match[3]) ? $match[3] : null;
				return '<td' . ($col > 1 ? ' colspan="' . $col . '"' : '') . ($row > 1 ? ' rowspan="' . $row . '"' : '') . ($w ? ' width="' . $w . '"' : '') . '>';
			}
		}
		$sHtml = preg_replace_callback("/\[td\s*(?:=\s*(\d{1,2})\s*,\s*(\d{1,2})\s*(?:,\s*(\d{1,4}%?))?)?\s*\]/i", 'getTD', $sHtml);
		$sHtml = preg_replace("/\[\/(table|tr|td)\]/i", '</$1>', $sHtml);
		$sHtml = preg_replace("/\[\*\]((?:(?!\[\*\]|\[\/list\]|\[list\s*(?:=[^\]]+)?\])[\s\S])+)/i", '<li>$1</li>', $sHtml);
		if (!$bUbb2htmlFunctionInit)
		{

			function getUL($match)
			{
				$str = '<ul';
				if (isset($match[1]))
					$str .= ' type="' . $match[1] . '"';
				return $str . '>';
			}
		}
		$sHtml = preg_replace_callback('/\[list\s*(?:=\s*([^\]"]+))?(?:"[^\]]*?)?\s*\]/i', 'getUL', $sHtml);
		$sHtml = preg_replace("/\[\/list\]/i", '</ul>', $sHtml);
		$sHtml = preg_replace("/\[hr\/\]/i", '<hr />', $sHtml);

		for($i = 1; $i <= $cnum; $i++)
			$sHtml = str_replace("[\tubbcodeplace_" . $i . "\t]", $arrcode[$i], $sHtml);

		if (!$bUbb2htmlFunctionInit)
		{

			function fixText($match)
			{
				$text = $match[2];
				$text = preg_replace("/\t/", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $text);
				$text = preg_replace("/ /", '&nbsp;', $text);
				return $match[1] . $text;
			}
		}
		$sHtml = preg_replace_callback('/(^|<\/?\w+(?:\s+[^>]*?)?>)([^<$]+)/i', 'fixText', $sHtml);
		$sHtml = str_replace('[code]', '<pre>', $sHtml);
		$sHtml = str_replace('[/code]', '</pre>', $sHtml);
		$sHtml = str_replace('&amp;', '&', $sHtml);
		$sHtml = str_replace('&quot;', '"', $sHtml);
		$bUbb2htmlFunctionInit = true;
		return $sHtml;
	}

	public static function get_simple_datetime($timestamp)
	{
		if (date("Y-m-d", $timestamp) == date("Y-m-d"))
		{
			return date("H:i", $timestamp);
		}
		elseif (date("Y", $timestamp) == date("Y"))
		{
			return date("m-d H:i", $timestamp);
		}
		else
		{
			return date("Y-m-d H:i", $timestamp);
		}
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

	public static function direct_output($str)
	{
		echo str_pad(' ', 4096);
		echo $str;
		ob_flush();
		flush();
	}

	public static function mksize($bytes)
	{
		$bytes = max(0, $bytes);
		if ($bytes < 1024 * 1024)
		{
			return number_format($bytes / 1024, 0, ".", "") . " kB";
		}
		elseif ($bytes < 1024 * 1048576)
		{
			return number_format($bytes / 1048576, 1, ".", "") . " MB";
		}
		elseif ($bytes < 1024 * 1073741824)
		{
			return number_format($bytes / 1073741824, 2, ".", "") . " GB";
		}
		elseif ($bytes < 1024 * 1024 * 1073741824)
		{
			return number_format($bytes / 1099511627776, 3, ".", "") . " TB";
		}
		else
		{
			return number_format($bytes / 1125899906842624, 2, ".", "") . " PB";
		}
	}

	public static function guid()
	{
		if (function_exists('com_create_guid'))
		{
			$uuid = com_create_guid();
			return substr($uuid, 1, -1);
		}
		else
		{
			mt_srand((double)microtime() * 10000);
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);
			$uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
			return $uuid;
		}
	}

	public static function explode($s, $delimiter = '')
	{
		if (empty($s))
		{
			return array();
		}
		$s = str_replace(array(
			';',
			"\r",
			"\n"
		), ',', $s);
		if ($delimiter != '')
		{
			$s = str_replace($delimiter, ",", $s);
		}
		$s = preg_replace('/,+/i', ',', $s);
		return explode(',', $s);
	}

	/**
	 * 编码转换,gbk -> utf8
	 */
	public static function gbk2utf8($s)
	{
		return self::iconv_array("gbk", "utf-8", $s);
	}

	/**
	 * 编码转换,utf8 -> gbk
	 */
	public static function utf82gbk($s)
	{
		return self::iconv_array("utf-8", "gbk", $s);
	}

	/**
	 * 对数组进行编码转换
	 *
	 * @param strint $in_charset  输入编码
	 * @param string $out_charset 输出编码
	 * @param array  $arr         输入数组
	 * @return array              返回数组
	 */
	public static function iconv_array($in_charset, $out_charset, $arr)
	{
		if (strtolower($in_charset) == "utf8")
		{
			$in_charset = "UTF-8";
		}
		if (strtolower($out_charset) == "utf-8" || strtolower($out_charset) == 'utf8')
		{
			$out_charset = "UTF-8";
		}
		if (is_array($arr))
		{
			foreach ($arr as $key => $value)
			{
				$arr[$key] = self::iconv_array($in_charset, $out_charset, $value);
			}
		}
		else
		{
			if (!is_numeric($arr))
			{
				$arr = iconv($in_charset, $out_charset, $arr);
			}
		}
		return $arr;
	}

	public static function full2half($str)
	{
		$chars = Array(
			'【' => '[',
			'】' => ']',
			'０' => '0',
			'１' => '1',
			'２' => '2',
			'３' => '3',
			'４' => '4',
			'５' => '5',
			'６' => '6',
			'７' => '7',
			'８' => '8',
			'９' => '9',
			'Ａ' => 'A',
			'Ｂ' => 'B',
			'Ｃ' => 'C',
			'Ｄ' => 'D',
			'Ｅ' => 'E',
			'Ｆ' => 'F',
			'Ｇ' => 'G',
			'Ｈ' => 'H',
			'Ｉ' => 'I',
			'Ｊ' => 'J',
			'Ｋ' => 'K',
			'Ｌ' => 'L',
			'Ｍ' => 'M',
			'Ｎ' => 'N',
			'Ｏ' => 'O',
			'Ｐ' => 'P',
			'Ｑ' => 'Q',
			'Ｒ' => 'R',
			'Ｓ' => 'S',
			'Ｔ' => 'T',
			'Ｕ' => 'U',
			'Ｖ' => 'V',
			'Ｗ' => 'W',
			'Ｘ' => 'X',
			'Ｙ' => 'Y',
			'Ｚ' => 'Z',
			'ａ' => 'a',
			'ｂ' => 'b',
			'ｃ' => 'c',
			'ｄ' => 'd',
			'ｅ' => 'e',
			'ｆ' => 'f',
			'ｇ' => 'g',
			'ｈ' => 'h',
			'ｉ' => 'i',
			'ｊ' => 'j',
			'ｋ' => 'k',
			'ｌ' => 'l',
			'ｍ' => 'm',
			'ｎ' => 'n',
			'ｏ' => 'o',
			'ｐ' => 'p',
			'ｑ' => 'q',
			'ｒ' => 'r',
			'ｓ' => 's',
			'ｔ' => 't',
			'ｕ' => 'u',
			'ｖ' => 'v',
			'ｗ' => 'w',
			'ｘ' => 'x',
			'ｙ' => 'y',
			'ｚ' => 'z',
			'：' => ':',
			'、' => '/',
			'／' => '/',
			'，' => ',',
			'～' => '~',
			'。' => '.',
			'（' => '(',
			'）' => ')',
			'＋' => '+',
			'　' => ' ',
			'—' => '-',
			'•' => '·'
		);
		return str_replace(array_keys($chars), $chars, $str);
	}
}