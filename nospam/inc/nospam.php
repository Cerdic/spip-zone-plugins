<?php
/**
 * Plugin No-SPAM
 * (c) 2008 Cedric Morin Yterium.net
 * Licence GPL
 *
 */


/**
 * Calcule une cle de jeton pour un formulaire
 *
 * @param string $form nom du formulaire
 * @return string cle calculee
 */
function creer_jeton($form, $qui=NULL) {
	$time = date('Y-m-d-H');
	if (is_null($qui)){
		if (isset($GLOBALS['visiteur_session']['id_auteur']))
			$qui = md5(serialize($GLOBALS['visiteur_session']));
		else {
			include_spip('inc/session');
			$qui = hash_env();
		}
	}
	include_spip('inc/securiser_action');
	// le jeton prend en compte l'heure et l'identite de l'internaute
	return calculer_cle_action("jeton$form$time$qui");
}

/**
 * Verifie une cle de jeton pour un formulaire
 *
 * @param string $form nom du formulaire
 * @param string cle recue
 * @return bool cle correcte ?
 */
function verifier_jeton($jeton, $form, $qui=NULL) {
	$time = time();
	$time_old = date('Y-m-d-H',$time-3600);
	$time = date('Y-m-d-H',$time);

	if (is_null($qui)){
		if (isset($GLOBALS['visiteur_session']['id_auteur']))
			$qui = md5(serialize($GLOBALS['visiteur_session']));
		else {
			include_spip('inc/session');
			$qui = hash_env();
		}
	}
	
	return (verifier_cle_action("jeton$form$time$qui",$jeton)
			or verifier_cle_action("jeton$form$time_old$qui",$jeton));
}

?>
