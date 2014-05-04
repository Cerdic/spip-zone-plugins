<?php 
/**
 * Plugin Diogene
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * Distribue sous licence GNU/GPL
 *
 * Action de suppression d'un Diogène
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_diogene_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_diogene = intval($arg);
	
	if ($id_diogene > 0){
		include_spip('action/editer_diogene');
		diogene_supprimer($id_diogene);
	}
}

?>