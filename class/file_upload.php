<?php
class file_upload
{
	private $allowed_ext = array(
		'image' => 'jpg,png,gif,jpeg',
		'torrent' => 'torrent',
		'subtitles' => 'srt,idx,sub,txt,ssa,ass,rar,zip,7z',
		'nfos' => 'nfo',
		'softsite' => 'zip,rar,7z,dmg'
	);
	private $config = array(
		'type' => 'image',
		'max_size' => 0,
		'save_path' => 'attachments',
		'parent_id' => 0
	);
	public $errors = array(
		0 => '文件上传成功',
		1 => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。 ',
		2 => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。 ',
		3 => '文件只有部分被上传',
		4 => '没有文件被上传 ',
		5 => '',
		6 => '找不到临时文件夹',
		7 => '文件写入失败',
		8 => '文件扩展名错误',
		9 => '文件大小超出限制',
		10 => '没有权限',
		11 => 'copy失败'
	);
	public $errno = 0;
	public $saved_file = array();
	private $ext = '';
	private $file_md5 = '';

	/**
	 *
	 * @param  array $config
	 */
	public function __construct($config)
	{
		if (!empty($config))
		{
			$this->config = array_merge($this->config, $config);
		}
	}

	public function save($file_field_name)
	{
		if ($this->check_writeable() === false)
		{
			$this->error(10);
			return false;
		}
		if (isset($_SERVER['HTTP_CONTENT_DISPOSITION']) && preg_match('/attachment;\s+name=".+?";\s+filename="(.+?)"/i', $_SERVER['HTTP_CONTENT_DISPOSITION'], $matches))
		{
			$filedata = file_get_contents("php://input");
			$filename = urldecode($matches[1]);
			return $this->save_one_html5($filedata, $filename);
		}

		if (!isset($_FILES[$file_field_name]))
		{
			$this->error(4);
			return false;
		}

		if (isset($_FILES[$file_field_name][0]['name']))
		{
			foreach ($_FILES[$file_field_name] as $file)
			{
				//if ($_FILES[$file_field_name][0]['error'])
				if ($this->save_one($file) === false)
				{
					return false;
					break;
				}
			}
		}
		else
		{
			return $this->save_one($_FILES[$file_field_name]);
		}
	}

	private function check_writeable()
	{
		if (!is_writeable($this->config['save_path']))
		{
			return false;
		}
	}

	private function save_one_html5($filedata, $filename)
	{
		$this->ext = $this->check_ext($filename);
		if ($this->ext === false)
		{
			$this->error(8);
			return false;
		}
		$filesize = strlen($filedata);
		if ($this->check_size($filesize) === false)
		{
			$this->error(9);
			return false;
		}

		$dst_filename = $this->get_dst_filename($filedata, true);
		$ret = $this->recursive_mkdir($dst_filename);
		if (!$ret)
		{
			$this->error(10);
			return false;
		}
		//echo $this->config['save_path'] . '/' . $dst_filename;
		$ret = file_put_contents($this->config['save_path'] . '/' . $dst_filename, $filedata);
		if (!$ret)
		{
			$this->error(11);
			return false;
		}
		$this->saved_file[] = array(
			'type' => 'html5data',
			'size' => $filesize,
			'old_name' => $filename,
			'new_name' => $dst_filename,
			'file_md5' => $this->file_md5
		);
		return true;
	}

	private function save_one($file)
	{
		$this->ext = $this->check_ext($file['name']);
		if ($this->ext === false)
		{
			$this->error(8);
			return false;
		}

		if ($this->check_size($file['size']) === false)
		{
			$this->error(9);
			return false;
		}

		if (!file_exists($file['tmp_name']))
		{
			$this->error(4);
			return false;
		}

		$dst_filename = $this->get_dst_filename($file, false);
		$ret = $this->recursive_mkdir($dst_filename);
		if (!$ret)
		{
			$this->error(10);
			return false;
		}
		//echo $this->config['save_path'] . '/' . $dst_filename;
		$ret = copy($file['tmp_name'], $this->config['save_path'] . '/' . $dst_filename);
		if (!$ret)
		{
			$this->error(11);
			return false;
		}
		$this->saved_file[] = array(
			'type' => $file['type'],
			'size' => $file['size'],
			'old_name' => $file['name'],
			'new_name' => $dst_filename,
			'file_md5' => $this->file_md5
		);
		return true;
	}

	private function get_dst_filename($file, $is_html5 = false)
	{
		$dst_filename = '';
		if ($is_html5)
		{
			$md5 = md5($file);
		}
		else
		{
			$md5 = md5_file($file['tmp_name']);
		}
		$this->file_md5 = $md5;
		if ($this->config['type'] == 'image')
		{
			$dir1 = $md5{0} . $md5{1};
			$dir2 = $md5{2} . $md5{3};
			$dst_filename = $dir1 . '/' . $dir2 . '/' . $md5 . '.' . $this->ext;
		}
		else
		{
			$dir1 = date("Ym");
			$dir2 = date("d");
			$postfix = substr(md5(microtime() . '_' . mt_rand(0, 10000)), 0, 8);
			$parent_id = $this->config['parent_id'];
			if ($parent_id > 0)
			{
				$dst_filename = $dir1 . '/' . $dir2 . '/' . $parent_id . '_' . $postfix . '.' . $this->ext;
			}
			else
			{
				$dst_filename = $dir1 . '/' . $dir2 . '/' . $md5 . '.' . $this->ext;
			}
		}
		return $dst_filename;
	}

	private function recursive_mkdir($dst_filename)
	{
		$dirs = explode('/', $dst_filename);
		$dir = $this->config['save_path'] . '/';
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

	private function random($length, $numeric = 0)
	{
		if ($numeric)
		{
			$hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
		}
		else
		{
			$hash = '';
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++)
			{
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	private function error($errno)
	{
		$this->errno = $errno;
	}

	private function check_size($size)
	{
		if ($size == 0)
		{
			return false;
		}
		if ($this->config['max_size'] > 0 && $size > $this->config['max_size'])
		{
			return false;
		}
		return true;
	}

	private function check_ext($filename)
	{
		$dict_ext = isset($this->allowed_ext[$this->config['type']]) ? $this->allowed_ext[$this->config['type']] : $this->allowed_ext['image'];
		$ext = trim(substr($filename, strrpos($filename, '.') + 1));
		if (empty($ext) || stripos($dict_ext, $ext) === false)
		{
			return false;
		}
		return $ext;
	}
}
