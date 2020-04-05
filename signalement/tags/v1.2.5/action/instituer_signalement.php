<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Action d'institution d'un signalement
 *
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_signalement_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($id_signalement, $statut) = preg_split('/\W/', $arg);
	$id_signalement = intval($id_signalement);
	$row = sql_fetsel("*", "spip_signalements", "id_signalement=".intval($id_signalement));
	if (!$row) return;

	instituer_un_signalement($statut,$row);
}

function instituer_un_signalement($statut,$row){

	$id_signalement = $row['id_signalement'];
	$old = $row['statut'];
 // rien a faire si pas de changement de statut
 	if ($old==$statut)
		return;

	// changer le statut de toute l'arborescence dependant de ce message
	$id_signalements = array($id_signalement);
	include_spip('action/editer_objet');

	objet_instituer('signalement',$id_signalement,array('statut'=>$statut));

	// invalider les pages comportant ce signalement
	include_spip('inc/invalideur');
	suivre_invalideur("id='signalement/$id_signalement'");
	suivre_invalideur("id='".$row['objet']."/".$row['id_objet']."'");
}

?>
