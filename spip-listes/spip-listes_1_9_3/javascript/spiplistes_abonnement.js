/**
 * spiplistes_abonnement.js
 * Appele' pour le squelette abonnement
 * 
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */
jQuery(document).ready(function(){
	jQuery('span.listeDescriptif').hide();
	$('ul.liste-des-listes li label').hover(
		function () {
			jQuery('span.listeDescriptif').hide();
			jQuery('#listeDescriptif' + jQuery(this).children('input').val()).fadeIn();
		}, 
		function () {
			jQuery('#listeDescriptif' + jQuery(this).children('input').val()).hide();
		}
	);
});