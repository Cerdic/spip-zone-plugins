<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


	function dd_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['auteurs']->sousmenu['dd']= new Bouton(
			"../"._DIR_PLUGIN_DD."/dd-24.png",  // icone
			_T('dd:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}
	
// Ajoute le bouton d'amin aux webmestres
function dd_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
	//$exec =  $flux['args']['exec'];
	//if ($exec=='configuration'){
		// on voit le bouton dans la barre "maintenance"
		$flux['data']['dd'] =
			new Bouton(
			_DIR_PLUGIN_DD."/dd-22.png",  // icone
			_L('Download Dump'),	// titre
			generer_url_ecrire('dd'),
			NULL,
			'dd'
			);
	}
	return $flux;
}


?>