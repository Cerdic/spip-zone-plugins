<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_actionner_dist() {
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	include_spip('inc/svp_actionner');
	include_spip('inc/headers');
	$actionneur = new Actionneur();
	$actionneur->log = true;
	$actionneur->get_actions();
	if ($actionneur->one_action()) {
		include_spip('inc/minipres');
		$url = generer_action_auteur('actionner', '',  _request('redirect'));
		$pres = $actionneur->presenter_actions();
		echo minipres( _T('svp:installation_en_cours'), $pres . '<br /><br /><br />' . $url);
		die();
		#redirige_par_entete(str_replace('&amp;','&', $url));
	}
	
	include_spip('inc/svp_depoter_local');
	svp_actualiser_paquets_locaux();

	// retour
	redirige_par_entete(_request('redirect'));

}

?>
