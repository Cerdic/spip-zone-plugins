<?php

function action_supprimer_marquepage(){
	
	include_spip('inc/marquepages_api');
	include_spip('inc/headers');
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	$id_forum = intval($arg);
	
	global $auteur_session;
	$redirect = rawurldecode(_request('redirect'));
	
	$ok = marquepages_supprimer($id_forum);
	redirige_par_entete($redirect);
	
}

?>
