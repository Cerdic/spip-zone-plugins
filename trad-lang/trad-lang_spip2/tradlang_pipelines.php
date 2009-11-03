<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

/**
 * Ajout d'un bouton dans la barre d'onglet de configuration des langues
 * 
 * @param object $flux
 * @return 
 */
function tradlang_ajouter_onglets($flux) {
	if($flux['args']=='config_lang')
		$flux['data']['tradlang'] = new Bouton( 
			"traductions-24.gif", _L('tradlang:gestion_des_traductions'),
			generer_url_ecrire("tradlang"));
	return $flux;
}


?>
