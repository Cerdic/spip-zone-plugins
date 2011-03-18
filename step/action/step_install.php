<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_step_install_dist() {
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip('inc/step');
	include_spip('inc/step_actionneur');
	include_spip('inc/headers');
	$actionneur = new Actionneur();
	$actionneur->get_actions();
	while($actionneur->one_action()) {
		$url = generer_action_auteur('step_install', '',  _request('redirect'));
		redirige_par_entete(str_replace('&amp;','&', $url));
	}
	
	step_actualiser_plugins_locaux();

	// retour
	redirige_par_entete(_request('redirect'));

}

?>
