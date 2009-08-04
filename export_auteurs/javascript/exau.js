/**
 * javascript/exau.js
 *
 * $LastChangedRevision$
 * $LastChangedBy$
 * $LastChangedDate$
 */

jQuery(document).ready(function(){
	
	jQuery('#exau-ajax-loader').ajaxStart(function(){
			jQuery(this).show();
	});
		
	jQuery('#exau-ajax-loader').ajaxStop(function(){
		jQuery(this).hide();
	});

	jQuery('#export_auteurs').click(function () {
		var $url = jQuery('#x-exau-url').attr('content');
		var $titre = jQuery('#x-exau-titre').attr('content');
		//alert($url);
		jQuery.ajax({
			type: 'POST',
			url: jQuery('#x-exau-url').attr('content') ,
			async: false ,
			success: function(data){
				var $fenetre = window.open("", $titre, "width=600,height=600,location=no,resizable=yes,scrollbars=yes");
				$fenetre.document.open()
				$fenetre.document.write(data)
				$fenetre.document.close()                
			}
		});
		return(false);
    });

});