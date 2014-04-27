/*
 * Check Browser
 * Readme: http://xliar.com/thread-138-1-1.html
 * Author: webmaster@xliar.com, weibo.com/antiliar
 * 2013/01/14 create
 * 2013/01/28 update (checkBrowserEx/html5test)
 * 2013/02/02 update (checkBrowser/avant)
 *
 * Usage:
 * 1. Include this JavaScript file;
 * 2. Create functions denyHandler(), warnHandler(), if necessary create normalHandler();
 * 3. Call checkBrowser(denyHandler, warnHandler, normalHandler) when page body loaded;
 */

// Check browser
function checkBrowser(deny, warn, pass) {
	try {
		if(!document.body) { alert("请修改脚本，在 <body> 后调用检测函数。"); return pass(); }
		if(marked()) return deny();
		var ua = navigator.userAgent;
		if(/(firefox|opera|lbbrowser|qqbrowser|tencenttraveler|bidubrowser|alibrowser|maxthon|se [0-9]\.x|greenbrowser|myie2|theworld|avast|comodo|avant)/ig.test(ua)) return pass();
		if(/(baidu|soso|sogou|youdao|jike|google|bing|msn|yahoo)/ig.test(ua)) return pass();
		if(/(360|qihu)/ig.test(ua)) { mark(); return deny(); } // 360se|360ee|360spider
		if(/chrome/ig.test(ua)) { if(subtitleEnabled() && microdataEnabled() && scopedEnabled()) { mark(); return deny(); } }
		else if(/safari/ig.test(ua)) { return pass(); }
		if(/msie/ig.test(ua) && !addSearchProviderEnabled() && (""+window.external) == "undefined") return checkBrowserEx(deny, warn);
		return pass();
	} catch(e) {
		alert("脚本异常，请通知网站管理员。\n" + e);
	}
}

// Check browser - extend
function checkBrowserEx(deny, warn) {
	var script = document.createElement("script");
	script.setAttribute("type", "text/javascript");
	var head = document.getElementsByTagName('head')[0];
	head.appendChild(script);
	script.onload = script.onreadystatechange = function() {
		if(this.readyState=="loaded") {
			// 2013/01/28
			// var name = new UserAgents().browser.name;
			try { var name = new UserAgents().browser.name; } catch(e) { }
			if(typeof name == "undefined") { warn(); }
			else if(/360 (safe|extreme)/ig.test(name)) { mark(); deny(); } else warn();
			head.removeChild(script);
		}
	}
	// 2013/01/28
	// script.setAttribute("src", "http://html5test.com/scripts/useragents.php");
	script.setAttribute("src", "http://html5test.com/scripts/useragents/detect.js?" + parseInt(Math.random() * 10000000000));
	return 0;
}

function mark() { document.cookie = "helloThisIs360Browser;expires=Wed, 30 Dec 2020 20:20:20 GMT"; }
function marked() { return (document.cookie.indexOf("helloThisIs360Browser") != -1); }

// Subtitle support
function subtitleEnabled() { return "track" in document.createElement("track"); }

// Scoped style element
function scopedEnabled() { return "scoped" in document.createElement("style"); }

// Custom search providers
function addSearchProviderEnabled() { return !!(window.external && typeof window.external.AddSearchProvider != "undefined" && typeof window.external.IsSearchProviderInstalled != "undefined"); }

// Microdata
function microdataEnabled() {
	var div = document.createElement("div");
	div.innerHTML = '<div id="microdataItem" itemscope itemtype="http://example.net/user"><p>My name is <span id="microdataProperty" itemprop="name">Jason</span>.</p></div>';
	document.body.appendChild(div);
	var item = document.getElementById("microdataItem");
	var property = document.getElementById("microdataProperty");
	var bEnabled = true;
	bEnabled = bEnabled && !!("itemValue" in property) && property.itemValue=="Jason";
	bEnabled = bEnabled && !!("properties" in item) && item.properties.name[0].itemValue=="Jason";
	if(!!document.getItems) {
		item = document.getItems("http://example.net/user")[0];
		bEnabled = (bEnabled && item.properties.name[0].itemValue == "Jason");
	}
	document.body.removeChild(div);
	return bEnabled;
}


//////////////////////////////////////////////////////////////////

function deny() {
	// 建议：拒绝访问，或每页中心位置都发出红色警告，推荐浏览器
	alert("本站不支持360浏览器访问，建议使用IE或者谷歌或者火狐浏览器。");
	document.location = "http://pt.zhixing.bjtu.edu.cn/list/soft/";
}

function warn() {	
}

function pass() {
}
