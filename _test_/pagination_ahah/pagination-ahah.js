$.addFade = function() {
	$("*",this).addClass('ahah_surligne_1');
	setTimeout('$(".ahah_surligne_1").removeClass("ahah_surligne_1").addClass("ahah_surligne_2");',500);
	setTimeout('$(".ahah_surligne_2").removeClass("ahah_surligne_2").addClass("ahah_surligne_3");',1000);
	setTimeout('$(".ahah_surligne_3").removeClass("ahah_surligne_3");',1500);
}

$.blocpagination = function() {
		var blocfrag = this;
		$('.pagination',this).each(function(){
			var divpagi=this;
			$('a',this).each(function(){
				var url = this.href.split('#');
				url[0] += (url[0].indexOf("?")>0 ? '&':'?')+'var_fragment='+blocfrag.id;
				$(this).click(function(){
					var placeholder=$('.searching_placeholder',divpagi);
					if (placeholder.length) placeholder.prepend('<div class="ahah_searching_right">&nbsp;</div>');
					else $(divpagi).before('<div class="ahah_searching_right">&nbsp;</div>');
					$(blocfrag).load(url[0],null,function(){
						$.addFade.apply(blocfrag);
						window.location.hash = url[1];
						$.blocpagination.apply(blocfrag);
					});
					return false;
				});
			});
		});
};


$(document).ready(function(){
	$('div.fragment').each($.blocpagination);
});
