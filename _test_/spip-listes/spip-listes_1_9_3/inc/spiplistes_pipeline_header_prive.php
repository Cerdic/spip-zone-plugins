<?php 

// inc/spiplistes_pipeline_header_prive.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut reactiver le plugin (config/plugin: desactiver/activer)
		Pas sur SPIP.2
	Nota:
		SPIP 2 fait automatiquement un cache des css pour l'espace prive'.
		Penser a vider le cache ou simplement local/cache-css/ lors de modification de la feuille.
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function spiplistes_header_prive ($flux) {

	$exec = _request('exec');

	$flux .= ""
		. "\n\n<!-- SPIPLISTES GADGETS v.: ".spiplistes_real_version_get(_SPIPLISTES_PREFIX)." -->\n"
		. "<script src='".url_absolue(find_in_path('javascript/spiplistes_gadgets.js'))."' type='text/javascript'></script>\n"
		;

	if(in_array($exec, array(
		_SPIPLISTES_EXEC_ABONNES_LISTE
		, _SPIPLISTES_EXEC_COURRIER_GERER
		, _SPIPLISTES_EXEC_COURRIER_EDIT
		, _SPIPLISTES_EXEC_COURRIERS_LISTE
		, _SPIPLISTES_EXEC_LISTES_LISTE
		, _SPIPLISTES_EXEC_LISTE_GERER
		, _SPIPLISTES_EXEC_LISTE_EDIT
		, _SPIPLISTES_EXEC_MAINTENANCE
		, _SPIPLISTES_EXEC_CONFIGURE
		, 'auteur_infos' // liste-listes
		, _SPIPLISTES_EXEC_IMPORT_EXPORT
		)
		)
	) {
		
		$flux .= "\n\n<!-- PLUGIN SPIPLISTES v.: ".spiplistes_real_version_get(_SPIPLISTES_PREFIX)." -->\n" 
			. "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPLISTES."spiplistes_style_prive.css' type='text/css' media='all' />\n"
			;

		switch($exec) {
			case _SPIPLISTES_EXEC_COURRIER_EDIT:
				$flux .= ""
					. "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "javascript/spiplistes_courrier_edit.js\"></script>\n"
/*
 le datepicker ne sert plus dans cette version.					
					. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />\n"
					. "<script src='".url_absolue(find_in_path('javascript/datepicker.js'))."' type='text/javascript'></script>\n"
 A priori, ce bout de code ne sert plus
					. "<script src='".url_absolue(find_in_path('javascript/jquery-dom.js'))."' type='text/javascript'></script>\n"
*/					
					. "<meta http-equiv='expires' content='0' />\n"
					. "<meta http-equiv='pragma' content='no-cache' />\n"
					. "<meta http-equiv='cache-control' content='no-cache' />\n"
					;
				break;
			case _SPIPLISTES_EXEC_COURRIERS_LISTE:
				break;
			case _SPIPLISTES_EXEC_CONFIGURE:
				$flux .= "
<!-- SpipListes JS -->
<script type='text/JavaScript'>
<!--
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
//-->
</script>
";
				break;
			case _SPIPLISTES_EXEC_LISTE_GERER:
			case _SPIPLISTES_EXEC_ABONNES_LISTE:
				$flux .= "
<!-- SpipListes JS -->
<script type='text/JavaScript'>
<!--
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
//-->
</script>
<style type='text/css'>
.spiplistes .supprimer_cet_abo {background-image:url(".find_in_path("images/croix-rouge.gif").")}
</style>
";			
				break;
		}

	}
	$flux .= "<!-- / SPIPLISTES -->\n\n";

	return ($flux);
}

?>