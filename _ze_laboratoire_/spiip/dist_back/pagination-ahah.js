$(document).ready(function(){
	$('div.bloc_ahah_pagination').each(function(){
		var id = this.id;
		var group = this;
		$('a.lien_pagination',group).each(function(){
			var url = this.href;
			var reg = new RegExp('#[a-z0-9_]*$','i');
			url = url.replace(reg,'')+'&ahah_id='+id;
			$(this).click(function(){
				var idtemp = 'temp_'+id;
				//$('#'+id).before("<div id='"+idtemp+"'></div>");
				$('div#'+id).wrap('<div id="'+idtemp+'"></div>');
				$('div#'+idtemp).load(url,'');
				return false;
			});
		});
	});
});