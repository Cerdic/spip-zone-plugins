<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_changer_type_charger_dist($id_visuel,$type=''){
	$valeurs = array(
		'id_visuel' => $id_visuel,
		'type' => $type,
		'select_type_visuel_'.$id_visuel => _request("select_type_visuel_".$id_visuel)
	);

	return $valeurs;
}


function formulaires_changer_type_verifier_dist(){
	
}

function formulaires_changer_type_traiter_dist($id_visuel){
	
	$champs = array(
		'id_visuel' => $id_visuel,
		'select_type_visuel_'.$id_visuel => _request("select_type_visuel_".$id_visuel)
	);

	$type = $champs['select_type_visuel_'.$id_visuel];

	sql_updateq('spip_visuels',array('type'=>$type),"id_visuel='".$champs['id_visuel']."'");


	return array('message_ok'=> "ok");
}
