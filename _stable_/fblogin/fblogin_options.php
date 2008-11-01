<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

if (defined('_FB_API_KEY')) {
	session_start();
	if (isset($_SESSION['fb_session'])
	 && isset($GLOBALS['visiteur_session']['id_auteur'])) {
	 	// associer le login fb et le login spip
		$fblogin_auth = charger_fonction('fblogin_auth','inc');
		$redirect = $fblogin_auth();
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}

?>
