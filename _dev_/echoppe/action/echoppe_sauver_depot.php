<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_depot(){
	//include_spip('inc/utils');  necessaire ?
	$new = _request('new');
	$id_depot = _request('id_depot');
	$titre = _request('titre');
	$description = _request('texte');
	$adresse = serialize(_request('adresse'));
	
	switch ($new){
		case 'oui':
			$arg_inser_depot = array(
			'id_depot' => '',
			'titre' => addslashes($titre), 
			'descriptif' => addslashes($description), 
			'adresse' => $adresse, 
			'maj' => 'NOW()'
			);
			$id_depot = sql_insertq('spip_echoppe_depots',$arg_inser_depot);
	
		break;
	
		default :
			$arg_inser_depot = array(
			'id_depot' => $id_depot,
			'titre' => $titre, 
			'descriptif' => $description,
			'adresse' => $adresse, 
			'maj' => 'NOW()'
			);
			sql_updateq("spip_echoppe_depots",$arg_inser_depot,"id_depot=$id_depot");
			
		break;
		
	}
	//echo "SQL1 - ".$sql_sauver_depot;
	$redirect = generer_url_ecrire('echoppe_gerer_depots');
	redirige_par_entete($redirect);
	
}

?>