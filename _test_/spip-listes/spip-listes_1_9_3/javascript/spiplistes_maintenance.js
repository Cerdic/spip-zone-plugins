// spiplistes_maintenance.js

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

jQuery(document).ready(function() {
	
	$('#sl-modif-fmt').hide();
	
	$('#confirmer_modifier_formats').click( function() {
		if($(this).attr('checked')) {
			$('#sl-modif-fmt').show();
		}
		else {
			$('#sl-modif-fmt').hide();
		}
	});
	
});