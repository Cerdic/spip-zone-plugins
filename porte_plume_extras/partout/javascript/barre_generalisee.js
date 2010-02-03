(function($){
$(document).ready(function(){
	function addbarre(){
	$('.formulaire_spip').not('.formulaire_spip_compact')
		.find('.editer_ps,.editer_descriptif,.editer_chapo')
		.find('textarea')
		.barre_outils('edition');
	}
	addbarre();
	onAjaxLoad(addbarre);
});
})(jQuery);