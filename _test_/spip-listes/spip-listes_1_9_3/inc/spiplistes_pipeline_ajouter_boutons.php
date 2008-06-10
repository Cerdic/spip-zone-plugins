<?php
// inc/spiplistes_pipeline_ajouter_boutons.php
/*
	SPIP-Listes pipeline
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut parfois réactiver le plugin (config/plugin: désactiver/activer)
	
*/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/spiplistes_api_globales');

function spiplistes_ajouterBoutons($boutons_admin) {

	if($GLOBALS['connect_statut'] == "0minirezo") {
	// affiche le bouton dans "Edition"
		$boutons_admin['naviguer']->sousmenu['spiplistes'] = new Bouton(
			_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png"  // icone
			, _T('spiplistes:listes_de_diffusion_')	// titre
			, _SPIPLISTES_EXEC_COURRIERS_LISTE
		);
	}
	return ($boutons_admin);
}

?>