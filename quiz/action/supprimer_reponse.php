<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_reponse_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_reponse = intval($arg);

	// suppression
	sql_delete('spip_reponses', 'id_reponse='.$id_reponse);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}

?>