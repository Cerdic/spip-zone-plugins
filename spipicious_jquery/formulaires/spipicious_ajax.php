<?php

/**
 * spipicious
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * Quentin Drouet
 * Erational
 *
 * 2007-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function formulaires_spipicious_ajax_charger($id_objet,$type='article') {
	global $visiteur_session;
	$autorise = lire_config('spipicious/people',array());
	if (!$visiteur_session['id_auteur'] OR !in_array($visiteur_session['statut'],$autorise)) {
		return array('editable'=> false);
	}
	$id_type = id_table_objet($type);
	$id_groupe = lire_config('spipicious/groupe_mot');
	$valeurs = array($id_type=>$id_objet,'type'=>$type,'id_objet'=>$id_objet,'spipicious_groupe'=>$id_groupe);
	return $valeurs;
}

function formulaires_spipicious_ajax_traiter($id_objet,$type) {
	$add_tags = _request('add_tags');
	$remove_tag = _request('remove_tags');

	if (is_array($remove_tag)) {
		$supprimer_tags = charger_fonction('spipicious_supprimer_tags','action');
		list($message,$invalider,$err) = $supprimer_tags();
	}

	if(!empty($add_tags)){
		$ajouter_tags = charger_fonction('spipicious_ajouter_tags','action');
		list($message,$invalider,$err) = $ajouter_tags();
	}

	if($invalider){
		spip_log('invalider');
		include_spip ("inc/invalideur");
		suivre_invalideur("0",true);
	}
	return array('editable'=>true,'message'=>$message);
}
?>