<?php
class imdb_model extends base_model
{

	public function get_by_id($imdb_id)
	{
		$url = "http://imdbapi.org/?id=$imdb_id&lang=zh-CN";
		cg::load_core('cg_http');
		$http = new cg_http($url);
		return $http->send_request();

	}

	/**
	 *
	 * @return imdb_model
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
}
