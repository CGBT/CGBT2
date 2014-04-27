DROP TABLE IF EXISTS `attachments`;


CREATE TABLE `attachments` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `torrent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `price` smallint(6) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  `filetype` varchar(50) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment` varchar(100) NOT NULL DEFAULT '',
  `downloads` mediumint(8) NOT NULL DEFAULT '0',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pic_s100` varchar(50) NOT NULL DEFAULT '' COMMENT '缩略图',
  `pic_s100_size` int(11) NOT NULL DEFAULT '0' COMMENT '缩略图大小',
  PRIMARY KEY (`aid`),
  KEY `tid` (`torrent_id`),
  KEY `pid` (`aid`),
  KEY `uid` (`uid`),
  KEY `is_image` (`isimage`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_agentinfo`;


CREATE TABLE `cgbt_agentinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent` (`agent`)
) ENGINE=MyISAM AUTO_INCREMENT=448 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cgbt_category`;

CREATE TABLE `cgbt_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `properties` varchar(100) NOT NULL DEFAULT '',
  `input_items` varchar(100) NOT NULL DEFAULT '',
  `admins` varchar(1000) NOT NULL DEFAULT '',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `forums_fid` int(11) NOT NULL,
  `hot_keywords` text NOT NULL,
  `hot_keywords_count` tinyint(4) NOT NULL,
  `app` varchar(10) NOT NULL DEFAULT 'torrents',
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_en` (`name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_category_options`;


CREATE TABLE `cgbt_category_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT '',
  `orderid` tinyint(4) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL DEFAULT '',
  `variable_search` varchar(5) NOT NULL,
  `variable` varchar(50) NOT NULL,
  `bind_field` varchar(10) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `options` text NOT NULL,
  `insearch_item` tinyint(4) NOT NULL DEFAULT '0',
  `insearch_keyword` tinyint(4) NOT NULL,
  `indetail` tinyint(4) NOT NULL DEFAULT '0',
  `intitle` tinyint(4) NOT NULL DEFAULT '0',
  `intag` tinyint(4) NOT NULL DEFAULT '0',
  `tip` varchar(255) NOT NULL,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_chat`;


CREATE TABLE `cgbt_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `user_title` char(20) NOT NULL DEFAULT '',
  `forums_uid` int(11) NOT NULL,
  `groupid` tinyint(4) NOT NULL DEFAULT '0',
  `txt` text NOT NULL,
  `ip` varchar(40) NOT NULL DEFAULT '',
  `room` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `reply_uid` int(11) NOT NULL,
  `reply_username` varchar(30) NOT NULL,
  `parse_ubb` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `room` (`room`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=6837 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_favorite`;

CREATE TABLE `cgbt_favorite` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_tid` (`uid`,`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=114327 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_fetched_images`;

CREATE TABLE `cgbt_fetched_images` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `images_id` bigint(8) unsigned NOT NULL DEFAULT '0',
  `file_md5` char(32) NOT NULL DEFAULT '',
  `url_md5` char(32) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url_md5` (`url_md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_images`;

CREATE TABLE `cgbt_images` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `file_md5` char(32) NOT NULL DEFAULT '',
  `oldpath` varchar(100) NOT NULL DEFAULT '',
  `newpath` varchar(100) NOT NULL,
  `views` int(8) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `file_md5` (`file_md5`)
) ENGINE=MyISAM AUTO_INCREMENT=315692 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_images_url_queue`;


CREATE TABLE `cgbt_images_url_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `url_md5` char(32) NOT NULL,
  `file_md5` char(32) NOT NULL,
  `fetched` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0,1,2',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `last_fetch_time` int(11) NOT NULL DEFAULT '0',
  `fetched_times` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `path` varchar(50) NOT NULL DEFAULT '',
  `replaced` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url_md5` (`url_md5`),
  KEY `fetched` (`fetched`,`fetched_times`) USING BTREE,
  KEY `replaced` (`replaced`)
) ENGINE=MyISAM AUTO_INCREMENT=238472 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_invite`;


CREATE TABLE `cgbt_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` char(30) NOT NULL,
  `code` char(32) NOT NULL,
  `used_username` char(30) NOT NULL,
  `used_uid` int(11) NOT NULL,
  `expiretime` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `updatetime` int(11) NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `award` int(11) NOT NULL DEFAULT '0',
  `sent` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `query` (`uid`,`used_uid`,`id`) USING BTREE,
  KEY `used_username` (`used_username`) USING BTREE,
  KEY `used_uid` (`used_uid`) USING BTREE,
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_actions`;


CREATE TABLE `cgbt_logs_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `createtime` int(11) NOT NULL,
  `is_moderator` tinyint(4) NOT NULL,
  `is_admin` tinyint(4) NOT NULL,
  `tid` int(11) NOT NULL DEFAULT '0',
  `action` varchar(30) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_browse`;


CREATE TABLE `cgbt_logs_browse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_completed`;


CREATE TABLE `cgbt_logs_completed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_credits`;


CREATE TABLE `cgbt_logs_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `createtime` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `field` varchar(30) NOT NULL,
  `details` text NOT NULL,
  `operator` int(11) NOT NULL,
  `operator_username` varchar(30) NOT NULL,
  `ip` char(40) NOT NULL,
  `action` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_cron`;


CREATE TABLE `cgbt_logs_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `endtime` int(11) NOT NULL,
  `controller` varchar(50) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `exec_result` varchar(255) NOT NULL DEFAULT '',
  `force` tinyint(4) NOT NULL,
  `interval` int(11) NOT NULL,
  `real_exec` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_day_stat`;


CREATE TABLE `cgbt_logs_day_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `uploaded` bigint(20) NOT NULL,
  `uploaded2` bigint(20) NOT NULL,
  `downloaded` bigint(20) NOT NULL,
  `downloaded2` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_uploaded` (`date`,`uploaded`) USING BTREE,
  KEY `date_downloaded` (`date`,`downloaded`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_daystats`;


CREATE TABLE `cgbt_logs_daystats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thedate` date NOT NULL,
  `users_count` int(11) NOT NULL DEFAULT '0',
  `search_count` int(11) NOT NULL,
  `completed_count` int(11) NOT NULL,
  `browse_count` int(11) NOT NULL,
  `download_count` int(11) NOT NULL,
  `login_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_debug`;


CREATE TABLE `cgbt_logs_debug` (
  `createtime` int(11) NOT NULL DEFAULT '0',
  `logtype` varchar(30) NOT NULL DEFAULT '',
  `txt` text NOT NULL,
  KEY `createtime` (`createtime`),
  KEY `logtype` (`logtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_develop`;


CREATE TABLE `cgbt_logs_develop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '2',
  `enabled` tinyint(4) NOT NULL DEFAULT '0',
  `cdatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_download`;


CREATE TABLE `cgbt_logs_download` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  KEY `c` (`createtime`,`uid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `cgbt_logs_hour_stat`;


CREATE TABLE `cgbt_logs_hour_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `hour` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `uploaded` bigint(20) NOT NULL,
  `uploaded2` bigint(20) NOT NULL,
  `downloaded` bigint(20) NOT NULL,
  `downloaded2` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_hour` (`date`,`hour`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_login`;


CREATE TABLE `cgbt_logs_login` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(40) NOT NULL DEFAULT '',
  KEY `uid` (`uid`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_loginfail`;


CREATE TABLE `cgbt_logs_loginfail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(40) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_memcached`;


CREATE TABLE `cgbt_logs_memcached` (
  `createtime` int(11) NOT NULL DEFAULT '0',
  `total_size` varchar(50) NOT NULL DEFAULT '',
  `used_size` varchar(50) NOT NULL DEFAULT '',
  `bytes_read_speed` varchar(50) NOT NULL DEFAULT '',
  `bytes_written_speed` varchar(50) NOT NULL DEFAULT '',
  `increase_cmd_get_speed` int(11) NOT NULL DEFAULT '0',
  `increase_cmd_set_speed` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_modgroup`;


CREATE TABLE `cgbt_logs_modgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `operator` varchar(30) NOT NULL DEFAULT '',
  `operator_uid` int(11) NOT NULL DEFAULT '0',
  `old_groupid` int(11) NOT NULL DEFAULT '0',
  `new_groupid` int(11) NOT NULL DEFAULT '0',
  `old_groupname` varchar(30) NOT NULL DEFAULT '',
  `new_groupname` varchar(30) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1116 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_passkey`;


CREATE TABLE `cgbt_logs_passkey` (
  `createtime` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `passkey` char(32) NOT NULL DEFAULT '',
  KEY `passkey` (`passkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_rate`;


CREATE TABLE `cgbt_logs_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20960 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_search`;


CREATE TABLE `cgbt_logs_search` (
  `keyword` varchar(15) NOT NULL DEFAULT '',
  `category` tinyint(4) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  KEY `createtime` (`createtime`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_sitestat`;


CREATE TABLE `cgbt_logs_sitestat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL,
  `date` char(10) CHARACTER SET latin1 NOT NULL,
  `total_user_count` int(11) NOT NULL,
  `active_torrent_count` int(11) NOT NULL,
  `torrent_count` int(11) NOT NULL,
  `leecher_count` int(11) NOT NULL,
  `seeder_count` int(11) NOT NULL,
  `peer_user_count` int(11) NOT NULL,
  `leech_peer_count` int(11) NOT NULL,
  `seed_peer_count` int(11) NOT NULL,
  `total_peer_count` int(11) NOT NULL,
  `active_size` bigint(20) NOT NULL,
  `total_size` bigint(20) NOT NULL,
  `online_user` int(11) NOT NULL,
  `online_guest` int(11) NOT NULL,
  `max_user_time` datetime NOT NULL,
  `max_online_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131854 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_logs_work`;


CREATE TABLE `cgbt_logs_work` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` char(30) NOT NULL,
  `type` char(20) NOT NULL DEFAULT '',
  `tid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL,
  `duration` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_peers`;


CREATE TABLE `cgbt_peers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `peer_id` varchar(60) NOT NULL DEFAULT '',
  `ip` varchar(40) NOT NULL DEFAULT '',
  `ipv6` varchar(40) NOT NULL DEFAULT '0',
  `port` int(11) NOT NULL DEFAULT '0',
  `uploaded` bigint(20) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `is_seeder` tinyint(4) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `last_event` varchar(10) NOT NULL,
  `last_action` int(11) NOT NULL DEFAULT '0',
  `connectable` tinyint(4) NOT NULL DEFAULT '1',
  `agent` varchar(60) NOT NULL DEFAULT '',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `completed_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tid_peer_id` (`tid`,`peer_id`),
  KEY `uid` (`uid`),
  KEY `tid` (`tid`),
  KEY `last_action` (`last_action`),
  KEY `is_seeder` (`is_seeder`),
  KEY `ipv6` (`ipv6`,`port`) USING BTREE,
  KEY `ip` (`ip`,`ipv6`,`port`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_peers_connectable`;


CREATE TABLE `cgbt_peers_connectable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(40) NOT NULL,
  `ipv6` varchar(40) NOT NULL DEFAULT '',
  `port` int(11) NOT NULL,
  `connectable` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '-1,未检查，0 两个都不可连接，1 ip可连接 2ipv6可连接 3两个都可连接',
  `checktime` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`,`ipv6`,`port`) USING BTREE,
  KEY `checktime` (`checktime`),
  KEY `connectable` (`connectable`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_privileges`;


CREATE TABLE `cgbt_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_front` tinyint(10) NOT NULL DEFAULT '1' COMMENT '前台权限or后台权限',
  `name` varchar(30) NOT NULL DEFAULT '',
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `orderid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT '',
  `options` text NOT NULL,
  `default_value` varchar(50) NOT NULL,
  `vip_default_value` varchar(50) NOT NULL,
  `admin_default_value` varchar(50) NOT NULL DEFAULT '',
  `tip` varchar(250) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `can_ban` tinyint(4) NOT NULL DEFAULT '0',
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_search_keywords`;


CREATE TABLE `cgbt_search_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(15) NOT NULL DEFAULT '',
  `pinyin` varchar(50) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pinyini` (`pinyin`,`count`),
  KEY `keyword` (`keyword`,`count`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_setting`;


CREATE TABLE `cgbt_setting` (
  `skey` char(50) NOT NULL,
  `svalue` text NOT NULL,
  PRIMARY KEY (`skey`),
  UNIQUE KEY `skey` (`skey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_soft`;


CREATE TABLE `cgbt_soft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `memo` text NOT NULL,
  `download` int(11) NOT NULL DEFAULT '0',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `link2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cgbt_torrents`;

CREATE TABLE `cgbt_torrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '审核/删除状态,-2删除,-1审核不通过,0未审核,1审核通过',
  `info_hash` char(40) NOT NULL DEFAULT '' COMMENT '种子hash',
  `bt_info_hash` char(40) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_title` varchar(20) NOT NULL,
  `save_as` varchar(200) NOT NULL DEFAULT '' COMMENT '下载的文件名',
  `filename` varchar(100) NOT NULL DEFAULT '' COMMENT '存储的文件名',
  `files` int(11) NOT NULL DEFAULT '0' COMMENT '文件数量',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '资源大小',
  `category` varchar(50) NOT NULL DEFAULT '' COMMENT '分类',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '中文名称',
  `name_en` varchar(100) NOT NULL DEFAULT '' COMMENT '英文名称',
  `year` int(11) NOT NULL DEFAULT '0' COMMENT '出品年份',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '出品日期',
  `district` varchar(20) NOT NULL DEFAULT '' COMMENT '国家/地区',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '剧情类型',
  `format` varchar(50) NOT NULL DEFAULT '' COMMENT '格式',
  `subtitle` varchar(50) NOT NULL DEFAULT '' COMMENT '字幕',
  `actor` varchar(50) NOT NULL DEFAULT '' COMMENT '主演，演员，歌手等',
  `memo` varchar(255) NOT NULL DEFAULT '' COMMENT '其他备注',
  `text1` varchar(50) NOT NULL DEFAULT '',
  `text2` varchar(50) NOT NULL DEFAULT '',
  `opt1` varchar(20) NOT NULL DEFAULT '',
  `opt2` varchar(20) NOT NULL DEFAULT '',
  `istop` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `isrecommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `isfree` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否免费',
  `ishalf` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否50%下载',
  `is30p` tinyint(4) NOT NULL DEFAULT '0',
  `is2x` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否两倍上传',
  `iscollection` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否合集',
  `is0day` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否0day',
  `isft` tinyint(4) NOT NULL DEFAULT '0' COMMENT '禁转',
  `ishot` tinyint(4) NOT NULL DEFAULT '0',
  `anonymous` tinyint(4) NOT NULL DEFAULT '0',
  `price` tinyint(4) NOT NULL DEFAULT '0',
  `imdb` varchar(10) NOT NULL DEFAULT '' COMMENT 'imdb号',
  `forums_tid` int(11) NOT NULL DEFAULT '0' COMMENT '论坛帖子tid',
  `oldname` varchar(255) NOT NULL,
  `audit_note` varchar(250) NOT NULL DEFAULT '' COMMENT '审核结果',
  `season` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`) USING BTREE,
  KEY `bt_info_hash` (`bt_info_hash`),
  KEY `size` (`size`)
) ENGINE=MyISAM AUTO_INCREMENT=536939 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_attachments`;


CREATE TABLE `cgbt_torrents_attachments` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(8) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `old_name` varchar(200) NOT NULL DEFAULT '',
  `file_md5` char(32) NOT NULL DEFAULT '',
  `download` int(8) NOT NULL DEFAULT '0',
  `username` char(30) NOT NULL DEFAULT '',
  `uid` int(8) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `guid` char(36) NOT NULL DEFAULT '' COMMENT 'unique id',
  `newpath` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`),
  KEY `guid` (`guid`),
  KEY `uid_2` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=291995 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_award`;


CREATE TABLE `cgbt_torrents_award` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `user_title` varchar(20) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=8352 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_descr`;


CREATE TABLE `cgbt_torrents_descr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  `descr` text NOT NULL,
  `uid` int(11) NOT NULL,
  `username` char(30) NOT NULL,
  `replaced` tinyint(4) NOT NULL,
  `url_queued` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`createtime`),
  KEY `replaced` (`replaced`),
  KEY `url_queued` (`url_queued`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cgbt_torrents_files`;


CREATE TABLE `cgbt_torrents_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_id`;


CREATE TABLE `cgbt_torrents_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_images`;


CREATE TABLE `cgbt_torrents_images` (
  `id` bigint(8) unsigned NOT NULL AUTO_INCREMENT,
  `images_id` bigint(8) NOT NULL DEFAULT '0',
  `tid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` char(30) NOT NULL DEFAULT '',
  `uid` int(8) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `guid` char(36) NOT NULL DEFAULT '' COMMENT 'unique id',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`),
  KEY `guid` (`guid`),
  KEY `images_id` (`images_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_index`;


CREATE TABLE `cgbt_torrents_index` (
  `id` int(11) NOT NULL COMMENT '主键',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '审核/删除状态,-2删除,-1审核不通过,0未审核,1审核通过',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `files` int(11) NOT NULL DEFAULT '0' COMMENT '文件数量',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '资源大小',
  `category` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分类',
  `year` smallint(11) NOT NULL DEFAULT '0' COMMENT '出品年份',
  `date` int(11) NOT NULL DEFAULT '0' COMMENT '出品日期',
  `district` tinyint(11) NOT NULL DEFAULT '0' COMMENT '国家/地区',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '剧情类型',
  `format` tinyint(11) NOT NULL DEFAULT '0' COMMENT '格式',
  `subtitle` int(11) NOT NULL DEFAULT '0' COMMENT '字幕',
  `subs` tinyint(4) NOT NULL DEFAULT '0',
  `opt1` tinyint(11) NOT NULL DEFAULT '0',
  `opt2` tinyint(11) NOT NULL DEFAULT '0',
  `istop` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `isrecommend` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `isfree` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否免费',
  `is30p` tinyint(4) NOT NULL DEFAULT '0',
  `ishalf` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否50%下载',
  `is2x` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否两倍上传',
  `iscollection` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否合集',
  `is0day` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否0day',
  `ishd` tinyint(4) NOT NULL DEFAULT '0',
  `seeder` int(11) NOT NULL DEFAULT '0' COMMENT '种子数',
  `leecher` int(11) NOT NULL DEFAULT '0' COMMENT '下载数',
  `view` int(11) NOT NULL DEFAULT '0' COMMENT '查看数',
  `download` int(11) NOT NULL DEFAULT '0' COMMENT '下载数',
  `complete` int(11) NOT NULL DEFAULT '0' COMMENT '完成数',
  `comment_time` int(11) NOT NULL DEFAULT '0' COMMENT '评论时间',
  `comment_count` int(11) NOT NULL DEFAULT '0' COMMENT '评论数量',
  `extcredits1` double(20,5) NOT NULL,
  `imdb` char(9) NOT NULL,
  `keyword` char(250) NOT NULL DEFAULT '',
  `forums_tid` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `category` (`category`),
  KEY `seeder` (`seeder`),
  KEY `uid` (`uid`),
  KEY `forums_tid` (`forums_tid`),
  KEY `status` (`status`),
  KEY `size` (`size`),
  KEY `imdb` (`imdb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_keywords`;


CREATE TABLE `cgbt_torrents_keywords` (
  `id` int(11) NOT NULL DEFAULT '0',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_mod`;


CREATE TABLE `cgbt_torrents_mod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `operator_uid` int(11) NOT NULL,
  `operator_username` varchar(30) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '删除状态,-1表示删除，1表示正常',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`enabled`) USING BTREE,
  KEY `start_time` (`start_time`,`end_time`)
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_nfo`;


CREATE TABLE `cgbt_torrents_nfo` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `torrent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `filename` char(100) NOT NULL DEFAULT '',
  `description` char(100) NOT NULL DEFAULT '',
  `filetype` char(50) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `attachment` char(100) NOT NULL DEFAULT '',
  `downloads` mediumint(8) NOT NULL DEFAULT '0',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `username` char(30) NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `added` int(11) NOT NULL DEFAULT '0',
  `pic_s100` char(50) NOT NULL DEFAULT '' COMMENT '缩略图',
  `pic_s100_size` int(11) NOT NULL DEFAULT '0' COMMENT '缩略图大小',
  `guid` char(36) NOT NULL DEFAULT '' COMMENT 'unique id',
  PRIMARY KEY (`aid`),
  KEY `tid` (`torrent_id`),
  KEY `uid` (`uid`),
  KEY `is_image` (`isimage`),
  KEY `guid` (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_price_mod`;


CREATE TABLE `cgbt_torrents_price_mod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `sort_price` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `price_type` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '删除状态,-1表示删除，1表示正常',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`enabled`) USING BTREE,
  KEY `start_time` (`start_time`,`end_time`)
) ENGINE=MyISAM AUTO_INCREMENT=393 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_torrents_stat`;


CREATE TABLE `cgbt_torrents_stat` (
  `id` int(11) NOT NULL,
  `seeder` int(11) NOT NULL,
  `leecher` int(11) NOT NULL,
  `view` int(11) NOT NULL,
  `download` int(11) NOT NULL,
  `complete` int(11) NOT NULL,
  `extcredits1` double(20,5) NOT NULL,
  `last_action` int(11) NOT NULL,
  `last_uid` int(11) NOT NULL,
  `last_username` char(30) NOT NULL,
  `update_uid` int(11) NOT NULL,
  `update_username` char(30) NOT NULL,
  `comment_time` int(11) NOT NULL,
  `comment_count` int(11) NOT NULL,
  `support` int(11) NOT NULL,
  `against` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cgbt_users`;


CREATE TABLE `cgbt_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(20) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL DEFAULT '',
  `salt` char(40) NOT NULL DEFAULT '',
  `forums_uid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `parked` tinyint(4) NOT NULL DEFAULT '0',
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `title` char(20) NOT NULL DEFAULT '',
  `oldemail` char(80) NOT NULL,
  `email` char(80) NOT NULL DEFAULT '',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `regip` char(40) NOT NULL DEFAULT '',
  `class_road` tinyint(4) NOT NULL DEFAULT '0',
  `avatar` char(100) NOT NULL DEFAULT '',
  `passkey` char(32) NOT NULL DEFAULT '',
  `pktime` int(11) NOT NULL DEFAULT '0',
  `gender` enum('','male','female') NOT NULL DEFAULT '',
  `need_audit` tinyint(4) NOT NULL DEFAULT '1',
  `duty` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `passkey` (`passkey`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_users_bans`;


CREATE TABLE `cgbt_users_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `username` varchar(30) NOT NULL DEFAULT '',
  `starttime` int(11) NOT NULL DEFAULT '0',
  `endtime` int(11) NOT NULL DEFAULT '0',
  `operator` varchar(30) NOT NULL DEFAULT '',
  `operator_uid` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `updatetime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `reason` varchar(250) NOT NULL DEFAULT '',
  `memo` varchar(250) NOT NULL DEFAULT '',
  `privileges_name` varchar(30) NOT NULL DEFAULT '',
  `privileges_value` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`starttime`,`endtime`,`status`) USING BTREE,
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1187 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_users_group`;


CREATE TABLE `cgbt_users_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `orderid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `color` varchar(10) NOT NULL,
  `min_credits` int(11) NOT NULL DEFAULT '0',
  `max_credits` int(11) NOT NULL DEFAULT '0',
  `privileges` text NOT NULL,
  `admin_privileges` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_users_stat`;


CREATE TABLE `cgbt_users_stat` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL,
  `class` tinyint(4) NOT NULL,
  `last_check` int(11) NOT NULL,
  `last_action` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_access_both` int(11) NOT NULL,
  `last_access` int(11) NOT NULL DEFAULT '0',
  `last_access_ipv6` int(11) NOT NULL DEFAULT '0',
  `last_browse` int(11) NOT NULL DEFAULT '0',
  `last_ip` char(40) NOT NULL DEFAULT '',
  `last_ipv6` char(40) NOT NULL DEFAULT '',
  `uploaded` bigint(20) NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploaded2` bigint(20) NOT NULL DEFAULT '0',
  `downloaded2` bigint(20) NOT NULL DEFAULT '0',
  `extcredits1` double(20,5) NOT NULL DEFAULT '0.00000',
  `extcredits2` int(11) NOT NULL DEFAULT '0',
  `total_torrent_size` bigint(20) NOT NULL DEFAULT '0',
  `total_torrent_count` int(11) NOT NULL DEFAULT '0',
  `total_upload_times` int(11) NOT NULL DEFAULT '0',
  `total_upload_size` bigint(20) NOT NULL DEFAULT '0',
  `current_torrent_size` bigint(20) NOT NULL DEFAULT '0',
  `current_torrent_count` int(11) NOT NULL DEFAULT '0',
  `total_download_times` int(11) NOT NULL DEFAULT '0',
  `total_completed_count` int(11) NOT NULL,
  `total_credits` int(11) NOT NULL DEFAULT '0',
  `today_uploaded` bigint(20) NOT NULL DEFAULT '0',
  `today_uploaded2` bigint(20) NOT NULL DEFAULT '0',
  `today_downloaded2` bigint(20) NOT NULL DEFAULT '0',
  `today_downloaded` bigint(20) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  `hour_uploaded` bigint(20) NOT NULL DEFAULT '0',
  `hour_uploaded2` bigint(20) NOT NULL DEFAULT '0',
  `hour_downloaded2` bigint(20) NOT NULL DEFAULT '0',
  `hour_downloaded` bigint(20) NOT NULL DEFAULT '0',
  `extcredits3` double(20,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`uid`),
  KEY `class` (`class`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `today_uploaded` (`today_uploaded`),
  KEY `today_downloaded` (`today_downloaded`),
  KEY `hour_uploaded` (`hour_uploaded`),
  KEY `hour_downloaded` (`hour_downloaded`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cgbt_work_stats`;


CREATE TABLE `cgbt_work_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` char(30) NOT NULL,
  `type` char(20) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `duration` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fetched_pic`;


CREATE TABLE `fetched_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `torrentid` int(11) NOT NULL DEFAULT '0',
  `url` char(255) NOT NULL DEFAULT '',
  `md5` char(32) NOT NULL DEFAULT '',
  `path` char(43) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=144425 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sql_error_log`;


CREATE TABLE `sql_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_sql` text NOT NULL,
  `error_descr` text NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


truncate table attachments;
truncate table cgbt_agentinfo;
truncate table cgbt_attachments_new;
truncate table cgbt_category;
truncate table cgbt_category_options;
truncate table cgbt_chat;
truncate table cgbt_favorite;
truncate table cgbt_fetched_images;
truncate table cgbt_images;
truncate table cgbt_images_url_queue;
truncate table cgbt_invite;
truncate table cgbt_logs_actions;
truncate table cgbt_logs_browse;
truncate table cgbt_logs_completed;
truncate table cgbt_logs_credits;
truncate table cgbt_logs_cron;
truncate table cgbt_logs_day_stat;
truncate table cgbt_logs_daystats;
truncate table cgbt_logs_debug;
truncate table cgbt_logs_develop;
truncate table cgbt_logs_download;
truncate table cgbt_logs_hour_stat;
truncate table cgbt_logs_login;
truncate table cgbt_logs_loginfail;
truncate table cgbt_logs_memcached;
truncate table cgbt_logs_modgroup;
truncate table cgbt_logs_passkey;
truncate table cgbt_logs_rate;
truncate table cgbt_logs_search;
truncate table cgbt_logs_sitestat;
truncate table cgbt_logs_work;
truncate table cgbt_peers;
truncate table cgbt_peers_connectable;
truncate table cgbt_privileges;
truncate table cgbt_search_keywords;
truncate table cgbt_setting;
truncate table cgbt_softsite_images;
truncate table cgbt_torrents;
truncate table cgbt_torrents_attachments;
truncate table cgbt_torrents_award;
truncate table cgbt_torrents_descr;
truncate table cgbt_torrents_files;
truncate table cgbt_torrents_id;
truncate table cgbt_torrents_images;
truncate table cgbt_torrents_index;
truncate table cgbt_torrents_keywords;
truncate table cgbt_torrents_mod;
truncate table cgbt_torrents_nfo;
truncate table cgbt_torrents_price_mod;
truncate table cgbt_torrents_stat;
truncate table cgbt_torrents_subtitles_old;
truncate table cgbt_users;
truncate table cgbt_users_bans;
truncate table cgbt_users_group;
truncate table cgbt_users_stat;
truncate table cgbt_work_stats;
truncate table fetched_pic;
truncate table sql_error_log;
DROP TABLE IF EXISTS `cgbt_setting`;


CREATE TABLE `cgbt_setting` (
  `skey` char(50) NOT NULL,
  `svalue` text NOT NULL,
  PRIMARY KEY (`skey`),
  UNIQUE KEY `skey` (`skey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_setting` VALUES ('site_name','PT');
INSERT INTO `cgbt_setting` VALUES ('static_prefix','');
INSERT INTO `cgbt_setting` VALUES ('forums_url','http://zhixing.bjtu.edu.cn/');
INSERT INTO `cgbt_setting` VALUES ('forums_template_dir','template');
INSERT INTO `cgbt_setting` VALUES ('forums_register_url','http://zhixing.bjtu.edu.cn/member.php?mod=register');
INSERT INTO `cgbt_setting` VALUES ('forums_lost_password_url','http://zhixing.bjtu.edu.cn/member.php?mod=logging&action=login&viewlostpw=1');
INSERT INTO `cgbt_setting` VALUES ('tracker_announce_interval','1800');
INSERT INTO `cgbt_setting` VALUES ('tracker_min_interval','120');
INSERT INTO `cgbt_setting` VALUES ('tracker_min_interval_limit','60');
INSERT INTO `cgbt_setting` VALUES ('forums_thread_url','http://zhixing.bjtu.edu.cn/thread-{$tid}-1-1.html');
INSERT INTO `cgbt_setting` VALUES ('tracker_log_speed','5');
INSERT INTO `cgbt_setting` VALUES ('extcredits1_max','20');
INSERT INTO `cgbt_setting` VALUES ('extcredits1_min','0.1');
INSERT INTO `cgbt_setting` VALUES ('extcredits1_size','5');
INSERT INTO `cgbt_setting` VALUES ('extcredits1_seeders','7');
INSERT INTO `cgbt_setting` VALUES ('extcredits1_weeks','8');
INSERT INTO `cgbt_setting` VALUES ('site_qq_qun','84195708');
INSERT INTO `cgbt_setting` VALUES ('torrents_source','PT(http://xxxx.org)');
INSERT INTO `cgbt_setting` VALUES ('torrents_size_limit','50');
INSERT INTO `cgbt_setting` VALUES ('forums_modify_password_url','http://zhixing.bjtu.edu.cn/home.php?mod=spacecp&ac=profile&op=password');
INSERT INTO `cgbt_setting` VALUES ('site_cookie_expire','30');
INSERT INTO `cgbt_setting` VALUES ('tracker_download_unaudited_user','admin');
INSERT INTO `cgbt_setting` VALUES ('tracker_peer_clean_time','3800');
INSERT INTO `cgbt_setting` VALUES ('forums_type','discuz x2');
INSERT INTO `cgbt_setting` VALUES ('upload_note','请选择种子分类，认真填写种子信息。');
INSERT INTO `cgbt_setting` VALUES ('torrents_save_path','/data/webserver/cgbt/attachments/torrents/');
INSERT INTO `cgbt_setting` VALUES ('images_domain','http://img1.cgbt.cn/');
INSERT INTO `cgbt_setting` VALUES ('images_save_path','/data/webserver/cgbt/attachments/images/');
INSERT INTO `cgbt_setting` VALUES ('tracker_url','http://tracker.cgbt.cn/announce.php?passkey={$passkey}');
INSERT INTO `cgbt_setting` VALUES ('images_size_limit','4');
INSERT INTO `cgbt_setting` VALUES ('site_qq_qun_name','知行PT粉丝及咨询台QQ群');
INSERT INTO `cgbt_setting` VALUES ('tracker_black_peer_id','XL00:Xunlei\r\nFG0:FlashGet\r\nQD1:qqdownload\r\nBC006:BitComet 0.6\r\nBC007:BitComet 0.7\r\nBC008:BitComet 0.8\r\nBC009:BitComet 0.9\r\nBC010:BitComet 1.0\r\nBC0109:BitComet 1.9\r\nBC0110:BitComet 1.10\r\nUT13:uTorrent 1.3\r\nUT14:uTorrent 1.4\r\nUT15:uTorrent 1.5\r\nUT16:uTorrent 1.6\r\nUT17:uTorrent 1.7\r\nUT180B:uTorrent 1.8 Beta\r\nUT1800:uTorrent 1.80\r\nUT1810:uTorrent 1.81\r\nUT1820:uTorrent 1.82\r\nUT1830:uTorrent 1.83\r\nUT1840:uTorrent 1.84\r\nUT181B:uTorrent Beta\r\nUT183B:uTorrent Beta\r\nUT190B:uTorrent Beta\r\nUT220B:uTorrent Beta\r\nUT221B:uTorrent Beta\r\nUT210B:uTorrent Beta\r\nUT200B:uTorrent Beta\r\nUT201B:uTorrent Beta\r\nUT203B:uTorrent Beta\r\nUT220B:uTorrent Beta\r\nUT300B:uTorrent Beta\r\nUT310B:uTorrent Beta\r\nUT311B:uTorrent Beta\r\nUT312B:uTorrent Beta\r\nUT320B:uTorrent Beta\r\nUT321B:uTorrent Beta\r\nUT322B:uTorrent Beta\r\nUT323B:uTorrent Beta\r\nUT300B:uTorrent Beta\r\nUT330B:uTorrent Beta\r\nUT331B:uTorrent Beta\r\nUT332B:uTorrent Beta');
INSERT INTO `cgbt_setting` VALUES ('tracker_black_agent','Opera\r\nOneSwarm\r\nMediaGet\r\nDalvik\r\nTribler\r\nfolx\r\nCTorrent\r\nXfplay\r\nnsTorrent\r\nXtorrent\r\nRK0001\r\nJava\r\nlftp\r\nShareaza\r\nlibtorrent\r\nHalite\r\nMozilla\r\nMSIE\r\nDNA\r\nFDM\r\nKGet\r\nABC\r\nMLDonkey\r\nBitComet\r\nBitsOnWheels\r\nMooPolice\r\naria\r\nagent\r\nBitsCast\r\nBitTornado\r\nBTSP\r\ncurl\r\ndebian\r\nNP020\r\nDLBT\r\nVeryCD\r\nTixati\r\nGoogle\r\nDeluge\r\nBitTorrent\r\nbtcs');
INSERT INTO `cgbt_setting` VALUES ('online_time','20');
INSERT INTO `cgbt_setting` VALUES ('delete_torrents_reasons','发种人自己删除\r\n种子在审核区过久\r\n发种后24小时无种\r\n该种子长期没有人做种\r\n已有正规资源\r\n重复发种\r\n长时间未完善种子名称及介绍\r\n请勿发布枪版等低质量格式电影\r\n资源版本不统一，不允许打包发布\r\n含宣传或广告性质的信息\r\n含求种信息\r\n含有病毒\r\n不允许发布各种视频短片\r\n不允许发布各种写真\r\n电影分级:NC17\r\n电影分级:香港Ⅲ级\r\n所谓伦理片或情色片\r\n含色情或色情擦边内容\r\n禁片或解禁片\r\n不适合发布\r\n');
INSERT INTO `cgbt_setting` VALUES ('header_background_pic','');
INSERT INTO `cgbt_setting` VALUES ('extcredits12uploaded_need_extcredits1','80');
INSERT INTO `cgbt_setting` VALUES ('extcredits12uploaded_max','8000');
INSERT INTO `cgbt_setting` VALUES ('extcredits12uploaded_days_interval','1');
INSERT INTO `cgbt_setting` VALUES ('forums_money_field','extcredits1');
INSERT INTO `cgbt_setting` VALUES ('money2uploaded_need_money','25');
INSERT INTO `cgbt_setting` VALUES ('money2uploaded_days_interval','60');
INSERT INTO `cgbt_setting` VALUES ('money2uploaded_max','2000');
INSERT INTO `cgbt_setting` VALUES ('torrents_price_times','20');
INSERT INTO `cgbt_setting` VALUES ('tracker_black_ips','202.112.154.26');
INSERT INTO `cgbt_setting` VALUES ('audit_torrents_reasons','没人做种了请继续做种。\r\n发种没有成功请参考帮助中心-发种教程\r\n请参照版规完善种子名称和介绍\r\n请完善种子介绍\r\n请完善种子名称\r\n非正规格式资源请补充至少3张视频截图\r\n别人已经发过了你可以直接去下载然后续种。\r\n枪版电影质量太差请勿发布');
INSERT INTO `cgbt_setting` VALUES ('download_torrents_name_prefix','[ZXPT]');
INSERT INTO `cgbt_setting` VALUES ('tracker_peer_force_clean_time','3800');
INSERT INTO `cgbt_setting` VALUES ('site_domain','http://pt.zhixing.bjtu.edu.cn');
INSERT INTO `cgbt_setting` VALUES ('subtitles_size_limit','5');
INSERT INTO `cgbt_setting` VALUES ('subtitles_save_path','/data/webserver/cgbt/attachments/subtitles/');
INSERT INTO `cgbt_setting` VALUES ('nfos_save_path','/data/webserver/cgbt/attachments/nfos/');
INSERT INTO `cgbt_setting` VALUES ('nfos_size_limit','1');
INSERT INTO `cgbt_setting` VALUES ('login_fail_time','60');
INSERT INTO `cgbt_setting` VALUES ('login_fail_count','10');
INSERT INTO `cgbt_setting` VALUES ('enable_ratio_limit','0');
INSERT INTO `cgbt_setting` VALUES ('ratio_limit','1000:1.7\r\n500:1.5\r\n300:1.3\r\n100:1.0\r\n50:0.8\r\n20:0.6\r\n0:0');
INSERT INTO `cgbt_setting` VALUES ('enable_seed_count_limit','1');
INSERT INTO `cgbt_setting` VALUES ('seed_count_limit','1:1\r\n5:3\r\n10:6\r\n20:10\r\n30:15\r\n50:20\r\n10000:100');
INSERT INTO `cgbt_setting` VALUES ('enable_seed_size_limit','1');
INSERT INTO `cgbt_setting` VALUES ('seed_size_limit','1:1\r\n5:3\r\n10:6\r\n20:10\r\n30:15\r\n50:20\r\n10000:100');
INSERT INTO `cgbt_setting` VALUES ('all_free','0');
INSERT INTO `cgbt_setting` VALUES ('all_2x','0');
INSERT INTO `cgbt_setting` VALUES ('new_torrents_free_time','1');
INSERT INTO `cgbt_setting` VALUES ('new_torrents_30p_time','3');
INSERT INTO `cgbt_setting` VALUES ('new_torrents_half_time','5');
INSERT INTO `cgbt_setting` VALUES ('torrents_free_min_size','3');
INSERT INTO `cgbt_setting` VALUES ('download_need_extcredits1','500');
INSERT INTO `cgbt_setting` VALUES ('newbie_enable','1');
INSERT INTO `cgbt_setting` VALUES ('newbie_days','60');
INSERT INTO `cgbt_setting` VALUES ('newbie_uploaded','50');
INSERT INTO `cgbt_setting` VALUES ('newbie_downloaded','30');
INSERT INTO `cgbt_setting` VALUES ('newbie_extcredits1','200');
INSERT INTO `cgbt_setting` VALUES ('newbie_startdate','2013-03-16');
INSERT INTO `cgbt_setting` VALUES ('hot_torrents_seed_count_limit','0');
INSERT INTO `cgbt_setting` VALUES ('hot_torrents_seed_size_limit','0');
INSERT INTO `cgbt_setting` VALUES ('torrents_price','2\r\n4\r\n6\r\n8\r\n10');
INSERT INTO `cgbt_setting` VALUES ('torrents_price_tax','50');
INSERT INTO `cgbt_setting` VALUES ('softsite_save_path','/data/webserver/cgbt/attachments/softsite/');
INSERT INTO `cgbt_setting` VALUES ('softsite_size_limit','50');
INSERT INTO `cgbt_setting` VALUES ('modify_title_need_extcredits1','500');
INSERT INTO `cgbt_setting` VALUES ('torrents_award','5\r\n10\r\n15\r\n20\r\n30\r\n40\r\n50\r\n60\r\n70\r\n80\r\n90\r\n100');
INSERT INTO `cgbt_setting` VALUES ('req_seed_extcredits1','100');
INSERT INTO `cgbt_setting` VALUES ('upload_factor_link','http://zhixing.bjtu.edu.cn/thread-440202-1-1.html');
INSERT INTO `cgbt_setting` VALUES ('enable_upload_factor','1');
INSERT INTO `cgbt_setting` VALUES ('mod_price_min','500');
INSERT INTO `cgbt_setting` VALUES ('upload_sub_extcredits1','10');
INSERT INTO `cgbt_setting` VALUES ('search_page_announce','<a target=\"_blank\" style=\"color:red;font-weight:bold;font-size:20px;\" href=\"http://zhixing.bjtu.edu.cn/thread-862253-1-1.html\">26日PT维护公告</a>');
INSERT INTO `cgbt_setting` VALUES ('torrents_comments_extcredits2','1');
INSERT INTO `cgbt_setting` VALUES ('torrents_award_extcredits2','2');
INSERT INTO `cgbt_setting` VALUES ('torrents_rate_extcredits2','1');
INSERT INTO `cgbt_setting` VALUES ('upload_sub_extcredits2','5');
DROP TABLE IF EXISTS `cgbt_users_group`;


CREATE TABLE `cgbt_users_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `orderid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `color` varchar(10) NOT NULL,
  `min_credits` int(11) NOT NULL DEFAULT '0',
  `max_credits` int(11) NOT NULL DEFAULT '0',
  `privileges` text NOT NULL,
  `admin_privileges` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=160 DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_users_group` VALUES (2,'user',2,1,'武林新丁','#008000',-1,20,'{\"login\":\"1\",\"favorite_count\":\"5\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"0\",\"download_hot_torrents\":\"0\",\"userbar\":\"0\",\"dont_need_audit\":\"0\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"2\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"0\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (3,'user',3,2,'江湖小虾','#ADFF2F',20,100,'{\"login\":\"1\",\"favorite_count\":\"10\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"0\",\"download_hot_torrents\":\"0\",\"userbar\":\"0\",\"dont_need_audit\":\"0\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"4\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"0\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (4,'user',4,3,'后起之秀','#DAA520',100,200,'{\"login\":\"1\",\"favorite_count\":\"20\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"0\",\"userbar\":\"0\",\"dont_need_audit\":\"0\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"7\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"0\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (5,'user',5,4,'武林高手','#40E0D0',200,500,'{\"login\":\"1\",\"favorite_count\":\"40\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"0\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"11\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"1\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (6,'user',6,5,'风尘奇侠','#1E90FF',500,1000,'{\"login\":\"1\",\"favorite_count\":\"60\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"16\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"1\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (7,'user',7,6,'无双隐士','#4B0082',1000,2000,'{\"login\":\"1\",\"favorite_count\":\"100\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"1\",\"userbar2\":\"0\",\"download_count_everyday\":\"25\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"1\",\"chat_use_ubb\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (8,'user',8,7,'世外高人','#FF1493',2000,4000,'{\"login\":\"1\",\"favorite_count\":\"160\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"1\",\"userbar2\":\"1\",\"download_count_everyday\":\"36\",\"rss\":\"1\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"1\",\"price_top\":\"1\",\"chat_use_ubb\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (9,'user',9,8,'江湖侠隐','#D8BFD8',4000,7000,'{\"login\":\"1\",\"favorite_count\":\"200\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"1\",\"userbar2\":\"1\",\"download_count_everyday\":\"50\",\"rss\":\"1\",\"view_sitelog\":\"1\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"1\",\"price_top\":\"1\",\"chat_use_ubb\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (10,'user',10,9,'无敌圣者','#6A5ACD',7000,12000,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"0\",\"favorite_count\":\"240\",\"download_count_everyday\":\"80\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\",\"comment\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (11,'user',11,10,'三界贤君','#FF00FF',12000,999999,'{\"login\":\"1\",\"favorite_count\":\"400\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"1\",\"userbar2\":\"1\",\"download_count_everyday\":\"100\",\"rss\":\"1\",\"view_sitelog\":\"1\",\"multi_ip_download\":\"1\",\"download_unaudit\":\"1\",\"display_download_link\":\"1\",\"price_top\":\"1\",\"chat_use_ubb\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (1,'user',1,0,'地狱使者','#000000',-999,-1,'{\"login\":\"1\",\"favorite_count\":\"2\",\"download_top\":\"1\",\"comment\":\"0\",\"download\":\"0\",\"upload\":\"0\",\"download_hot_torrents\":\"0\",\"userbar\":\"0\",\"dont_need_audit\":\"0\",\"credits2invite\":\"0\",\"userbar2\":\"0\",\"download_count_everyday\":\"3\",\"rss\":\"0\",\"view_sitelog\":\"0\",\"multi_ip_download\":\"0\",\"download_unaudit\":\"0\",\"display_download_link\":\"0\",\"price_top\":\"0\",\"chat_use_ubb\":\"0\"}','');
INSERT INTO `cgbt_users_group` VALUES (12,'vip',12,12,'驻晨光大使','#32CD32',0,0,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"1000\",\"download_count_everyday\":\"1000\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (13,'vip',13,14,'VIP用户','#8A2BE2',0,0,'{\"login\":\"1\",\"favorite_count\":\"1000\",\"download_top\":\"1\",\"comment\":\"1\",\"download\":\"1\",\"upload\":\"1\",\"download_hot_torrents\":\"1\",\"userbar\":\"1\",\"dont_need_audit\":\"1\",\"credits2invite\":\"1\",\"userbar2\":\"1\",\"download_count_everyday\":\"1000\",\"rss\":\"1\",\"view_sitelog\":\"1\",\"multi_ip_download\":\"1\",\"download_unaudit\":\"1\",\"display_download_link\":\"1\",\"price_top\":\"1\",\"chat_use_ubb\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (14,'admin',15,22,'资源审核组','#FF6600',0,0,'{\"can_upload\":\"1\",\"need_audit\":\"0\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"400\",\"download_count_everyday\":\"100\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"download_hot_torrents\":\"1\"}','{\"index\\/index\":1,\"index\\/index2\":1,\"index\\/memcache\":1,\"index\\/getcache\":1,\"index\\/clearcache\":1,\"index\\/daystats\":1,\"index\\/myip_stat\":1,\"index\\/phpinfo\":1,\"index\\/credits\":1,\"index\\/agentinfo\":1,\"index\\/logslogin\":1,\"torrentindex\\/index\":1,\"torrentindex\\/update\":1,\"keyword2pinyin\\/index\":1,\"keyword2pinyin\\/update\":1,\"setting\\/index\":1,\"setting\\/admins\":1,\"setting\\/forums\":1,\"setting\\/tracker\":1,\"setting\\/credits\":1,\"setting\\/upload\":1,\"setting\\/rule\":1,\"setting\\/newbie\":1,\"category\\/index\":1,\"category\\/edit\":1,\"category\\/insert\":1,\"category\\/update\":1,\"category_options\\/index\":1,\"category_options\\/edit\":1,\"category_options\\/insert\":1,\"category_options\\/update\":1,\"category\\/rules\":1,\"category\\/rules_update\":1,\"user\\/search\":1,\"user\\/list\":1,\"user\\/edit_group\":1,\"user\\/update_group\":1,\"users_group\\/index\":1,\"users_group\\/edit\":1,\"users_group\\/update\":1,\"privileges\\/index\":1,\"privileges\\/edit\":1,\"privileges\\/insert\":1,\"privileges\\/update\":1,\"privileges\\/front\":1,\"privileges\\/back\":1,\"privileges\\/addfront\":1,\"privileges\\/addback\":1,\"bans\\/add\":1,\"bans\\/index\":1,\"bans\\/insert\":1,\"bans\\/edit\":1,\"bans\\/update\":1}');
INSERT INTO `cgbt_users_group` VALUES (15,'admin',16,24,'资源发布组','#4169E1',0,0,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"1000\",\"download_count_everyday\":\"1000\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (16,'admin',17,26,'资源更新组','#9400D3',0,0,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"1000\",\"download_count_everyday\":\"1000\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\"}','');
INSERT INTO `cgbt_users_group` VALUES (17,'admin',18,28,'版主','#C71585',0,0,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"1000\",\"download_count_everyday\":\"1000\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\"}','{\"category\\/index\":1,\"category_options\\/index\":1,\"user\\/search\":1}');
INSERT INTO `cgbt_users_group` VALUES (18,'admin',19,100,'管理员','#FF0000',0,0,'{\"can_upload\":\"1\",\"need_audit\":\"0\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"400\",\"download_count_everyday\":\"100\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"download_hot_torrents\":\"1\"}','{\"index\\/index\":1,\"index\\/index2\":1,\"index\\/memcache\":1,\"index\\/getcache\":1,\"index\\/clearcache\":1,\"index\\/daystats\":1,\"index\\/myip_stat\":1,\"index\\/phpinfo\":1,\"index\\/credits\":1,\"index\\/agentinfo\":1,\"index\\/logslogin\":1,\"torrentindex\\/index\":1,\"torrentindex\\/update\":1,\"keyword2pinyin\\/index\":1,\"keyword2pinyin\\/update\":1,\"setting\\/index\":1,\"setting\\/admins\":1,\"setting\\/forums\":1,\"setting\\/tracker\":1,\"setting\\/credits\":1,\"setting\\/upload\":1,\"setting\\/rule\":1,\"setting\\/newbie\":1,\"category\\/index\":1,\"category\\/edit\":1,\"category\\/insert\":1,\"category\\/update\":1,\"category_options\\/index\":1,\"category_options\\/edit\":1,\"category_options\\/insert\":1,\"category_options\\/update\":1,\"category\\/rules\":1,\"category\\/rules_update\":1,\"user\\/search\":1,\"user\\/list\":1,\"user\\/edit_group\":1,\"user\\/update_group\":1,\"users_group\\/index\":1,\"users_group\\/edit\":1,\"users_group\\/update\":1,\"users_group\\/editadmin\":1,\"users_group\\/updateadmin\":1,\"privileges\\/index\":1,\"privileges\\/edit\":1,\"privileges\\/insert\":1,\"privileges\\/update\":1,\"privileges\\/front\":1,\"privileges\\/back\":1,\"privileges\\/addfront\":1,\"privileges\\/addback\":1,\"bans\\/add\":1,\"bans\\/index\":1,\"bans\\/insert\":1,\"bans\\/edit\":1,\"bans\\/update\":1}');
INSERT INTO `cgbt_users_group` VALUES (159,'vip',14,15,'离任资源组','#B0C4DE',0,0,'{\"login\":\"1\",\"download_top\":\"1\",\"download\":\"1\",\"download_hot_torrents\":\"1\",\"upload\":\"1\",\"dont_need_audit\":\"1\",\"multi_ip_download\":\"1\",\"userbar\":\"1\",\"userbar2\":\"1\",\"download_unaudit\":\"1\",\"favorite_count\":\"1000\",\"download_count_everyday\":\"1000\",\"view_sitelog\":\"1\",\"credits2invite\":\"1\",\"rss\":\"1\"}','');
DROP TABLE IF EXISTS `cgbt_category`;


CREATE TABLE `cgbt_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `properties` varchar(100) NOT NULL DEFAULT '',
  `input_items` varchar(100) NOT NULL DEFAULT '',
  `admins` varchar(1000) NOT NULL DEFAULT '',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `forums_fid` int(11) NOT NULL,
  `hot_keywords` text NOT NULL,
  `hot_keywords_count` tinyint(4) NOT NULL,
  `app` varchar(10) NOT NULL DEFAULT 'torrents',
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_en` (`name_en`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_category` VALUES (1,'电影','movie','地区,类型,年代,格式,编码,字幕,合集，评分','','lovebeyond37\r\nC751412K\r\nsgdxl007\r\nohperhaps\r\nlouiszz\r\nlchkid\r\nnightmare\r\nlich13\r\nliu751078762\r\nedashao','movie.png',313,'霍比特人\r\n云图\r\n西游降魔篇\r\n少年派\r\n悲惨世界\r\n十二生肖\r\n007\r\n林肯\r\n大上海\r\n奥斯卡\r\n逃离德黑兰\r\n虎胆龙威\r\n碟中谍\r\n王的盛宴',14,'torrents','允许发布的电影格式如下：\r\n1080P，720P ，BDRip，DVDRip，MiniSD\r\nHalfcd，R5，HD-HDTV, HDTVRip, DVDSCR，HDDVDRip\r\n\r\n禁止发布的电影格式如下：\r\n枪版，TS，cam，TC，VCD，DVDR，flv，wmv， asf，MP4（非高清格式），以及部分非0day的R5，DVDSCR等格式。\r\n以前发布的此类格式影片将酌情删除，再有发布此类格式电影的一律删除。\r\n\r\n本规则仅针对电影分类，其他分类请参考各版发种规则。\r\n\r\n[hr/]\r\n一 种子内包含的文件要求\r\n\r\n1. 种子内可以包含的文件\r\n   视频文件，Sample文件，字幕文件(不推荐)，nfo文件，音频文件(电影原声或独立音轨)\r\n\r\n2. 种子内不可以包含的文件\r\n   不要包含如影片介绍txt，sfv，jpg，url链接，utorrent临时文件等。\r\n\r\n3. 种子内文件的命名及目录格式，有ABC三种方式可选。\r\n\r\n   命名方式A\r\n       文件名：中文名称(使用方括号)，英文全名，格式，版本(即制作小组：如DiAMOND，CHD)，cd数(小写，如cd1，cd2)。\r\n       中英文字幕：srt格式字幕中英文命名分别为chs，eng，不要使用gb，en。idx+sub格式的字幕与视频文件名相同。\r\n\r\n       目录及文件格式如下：\r\n       双CD的\r\n       [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd1.avi\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd1.chs.srt\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd1.eng.srt\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd2.avi\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd2.chs.srt\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-cd2.eng.srt\r\n       │  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND-sample.avi\r\n       └  [老无所依].No.Country.for.Old.Men.DVDRip.XviD-DiAMOND.nfo\r\n\r\n       单CD的\r\n       [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD\r\n       │  [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD.eng.srt\r\n       │  [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD.chs.srt\r\n       │  [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD.mkv\r\n       │  [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD.nfo\r\n       └  [触不到的恋人].The.Lake.House.2006.HD-DVD.720p.x264.AC3-CHD.Sample.mkv\r\n\r\n   命名方式B\r\n       文件命名保留0day命名，但是要把2cd放到同一个目录里，字幕要求同命名方式A。\r\n       Stardust.DVDRip.XviD-DiAMOND\r\n       │  dmd-stardust-cd1.avi\r\n       │  dmd-stardust-cd1.chs.avi\r\n       │  dmd-stardust-cd1.eng.avi\r\n       │  dmd-stardust-cd2.avi\r\n       │  dmd-stardust-cd2.chs.avi\r\n       └  dmd-stardust-cd2.eng.avi\r\n\r\n   命名方式C\r\n       不符合命名方式A，B，但文件名必须符合种子名称命名规则第1点。\r\n\r\n4 种子内字幕要求\r\n  种子内字幕文件命名必须匹配视频文件，即播放器可以直接加载。\r\n  srt格式字幕中英文命名分别为chs，eng，不要使用gb，en。\r\n  idx+sub格式的字幕与视频文件名相同。\r\n\r\n5 合集电影要求\r\n\r\n  可以发布的合集：\r\n  奥斯卡合集(提名或获奖)，迪斯尼、皮克斯动画合辑，IMDB TOP系列合集\r\n  影星/导演/系列片合集，如丹泽尔华盛顿合集，斯皮尔伯格合集，星战系列，指环王系列等。\r\n\r\n  不可以发布的合集：\r\n  (1)类型片合集，如恐怖片合集。\r\n  (2)没有任何关系的多部影片合集。\r\n  (3)各种机构评定的十佳，十大某某影片合集。\r\n\r\n  合集内所有影片文件命名要有一定的规则。\r\n  合集内所有影片的字幕要符合字幕要求。\r\n  合集种子介绍要包含合集介绍及合集内影片列表。\r\n  合集种子发种人做种时间必须在一个月以上。\r\n\r\n[hr/]\r\n二  删种原则\r\n\r\n1. 种子名称命名不符合规则要求的。\r\n2. 种子内文件不符合规则要求的。\r\n3. 重复的种子。重复种子的认定原则：\r\n    相同的版本(格式大小一致)。\r\n    如果已经发布了DVDRip，再发布DVDSCR或R5格式属于重复。\r\n4. 被Proper或Repack的种子。\r\n5. 同一影片多个种子优先删除：\r\n    (1)视频质量差的种子。\r\n    (2)从种子文件名看不出种子版本的。\r\n    (3)介绍不完善的种子。 \r\n    (4)没有字幕的种子。 \r\n6. 种子下载无法完成100%的种子。\r\n7. MP4，FLV，asf及其他低质量格式电影。\r\n8. 美国电影分级NC-17，香港电影分级3级影片。\r\n9. 各种所谓的禁片或曾禁播影片或解禁片。\r\n10. 各种所谓的伦理片，情色片。\r\n\r\n[hr/]\r\n三 惩罚规定\r\n   \r\n   违反规定酌情扣除0-50G上传流量，封禁发布种子权限1-4周。\r\n\r\n1. 违反种子名称命名规则。        10G  1周\r\n2. 重复种子。                             20G  1周\r\n3. 发布低质量格式电影               30G  2周\r\n4. 发布NC-17，香港3级影片       50G  4周\r\n5. 发布各种禁片。                 10-30G  2周\r\n6. 发布所谓的伦理片，情色片。 10G  1周\r\n7. 发布封禁电影列表里的电影。 50G  4周 \r\n\r\n[hr/]\r\n四 种子内回复\r\n不要挑战管理员的权威！\r\n故意做种限速，一经系统专门用来排查的程序和人工手动观察做种上传行为的，确定后将会被Ban。\r\n恶意做假种，即利用BT软件强制做种并上传假流量（与原种内容其实完全不符）行为的，确定后将会被Ban。\r\n不要攻击任何人和任何作品 \r\n不要回复求字幕等语言 \r\n\r\n[hr/]\r\n五 置顶推荐原则\r\n\r\n1. 置顶电影原则\r\n   (1)最新出品的电影佳作，IMDB评分应在7.0以上。\r\n   (2)最新出品的受关注程度高的电影，有特殊意义的电影。比如国产的大片，影院热播等，IMDB评分可降低要求。\r\n   (3)必须符合种子命名要求。\r\n   (4)必须有详尽的介绍：海报或截图，导演演员信息，剧情介绍等。\r\n   (5)非国语影片必须包含简体中文字幕。\r\n   (6)视频格式最低为DVDRip。\r\n   (7)影片合集不置顶。\r\n\r\n2. 推荐电影的原则\r\n   (1)曾置顶的电影，具有重复可看性的，格式是720P及以上。如果是在不易获得的老电影则酌情处理。\r\n   (2)不具备置顶标准，但又很优秀的电影。比如较为小众的，内容有点晦涩但艺术高度不错的。\r\n   (3)以前未被置顶的品质优秀的电影。\r\n   (4)丰富内容的，优化画质的，重新着色，重新剪辑的经典影片。\r\n   (5)各种影片合集。\r\n   \r\n   符合上述规定的电影，管理员可直接置顶，推荐，\r\n   如果认为自己发布种子符合以上规定的，可以向管理员提出申请，由管理员认定后进行操作。\r\n   每个种子置顶推荐时间不超两个月，如果置顶超过10个，则按时间顺序将之前的置顶种子取消。\r\n\r\n[hr/]\r\n补充规则：\r\n\r\n晨光BT开放审核区以后，可以发布RMVB格式的电影。要求如下：\r\n\r\n1 必须提供完善的电影介绍：包括电影中英文名称、年代、演职员表，内容介绍等等。\r\n   推荐使用http://verycd.com或http://simplecd.org搜索电影介绍，或者搜索晨光以前的发布过的种子介绍。\r\n   百度百科和豆瓣的电影介绍一律不予通过审核。    \r\n  \r\n2 必须提供电影海报（不少于1张），必须提供视频截图（不少于3张）。  \r\n   种子介绍的内容请按以下顺序放置：最上面是海报，其次是文字介绍，视频截图放在最后面。\r\n   种子介绍格式参考： http://cgbt.cn/details.php?tid=324672&dt=1\r\n\r\n3 发布前请搜索别人是否已经发布过了，重复发种一律不予通过。\r\n  如果已经发布DVDRIP，720P，1080P，halfcd，dvdscr，R5等格式，再发布资源信息不全或rmvb等则视为重复。\r\n\r\n4 影片中不得含有广告水印或其它非影片中自带内容。\r\n\r\n其他注意事项：\r\n5 其他电影格式如DVDRIP，对于种子介绍的要求同1。\r\n6 如果发种违规，审核不通过后请发种人自行删除。超过1天不删的话，版主删除时将扣除发种人10G上传流量。\r\n7 部分人发种可以直接进入种子列表而不进入种子审核区。此时如果发种违规，包括重复种子介绍不完善等，\r\n   我们会封禁你的发种直接进入种子列表的权限，以后再发种将进入种子审核区。\r\n8 请勿发布各种枪版影片。');
INSERT INTO `cgbt_category` VALUES (2,'剧集','tv','地区,年代,格式,合集,字幕','','wangfeng35\r\nrayfashion\r\nliu751078762\r\ndemiris\r\nShunIbuki\r\nhl402425661\r\ngr1990\r\ncbgmu\r\nw08223031\r\nohperhaps\r\nqiantiandunzi\r\nmimengtianshi\r\nlouiszz\r\nlv615\r\nfanhaokun\r\nliuxuan111234\r\nlovebeyond37\r\nssuperman','tv.png',314,'笑傲江湖\r\n吸血鬼日记\r\n生活大爆炸\r\n行尸走肉\r\n斯巴达克斯\r\n冰与火之歌\r\n尼基塔\r\n想你\r\n夏洛克\r\n楚汉传奇\r\n隋唐英雄\r\n爱情自有天意\r\n国土安全\r\ngossip girl\r\n麻辣女兵\r\n大太监\r\n美人无泪\r\n隋唐演义\r\n法网狙击\r\n老友记\r\n绿箭侠\r\n清潭洞爱丽丝\r\n唐顿庄园\r\n爱情公寓\r\n终极一班\r\n甄嬛传\r\n',40,'torrents','置顶 \r\n1、经典,热门,优秀,精彩的电视剧合辑(发布组、字幕组必须统一)  \r\n2、 热门,优秀,精彩的电视剧更新剧集(置顶不超过一周)\r\n2、其他大家强烈推荐要求置顶的种子\r\n\r\n置顶要求\r\n1、视频文件完整  清晰 音效好(对部分经典剧可降低要求)\r\n2、合集种子要求完整 并较长时间有种\r\n3、置顶种子要求严格符合剧集版发种格式,且要有剧照或截图,详细介绍等\r\n\r\n推荐\r\n1、未达到置顶标准但是质量很好，或者许多支持的优秀种子.\r\n\r\n删种规则：\r\n1、含色情,暴力,政治等不合适发布的内容.\r\n2、标题、介绍与内容不符\r\n3、种子名称、介绍 or 文件名含污秽言语或广告性质的文字\r\n4、从种子名称看不出内容，完全没有介绍的\r\n5、种子文件损坏,或在附件中故意夹杂木马病毒的\r\n6、重复种子(同一字幕组).\r\n7、含有\"要求置顶\",\"要求精华\"等类似标题 内容.\r\n8、合集内容必须统一版本，如遇特殊情况，如字幕组停止制作，可与版主播种等人联系申请，然后给予特殊处理，文件名和内容必须能表明版本及字幕组，若下外后改动者，直接删种\r\n9、不允许发布flv格式视频\r\n10、不允许发布还在连载剧集的合集\r\n注:勿在种子名称中加入求种信息\r\n\r\n奖励 \r\n1、发布全集合集     +20金币,+999浮云\r\n2、热门,精彩,优秀电视剧更新剧集  +6金币,+666浮云\r\n\r\n惩罚\r\n1、适当允许非恶意的纯表情和纯引用，对于恶意灌水者，第一次警告、多次不听劝阻的封ID一周，多次被封禁不改者，永久封禁\r\n2、攻击站友的删帖封ID一周，多次被封禁不改者，永久封禁\r\n3、在别人种子里发表不和谐言论的（如果不喜欢可以无视，请不要人身攻击）删帖，屡教不改者封ID一周，多次被封禁不改者，永久封禁\r\n4、催片的封禁ID登陆两周，催片三次以上，直接永久封禁\r\n以上封ID时间视情节严重决定，均附加扣分\r\n\r\n注意：\r\n1、不要攻击别人喜欢的偶像，明星,不要攻击别人喜欢的电视剧(不管是何类型).不喜欢请无视 否则等同于攻击他人\r\n2、如有任何疑问请询问斑竹，不要擅自作主\r\n\r\n剧集讨论区版规\r\n\r\n精华\r\n1、原创，转载一般不考虑加精除非是特别精彩的\r\n2、大部分会员都觉得不错的\r\n3、可以自行PM斑竹申请(标题中不允许出现申精等字样)\r\n\r\n发帖\r\n1、严禁发反动，暴力，色情，危害国家利益等违反国家法规的帖子（包括图片）。\r\n2、严格禁止任何形式的广告帖。\r\n3、求种帖请在求种专贴发布\r\n4、鼓励原创。转帖要注明。\r\n\r\n删帖\r\n1、适当允许纯水、纯表请帖、纯引用，\r\n2.字数不足5个汉字的无明确意义的（顶、好、谢、沙发、不错、哦、看看、路过、灌、这样、呵呵、哈哈、3Q、9494、kao、hehe、ding、BG、强、可以、一般、收藏、留名）\r\n3、对于刷版、挖坟等行为，适当允许，但禁止恶意顶老帖\r\n4、攻击他人的、含有脏字，人格侮辱的词语的；\r\n5. 含有“垃圾、骗人、BS、烂片、无聊”等等消极词语，且未说明具体的令人信服的原因的\r\n\r\n管理\r\n1、对于不符合规则的视情况给予扣分警告、删帖，严重者封ID\r\n2、对于发布精彩话题的给予加分奖励\r\n3、对于社区建设等提出好的建议给予奖励\r\n\r\n剧集区主要是大家来下载,观看,讨论自己喜爱的电视剧的，剧集区的宗旨也是提供给大家好看,优秀的电视剧资源，让大家放松、心情愉悦，所以只要大家遵守一些基本的规则，不超过底线都是可以被允许的，希望大家共同营造一个良好的论坛氛围，在这里玩得开心\r\n\r\n种子内回复\r\n\r\n不要挑战管理员的权威！\r\n故意做种限速，一经系统专门用来排查的程序和人工手动观察做种上传行为的，确定后将会被Ban。\r\n恶意做假种，即利用BT软件强制做种并上传假流量（与原种内容其实完全不符）行为的，确定后将会被Ban。\r\n不要攻击任何人和任何作品 \r\n不要回复求字幕等语言');
INSERT INTO `cgbt_category` VALUES (3,'音乐','music','地区,格式,语言,风格,年代,合集','','admin\r\nraistlin\r\nlchkid\r\nlich13\r\nrayfashion\r\nohperhaps\r\n','music.png',315,'周杰伦\r\n林俊杰\r\n我是歌手\r\n演唱会\r\n张学友\r\n周笔畅\r\nwestlife\r\nAKB48\r\n少女时代\r\n老鹰乐队\r\n陈奕迅\r\n五月天\r\n',14,'torrents','删除种子原则：\r\n1.禁止发布单首歌曲，正规发行的单曲或配信除外。\r\n2.命名不规范，有介绍的将PM改正，3小时内不改的删除\r\n3.命名不规范，没有介绍的直接删除，并扣除1G上传流量\r\n4.连续发布3个以上不规范的种子（包括3个）的，删除种子，并处以封禁1周+扣除10G上传流量惩罚\r\n5.任何192kbps以下码率的MP3种子以及任何wma格式种子\r\n\r\n一、分区种子描述规则：\r\n\r\n1、帖子要求：\r\n\r\n  （1）种子描述参考置顶种子的规范来写，包括\r\n            专辑信息，如：\r\n\r\n            专辑名称：101\r\n            专辑歌手：Keren Ann \r\n            唱片公司：EMI France\r\n            发行时间：2011-02-28\r\n            风格流派：Indie Pop / Folk Pop\r\n            资源码率：320 KBPS\r\n          \r\n            专辑简介，即专辑附带的介绍文案；专辑封面图片；专辑曲目。\r\n\r\n  （2）添加相关图片:专辑带封面图片，非专辑可以带歌手图片\r\n  （3）添加发布乐手的相关介绍、从艺经历或获奖情况等（鼓励）\r\n  （4）添加自己的听歌感受（鼓励）\r\n\r\n2.专辑资料查找：\r\n\r\n专辑图片：（1）http://www.1ting.com/（专辑介绍也有，不过复制编写比较麻烦）\r\n                   （2）Google图片搜索，很容易找到大的封面图片。\r\n其他：VeryCD：http://www.verycd.com\r\n\r\n关于无损、mv、古典资源专区的一点说明：\r\n\r\n1、无损专区：常见的无损格式为wav、ape、flac以及IPOD专用的格式。此专区无分华语欧美日韩。古典无损音乐发布在古典区，其他无损音乐全部都发在无损区。\r\n2、mv专区：同样不分华语mv、欧美mv、日韩mv，是视频格式音乐相关便放mv区。\r\n3、古典资源区：纯音乐、背景音乐、原声音乐及New Age等按分类发到相应的“华语 欧美 日韩”区，没有对应分类一律发布在 其他-MP3 区，请勿发布在古典音乐区。古典音乐无损格式较多，所以无损古典音乐不用发到无损区，直接发在古典区。\r\n\r\n二、评分原则：\r\n\r\n1.最新发行的专辑，演唱会，专辑MV +20\r\n2.最新受关注单曲，高品质MV +10\r\n（新资源下载查看：http://cgbt.cn/forums/viewthread.php?tid=34850）\r\n3.发布歌手个人合辑, 资源质量较高, 基本符合置顶要求 +20\r\n4.发布高质量的个人评价(不限资源区和讨论区) +20（会视情况酌情加分）\r\n5.无介绍或者命名不符的种子不给予评分。\r\n\r\n三、删种(帖)原则：\r\n\r\n1.种子：\r\n\r\n  （1）发布政治、色情、污秽内容，或在文件中故意夹杂木马病毒的\r\n  （2）发布种子或其名称夹带恶意欺骗、夸张、宣传、广告等内容(严禁出现跪求，裸求之类语言)\r\n  （3）发布内容与种子标题不符或种子命名不符合命名要求的\r\n  （4）种子名称、文件名或种子信息介绍含污秽言语\r\n  （5）发布的种子文件已损坏\r\n  （6）含太多惊叹号或其他为了吸引眼球的文字或符号、噱头\r\n  （7）含求种信息\r\n  （8）重复内容，优先保留高码率\r\n  （9）从种子名称看不出内容，而且没有介绍的\r\n  \r\n2.帖子：\r\n\r\n  （1）发布纯水帖的：\r\n    纯表请帖，纯引用，字数不足5个汉字的无明确意义的（如：顶、好、谢、沙发、不错、哦、看看、路过、灌、这样、呵呵、哈哈、3Q、9494、kao、hehe、ding、BG、强、可以、一般、收藏、留名）\r\n\r\n  （2）发布含有令人不快语句的：\r\n    含有脏字，人格侮辱的词语的；\r\n    含有“垃圾、骗人、BS”等等消极词语，且未说明具体的令人信服的原因的\r\n\r\n特别说明：关于音乐版杂锦合集种子发布的说明\r\n\r\n这里所说的合集是指多歌手的合集，也就是一般常说的Various Artists，而不包括单一歌手的专辑合集。\r\n\r\n具体命名和内容规定如下：\r\n种子分类：按歌手的国家来分类。如果是多国家，多种类的，此处选大类音乐\r\n时间：如果有明确的时间跨度就按时间跨度来，如1995-2000；如果不清楚就留空\r\n歌手：一律填写Various Artists\r\n专辑名称：统一为杂锦合集\r\n格式：按发布的音乐格式来，如mp3等\r\n码率：统一的就按mp3的码率来。不统一或无损格式的留空\r\n\r\n举例说明：[华语][2000-2007][Various Artists][杂锦合集][mp3][192Kbps]\r\n\r\n内容：要求有曲目列表，可以使用tree命令，或添加文件夹截图，能看清曲目名称就行。具体方法如下\r\n运行-cmd\r\n目标目录下   tree/f>*.txt  这个是显示目标目录文件名的命令，就能显示文件夹下所有文件的目录树\r\n另附详细规则介绍链接：\r\n欧美资源区版规\r\n日韩资源区版规\r\n华语资源区版规\r\n\r\n种子置顶及推荐规则\r\n一、分区种子置顶原则：\r\n\r\n1.置顶内容：\r\n\r\n  （1）最新发行的音乐专辑、演唱会及DVD的专辑MV（想要下载最新的音乐资源，查看：http://cgbt.cn/forums/viewthread.php?tid=34850）\r\n  （2）某个歌手或乐队的个人歌曲、MV合辑\r\n  （3）其他大家强烈推荐要求置顶的种子\r\n\r\n2.置顶种子的要求（请仔细查看）：\r\n\r\n  （1）置顶种子必须标题规范，有详细的内容介绍\r\n  （2）对于新专辑，必须要满足：\r\n① 0day发布专辑要求为tosk或coc小组发布，歌曲码率为192kbpsVBR，要求保留专辑封面和nfo文件，删除其余无关的文件，专辑曲目文件名改为中文\r\n② 有相应的专辑封面（封面可以到http://www.1ting.com/上找到）\r\n③ 有相应的专辑详细介绍\r\n④ 文件ID3信息最好修改统一，歌手和歌曲名不得留有外网网站的残留信息（用千千查看歌曲信息即可修改）\r\n⑤ 专辑文件夹里请删除类似网站url等无关文件\r\n  （3）对于演唱会和专辑MV，需要要求品质，最好是DVDRip版本的，种子介绍里最好用暴风截张缩略图\r\n  （4）对于个人合辑，要达到置顶必须满足：\r\n① 歌曲码率要求在192kbps以上，尽量能统一码率，每张专辑文件夹名字格式要统一（比如不要有的专辑有加发行时间，有的没有，有的专辑有书名号，有的又没有）\r\n② 有相应的歌手资料介绍，最好能有各专辑的详细介绍和封面并附上一张歌手照片\r\n③ 可以添加文件树形列表，用FtpList软件可以很方便的做到\r\n④ 文件ID3信息最好修改统一，歌手和歌曲名不得留有外网网站的残留信息（用千千查看歌曲信息即可修改）\r\n⑤ 专辑文件夹里请删除类似网站url等无关文件\r\n  （参照帖：[2004 - 2007][高音质MP3合辑系列之09][张韶涵][VBR][320kbps]）\r\n  （5）关于音乐资源置顶的时限和数量，原则上音乐区置顶数量不超过4个，当置顶数超过4个，将会合理撤下以前的置顶种子。\r\n置顶时间：热门新专辑置顶2周，精品合辑置顶2 ~ 4周，到期解除置顶，改为推荐。新专辑到期后一般不会改为推荐。\r\n\r\n3.置顶程序\r\n\r\n    符合上述规定的专辑，版主直接置顶，加精，推荐或加高亮。（对于新专辑，关键看重是否更好得满足置顶条件，而并非看重是否是第一个发布的）\r\n    认为自己发布种子符合以上置顶规定的，发信向斑竹提出申请，我们认为适合的，会及时作出相关处理。\r\n\r\n4.推荐原则\r\n\r\n  （1）新专辑或者值得回味的老专辑的APE版本\r\n  （2）带有总结归类性质的种子\r\n  （3）精品合辑在解除置顶后\r\n  （4）推荐时间为一个月。\r\n二、高亮显示原则：\r\n\r\n1.国内区置顶种子加粗蓝色，推荐普通蓝色。\r\n2.欧美区置顶种子加粗紫色，推荐普通紫色。\r\n3.日韩区置顶种子加粗绿色，推荐普通绿色。\r\n4.古典区置顶种子加粗黄色，推荐普通黄色。\r\n5.无损区、MV及演唱会由所属类别同以上4条。\r\n6.其他高亮由版主商定。\r\n\r\n特别说明：关于无损区\r\n\r\n1、只允许发布WAV、APE和FLAC 等格式的音乐。 （目前无损压缩格式有APE、FLAC、WavPack、LPAC、WMALossless、AppleLossless、TTA、Tak、La、OptimFROG、Shorten，而常见的、主流的无损压缩格式目前有APE、FLAC、TTA、TAK。）\r\n无损码率一律填其他，具体命名方式见上文说明。\r\n      对不符合规定的种子将做删除和移动处理。\r\n2、置顶与推荐：原则上一般只会对合集进行置顶与推荐操作，合集置顶最长不超过3周。\r\n      置顶的合集种子解除后会改为推荐，一般专辑解除后不做处理。\r\n置顶推荐合集要求对于cue文件进行编辑，要求直接能用foobar打开cue文件播放，并且能正常显示歌曲列表，没编辑者将不给置顶，原则上要求每张专辑一个文件夹而且要有相应的intro文档和cover图片。\r\n3、关于短期置顶与推荐的说明：（非合集）\r\n      新发无损专辑会酌情考虑置顶7-14天，具体由各分区版主商议处理，过期撤去置顶。\r\n      新发无损专辑要求在确认频谱无误，确为无损文件后才会置顶。\r\n      精选无损专辑短推荐3天，过期撤去推荐。\r\n4、高亮显示：所有置顶及推荐种子均按种子所属的类别进行高亮处理（国内、欧美等）。\r\n5、为鼓励发布精品无损音乐，将对发布的所有无损专辑由版主加10-50分不等。');
INSERT INTO `cgbt_category` VALUES (4,'动漫','comic','类型,字幕组,格式,合集','','admin\r\ncryscisn\r\nwestghost\r\nnickyang','comic.png',316,'海贼王\r\n银魂\r\n死神\r\n柯南\r\n火影忍者\r\n秦时明月\r\n妖精的尾巴\r\n龙珠\r\n圣斗士\r\nEVA\r\n高达\r\n灌篮高手',13,'torrents','关于禁发种子\r\n\r\n一、禁止发布涉及政治、色情、血腥暴力的资源。\r\n18X：包括H动画，H漫画，H图片；非全年龄的同人志，画集；其它不适合发布的周边物。 擦边球过度的(解释权在本区版主)，有漏点的砍；也存在TV版发布无碍，但DVD/BD版修正画面后禁止发布的情况，拿捏不准的请先PM本区版主\r\n\r\n二、禁止发布FLV，HLV，F4V，XV格式及其他需要特定播放器播放的资源。\r\n\r\n三、禁止发布无压制信息或源文件名称被修改的资源。\r\n尤其是文件名称仅含有集数或中文名称的资源；动画资源必须与公网原种保持一致，请大家通过恰当渠道下载正规字幕组发布的资源，不要发布其他网站修改文件信息以及自己二压过的资源，比如“红旅首发”、“ZERO动漫”等\r\n\r\n四、禁止发布分辨率过低的资源，分辨率在480P以下的资源将视情况删除。\r\n个别新番先行版或年代久远难以搜集的动画可以发布480P以下的资源\r\n\r\n五、禁止发布多个字幕组、多种分辨率、多种格式的合集（百集以上长篇视具体情况而定）\r\n\r\n六、禁止发布连续单集动画刷版。\r\n未完结新番动画，如果某个字幕组某个格式的连载此前的话数均有人发布过，则完结之前禁发短期合集，特别是还剩一两话完结的时候；如果短期合集中的内容均无人发布过（如赶上假期或者冷门动画或者是非热门字幕组的作品），如果距离完结还有较长的时间，可以将此前话数以小合集的形式发布\r\n\r\n七、禁止发布压缩包形式的动画合集，个人随意打包的音乐合集、漫画合集。\r\n动画合集请发文件夹，音乐合集请发专辑，漫画请勿发布单话连载资源\r\n\r\n八、猪猪字幕组作品仅允许发布《死神》、《火影忍者》每周更新的连载动画\r\n禁止发布猪猪字幕组任何作品的合集\r\n\r\n九、禁发动画列表（发死亡笔记直接封）\r\n\r\n死亡笔记        一骑当千系列        魔乳秘剑帖        圣痕炼金士        间之楔\r\n妖精的旋律        R15        零度战姬        缘之空        AIKA-16\r\nToLOVERu OVA        KissxSis OAD        我的狐仙女友        loli时间        High School DxD\r\n女王之剑/女王之刃        变态生理研讨会        happy tree friends        最后大魔王\r\nat-x放送版        无赖勇者的鬼畜美学\r\n记忆女神的女儿        美少女死神还我H之魂        百花缭乱 无修版\r\n每季度更新\r\n\r\n违规者，初次警告并删种，视情况扣除上传流量。如若再犯，封号封IP3天起。\r\n\r\n关于置顶和推荐\r\n\r\n置顶：\r\n\r\n1. 『火影忍者』(jumpcn)，『死神』(jumpcn)，『海贼王』(OPFANS)这三部动画观众极广，速度优先，为方便寻找，最新连载长期置顶。\r\n\r\n2. 每一季新番开播都会挑选出1-3部优秀的动画置顶 (选取相对质量较好的字幕统一更新)。\r\n\r\n3. 最新的优秀的动画电影，OVA，版众原创物置顶。\r\n\r\n4. 十分难能可贵的合集，如高达全集。\r\n\r\n推荐：\r\n\r\n1. 较难收集的经典动画的合集。(至少是DVDRIP或BDRIP)\r\n\r\n2. 某些很有意义的周边。(如NicoNico人气歌曲合集)\r\n\r\n注：以前加过推荐的片子短期内二次发布的话，不再进行推荐；无中文字幕不推荐。\r\n\r\n关于种子命名及描述\r\n\r\n种子命名原则：\r\n\r\n1. 不得使用全角符号、特殊符号抢眼球。\r\n\r\n2. 不可在标题中进行过度的修饰和宣传。\r\n\r\n3. 不得使用不文明字眼；名字与说明中谢绝求种行为。\r\n\r\n4. 命名内容和种子内容相符。\r\n\r\n种子描述原则：\r\n\r\n1. 谢绝广告，求种，剧透，令大众不快的题外话。\r\n\r\n2. 种子描述中最好能对文件作简要介绍，特别是每部新番的第一集要有相应的介绍和图片。\r\n\r\n3. 可以加入个人发挥的成分，但要用词文明，同时要真实，如发现出入较大的，会修改并告知。\r\n\r\n4. 欢迎提供原创个人观感(用词文明)与大家交流。\r\n\r\n种子命名格式（红字为必填部分）\r\n\r\n动画：\r\n\r\n[字幕组名称][罗马音名称/英文名称][中文名称][集数][相关属性][分辨率][格式]\r\n\r\n注：[1] 简繁体一般不需特别标明，若发布相同字幕组、相同格式的简繁两个版本，可以标明GB/BIG5\r\n      [2] 编码方式(如x264)、其他附带资源请不要写在标题，有需要可在帖内种子介绍中注明\r\n\r\n例1\r\n[SumiSora][Oreshura][我女友与青梅竹马的惨烈修罗场][01][GB][一月新番][720P][MP4]\r\n\r\n例2\r\n[CASO&I.G][Higurashi Kira][寒蝉鸣泣之时 煌][OVA][03][GB][720P][MP4]\r\n\r\n例3\r\n[POPGO][Hotarubi no Mori e][萤火之森][Moive+sp][BDrip][1080p][10-bits][MKV]\r\n\r\n注：有多个中文译名的话，请尽量选取比较大众的一个。如有两个译名都要写的话，如以下格式即可：\r\n\r\n例4\r\n[CASO][Tamako Market][玉子市场/玉子超市][08][720P][MP4]\r\n\r\n完结动画：\r\n\r\n命名原则同新番动画，关于集数的描述可用此形式 ：TV 01-XX Fin +SP/OVA/MOVIE 或 Vol.1-Vol.X Fin\r\n\r\n例1\r\n[CASO&SumiSora][Persona4 the ANIMATION][女神异闻录4][TV 01-26 Fin+SP+MOVIE][BDRIP][1080P][MKV]\r\n\r\n例2\r\n[HKG][Toaru Majutsu no Index][魔法禁书目录][Vol.1-Vol.8Fin][BDRIP][1080p][MKV]\r\n\r\n漫画：\r\n\r\n[作者][中文/日文名称][卷数][完结否][版本属性][类型][格式]\r\n\r\n例1\r\n[青山刚昌][名侦探柯南][第01-50卷][未完结][台版][小学馆][推理][中文][ZIP]\r\n\r\n例2\r\n[高桥留美子][めぞん一刻][相聚一刻][第01-10卷][完结][ワイド版][爱情][小学馆][日文][JPG]\r\n\r\n音乐：\r\n\r\n[EAC][发售日期][专辑类型][专辑艺人/发行公司][类别][专辑名][品番][文件类型]\r\n\r\n1. 动画音乐请发专辑，欢迎一切与ACG相关的音乐资源的发布，无论是OST乃至同人。\r\n2. 如该音乐资源为EAC抓取,抬头需要加[EAC]。\r\n3. 音乐文件的后缀名写清楚，APE/FLAC/TAK/MP3等。\r\n4. 如果是MP3请写清楚码率。\r\n5. 新出的音乐请写清楚发售日期。\r\n6. 同人音乐请标明出处。\r\n\r\n例1\r\n[EAC][111019][TVアニメ「Fate/Zero」EDテーマ 「MEMORIA」/藍井エイル][FLAC+CUE+BK][RAR]\r\n\r\n例2\r\n[EAC][Aria Music Collection][水星领航员音乐合集][58CD][TTA+CUE+LOG+BK]\r\n\r\n例3\r\n[070817][C72][ウサギキノコ]「たぶん青春」／茶太][320K+BK][MP3]\r\n\r\n例4\r\n[EAC][080227][ALBUM][田村ゆかり][十六夜の月、カナリアの恋][TTA+CUE+LOG+BK]\r\n\r\n周边：\r\n\r\n随意，描述清楚即可，如有不妥版主会及时修改。');
INSERT INTO `cgbt_category` VALUES (5,'游戏','game','平台,类型,地区,公司,年代','','admin\r\nw08223031\r\ncgcyx\r\nohperhaps\r\nzhaojunhao','game.png',317,'dota\r\n英雄联盟\r\nlol\r\n电玩快打\r\n魔兽争霸\r\n仙剑奇侠\r\n古剑奇谭\r\n轩辕剑\r\n使命召唤\r\nfifa\r\n实况足球\r\n极品飞车\r\nNBA\r\n2K\r\n反恐精英\r\n',14,'torrents','[b][size=16px][color=#ff0000]具体可查看链接：[url]http://zhixing.bjtu.edu.cn/thread-550462-1-1.html[/url][/size][/color][/b]\r\n注意：\r\n发种时必须填写平台、中文名、文件格式以及游戏语言，不符合要求的种子一律删除\r\n命名不完整、填写错误或无介绍的种子原则上不推荐、不置顶\r\n贴内出现求置顶、求推荐字样的一律不予置顶、推荐，如有置顶、推荐要求请pm版主。\r\n\r\n各分类发种细则如下：（点击文字快捷跳到该楼）\r\n1.PC游戏发种细则（沙发）\r\n2.网络游戏发种细则（板凳）\r\n3.游戏视频发布规则：（地板）\r\n\r\n发种格式\r\n例：\r\nPC游戏： \r\n[PC][Empire Total War Special Forces Edition][帝国全面战争][即时战略游戏][SEGA][光盘镜像][英文]\r\n网游：\r\n[网游][DNF][地下城与勇士-SEASON2 V5.014完整包-2011.6.14][动作角色扮演游戏][腾讯][完整安装包][简体中文]\r\n[网游][DNF][地下城与勇士（补丁）-ACT13_V5.015-2011.6.23][动作角色扮演游戏][腾讯][补丁][简体中文]\r\nPSP游戏：\r\n[PSP][Monster Hunter Portable 2nd G][怪物猎人携带版2G][动作游戏][CAPCOM & CG汉化组][ISO][繁体汉化]\r\n其他平台游戏：\r\n[其他][NDS/L][DRAGON QUEST V][勇者斗恶龙V完美汉化版][角色扮演][SQUARE·ENIX & ACG汉化组][NDS][简体汉化]\r\n游戏视频：\r\n[其他][视频][DOTA][7l vs ps vigoss的船长][FLV]\r\n游戏音乐：\r\n[其他][音乐][Final Fantasy][最终幻想音乐合集][APE]\r\n游戏模拟器：\r\n[PC][FC模拟器][FC游戏合集][RAR]\r\n[PC][街机模拟器][拳皇游戏合集][RAR]\r\n其他：\r\n[其他][恶魔城终极研究之--塔之书][RAR]\r\n[其他][使命召唤4升级补丁1.4-1.5][安装包]\r\n\r\n1.平台(必填项)\r\nPC游戏\r\n种子分类：游戏-PC & 平台：PC\r\n网游\r\n种子分类：游戏-网游&平台：PC\r\nPSP游戏\r\n种子分类：游戏-PSP & 平台：PSP\r\n其他平台游戏\r\n种子分类：游戏-其他 & 平台：填写具体平台\r\n视频与音乐\r\n种子分类：游戏-其他 & 平台：填写视频/音乐\r\n模拟器\r\n种子分类：游戏-PC & 平台：FC模拟器/街机模拟器\r\n\r\n2.英文名\r\n游戏填写英文名，确实找不着英文名可以不填，根据需要可以填写日文名\r\n视频与音乐可以不填，建议填写\r\n\r\n3.中文名(必填项)\r\n填写常见中文名\r\n禁止出现广告、个人ID、“求种”、“求置顶”等词汇\r\n视频与音乐请填写相关内容\r\n\r\n4.游戏类型、发行公司 \r\nPCGAME：http://www.gamespot.com.cn/ \r\nTVGAME：http://www.levelup.cn/GameSearch/  \r\n汉化版可以填写“制作公司&汉化组” \r\n视频与音乐可以不填\r\n   \r\n5.文件格式(必填项)\r\n请正确填写文件格式，如果不会填写请按以下方法填写\r\nPCGAME \r\n光盘镜像：用Daemon等虚拟光驱软件载入安装 \r\n安装包：需要进行安装的安装程序或压缩包\r\n硬盘版：下载即玩或解压后直接可运行\r\n\r\n网游\r\n完整安装包：从官网下载的安装包（此格式是为了与PCGAME区分）\r\n补丁：从官网下载升级游戏所用\r\n\r\nTVGAME \r\nPSP游戏：填写ISO/CSO \r\nNDS游戏：填写NDS \r\nGBA游戏：填写GBA \r\nPS游戏：PSP模拟填写PBP，PC模拟填写光盘镜像 \r\n其余平台根据具体情况填写\r\n\r\n视频：自定义填写视频格式 \r\n音乐：自定义填写音乐格式\r\n其他：自定义填写相应文件格式\r\n\r\n6.游戏简介\r\nPCGAME：http://www.gamespot.com.cn/ \r\nTVGAME：http://www.levelup.cn/GameSearch/  \r\n建议写清楚安装方法，如果不会安装或者无法安装，请短信询问发种者\r\n\r\n7.其他注意事项\r\n1、网络游戏、游戏升级补丁、PSP破解包等必须写清版本号，否则一律删除\r\n\r\n置顶原则\r\n游戏新品大作，优秀游戏及相关合集，游戏相关优秀原创内容 \r\n大作：著名工作组制作或著名发行商发行、著名游戏续作、群众基础好期待度高的游戏 \r\n新品：指在0day release一周内发布到晨光的游戏 \r\n澄清一下，并不是非常优秀的游戏才会被置顶。置顶的意义更多的体现在新上，让更多的人了解接触新游戏 \r\n   \r\n推荐原则\r\n优秀、经典游戏的优秀版本。合集、原创内容优秀但还不够精华的 \r\n优秀游戏：以gamespot评分8分以上/fami通评分30分以上作为重要评判标准 \r\n优秀版本：汉化版，无损精简优化版，优秀mod改造版\r\n\r\n删种原则 \r\n1. 命名不规范，无简介 \r\n2. 种子内夹杂Torrent等无关文件\r\n3. 涉及色情和政治内容，18X GALGAME \r\n4. 完结的视频系列以后的单集视频\r\n5. 其他版主认为应当删除的资源\r\n\r\n惩罚措施\r\n违反规定酌情扣除0-10G上传流量，封禁发布种子权限1-4周。\r\n\r\n1. 命名不规范                                               1G  3天\r\n2. 恶意重复发种                                             2G  3天\r\n3. 发布内容与命名及介绍不符，即假种                 5G  1周\r\n4. 不服从版主管理，屡次违反规则                       5G  1周\r\n5. 发布内容涉及色情和政治或其他不适合发布内容   10G 4周\r\n\r\n版规最终解释权归游戏区版主所有\r\n\r\n置顶和推荐相关细则\r\n\r\n1.置顶时限为3个星期以上，最长不超过两个月。置顶种子数以6个为基准，最少不低于5个\r\n\r\n2.在种子超过置顶时限或置顶数超过基准较多时，版主以下载量为主要参考标准，结合实际情况，对超过时限和下载量较少种子的置顶进行撤销。由版主把握尺度\r\n\r\n3.相同版本的游戏不重复置顶，只置顶先发的种子，后发的种子算作重复种子。安装版和硬盘版可以同时置顶，撤销置顶先撤安装版\r\n\r\n4.撤销置顶的种子部分根据实际情况给予推荐：原则上合集和下载量超过100的种子给予推荐，其余不予推荐');
INSERT INTO `cgbt_category` VALUES (6,'综艺','zongyi','地区,格式,年代','','admin\r\nssuperman\r\nlchkid\r\nlouiszz\r\nohperhaps\r\nnatalie1990\r\ndandan724','zongyi.png',318,'天天向上\r\n我是歌手\r\n快乐大本营\r\n非诚勿扰\r\n康熙来了\r\n百变大咖秀\r\nrunning man\r\nmusic bank\r\nMusic.Core\r\n中国好声音\r\n跨年\r\n春晚',12,'torrents','[size=18px][color=#ff0000][b]具体可查看链接：[/b][/color][url=http://zhixing.bjtu.edu.cn/thread-219609-1-1.html][b][color=#3333ff]http://zhixing.bjtu.edu.cn/thread-219609-1-1.html\r\n[/color][/b][/url][/size]\r\n[size=24px][color=#cc33cc][b]2013-07 临时更新：禁发任何版本的中国好声音第二季及相关资源，违者版规处理！[/b][/color][/size]\r\n\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]该版规一切解释权归晨光综艺版所有[/color][/size][/font]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]种子基本要求：[/color][/size][/font]\r\n\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]flv、f4V类型视频以及XV等加密格式文件及由FLV、F4V自转格式的视频不允许发布（高清饭拍flv除外），土豆和优酷上下载及二次转码类视频不允许发布\r\n[/color][/size][/font]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]种子介绍基本要求：[/color][/size][/font]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]无文字描述必须有截图[/color][/size][/font]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]如只有文字描述，请不要复制种子名称当描述\r\n[/color][/size][/font][font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#444444]\r\n种子命名须严格按照[url=http://zhixing.bjtu.edu.cn/thread-422236-1-1.html]综艺版命名规则[/url]执行，不符合规范的一律打回审核区！[/color][/size][/font]\r\n\r\n\r\n[font=微软雅黑][size=14px][color=#444444]2013-03-22补充版规：[/color][/size][/font]\r\n[font=微软雅黑][size=14px][color=#444444]种子发布表单新增<类型>一项，发种时需根据资源类型分类表进行选择填写，详情请参照 [/color][/size][/font][url=http://zhixing.bjtu.edu.cn/thread-722176-1-1.html][color=#0000ff]综艺版节目资源分类汇总表[/color][/url][hr/]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#000000]2012-09-02补充版规：[/color][/size][/font]\r\n[align=left][color=#000000][font=微软雅黑][color=#ff00ff]1、日韩节目，四大常规音乐节目及群星大型演出（如dream concert、韩流演唱会、各类颁奖礼、年末歌谣大典等）高清版（720P及以上）live，在质量没有较大差异，只保留发布时间最早的版本。\r\n2、日韩综艺翻译类节目，在清晰度（450P，720P等字样为依据）没有差异的情况下，只保留发布时间最早的版本，个别字幕组翻译质量过差版本在规则之外，由晨光资源组成员决定。\r\n3、发布综艺节目、歌手打歌合集，需要保证合集内容完整、无缺漏，综艺节目仍在连载中需发布合集必须至少以年度为单位。 [/color]\r\n[color=#ff00ff]4、合集发布要求格式必须统一；公网PT所出高清资源制作组必须统一，其余字幕组尽量统一；如节目资源稀有可酌情考虑格式问题[/color][/font][/color][/align]\r\n\r\n[hr/]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#ff0000]资源发布区奖惩制度[/color][/size][/font]\r\n[font=Tahoma, Helvetica, SimSun, sans-serif, Hei, \\\'Microsoft YaHei\\\'][size=14px][color=#0000ff]置顶 [/color][/size][/font]\r\n\r\n[align=left][size=16px][color=black]1.CGTV的节目，1080i的推荐+30%，720p的置顶，具体时间由版主决定。\r\n[/color][/size][size=16px][color=black]2.综艺节目的合集(全年或某季节目)发布组、字幕组、格式统一，推荐或置顶并给予适当下载优惠\r\n3.最近的受关注较高的大型晚会、演唱会、颁奖典礼 (尽量有星光大道和颁奖礼部分)  置顶一段时间后改为推荐[/color][/size][/align][align=left][size=16px][color=black]4.港台节目中，《康熙来了》置顶一天（周五的置顶三天至下周一节目发出后撤销），其余节目由版主决定置顶或推荐精彩、完整清晰的节目[/color][/size][/align][align=left][size=16px][color=black]5日韩节目中，四大现场高清版本置顶至下一期节目发出，其余节目由版主决定置顶或推荐\r\n[/color][/size]\r\n\r\n[color=#0000ff]置顶要求[/color]\r\n[color=#000000]1、视频文件完整  清晰 音效好[/color]\r\n[color=#000000]2、合集种子要求完整 并较长时间有种[/color]\r\n[color=#000000]3、置顶种子要求严格符合综艺区发种格式：  标题  简介  截图[/color]\r\n\r\n[color=#000000][color=#0000ff]推荐\r\n[/color]1、未达到置顶标准的优秀种子[/color]\r\n\r\n[color=#000000][color=#0000ff]发种格式\r\n[/color]1、标题：[日期][综艺名称][主题 (来宾)]  \r\n2、日期格式：[年-月-日]  如[2008-01-01]  时间不清楚可不写、合集可不写\r\n3、节目名称尽量完整、主题尽量简洁。如写来宾  不要太繁琐[/color]\r\n[color=#000000]4、简介： 来宾  内容简介 [/color]\r\n[color=#000000]5、截图： 尽量 [/color]\r\n[color=#000000]6、以往节目请尽量发布合集\r\n[/color]7.合集发布要求格式必须统一；字幕组尽量统一\r\n8.已出合集禁止发布下载后重新发布单集[/align][align=left][color=#0000ff]\r\n标题及种子文件格式参照(点击查看种子文件格式)\r\n[/color]单期如：[font=Tahoma][size=12px][url=http://zhixing.bjtu.edu.cn/thread-720011-1-1.html][大陆][综艺娱乐][2013-03-16][★禁转★快乐大本营.Happy.Camp.20130316.HDTV.720P.x264-CGTV][F(x)合体首秀/宋茜谢娜过招比摔跤][f（x）][720P][/url][/size][/font][font=微软雅黑][color=#333399]\r\n合集如：[/color][/font][url=http://zhixing.bjtu.edu.cn/thread-697156-1-1.html][size=12px][大陆][盛会典礼][2012-12-31][★本资源禁转★2012-2013跨年合集包.Over.Year.HDTV.720P.x264-CGTV][谨以此合集献给各位考研结束的学长学姐][720P][/size][/url][color=#000000][font=微软雅黑][color=#333399]\r\n典礼、晚会如：[/color][/font][/color][url=http://zhixing.bjtu.edu.cn/thread-707128-1-1.html][size=12px][大陆][盛会典礼][2013-02-09][CCTV2013春节联欢晚会.CCTV1.Spring.Festival.Evening.Gala.20130209.HDTV.720P.x264-CGTV][720P][/size][/url][font=微软雅黑][color=#333399][size=10pt]\r\n\r\n删种规则\r\n1、含色情,暴力,政治等不合适发布的内容.\r\n2、标题、介绍与内容不符\r\n3、种子名称、介绍 or 文件名含污秽言语或广告性质的文字\r\n4、从种子名称看不出内容，完全没有介绍的\r\n5、种子文件损坏\r\n6、重复种子.\r\n7、含有\\\"要求置顶\\\",\\\"要求精华\\\"等类似标题 内容.\r\n\r\n惩罚\r\n1、纯表情，纯引用，第一次警告、多次不听劝阻的封ID\r\n2、攻击站友的删帖封ID\r\n3、在别人种子里发表不和谐言论的（如果不喜欢可以无视，请不要人身攻击）删帖，屡教不改者封ID\r\n以上封ID时间视情节严重决定，均附加扣分\r\n\r\n注意\r\n1、不要攻击别人喜欢的偶像，明星不喜欢请无视 否则等同于攻击他人\r\n2、如有任何疑问请询问斑竹，不要擅自作主\r\n[/size][/color][/font][/align]');
INSERT INTO `cgbt_category` VALUES (7,'体育','sports','类型,地区,年代,语言解说','','admin\r\nsss67\r\nwutong19901008\r\nliu751078762\r\ncgcyx\r\nlouiszz\r\nIronway\r\nohperhaps','sports.png',319,'欧洲杯\r\n欧冠\r\nNBA\r\n斯诺克\r\n天下足球\r\n大师赛\r\n费德勒\r\n纳达尔\r\nMLB\r\n',13,'torrents','发种标题格式：[日期][资源名称][解说/语言][格式]\r\n\r\n1、日期格式：[年-月-日]，如[2011-10-01]。可以写一个时间范围，如[1970-2010]、[2011-04]。\r\n2、资源名称：要求在保证信息完整的情况下尽量简洁。如[西甲第7轮 希洪竞技VS巴塞罗那]、[NBA常规赛 热火VS公牛]。\r\n3、解说/语言：要说明资源播出的解说单位和语言，也可填写具体解说员姓名。如[CCTV5 段暄/国语]、[BBC/英语]。 \r\n4、格式：可以选择720p、mkv、avi、rmvb、wmv等。如果发布的资源格式不在所提供的格式选项中，选择“其他”。 \r\n5、不应出现的文字：\r\n    （1）不要使用全角括号，如【 】〖 〗『 』。\r\n    （2）不要出现广告等与种子无关的信息。\r\n    （3）原则上不要在帖子名称中加入个人id等信息，如果为发布者原创等情况，酌情处理。\r\n    （4）尽量不要出现吸引眼球的文字，符号，噱头。\r\n\r\n标题及种子文件格式参照(点击查看种子文件格式)\r\n             [2011-10-02][英超第7轮 热刺vs阿森纳][五星体育/国语][RMVB]\r\n             [2011-05-23][NBA东部决赛G3 热火VS公牛][ESPN/英语][720p]\r\n             [2000-2009][科比得分50+比赛25场合集][多国语言]\r\n\r\n删种规则\r\n1、含色情,暴力,政治等不合适发布的内容.\r\n2、标题、介绍与内容不符\r\n3、种子名称、介绍 or 文件名含污秽言语或广告性质的文字\r\n4、从种子名称看不出内容，完全没有介绍的\r\n5、种子文件损坏\r\n6、重复种子.\r\n7、含有\"要求置顶\",\"要求精华\"等类似标题 内容.\r\n8、清晰度较差、严重影响观感的视频。\r\n\r\n回帖规则及管理条例\r\n\r\n1、对于以下不符合规则的视情况给予扣分警告、删帖，严重者封ID\r\n（1）严禁发反动，暴力，色情，危害国家利益等违反国家法规的帖子（包括图片）。\r\n（2）严格禁止任何形式的广告帖。\r\n（3）严禁催种和侮辱发种者。\r\n（4）严禁对任何球队及球迷进行言语上的攻击和侮辱。\r\n（5）请不要在种子介绍及回帖中透露比赛结果 \r\n2、对于发布优秀资源的给予加分、置顶或推荐等奖励，尤其鼓励新人发种。\r\n\r\n置顶、推荐规则\r\n\r\n发布的体育类种子如满足以下条件则给予置顶操作：\r\n\r\n足球类：\r\n\r\n1 第一时间发布的“强强对话”比赛，这里的强队包括各豪门球队和近来排名靠前表现出众的非豪门球队。联赛比赛置顶时限为一周，欧冠比赛置顶时限为两周，另可视实际情况缩减。\r\n2 第一时间发布的，比赛过程非常精彩，戏剧性强，进球数量较多，或比赛意义重大等具有推荐价值的比赛。联赛比赛置顶时限为一周，欧冠比赛置顶时限为两周，另可视实际情况缩减。\r\n3 受欢迎的足球专题性节目，如天下足球。置顶时限为一周。\r\n4 制作精良、内容精彩、意义重大的精华类视频或合集，经体育版各版主同意后给予置顶操作。如“米兰3000球(米兰体育报最新经典大制作)”。置顶时限为截止到该种子无人做种为止。\r\n5 其他体育版版主或会员们一致认为应该给予置顶操作的足球类视频。\r\n\r\n篮球类：\r\n\r\n1 第一时间发布的当日NBA比赛，比赛有一定观赏价值（注：视频质量较差的除外，如以NBA官方网站为录制源的WMV版本）。置顶期限为2天（强强对话等精彩比赛置顶期限适当延长）。\r\n2 第一时间发布的非NBA比赛，如CBA、欧洲篮球联赛等精彩比赛。置顶期限为2天。\r\n3 制作精良、内容精彩、意义重大的精华类视频或合集，经体育版各版主同意后给予置顶操作。置顶时限为截止到该种子无人做种为止。\r\n4 其他体育版版主或会员们一致认为应该给予置顶操作的篮球类视频。\r\n\r\n其他类：\r\n\r\n1 大型赛事精彩比赛视频，如奥运会、世锦赛等。视频合集置顶时限为截止到该种子无人做种为止；单场比赛置顶时限为半周或一周。\r\n2 第一时间发布的F1、斯诺克、网球、橄榄球、冰球、棒球、飞镖等比赛或视频。置顶时限为半周或一周。\r\n\r\n发布的体育类种子如满足以下条件则给予推荐操作：\r\n\r\n1 高清质量但非第一时间发布的足球、篮球等比赛或视频。\r\n2 某些不能满足置顶条件，但会员们或体育版版主一致认为仍有推荐价值的视频。\r\n\r\n本规则最终解释权由晨光体育版所有。');
INSERT INTO `cgbt_category` VALUES (8,'软件','software','类型,格式','','admin\r\nzxx274213265\r\ncrazyzhc','software.png',320,'matlab\r\noffice\r\nphotoshop\r\nwin 7\r\nCAD\r\nvisio\r\nc++\r\nlinux\r\nsolidworks\r\nMultisim\r\nVisual Studio\r\nlabview\r\n会声会影\r\n',13,'torrents','一.置顶原则\r\n1.所有置顶种子必须标题规范，有详细的种子介绍以及安装、使用方法；\r\n2.正版软件或完美破解版本软件；\r\n3.非中文软件的最新官方简体中文版；\r\n4.版本较新，种类实用齐全的软件合集或光盘；\r\n5.原创软件；\r\n6.稀有的精品软件，或其他专业软件；\r\n7.当前因为毕设等原因大家都迫切需要的软件；\r\n\r\n说明：\r\n1.如果软件质量确实高，但标题或介绍不够规范，经办住简单编辑或提醒发种人修改规范后，即可置顶；\r\n2.对于热心版友辛辛苦苦从外网花费几天时间拖过来的大型软件，考虑置顶或推荐；\r\n3.置顶时间一般不超过1周，一旦发现有同种软件的最新版本的种子发布，应该立即解除置顶；\r\n4.置顶总数以不影响观看版面的视觉效果为准，原则上同一时间不应超过7个；\r\n5.如果某一软件下载人数接近或超过1000，但已经沉底，应该置顶或者重新置顶，方便大家下载，同时也是对发种人的一种奖励；\r\n6.对于达不到置顶条件，但是也相当不错的种子，酌情予以推荐鼓励；\r\n7.不鼓励发布rar格式的文件，因其不便于共享；鼓励将比较大型的单个软件如office 金山词霸 操作系统等制作成iso bin等可以直接刻盘的格式。\r\n\r\n二.删种原则\r\n1.内容危害国家安全及社会稳定，涉及政治、色情，或种子名称简介中包含淫秽挑逗言语等，危及bt生存的种子，已经发现立即删除；\r\n2.文件中夹带病毒木马；\r\n3.种子命名严重违反命名原则；\r\n4.种子所描述内容与实际内容相当不符；\r\n5.已经证明种子损坏等原因导致软件安装失败、没法使用等；\r\n6.涉嫌发布广告的种子；\r\n7.从种子名称看不出种子的用途，并且没有种子介绍。\r\n\r\n说明：\r\n1.当种子符合以上除第一条之外的任意一条时，由版主与发种人沟通，并在标题处重新编辑，提醒已经下载的同学以及尚未下载的同学注意查毒或删除等。如果在一个小时内没有回信，删除！如果再次发同样的种子，上报管理员批准之后，封帐号；\r\n2.同样的软件有更新版本发布时，如果新版软件的种子命名、介绍等方面更规范更详尽，并且两者的发布时间间隔大于5天，或者间隔页数大于等于2页，删除旧种。\r\n\r\n三.种子命名原则\r\n1.种子名称尽量不要超过1行；\r\n2.种子名称在满足条件1的前提下应尽量写的详尽；\r\n3.种子标题中不能出现求种信息，广告等；\r\n一点建议：\r\n现在软件版的命名格式为[软件名称][版本][软件类型]，经过半年来的操作，我感觉下面的格式也许更合适一些：\r\n[软件名称][软件类型][软件格式][个人说明]\r\n说明：\r\n1.因为软件名称一般是习惯包含版本号的，比如迅雷5.1.6.198，如果单单将5.1.6.198放在一个中括号中，有些别扭，建议将版本名称合并到软件名称中；\r\n2.软件类型主要写软件功能等，比如杀毒软件，图像编辑，下载工具等等；\r\n3.软件格式，比如ISO，EXE，RAR，如果是文件夹一般写FILE等；\r\n4.个人说明，在标题不超过一行的前提下，可以在最后这个中括号内发挥一下，写些自己的心得或者对所发软件自己特别希望别人了解的特点或者说明，有点像摘要或关键词。一来可以帮下载的同学对种子的定位有个迅速的把握，二来体现一种个性的张扬（当然不许超出版规的规定），避免千篇一律都是一个模子里出来的样子。。。另外，如果写的太夸张，必然会遭高人拍砖和斑竹的注意，所以最好量力而写，可写可不写。\r\n\r\n四.本分区种子文件描述规则\r\n1.种子描述要有实质内容，尽量按置顶种子规范来写；\r\n2.鼓励以图片形式或者详细的文字来介绍软件及其使用方法；\r\n3.鼓励发表自己使用后的心得；\r\n4.对于软件合集应该写清楚软件清单。\r\n\r\n五、补充\r\n\r\n1、鼓励对种子详细介绍：软件详细介绍的由版主酌情加分，不介绍的不加分但也不处理');
INSERT INTO `cgbt_category` VALUES (9,'学习','study','类型,格式','','admin\r\nlsp2010\r\nPretend\r\nliu751078762\r\nyhzyhzyhz','study.png',321,'考研\r\nVOA\r\n六级\r\nNBC\r\nPPT\r\n托福\r\n新概念\r\nPS\r\njava\r\nCAD\r\nmatlab\r\n新东方\r\nlinux\r\n会声会影\r\n',14,'torrents','总则\r\n\r\n      1、严禁发布政治敏感、反动、暴力、色情以及其他能引起会员严重反感的内容\r\n\r\n      2、严禁发布各种形式的广告及链接\r\n\r\n      3、严禁发布带有病毒或木马程序的资源\r\n\r\n      4、不得发布各种露点或涉及性内容的资源\r\n\r\n      5、发种前请善用搜索，搜索时选择“全部种子”，不得发布重复资源\r\n\r\n************************************************************************************\r\n\r\n一、种子发布规范\r\n\r\n种子命名规范\r\n\r\n[资源类型][资源名称][格式][其它说明]\r\n\r\n1.资源类型（必填）\r\n系统现有的可选资源类型有计算机、外语、考研、课件、开放课程\r\n注：如果不能确定小类可以选择大类\r\n\r\n2.资源名称（必填）\r\n此处填写资源中文名称和英文名称，且至少有其一；中英文名之间用“/”分隔；如有中文名称，则必须填写中文名称，且须为简体中文，如无中文名称，可只填英文名称；资源名称中不允许包含带感情色彩的词语（比如绝对好用、超经典、真的实用等等）\r\n\r\n4.格式（必填）\r\n系统现有的可选格式有MP3、WMV、RMVB、AVI、ISO、CHM、PDF、DOC、HTML、PPT、RAR、其它，如所发资源格式多于一种或未包含在上述格式中，请在“其他说明”一栏注明格式\r\n\r\n5.其他说明（可填）\r\n说明语言要与所发资源内容相关，如资源发行日期、版本、视频课件主讲人等等\r\n\r\n种子命名举例（标准）：\r\n[计算机][编程之美:微软技术面试心得][PDF]\r\n[外语][朗文当代高级词典第5版Longman Dictionary of Contemporary English][ISO][附破解补丁]\r\n[考研][2009文都考研计算机专业课视频][AVI]\r\n[课件][全美经典学习指导系列丛书][ISO]\r\n[开放课程][耶鲁大学开放课程:古希腊历史简介/Open.Yale.course:Introduction.to.Ancient.Greek.History.Chi_Eng.640X360-YYeTs][RMVB][人人影视制作中英双字幕]\r\n\r\n种子介绍要求\r\n\r\n1.必须有与所发资源内容相符的介绍，且种子介绍内容应尽量详细\r\n2.为丰富介绍，鼓励添加与资源相关的图片，图片在发种时须本地上传，请勿直接使用外链图片\r\n\r\n************************************************************************************\r\n\r\n置顶原则\r\n\r\n1.优秀学习资源，资料大合集资源，最新学习资料以及罕见资源等。\r\n2.原创资源（比如会员精心整编）。\r\n3.迫切需求资源（比如考研，计算机等级考试等）。\r\n4.其它资料（会员强烈要求置顶，经版主核实）可酌情置顶。\r\n\r\n推荐原则\r\n\r\n达不到置顶要求的优秀的资源可推荐（由版主酌情决定）。\r\n\r\n注：置顶/推荐的必要条件——种子名称规范、介绍详细。置顶/推荐的资源会依质量情况酌情给予FREE、30%、50%等下载优惠设置。\r\n\r\n************************************************************************************\r\n\r\n二、规则\r\n\r\n删种规则\r\n\r\n1.重复资源（对于无种资源，若想发布请先PM本版版主，版主允许后再发布，擅自发布者按重复资源处理）\r\n2.标题命名不规范或种子介绍过于简单的资源\r\n3.种子介绍所描述内容与种子实际内容不符的资源\r\n4.涉及色情，政治（特别是在一些文档中）等不和谐内容的资源\r\n5.种子内包含.torrent文件的资源迅雷下载会在主文件夹内生成隐藏的种子文件，做种前请删除\r\n6.发种人有义务做种三天或至出5种以上，如果无做种条件请不要发种\r\n\r\n一切不符合总则的资源都会被删除\r\n\r\n奖励规则\r\n\r\n1.发布优秀资源者，置顶资源加20金币和999浮云；推荐资源加15金币和999浮云；其他未置顶或推荐的优秀资源由版主酌情加分。\r\n2.举报违规资源者，经版主核实，每举报一个违规资源，奖励5金币和999浮云。\r\n\r\n惩罚规则\r\n\r\n1.发布涉及敏感话题（特别是政治倾向）、色情内容者封禁发种权限一周，再犯或情节严重者直接封禁BT站权限\r\n2.对他人发布的资源，不喜欢可以无视，回帖发表不和谐言论、攻击发种者，警告+删帖，情节严重者酌情禁言。\r\n3.禁止无意义纯水，违者删帖，情节严重者酌情禁言。\r\n   纯水基本定义如下：\r\n      ①不足5个汉字且无明确意义，如：顶、好、谢谢、沙发、不错、哦、看看、BS、路过、灌、这样、呵呵、哈哈、3Q、9494、kao、hehe、ding、BG、强、可以、一般、收藏、留名之类；\r\n      ②纯数字、纯英文字母、纯标点符号；\r\n      ③纯引用、纯表情、momo、slap、orz等用语；\r\n      ④与主题不相干、自言自语\r\n5.原则上不允许挖坟、恶意顶老帖，（在一些置顶帖、精华帖中参与讨论可以例外）违者警告+删帖，情节严重者酌情禁言。\r\n   挖坟定义为回复15天未有回复的帖子，老帖定义为发布时间30天以上的帖子\r\n6.禁止重复发表或回复相同内容，或本可一次发表完的内容分两次甚至多次发表，禁止三连贴，违者删帖，情节严重者酌情禁言。\r\n7.禁止刷版。刷版定义为短时间内有6个主题帖的最后回复者为同一ID，这个由版主酌情处理。\r\n8.以上情况，多次不改者，永久禁止访问。\r\n9.如果对会员、论坛有意见，可以pm相关会员解决或到意见建议版投诉，以任何方式恶意攻击版主、管理员、论坛者永久禁止访问。');
INSERT INTO `cgbt_category` VALUES (10,'纪录片','documentary','类型','','admin\r\nliu751078762','documentary.png',323,'国家地理\r\n人文地图\r\n超级工厂\r\n',14,'torrents','纪录片版资源格式说明\r\n1.禁发 FLV、ASF等低质量格式资源(可以发到其他 视频区)\r\n2.RMVB格式资源在无同内容资源情况下可以发布，但在出现同内容高质量版本后会被删除\r\n3.其余格式暂不受限 -------------------------------------------------------------------------------------------------------\r\n一、公用规则(CGBT站通用规则，适用任何板块，请大家在首先遵守此大前提)\r\n1. 严禁发布色情，反动，涉及到党和国家领导人，有害社会安定和其他违反国家法律、法规的资源和帖子；\r\n2. 禁止用不文明语言或者图片、文字等对他人进行攻击、诬陷、漫骂和讽刺，禁止发布恶意丑化他人、团体形象的，有煽动性的，有损论坛声誉的帖子；\r\n3. 严禁注册与他人ID、昵称相似并以此假冒原ID发表歪曲事实、捏造假象、诽谤他人的内容，造成恶劣影响；\r\n4. 不得以任何形式（包括各种广告图片，如果图片上标有不良网址，请在贴图前自行处理掉），任何借口（包括发布地址链接）发布任何内容的广告；\r\n5.禁止发表任何设计金钱交易的主题贴，如有交易需要，请到TS相应版块发帖，目前CGBT还没有这项制度，尚未提供该平台；\r\n6. 严禁发表与论坛其它相关管理规定相悖甚至相冲突的言论。\r\n\r\n-------------------------------------------------------------------------------------------------------\r\n二、种子名称命名规则\r\n   种子名称命名说明\r\n\r\n1、标题：\r\n[年份][节目源][中文名][英文名][字幕][格式]\r\n\r\n年份：发行日期，只填写年代即可。\r\n节目源：纪录片制作发行公司或者电视台，如BBC、CCTV、IMAX。\r\n中文名：纪录片中文名。\r\n英文名：如果有英文，请尽量填写。\r\n资源格式：资源格式现有1080p，720p，HDTV，BDrip，DVDRip，Halfcd，MiniSD如还有其它好效果的格式，请选择“其它”，并在介绍中注明。\r\n\r\n标题参照\r\n[2008][BBC][海洋][BBC.Oceans.2008.720p.x264.AC3-CMCT][中英字幕][720P]\r\n\r\n2、简介：\r\n    内要介绍力求详细，最好能上传一张海报或封面；至少一张视频截图及一张情节串联图。请尽量避免简介内容只有一句话或者很少的内容含糊不清，以及没有剧照或者截图的\r\n\r\n3、不应出现的文字：\r\n（1）不要使用全角括号，如【 】〖 〗『 』。\r\n（2）不要出现广告等与种子无关的信息。\r\n（3）原则上不要在帖子名称中加入个人id等信息，如果为发布者原创等情况，酌情处理。\r\n（4）尽量不要出现吸引眼球的文字，符号，噱头。\r\n-------------------------------------------------------------------------------------------------------\r\n\r\n三、置顶推荐原则\r\n   置顶原则\r\n1、资源内容新颖，经典、热门、有一定价值的合集(发布组、字幕组尽量统一)  \r\n2. 热门,优秀,精彩的记录片更新(置顶不超过一周)\r\n2、其他大家强烈推荐要求置顶的种子\r\n\r\n   推荐原则\r\n1、未达到置顶标准但是质量很好，或者许多支持的优秀种子.\r\n2、不具备置顶标准，但又很优秀的资源。\r\n3、经典纪录片合集。\r\n   说明\r\n1、所有置顶和推荐种子必须标题规范\r\n2、视频文件完整、清晰、音效好\r\n3、合集种子要求完整并较长时间有种\r\n4、种子要求严格符合种子命名规则,且要有剧照或截图,详细介绍等\r\n\r\n-------------------------------------------------------------------------------------------------------\r\n\r\n四、删种原则\r\n1、含色情,暴力,政治等不合适发布的内容以及各种违反公用规则的内容\r\n2、种子文件损坏,或在附件中故意夹杂木马病毒的\r\n3、种子命名严重违反命名原则；\r\n4、种子所描述内容与实际内容相当不符；\r\n5、涉嫌发布广告的种子；\r\n6、重复资源。\r\n7、种名称、介绍 or 文件名含污秽言语或广告性质的文字\r\n8、从种子名称看不出内容，完全没有介绍的\r\n9、含有\"要求置顶\",\"要求精华\"等类似标题 内容.\r\n注:勿在种子名称中加入求种信息\r\n\r\n-------------------------------------------------------------------------------------------------------\r\n\r\n五、奖励和惩罚\r\n同电影版奖励和惩罚');
INSERT INTO `cgbt_category` VALUES (11,'其他','other','类型','','glf\r\nlovebeyond37\r\nC751412K\r\nnatalie1990\r\nlchkid\r\nh2s582562632\r\ngoodman\r\nsgdxl007\r\nmemory1234\r\nheifei\r\nchenshuai063\r\nzqx2010cs123\r\nohperhaps\r\nlouiszz\r\nwangfeng35\r\nliu751078762\r\ndemiris\r\nShunIbuki\r\nhl402425661\r\ncbgmu\r\nw08223031\r\nohperhaps\r\nqiantiandunzi\r\nmimengtianshi\r\nlich13\r\nColdny\r\nrayfashion\r\nAltmanshi\r\ncryscisn\r\nwestghost\r\nwgyn02\r\nnickyang\r\nsecondFlight7\r\ngenghaopei\r\nsenshi\r\nw08223031\r\nlaioge\r\nlongyu11123\r\nsky0521\r\nminrui\r\nasdf123456\r\nzhaojunhao\r\nlovelife\r\nliu751078762\r\nssuperman\r\nlfish\r\nwsszyh\r\nlich0000\r\ndandan724\r\nsss67\r\nwutong19901008\r\nyingmingdeng\r\nphillippyq\r\nsalexheng\r\nzxcvbnqwerty\r\nchengming\r\ncgcyx\r\nzxx274213265\r\nxiaobaoxiao\r\nllkpersonal\r\ncrazyzhc\r\nantonywang\r\naixlx\r\nxiao4566\r\nyy253147720\r\nlsp2010\r\nmasiyuan\r\nPretend\r\nswufeliuyi2010\r\ndpmky\r\nIronway\r\nT3Hoar\r\nstriker5417','other.png',322,'有声',14,'torrents','');
INSERT INTO `cgbt_category` VALUES (20,'软件站','softsite','','','Ditto\r\nzxx274213265\r\nC751412K','software.png',555,'win7',10,'softsite','');
INSERT INTO `cgbt_category` VALUES (30,'二手书','book','','','Ditto','study.png',758,'',10,'book','');
DROP TABLE IF EXISTS `cgbt_category_options`;


CREATE TABLE `cgbt_category_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT '',
  `orderid` tinyint(4) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL DEFAULT '',
  `variable_search` varchar(5) NOT NULL,
  `variable` varchar(50) NOT NULL,
  `bind_field` varchar(10) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `options` text NOT NULL,
  `insearch_item` tinyint(4) NOT NULL DEFAULT '0',
  `insearch_keyword` tinyint(4) NOT NULL,
  `indetail` tinyint(4) NOT NULL DEFAULT '0',
  `intitle` tinyint(4) NOT NULL DEFAULT '0',
  `intag` tinyint(4) NOT NULL DEFAULT '0',
  `tip` varchar(255) NOT NULL,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_category_options` VALUES (1,'movie',2,'地区','d','district','district','select','大陆\n香港\n台湾\n日本\n韩国\n美国\n法国\n英国\n印度\n德国\n泰国\n其他',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (2,'movie',8,'类型','t','type','type','selects','剧情\n喜剧\n家庭\n动作\n恐怖\n惊悚\n爱情\n纪录\n犯罪\n科幻\n动画\n音乐\n奇幻\n冒险\n传记\n战争\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (3,'movie',4,'中文名','','name','name','text','',0,1,0,1,0,'电影中文名称',1,1);
INSERT INTO `cgbt_category_options` VALUES (4,'movie',6,'英文名','','name_en','name_en','text','',0,1,0,1,0,'需要包含影片信息，如：Mission.Impossible.I.II.III.BluRay.720p.DTS.x264-OnlyHD',1,1);
INSERT INTO `cgbt_category_options` VALUES (5,'movie',3,'年份','y','year','year','year','',1,0,1,1,1,'电影上映的年份',1,1);
INSERT INTO `cgbt_category_options` VALUES (6,'movie',9,'格式','f','format','format','select','原盘\n1080P\n720P\nBDRip\nMiniBD\nDVDRip\nDVDSCR\nHDTV\nHalfCD\nMiniSD\nR5\nRMVB\n其他',1,0,0,1,0,'非正规高清资源，请在简介中添加三张影片截图',1,1);
INSERT INTO `cgbt_category_options` VALUES (7,'movie',10,'视频编码','i','encoding','opt2','select','VC1\nXviD\nX264\nH264\nMPEG\nRMVB\n其他',0,0,0,0,0,'',0,1);
INSERT INTO `cgbt_category_options` VALUES (8,'movie',11,'字幕','k','subtitle','subtitle','select','无需字幕\n暂无字幕\n英文字幕\n中文字幕\n中英字幕\n帖内英文字幕\n帖内中文字幕\n帖内中英字幕\n其他',0,0,0,1,0,'介绍务必包含海报！',1,1);
INSERT INTO `cgbt_category_options` VALUES (9,'movie',7,'主演','','actor','actor','text','',0,1,0,1,0,'多个用斜杠/隔开，不要包含空格',0,1);
INSERT INTO `cgbt_category_options` VALUES (10,'tv',3,'中文名','','name','name','text','',0,0,0,1,0,'欧美剧请注明季，多个名用/隔开，如：丧/行尸走肉 第二季',1,1);
INSERT INTO `cgbt_category_options` VALUES (11,'tv',4,'英文名','','name_en','name_en','text','',0,0,0,1,0,'欧美剧及高清国产剧必填，如：The.Walking.Dead.S03E13.720p.HDTV.x264-EVOLVE\r\n',0,1);
INSERT INTO `cgbt_category_options` VALUES (12,'tv',5,'集数/集别','','season','season','text','',0,0,1,1,0,'单集：季数SXX，集数EXX如S01E03 合集：SXX全XX集',1,1);
INSERT INTO `cgbt_category_options` VALUES (13,'tv',2,'地区','d','district','district','select','大陆\n港台\n日本\n韩国\n英国\n美国\n泰国\n新加坡\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (14,'tv',1,'年份','y','year','year','year','',1,0,1,1,1,'',0,0);
INSERT INTO `cgbt_category_options` VALUES (15,'tv',7,'格式','f','format','format','select','1080P\n720P\nBDRip\nDVDRip\nHalfCD\nMiniSD\nHR-HDTV\nHDTV\nRMVB\n其他',1,0,0,1,0,'RMVB版一律选RMVB，非高清MP4版选HDTV',1,1);
INSERT INTO `cgbt_category_options` VALUES (16,'tv',6,'主演','','actor','actor','text','',0,1,0,1,0,'国产剧至少填两个主演，用/分隔，不要含有空格及其他符号',0,1);
INSERT INTO `cgbt_category_options` VALUES (17,'tv',8,'字幕','k','subtitle','subtitle','select','暂无字幕\n英文字幕\n中文字幕\n中英字幕\n帖内英文字幕\n帖内中文字幕\n帖内中英字幕\n其他',0,0,0,1,0,'介绍务必包含海报！',1,1);
INSERT INTO `cgbt_category_options` VALUES (18,'music',2,'地区','d','district','district','select','华语\n欧美\n日本\n韩国\n其他',1,0,0,1,0,'请选择资源语种',1,1);
INSERT INTO `cgbt_category_options` VALUES (19,'music',1,'类型','t','type','type','select','专辑\n合集\nMV\n演唱会\n单曲\nEP\n精选集\nLiveCD\n杂集\n原声\n其他',1,0,0,1,0,'请选择资源类型',1,1);
INSERT INTO `cgbt_category_options` VALUES (20,'music',3,'日期','','date','date','date','',0,0,0,1,0,'请填写资源日期,如:2013-03-18，没有具体日期就填写比如2013-00-00',0,1);
INSERT INTO `cgbt_category_options` VALUES (21,'music',4,'歌手','','actor','actor','text','',0,1,0,1,0,'多个歌手以/隔开,群星请填写Various Artists',1,1);
INSERT INTO `cgbt_category_options` VALUES (22,'music',5,'专辑名称','','name','name','text','',0,0,0,1,0,'多个名称以/隔开',1,1);
INSERT INTO `cgbt_category_options` VALUES (23,'music',6,'风格','j','style','opt1','select','流行\n摇滚\n乡村\n爵士\n古典\n原声\n纯音乐\n舞曲\n说唱\n其他',1,0,0,1,0,'请选择资源风格,若不确定可选其他，或留空不填',0,1);
INSERT INTO `cgbt_category_options` VALUES (24,'music',7,'格式','f','format','format','select','MP3\nAAC\nAPE\nFLAC\nWAV\nDVDRip\nVOB\nMKV\nWMV\nMPG\nRMVB\nMP4\nAVI\n其他',1,0,0,1,0,'请选择资源格式',1,1);
INSERT INTO `cgbt_category_options` VALUES (25,'music',8,'码率','i','bitrate','opt2','select','1080P\n720P\nV0\n192kbps\n192k VBR\n256kbps\n320kbps\nNoath VBR\n其他',0,0,0,1,0,'音频请选择音频码率,视频请选择视频分辨率,0day资源请选择V0,若不确定可留空不填',1,1);
INSERT INTO `cgbt_category_options` VALUES (26,'comic',2,'字幕组','','subtitles_group','actor','text','',0,1,0,1,0,'动画字幕组、压制组/漫画作者',0,1);
INSERT INTO `cgbt_category_options` VALUES (27,'comic',3,'英文或罗马音名称','','name_en','name_en','text','',0,0,0,1,0,'',0,1);
INSERT INTO `cgbt_category_options` VALUES (28,'comic',4,'中文名称','','name','name','text','',0,0,0,1,0,'多个中文译名以/隔开',0,1);
INSERT INTO `cgbt_category_options` VALUES (29,'comic',5,'集数/集别','','season','season','text','',0,0,0,1,0,'注明OVA/OAD/MOVIE；合集：TV 01-XX Fin +SP/OVA/MOVIE 或 Vol.1-Vol.X Fin',0,1);
INSERT INTO `cgbt_category_options` VALUES (30,'comic',6,'相关属性1','','memo','memo','text','',0,0,0,1,0,'简繁体、合集请注明DVDRIP、BDRIP、HDTVRIP、DVDISO、BDISO',0,1);
INSERT INTO `cgbt_category_options` VALUES (31,'comic',10,'格式','f','format','format','select','BDMV\nMKV\nMP4\nRMVB\nAVI\nWMV\nISO\nFLAC\nAPE\nMP3\nZIP\nRAR\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (32,'comic',1,'类型','t','type','type','select','新番连载\n完结动画\n剧场版\nOVA\n漫画\n音乐\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (33,'game',1,'平台','d','os','district','select','网游\nPC\nPS\nPS2\nPS3\nXBOX\nXBOX360\nNGC\nwii\nNDS/L\nGBA\nPSP\nDC\nFC模拟器\n街机模拟器\n视频\n其他',1,0,0,1,0,'游戏运行的平台；网游选填网游分类；另外需要原版游戏的补丁、DLC、资料片、手机游戏选填其它',1,1);
INSERT INTO `cgbt_category_options` VALUES (34,'game',2,'中文名','','name','name','text','',0,0,0,1,0,'游戏填写常见中文名，视频与音乐填写相关内容',1,1);
INSERT INTO `cgbt_category_options` VALUES (35,'game',3,'英文名','','name_en','name_en','text','',0,0,0,1,0,'填写游戏规范英文名，如无则填写中文名首字母缩写；视频可填中文，如星际争霸等',1,1);
INSERT INTO `cgbt_category_options` VALUES (36,'game',4,'类型','t','type','type','select','赛车游戏\n格斗游戏\n体育游戏\n战略游戏\n角色扮演\n动作游戏\n冒险游戏\n射击游戏\n桌面游戏\n益智游戏\n养成游戏\n卡片游戏\n即时战略游戏\n第一人称射击游戏\n动作角色扮演游戏\n战略角色扮演游戏\n在线角色扮演\n音乐游戏\n文字AVG\n模拟游戏\n视频\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (37,'game',5,'制作公司','','company','actor','text','',0,1,0,1,0,'注意是制作公司，不是发行公司',1,1);
INSERT INTO `cgbt_category_options` VALUES (38,'game',6,'数据格式','f','format','format','select','镜像\n安装包\n压缩包\n硬盘版\nnds\n补丁\n视频\n音乐\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (39,'game',7,'版本','','version','season','text','',0,0,0,1,0,'视频填超清、高清、标清等；网游注明版本号及客户端或补丁版本；游戏破解、汉化小组，补丁版本',0,1);
INSERT INTO `cgbt_category_options` VALUES (40,'game',8,'语言','k','language','subtitle','selects','简体中文\n繁体中文\n英文\n日文\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (41,'sports',1,'类型','d','district','district','select','足球\n篮球\n网球\n台球\n棒球\nF1\n其他',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (42,'sports',3,'日期','','date','date','date','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (43,'sports',4,'名称','','name','name','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (44,'sports',5,'解说/语言','','language','memo','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (45,'sports',6,'格式','f','format','format','select','720P\n1080P\nAVI\nRMVB\nFLV\nWMV\nMKV\nMP4\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (46,'zongyi',2,'日期','','date','date','date','',0,1,0,1,0,'本期节目播出日期',1,1);
INSERT INTO `cgbt_category_options` VALUES (47,'zongyi',3,'节目名称','','name','name','text','',0,1,0,1,0,'高清节目需附带英文名称，如：快乐大本营.Happy.Camp.20130309.HDTV.720P.x264-CGTV',1,1);
INSERT INTO `cgbt_category_options` VALUES (48,'zongyi',4,'节目内容','','memo','memo','text','本期节目内容简介',0,1,0,1,0,'所有空格请用\'/\'代替/本期节目内容简介/禁止使用节目名称代替',1,1);
INSERT INTO `cgbt_category_options` VALUES (49,'zongyi',0,'地区','d','district','district','select','大陆\n港台\n日韩\n美国\n其他',1,1,0,1,0,'节目播放地区',1,1);
INSERT INTO `cgbt_category_options` VALUES (50,'software',2,'类型','t','type','type','select','系统工具\n安全工具\n网络工具\n媒体工具\n图形图像\n磁盘工具\n办公软件\n游戏娱乐\n硬件工具\n手机数码\n编程开发\n管理软件\n教育教学',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (51,'software',3,'格式','f','format','format','select','光盘镜像\n安装程序\n绿色版\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (52,'software',4,'名称','','name','name','text','',0,0,0,1,0,'中文+外文名称（没有时可只填写一种），不允许含有“绝对好用”等带个人主观感情色彩的词语',1,1);
INSERT INTO `cgbt_category_options` VALUES (53,'software',5,'版本','','version','season','text','',0,0,0,1,0,'软件版本号，未填写将被直接删种',1,1);
INSERT INTO `cgbt_category_options` VALUES (54,'software',6,'其他说明','','memo','memo','text','',0,0,0,1,0,'软件界面语言、是否已破解、更新信息或简要表明软件亮点',0,1);
INSERT INTO `cgbt_category_options` VALUES (55,'study',1,'类型','t','type','type','select','计算机\n外语\n考研\n课件\n开放课程\n人文社会\n自然科学\n理工类\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (56,'study',3,'格式','f','format','format','select','音频\n视频\n文档\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (57,'study',2,'名称','','name','name','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (58,'study',4,'其他说明','','memo','memo','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (59,'documentary',1,'年份','y','year','year','year','',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (60,'documentary',2,'节目源','t','source','type','select','CCTV\nBBC\nPBS\nIMAX\nNHK\n国家地理\n探索频道\n历史频道\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (61,'documentary',3,'中文名','','name','name','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (62,'documentary',4,'英文名','','name_en','name_en','text','',0,0,0,1,0,'需要包含资源信息，如：The Fabric of the Cosmos 2011 720p Blu-ray DTS 2.0 x264-DON',1,1);
INSERT INTO `cgbt_category_options` VALUES (63,'documentary',5,'字幕','k','subtitle','subtitle','select','无需字幕\n暂无字幕\n英文字幕\n中文字幕\n中英字幕\n帖内英文字幕\n帖内中文字幕\n帖内中英字幕\n其他',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (64,'documentary',6,'格式','f','format','format','select','1080P\n1080i\n720P\nBDRip\nDVDRip\nHalfCD\nMiniSD\nRMVB\n其他',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (65,'other',1,'类型','t','type','type','select','视频\nMP3\n图片\n文档\n电子书\n播种机',1,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (66,'other',2,'名称','','name','name','text','',0,0,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (72,'movie',5,'副标题','','memo','memo','text','',0,1,0,1,0,'可以填写影片的特色',0,1);
INSERT INTO `cgbt_category_options` VALUES (67,'zongyi',1,'类型','t','type','type','select','新闻访谈\n盛会典礼\n综艺娱乐\n音乐现场\n其他',1,1,0,1,0,'节目性质：娱乐/现场/晚会',1,1);
INSERT INTO `cgbt_category_options` VALUES (68,'zongyi',5,'嘉宾','','actor','actor','text','',0,1,0,1,0,'cut资源必填',0,1);
INSERT INTO `cgbt_category_options` VALUES (69,'zongyi',6,'格式','f','format','format','select','1080p\n1080i\n720P\n其他',1,1,0,1,0,'youtube 版本一律为‘其他’',1,1);
INSERT INTO `cgbt_category_options` VALUES (73,'game',9,'备注','','memo','memo','text','',0,1,0,1,0,'视频、网游、PC游戏补丁更新日期等',0,1);
INSERT INTO `cgbt_category_options` VALUES (70,'sports',2,'赛事分类','t','type','type','select_input','NBA\n欧冠\n西甲\n意甲\n德甲\n英超\n奥运会\n世界杯\n欧洲杯\n其他',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (71,'movie',1,'IMDB链接','','imdb','imdb','text','',0,1,0,0,0,'格式如：<a target=\'_blank\' href=\'http://www.imdb.com/title/tt0468569/\'>http://www.imdb.com/title/tt0468569/</a>',0,1);
INSERT INTO `cgbt_category_options` VALUES (75,'sports',6,'备注','','memo','text1','text','',0,1,0,1,0,'',0,0);
INSERT INTO `cgbt_category_options` VALUES (74,'comic',9,'分辨率','j','opt1','opt1','select','1080P\n720P\n480P\n其他',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (76,'software',1,'平台','d','district','district','select','Windows\nLinux\nMac\nAndroid\niOS',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (77,'movie',12,'发布组','g','group','opt1','select_input','OnlyHD\nWiKi\nHDW\nNGB\nCMCT\nTLF\nCHD\nYYeTs\nCHDPAD\niHD\nMySilu\nBMDruCHinYaN\n0day\n其他',1,1,0,0,0,'请选择或者输入发布组，不明白的可以选\"其他\".',1,1);
INSERT INTO `cgbt_category_options` VALUES (78,'tv',9,'发布组','g','group','opt1','select_input','NGB\nCGTV\nHDWTV\nCHDTV\nCHDPAD\nHDSTV\nPHD\nBYRTV\nKiSHD\nYYeTs\n0day\n其他',1,1,0,0,0,'请选择或者输入发布组，不明白的可以选\"其他\".',1,1);
INSERT INTO `cgbt_category_options` VALUES (79,'comic',7,'相关属性2','','text1','text1','text','',0,1,0,1,0,'',0,1);
INSERT INTO `cgbt_category_options` VALUES (80,'comic',8,'相关属性3','','text2','text2','text','',0,1,0,1,0,'',0,1);
INSERT INTO `cgbt_category_options` VALUES (81,'book',1,'分类','c','cat','category','select','课程教材\n英语学习\n考研相关\n计算机类\n其他考试\n课外读物\n其他',1,1,0,0,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (82,'book',4,'教材适用学院','s','school','school','select','其他\n电子信息工程学院\n计算机与信息技术学院\n经济管理学院\n交通运输学院\n土木建筑工程学院\n机械与电子控制工程学院\n电气工程学院\n理学院\n人文社会科学学院\n语言与传播学院\n马克思主义学院\n软件学院\n建筑与艺术学院\n远程与继续教育学院\n法学院\n海滨学院',1,1,0,0,0,'教材请选择学院。非教材请选择其他',1,1);
INSERT INTO `cgbt_category_options` VALUES (83,'book',3,'所在位置','m','building','building','select','其他\n12\n15\n16\n18\n19\n2\n20\n22\nJA\nJB\nJC\nXY1\nXY2\nXY3\nXY4\nXY5\nXY6\nXY7\nXY8\n17号宿舍楼\n7号宿舍楼\n9号宿舍楼\n8号宿舍楼\n一号楼\n四号楼\n七号楼\n八号楼\n九号楼\n十号楼\n计算中心\n电气工程楼\n地下工程楼\n光波楼\n机械楼\n思源东楼\n思源楼\n思源西楼\n综合实验楼\n图书馆\n东校区(含家属区)\n主校区家属区',1,0,0,0,0,'选择地点，方便取货',1,1);
INSERT INTO `cgbt_category_options` VALUES (84,'book',2,'书名','','name','name','text','',0,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (85,'book',5,'出版社','','publisher','publisher','text','',0,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (86,'book',6,'版本','','version','version','text','',0,1,0,1,0,'第几版，印刷批次等',0,1);
INSERT INTO `cgbt_category_options` VALUES (87,'book',7,'链接','','link','link','text','',0,0,0,0,0,'填写当当/京东等网上链接，方便查看详细信息',0,1);
INSERT INTO `cgbt_category_options` VALUES (88,'book',8,'备注','','memo','memo','text','',0,1,0,1,0,'其他说明，100个字以内',0,1);
INSERT INTO `cgbt_category_options` VALUES (89,'book',7,'价格','j','price','price','range','0\n1-5\n5-10\n10-20\n20-30\n30-50\n50-80\n80-100\n100-1000',1,0,0,0,0,'请填写整数',1,1);
INSERT INTO `cgbt_category_options` VALUES (90,'book',10,'是否已售出','a','sold','sold','select','否\n是',1,0,0,0,0,'如果已经售出请修改',1,1);
INSERT INTO `cgbt_category_options` VALUES (91,'softsite',1,'平台','d','district','district','select','Windows\nLinux\nMac\nAndroid\niOS\nWP8',1,0,0,0,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (92,'softsite',2,'类型','t','type','category','select','系统工具\n安全工具\n网络工具\n媒体工具\n图形图像\n磁盘工具\n办公软件\n游戏娱乐\n硬件工具\n手机数码\n编程开发\n管理软件\n教育教学\n生活辅助',1,1,0,0,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (93,'softsite',6,'格式','f','format','format','select','安装版\n绿色版\n其他',1,1,0,1,0,'',1,1);
INSERT INTO `cgbt_category_options` VALUES (94,'softsite',4,'名称','','name','name','text','',0,1,0,1,0,'中文+外文名称（没有时可只填写一种），不允许含有“绝对好用”等带个人主观感情色彩的词语',1,1);
INSERT INTO `cgbt_category_options` VALUES (95,'softsite',5,'版本','','version','version','text','',0,1,0,1,0,'软件版本号，未填写将被直接删除',1,1);
INSERT INTO `cgbt_category_options` VALUES (96,'softsite',6,'其他说明','','memo','memo','text','',0,1,0,1,0,'常用软件请注明更新日期\r\n',0,1);
DROP TABLE IF EXISTS `cgbt_privileges`;


CREATE TABLE `cgbt_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_front` tinyint(10) NOT NULL DEFAULT '1' COMMENT '前台权限or后台权限',
  `name` varchar(30) NOT NULL DEFAULT '',
  `name_en` varchar(50) NOT NULL DEFAULT '',
  `orderid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT '',
  `options` text NOT NULL,
  `default_value` varchar(50) NOT NULL,
  `vip_default_value` varchar(50) NOT NULL,
  `admin_default_value` varchar(50) NOT NULL DEFAULT '',
  `tip` varchar(250) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `can_ban` tinyint(4) NOT NULL DEFAULT '0',
  `controller` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_privileges` VALUES (1,1,'发布种子','upload',5,'yes_no','','1','1','1','如果不能发种，则发种页面给提醒',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (2,1,'发种免审核','dont_need_audit',8,'yes_no','','1','1','1','发种不需要审核',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (3,1,'多IP下载','multi_ip_download',19,'yes_no','','1','1','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (4,1,'使用流量条','userbar',7,'yes_no','','1','1','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (5,1,'使用高级流量条','userbar2',11,'yes_no','','0','0','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (6,1,'下载未审核通过的种子','download_unaudit',20,'yes_no','','0','0','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (7,1,'收藏种子数量','favorite_count',2,'text','','10','50','1000','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (8,1,'每天可下载种子数','download_count_everyday',12,'text','','5','50','1000','',0,0,'','');
INSERT INTO `cgbt_privileges` VALUES (9,1,'查看站点日志','view_sitelog',17,'yes_no','','1','1','1','',0,0,'','');
INSERT INTO `cgbt_privileges` VALUES (10,1,'兑换邀请','credits2invite',10,'yes_no','','0','1','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (11,1,'下载热门种子','download_hot_torrents',6,'yes_no','','0','1','1','',0,0,'','');
INSERT INTO `cgbt_privileges` VALUES (12,1,'下载种子','download',4,'yes_no','','1','1','1','',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (13,1,'下载置顶推荐免费种子','download_top',2,'yes_no','','1','1','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (14,1,'登录','login',1,'yes_no','','1','1','1','',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (15,1,'使用RSS','rss',15,'yes_no','','0','1','1','',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (16,1,'发表种子评论','comment',3,'yes_no','','1','1','1','',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (19,1,'种子列表显示下载链接','display_download_link',21,'yes_no','','1','1','1','种子列表是否显示下载链接',1,0,'','');
INSERT INTO `cgbt_privileges` VALUES (20,1,'竞价置顶','price_top',22,'yes_no','','1','1','1','',1,1,'','');
INSERT INTO `cgbt_privileges` VALUES (21,1,'聊天室使用UBB标签','chat_use_ubb',23,'yes_no','','1','1','1','',1,1,'','');
DROP TABLE IF EXISTS `cgbt_soft`;


CREATE TABLE `cgbt_soft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `memo` text NOT NULL,
  `download` int(11) NOT NULL DEFAULT '0',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `link2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

INSERT INTO `cgbt_soft` VALUES (3,'优特(uTorrent)3.32交大专用版，适合交大用户使用','<a href=\'http://zhixing.bjtu.edu.cn/thread-474241-1-1.html\' target=\'_blank\' class=\"bluelink\" > 使用说明2 </a>',228415,'2013-10-05 13:09:22','http://d00.cgbt.cn/utorrent.v3.32.bjtu.install.exe','','http://pan.baidu.com/s/1yyU4W');
INSERT INTO `cgbt_soft` VALUES (4,'优特(uTorrent)3.32 IPv6专用版，适合非交大并且IPv4流量收费的用户使用','',114379,'2013-10-05 13:12:37','http://d00.cgbt.cn/utorrent.v3.32.ipv6.install.exe','','http://pan.baidu.com/s/1qryDx');
INSERT INTO `cgbt_soft` VALUES (5,'优特(uTorrent)3.32 v4v6通用版，适合非交大并且IPv4流量不收费的用户使用','',47677,'2013-10-05 13:13:45','http://d00.cgbt.cn/utorrent.v3.32.ipv4.install.exe','','http://pan.baidu.com/s/1xcZhl');
INSERT INTO `cgbt_soft` VALUES (6,'优特(uTorrent) Linux版','',898,'2013-03-30 17:29:08','http://www.utorrent.com/downloads/linux','','');
INSERT INTO `cgbt_soft` VALUES (7,'优特(uTorrent) Mac版','',774,'2013-03-30 17:29:36','http://www.utorrent.com/downloads/mac','','');
INSERT INTO `cgbt_soft` VALUES (8,'知行PT播种机器人v2.4 build 20131120','<a href=\'http://zhixing.bjtu.edu.cn/thread-11885-1-1.html\' target=\'_blank\'  class=\"bluelink\"> 使用说明1 </a> &nbsp;\r\n<a href=\'http://zhixing.bjtu.edu.cn/thread-474241-1-1.html\' target=\'_blank\' class=\"bluelink\" > 使用说明2 </a>\r\n',1857,'2013-10-12 17:58:30','http://d00.cgbt.cn/知行PT播种机器人v2.4.exe','','http://pan.baidu.com/s/1BZPi8');
INSERT INTO `cgbt_soft` VALUES (20,'谷歌Chrome浏览器','',575,'2013-03-30 22:09:10','http://d00.cgbt.cn/ChromeStandaloneSetup.exe','','');
INSERT INTO `cgbt_soft` VALUES (21,'火狐Firefox浏览器v19','',207,'2013-03-30 23:04:18','http://d00.cgbt.cn/FirefoxSetup19.0.2.exe','','');
INSERT INTO `cgbt_soft` VALUES (1,'6速PT下载软件','支持边下边播的PT下载软件，欢迎测试',6174,'2013-05-13 15:35:53','http://www.v6speed.org/v6Speed/v6Speed_setup.exe','','');
INSERT INTO `cgbt_soft` VALUES (9,'晨光转种机知行PT专用版','<a href=\'http://zhixing.bjtu.edu.cn/thread-803922-1-1.html\' target=\'_blank\'  class=\"bluelink\"> 使用说明 </a> &nbsp;',180,'2013-10-05 13:09:22','http://d00.cgbt.cn/cg_transfer.exe','','http://pan.baidu.com/s/1yyXcK');
INSERT INTO cgbt_setting VALUES ('admins_developer','');
INSERT INTO cgbt_setting VALUES ('admins_deliver','');
INSERT INTO cgbt_setting VALUES ('admins_admins','');
INSERT INTO cgbt_setting VALUES ('admins_trust_ips','');
INSERT INTO cgbt_setting VALUES ('check_invite_code','0');
INSERT INTO cgbt_setting VALUES ('check_forums_user_valid','0');
update cgbt_category set admins = ''
