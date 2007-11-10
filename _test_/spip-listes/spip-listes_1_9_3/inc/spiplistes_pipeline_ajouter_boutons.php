<?php
/*
	SPIP-Listes pipeline
	inc/spiplistes_pipeline_ajouter_boutons.php
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut réactiver le plugin (config/plugin: désactiver/activer)
	
*/

include_spip('inc/spiplistes_api_globales');


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


	function spiplistes_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['naviguer']->sousmenu["spiplistes"]= new Bouton(
			_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png"  // icone
			, _T('spiplistes:listes_de_diffusion_')	// titre
			, _SPIPLISTES_EXEC_COURRIERS_LISTE
			);
		}
		return $boutons_admin;
	}

?>