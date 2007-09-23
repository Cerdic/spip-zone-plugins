<?php 

/*
	SPIP-Listes
	
	Ajoute l'onglet de configuration SPIP-Listes
	
	Nota: si mise à jour du plugin, il faut désactiver/réactiver le plugin
	pour voir apparaître l'onglet
	
	From: SPIP-Listes-V, http://www.quesaco.org/
*/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
function spiplistes_ajouter_onglets ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	// seuls les super-admins ont accès au bouton
	if($connect_statut 
		&& $connect_toutes_rubriques
		&& $flux['args'] == 'configuration') {
		$flux['data']['spiplistes'] = new Bouton( 
			_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_listes-24.png"
			, _T("spiplistes:Listes_de_diffusion")
			, generer_url_ecrire(_SPIPLISTES_EXEC_CONFIGURE)
			)
			;
	}
	return ($flux);
}

?>