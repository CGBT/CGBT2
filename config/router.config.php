<?php
$router = array();
$router['*']['/'] = array(
	'index_controller',
	'index'
);
$router['*']['/error/404'] = array(
	'error_controller',
	'error404'
);
$router['*']['/admin'] = array(
	'admin/admin_index_controller',
	'index'
);

$router['*']['/upload/:category'] = array(
	'upload_controller',
	'index'
);

$router['*']['/search/demo'] = array(
	'search_controller',
	'demo'
);

$router['*']['/browse.php'] = array(
	'search_controller',
	'index'
);

$router['*']['/search/:search_params'] = array(
	'search_controller',
	'index'
);

$router['*']['/search/:category/:search_params'] = array(
	'search_controller',
	'index'
);

$router['*']['/audit/:search_params'] = array(
	'audit_controller',
	'index'
);

$router['*']['/audit/:category/:search_params'] = array(
	'audit_controller',
	'index'
);

$router['*']['/rss/:passkey'] = array(
	'rss_controller',
	'index'
);
$router['*']['/rss/:search_params/:passkey'] = array(
	'rss_controller',
	'index'
);

$router['*']['/rss/:category/:search_params/:passkey'] = array(
	'rss_controller',
	'index'
);

$router['*']['/announce.php'] = array(
	'announce_controller',
	'index'
);

$router['*']['/api/uc.php'] = array(
	'api/api_uc_controller',
	'index'
);
$router['*']['/api/at.php'] = array(
	'api/api_at_controller',
	'index'
);
$router['*']['/api/check_username.php'] = array(
	'api/api_user_controller',
	'index'
);

$router['*']['/user/:uid'] = array(
	'user_controller',
	'details',
	'match' => array(
		'uid' => '/^\d+$/'
	)
);

$router['*']['/user/:uid/:action'] = array(
	'user_controller',
	'other_details',
	'match' => array(
		'uid' => '/^\d+$/'
	)
);

$router['*']['/user/:uid/:action/:page'] = array(
	'user_controller',
	'other_details',
	'match' => array(
		'uid' => '/^\d+$/'
	)
);

$router['*']['/user/:uid/details'] = array(
	'user_controller',
	'details'
);

$router['*']['/torrents/:tid'] = array(
	'torrents_controller',
	'index'
);

$router['*']['/torrents/:tid/:mod'] = array(
	'torrents_controller',
	'index'
);

$router['*']['/subtitles/:page'] = array(
	'subtitles_controller',
	'index'
);

$router['*']['/subtitles/:sid/:mod'] = array(
	'subtitles_controller',
	'index'
);

$router['*']['/userbar/:username/:bar_type'] = array(
	'userbar_controller',
	'index'
);
$router['*']['/list/sitelog/:page'] = array(
	'list_controller',
	'sitelog'
);
$router['*']['/list/mysitelog/:page'] = array(
	'list_controller',
	'mysitelog'
);

$router['*']['/book/search/:search_params'] = array(
	'book_controller',
	'search'
);

$router['*']['/book/:bookid'] = array(
	'book_controller',
	'other',
	'match' => array(
		'bookid' => '/^\d+$/'
	)
);

$router['*']['/book/:bookid/:mod'] = array(
	'book_controller',
	'other',
	'match' => array(
		'bookid' => '/^\d+$/'
	)
);


$router['*']['/softsite/search/:search_params'] = array(
	'softsite_controller',
	'search'
);

$router['*']['/softsite/:softsiteid'] = array(
	'softsite_controller',
	'other',
	'match' => array(
		'softsiteid' => '/^\d+$/'
	)
);

$router['*']['/softsite/:softsiteid/:mod'] = array(
	'softsite_controller',
	'other',
	'match' => array(
		'softsiteid' => '/^\d+$/'
	)
);

$router['*']['/top/users/:type'] = array(
	'top_controller',
	'users'
);
$router['*']['/top/torrents/:type'] = array(
	'top_controller',
	'torrents'
);
$router['*']['/top/school/:type'] = array(
	'top_controller',
	'school'
);
$router['*']['/users/:username'] = array(
	'users_controller',
	'index'
);
$router['*']['/chat/room/:room'] = array(
	'chat_controller',
	'room'
);
return $router;