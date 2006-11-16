$.addFade = function() {
	$("*",this).addClass('ahah_surligne_1');
	setTimeout('$(".ahah_surligne_1").removeClass("ahah_surligne_1").addClass("ahah_surligne_2");',500);
	setTimeout('$(".ahah_surligne_2").removeClass("ahah_surligne_2").addClass("ahah_surligne_3");',1000);
	setTimeout('$(".ahah_surligne_3").removeClass("ahah_surligne_3");',1500);
}

var preloaded_urls = {};

$.blocpagination = function() {
		var blocfrag = this;
		$('.pagination',this).each(function(){
			var divpagi=this;
			$('a',this).each(function(){
				var url = this.href.split('#');
				url[0] += (url[0].indexOf("?")>0 ? '&':'?')+'var_fragment='+blocfrag.id;
				if($(divpagi).is('.preload') && !preloaded_urls[url[0]]) {
					$.ajax({"url":url[0],"success":function(r){preloaded_urls[url[0]]=r;}});
				}
				$(this).click(function(){
					var placeholder=$('.searching_placeholder',blocfrag);
					if (placeholder.length) placeholder.prepend('<div class="ahah_searching_right">&nbsp;</div>');
					else $(divpagi).before('<div class="ahah_searching_right">&nbsp;</div>');
					var on_pagination = function() {
							$.addFade.apply(blocfrag);
							window.location.hash = url[1];
							$.blocpagination.apply(blocfrag);					
					}
					if(preloaded_urls[url[0]]) {
						$(blocfrag).html(preloaded_urls[url[0]]);
						on_pagination();
					} else {
						$(blocfrag).load(url[0],null,on_pagination);
					}
					return false;
				});
			});
		});
};


$(document).ready(function(){
	$('div.fragment').each($.blocpagination);
});
