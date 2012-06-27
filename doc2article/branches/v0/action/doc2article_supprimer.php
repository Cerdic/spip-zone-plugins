<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_doc2article_supprimer_dist($arg=null){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/autoriser');
	if (intval($arg) AND autoriser('webmestre')){
		sql_delete('spip_doc2article','id_doc2article='.intval($arg));
		spip_log("suppression de la tache $arg de la file d'attente","doc2article");
		return true;
	}
	
	return false;
}

?>