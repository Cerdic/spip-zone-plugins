$.blocpagination = function(containerId) {
	$('#'+containerId).each(function(){
		var id = this.id;
		$('a.lien_pagination',this).each(function(){
			var reg = new RegExp('^(.*)#([a-z0-9_]*)$','i');
			var url = this.href;
			var ancre = url.replace(reg,'$2');
			url = url.replace(reg,'$1');
			if (url.indexOf("?")>0) url = url+'&';
			else url = url+'?';
			url=url + 'fragment='+id;
			$(this).click(function(){
				$(this.parentNode).before('<div class="ahah_searching_right">&nbsp;</div>');
				$('div#'+id).load(url,null,function(){
					window.location.hash = ancre;
					$.blocpagination(id);
				});
				return false;
			});
		});
	});
};


$(document).ready(function(){
	$('div.fragment').each(function(){
		$.blocpagination(this.id);
	});
});
