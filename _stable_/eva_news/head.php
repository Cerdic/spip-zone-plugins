<?php
// Samuel Bocharel "PoongalOO" : sam@poongaloo.org
// Equipe EVA-WEB : http://eva-web.edres74.net
// EASYNEWS : http://www.ezjquery.com/cgi-bin/webapp.rb?r=access#
//
//firstname has to be the id of the div containing the news
//secondname is the id of container where you like news to display
//thirdname:  is the id of container where you like news title to display
//fourthname :  is the id of container where images file inside(prev.pause.next)
//playingtitle: set any thing you like (default is Now Playing:)
//nexttitle:set any thing you like (default is Next News:)
//prevtitle:set any thing you like (default is Prev News:)
//newsspeed : auto play pause time (1000=1sec default is 6sec)
//effectis : 0 :fadeIn and Out 1:slideUp and Down 2:Left to Right(default is 0)
//effectspeed: fadeIn/Out or slideUp/Down speed adjustment (1000=1sec default is 600)
//mouseover:false for disable mouse over pause function,default is true
//imagedir: button images path of (prev.gif ,next.gif,pause.gif ....),default is blank
//newscountname: which DOM id to display news counter ex: <span id=test></span>
//disablenewscount: true for disable news counter(default is false)
//isauto:set timer true or false(default is true)
	
	function evanews_insert_head($flux){
	$newsspeed = lire_config('eva_news/pausevitesse');
	$effectis = lire_config('eva_news/effet');
	$effectspeed = lire_config('eva_news/vitesse');
	$playingtitle = "Article :"; 
	$nexttitle = "Suivant: ";
	$prevtitle = "Precedent: ";
	$mouseover = "true";
	$disablenewscount = "false";
	$isauto = "true";
	
	$flux .= '<link rel="stylesheet" TYPE="text/css" HREF="'._DIR_PLUGIN_EVANEWS.'css/easynews.css"></link>';
	//$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_EVANEWS.'javascript/jquery-1.2.3.pack.js"></script>';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_EVANEWS.'javascript/jquery.easynews.js"></script>';
	$flux .= '<script>
		$(document).ready(function(){
			var newsoption1 = {
  			firstname: "mynews",
  			secondname: "showhere",
  			thirdname: "news_display",
  			fourthname: "news_button",
			playingtitle: "'.$playingtitle.'",
			nexttitle: "'.$nexttitle.'",
			prevtitle: "'.$prevtitle.'",
			newsspeed:"'.$newsspeed.'",
			effectis:"'.$effectis.'",
			effectspeed:"'.$effectspeed.'"';
	$flux .= '}
		$.init_news(newsoption1);
		var myoffset=$("#news_button").offset();
		var mytop=myoffset.top-1;
		$("#news_button").css({top:mytop});
		});
		</script>
		';
	return $flux;
	}
?>
