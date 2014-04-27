<?php
$now = date("Y-m-d H:i:s");
$site_name = $data['setting']['site_name'];
$app_url = $data['app_url'];
$passkey = $data['user']['passkey'];

$xml = '<?xml version="1.0" ?>';
$xml .= <<<HTML
<rss version="2.0">
<channel>
	<title>$site_name($app_url)</title>
	<link>{$app_url}rss/</link>
	<description>$site_name($app_url)</description>
	<image>
		<url>$app_url/static/images/logo.gif</url>
		<title>$site_name</title>
		<link>$app_url</link>
	</image>
	<pubDate>$now</pubDate>
	<lastBuildDate>$now</lastBuildDate>
HTML;

foreach ($data['torrents'] as $row)
{
	if ($row['isft'])
	{
		continue;
	}		
	$category = $row["category"];
	$title = $row['title'];
	$tid = $row["id"];
	$descr = '';
	$added = date("r", $row["createtime"]);
	$username = $row["username"];
	$title = htmlspecialchars($title);
	$length = $row["size"];
	$size = $row["size_text"];
	$xml .= "\t<item>
		<title>$title ($size)</title>
		<link>{$app_url}torrents/$tid/</link>
		<comments>{$app_url}torrents/$tid/</comments>    
		<description>$descr</description>
		<author>$username</author>
		<pubDate>$added</pubDate>
		<category>$category</category>
		<guid isPermaLink='true'>{$app_url}torrents/$tid/</guid>
		<enclosure url='{$app_url}torrents/$tid/download/?passkey=$passkey' length='$length' type='application/x-bittorrent'></enclosure>
		</item>\n";
}
$xml .= "</channel>\n</rss>";
header("Content-type: text/xml; charset=utf-8");
echo $xml;

?>