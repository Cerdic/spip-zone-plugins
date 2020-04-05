<?php


if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_lier_produit_charger_dist($id_produit='new',$id_objet=0,$objet='',$rang=0){
	return 
		array(
			'id_objet'=>$id_objet,
			'objet'=>$objet,
			'id_produit'=>$id_produit,
		);
}


function formulaires_lier_produit_verifier_dist($id_produit='new',$id_objet=0,$objet='',$rang=0){	
	$erreurs = array();
	
	$id_produit  = _request('id_produit');
	if(!intval($id_produit)) $erreurs['id_produit']='merci d\'entrer un numéro';
	
	
	return $erreurs;
}

function formulaires_lier_produit_traiter_dist($id_produit='new',$id_objet=0,$objet='',$rang=0){
	
	$id_produit  = _request('id_produit');
	$id_objet  = _request('id_objet');
	$objet  = _request('objet');
	$rang  = 0;
	//$rang  = _request('rang');
	
	include_spip('action/lier_produit');
	$action_lier_produit = charger_fonction('lier_produit','action');
	
	if($action_lier_produit("$id_produit/$id_objet/$objet/$rang"))	
	return array("message_ok"=>"ok");
	
}

