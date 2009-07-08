<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function action_spipmotion_ajouter_file_encodage_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_spipmotion_ajouter_file_encodage_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	action_spipmotion_ajouter_file_encodage_post($r);
}

function action_spipmotion_ajouter_file_encodage_post($r)
{	
	global $visiteur_session;
	list(, $sign, $id, $type, $id_document) = $r;
	spip_log($id.' - '.$id_document.'  - '.$type);
	
	$presente = sql_getfetsel('id_spipmotion_attente','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode="non"');
	
	if(!$presente){
		sql_insertq('spip_spipmotion_attentes',array('id_auteur'=>$visiteur_session['id_auteur'],'id_document'=>$id_document,'objet'=>$type,'id_objet'=>$id,'encode'=>'non'));
	}
	
	$redirect = urldecode(_request('redirect'));

	return $redirect;
}
?>