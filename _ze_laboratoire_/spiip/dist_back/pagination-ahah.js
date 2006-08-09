$.blocpagination = function(containerId) {
	$('#'+containerId).each(function(){
		var id = this.id;
		var group = this;
		$('a.lien_pagination',group).each(function(){
			var url = this.href;
			var reg = new RegExp('#[a-z0-9_]*$','i');
			url = url.replace(reg,'')+'&fragment='+id;
			$(this).click(function(){
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