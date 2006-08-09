$.blocpagination = function(containerId) {
	$('#'+containerId).each(function(){
		var id = this.id;
		var group = this;
		$('a.lien_pagination',group).each(function(){
			var url = this.href;
			var reg = new RegExp('#[a-z0-9_]*$','i');
			url = url.replace(reg,'');
			if (url.indexOf("?")>0) url = url+'&';
			else url = url+'?';
			url=url + 'fragment='+id;
			$(this).click(function(){
				$(this.parentNode).before('<div class="ahah_searching_right">&nbsp;</div>');
				$('div#'+id).load(url,'',function(){
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
