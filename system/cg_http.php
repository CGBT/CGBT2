<?php
class cg_http
{
	private $agent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36';
	private $post_fields = array();
	private $post_files = array();
	private $headers = array();
	private $cookies = array();
	private $timeout = 15;
	private $curl;
	private $url = '';
	private $has_file = false;

	public function __construct($url = '')
	{
		$this->curl = curl_init();
		if ($url != '')
		{
			$this->set_url($url);
		}
	}

	public function set_url($url)
	{
		$this->url = $url;
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		$this->set_agent($this->agent);
	}

	public function send_request()
	{
		if ($this->url == '')
		{
			die('url is empty');
		}
		if (is_string($this->cookies))
		{
			curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookies);
		}
		elseif (is_array($this->cookies))
		{
			if (count($this->cookies) > 0)
			{
				curl_setopt($this->curl, CURLOPT_COOKIE, implode(';', $this->cookies));
			}
		}

		if (count($this->post_fields) > 0)
		{
			curl_setopt($this->curl, CURLOPT_POST, true);
			if ($this->has_file)
			{
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->post_fields);
			}
			else
			{
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($this->post_fields));
			}
		}
		if (count($this->headers) > 0)
		{
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
		}
		$result = curl_exec($this->curl);
		$error = curl_error($this->curl);
		if (!empty($error))
		{
			//return $error;
		}
		curl_close($this->curl);
		return $result;
	}

	public function add_fields($key, $value)
	{
		$this->post_fields[$key] = $value;
	}

	public function add_files($key, $filename)
	{
		$this->has_file = true;
		$this->post_fields[$key] = "@$filename";
	}

	public function add_cookie($key, $value)
	{
		$this->cookies[] = "$key=$value";
	}

	public function set_cookie($cookie)
	{
		$this->cookies = $cookie;
	}

	public function add_header($key, $value)
	{
		$this->headers[] = "$key: $value";
	}

	public function set_agent($agent)
	{
		curl_setopt($this->curl, CURLOPT_USERAGENT, $agent);
	}

	public function set_basic_auth($username, $password)
	{
		curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
	}

	public function set_proxy($host, $port, $username = '', $password = '', $type = 'http')
	{
		if ($type == 'http')
		{
			$proxy = "http://$host:$port";
			curl_setopt($this->curl, CURLOPT_PROXY, $proxy);
			if (!empty($username))
			{
				curl_setopt($this->curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				//curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
			}
		}
		elseif ($type == 'socks5')
		{
			curl_setopt($this->curl, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($this->curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			curl_setopt($this->curl, CURLOPT_PROXY, $host);
			curl_setopt($this->curl, CURLOPT_PROXYPORT, $port);
			if (!empty($username))
			{
				curl_setopt($this->curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
			}
		}
	}
}