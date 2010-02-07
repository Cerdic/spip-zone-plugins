(function($){
$(document).ready(function(){
	function addbarre(){
	$('.formulaire_spip').not('.formulaire_spip_compact')
		.find('.editer_ps,.editer_descriptif,.editer_chapo,.editer_bio')
		.find('textarea')
		.barre_outils('edition');
	$('#text_area')
		.height(($(window).height()/2.2)+'px');
	}
	addbarre();
	onAjaxLoad(addbarre);
});
})(jQuery);