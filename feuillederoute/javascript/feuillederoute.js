$(document).ready(function(){
	
	// preparer le chargement ajax
	$('body').append('<div class="charger_feuillederoute"></div>');
	
	// afficher/masquer la feuille de route
	$('.btn_feuillederoute').live("click",function(){
		var url_feuillederoute = $(this).attr('href');
		$('.charger_feuillederoute').load(url_feuillederoute,function(){
			$(this).toggle(200);
		});
		return false;
	});
	
});