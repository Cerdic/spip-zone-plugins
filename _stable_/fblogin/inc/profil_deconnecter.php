<?php
/*
 * Plugin gestion des profils
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

include_spip('base/abstract_sql');
include_spip('inc/filtres');
include_spip('inc/cookie');

/**
 * Deconnecter un profil
 *
 */
function inc_profil_deconnecter_dist(){
	if (isset($_COOKIE['spip_session']) OR isset($GLOBALS['visiteur_session']['id_auteur'])) {
		$session = charger_fonction('session', 'inc');
		$session($GLOBALS['visiteur_session']['id_auteur']);
		spip_setcookie('spip_session', '', time()-3600);
		unset($GLOBALS['visiteur_session']);
		unset($_COOKIE['spip_session']);
	}
}

?>