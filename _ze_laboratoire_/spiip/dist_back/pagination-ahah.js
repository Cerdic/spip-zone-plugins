$.blocpagination = function(containerId) {
	$('#'+containerId).each(function(){
		var id = this.id;
		var group = this;
		$('a.lien_pagination',group).each(function(){
			var url = this.href;
			url = url.replace(new RegExp('#[a-z0-9_]*$','i'),'');
			if (url.indexOf("?")>0) url += '&';
			else url += '?';
			url += 'fragment='+id;
			$(this).click(function(){
				$(this.parentNode).before('<div class="ahah_searching_right">&nbsp;</div>');
				$('div#'+id).load(url,null,function(){
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
