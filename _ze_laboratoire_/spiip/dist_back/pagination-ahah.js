$.fn.blocpagination = function() {
		var blocfrag = this;
		$('a.lien_pagination',this).each(function(){
			var url = this.href.split('#');
			url[0] += (url[0].indexOf("?")>0 ? '&':'?')+'var_fragment='+blocfrag.id;
			$(this).click(function(){
				$(this.parentNode).before('<div class="ahah_searching_right">&nbsp;</div>');
				$(blocfrag).load(url[0],null,function(){
					window.location.hash = url[1];
					$.apply(blocfrag,$.blocpagination);
				});
				return false;
			});
		});
};


$(document).ready(function(){
	$('div.fragment').each($.blocpagination);
});
