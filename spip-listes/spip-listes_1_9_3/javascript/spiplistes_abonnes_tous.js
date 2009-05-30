// spiplistes_abonnes_tous.js
// utilise' par _SPIPLISTES_EXEC_LISTE_GERER

// $LastChangedRevision: 15830 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-10-07 05:41:13 +0200 (dim., 07 oct. 2007) $

$(document).ready(function(){
		$('#btn_chercher_id').hide();
		$('#btn_ajouter_id_abo').hide();
		$('#btn_ajouter_id_mod').hide();
		$('#in_cherche_auteur').click( function() {
			$('#btn_chercher_id').show();
		});
		$('#sel_ajouter_id_abo').click( function() {
			$('#btn_ajouter_id_abo').show();
		});
		$('#sel_ajouter_id_mod').click( function() {
			$('#btn_ajouter_id_mod').show();
		});
	});