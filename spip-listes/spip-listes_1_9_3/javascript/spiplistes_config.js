// spiplistes_config.js
// utilise' par _SPIPLISTES_EXEC_CONFIGURE

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

jQuery.fn.toggle_options = function(div_options) {
		if($(this).attr('checked')) {
			$(div_options).show();
		}
		else {
			$(div_options).hide();
		}
};
$(document).ready(function(){
	$('#opt-lien-en-tete-courrier').click( function() { $(this).toggle_options('#div-lien-en-tete-courrier') } );
	$('#opt-ajout-tampon-editeur').click( function() { $(this).toggle_options('#div-ajout-tampon-editeur') } );
});