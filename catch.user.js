// ==UserScript==
// @name           	抓取
// @require        	http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js
// @include        	http://weibo.com/*/fans?page=*
// @grant       	GM_xmlhttpRequest
// ==/UserScript==

$(document).ready(function(){
	setTimeout(function(){
		users = [];
		$(".cnfList li").each(function(i, n){
			user = {};
			info = $(n);
			user.id = info.attr("action-data").split("&")[0].split("=")[1];
			user.name = info.attr("action-data").split("&")[1].split("=")[1];
			user.follow = info.find("a[href='/"+user.id+"/follow']").html();
			user.fans = info.find("a[href='/"+user.id+"/fans']").html();
			user.weibo = info.find(".connect").children("a").last().html();
			//console.log(info.find("a[href='/"+user.id+"/follow']"));
			users.push(user);
		});
		uself = {};
		uself.follow = $(".user_atten").find("[node-type='follow']").html();
		uself.fans = $(".user_atten").find("[node-type='fans']").html();
		uself.weibo = $(".user_atten").find("[node-type='weibo']").html();
		uself.name = $(".pf_name .name").html();
		uself.id = window.location.href.split("/")[3];
		console.log(uself);
		console.log(users);

		GM_xmlhttpRequest({
			method: "POST",
			url: "http://127.0.0.1/hindex/analysis.php",
			data: "uself="+JSON.stringify(uself)+"&users="+JSON.stringify(users),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			onload: function(response) {
				
				href = window.location.href.split("=")[0];
				num = parseInt(window.location.href.split("=")[1]);
				num++;
				setTimeout(function(){
					window.location.href = href + "=" + num;
				},5000);
				//console.log(response);
			}
		});

	}, 1000);
});