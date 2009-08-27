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
function creer_jeton($form) {
	$time = date('Y-m-d-H');
	$ip = $GLOBALS['ip'];
	include_spip('inc/securiser_action');
	// le jeton prend en compte l'heure et l'ip de l'internaute
	return calculer_cle_action("jeton$form$time$ip");		
}

/**
 * Verifie une cle de jeton pour un formulaire
 *
 * @param string $form nom du formulaire
 * @param string cle recue
 * @return bool cle correcte ?
 */
function verifier_jeton($form, $jeton) {
	$time = time();
	$time_old = date('Y-m-d-H',$time-3600);
	$time = date('Y-m-d-H',$time);
	$ip = $GLOBALS['ip'];

	return (verifier_cle_action("jeton$form$time$ip",$jeton) 
			or verifier_cle_action("jeton$form$time_old$ip",$jeton));
}

?>
