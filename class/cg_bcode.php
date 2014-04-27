<?php
if (!function_exists('bencode'))
{

	function bencode($var)
	{
		return cg_bcode::bencode($var);
	}
}

if (!function_exists('bdecode'))
{

	function bdecode($s)
	{
		return cg_bcode::bdecode($s);
	}
}
class cg_bcode
{

	/**
	 * bittorrent bdecode
	 * @param  string  $s     string to be decoded
	 * @param  integer $pos   start position
	 * @return array
	 */
	public static function bdecode($s)
	{
		$pos = 0;
		return self::bdecode_internal($s, $pos);
	}

	private static function bdecode_internal($s, &$pos = 0)
	{
		switch ($s[$pos])
		{
			case 'd':
				$ret = array();
				$pos++;
				while($s[$pos] != 'e')
				{
					$key = self::bdecode_internal($s, $pos);
					if ($key !== false)
					{
						$val = self::bdecode_internal($s, $pos);
						if ($val !== false)
						{
							$ret[$key] = $val;
						}
						else
						{
							$ret[$key] = 0;
						}
					}
					else
					{
						return false;
					}
				}
				$pos++;
				return $ret;
			case 'l':
				$ret = array();
				$pos++;
				while($s[$pos] != 'e')
				{
					$val = self::bdecode_internal($s, $pos);
					if ($val === false)
					{
						$val == 0;
					}
					$ret[] = $val;
				}
				$pos++;
				return $ret;
			case 'i':
				$pos++;
				$i = '';
				while($s[$pos] != 'e')
				{
					$i .= $s[$pos];
					$pos++;
				}
				$pos++;
				if (floatval($i) > pow(2, 64))
				{
					return false;
				}
				if (floatval($i) > pow(2, 31) - 1)
				{
					return floatval($i);
				}
				else
				{
					return intval($i);
				}
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case '.':
			case '-':
				$length_int = strpos($s, ':', $pos) - $pos;
				$str_lenth = intval(substr($s, $pos, $length_int));
				$pos += $length_int + 1;
				$str = substr($s, $pos, $str_lenth);
				$pos += $str_lenth;
				return $str;
			default:
				return false; //非种子文件长时间执行会造成服务器负载问题
				$pos++;
				return false;
		}

		return false;
	}

	/**
	 * bittorrent bencode
	 *
	 * @param  mixed   $var        the variable to be encoded
	 * @param  bool    $is_array   the variable is array or not
	 * @return string
	 */
	public static function bencode($var, $is_array = false)
	{
		if ($is_array || is_array($var))
		{
			$is_dict = false;
			$keys = array_keys($var);
			foreach ($keys as $k => $v)
			{
				if ($k !== $v)
				{
					$is_dict = true;
				}
			}

			$s = $is_dict ? 'd' : 'l';
			if ($is_dict)
			{
				//ksort($var, SORT_STRING);
			}
			else
			{
				//ksort($var, SORT_NUMERIC);
			}
			foreach ($var as $k => $v)
			{
				if ($is_dict)
				{
					$s .= strlen(strval($k)) . ':' . $k;
				}
				if (is_string($v))
				{
					$s .= strlen($v) . ':' . $v;
				}
				elseif (is_integer($v))
				{
					$s .= 'i' . $v . 'e';
				}
				elseif (is_array($v))
				{
					$s .= self::bencode($v, true);
				}
				elseif (is_float($v))
				{
					$s .= 'i' . sprintf('%.0f', round($v)) . 'e';
				}
				elseif (is_bool($v))
				{
					$s .= $v ? 'i1e' : 'i0e';
				}
			}
			return $s . 'e';
		}
		elseif (is_float($var))
		{
			return 'i' . sprintf('%.0f', round($var)) . 'e';
		}
		elseif (is_integer($var))
		{
			return 'i' . $var . 'e';
		}
		elseif (is_string($var))
		{
			return strlen($var) . ':' . $var;
		}
		elseif (is_bool($var))
		{
			return $var ? 'i1e' : 'i0e';
		}
		return false;
	}

	/**
	 * bdecode file
	 * @param string $file
	 */
	public static function bdecode_file($file)
	{
		if (file_exists($file))
		{
			return self::bdecode(file_get_contents($file));
		}
		return false;
	}
}