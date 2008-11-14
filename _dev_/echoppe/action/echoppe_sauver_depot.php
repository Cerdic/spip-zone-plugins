<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_depot(){
	//include_spip('inc/utils');  necessaire ?
	$new = _request('new');
	$id_depot = _request('id_depot');
	$titre = _request('titre');
	$description = _request('texte');
	$adresse_depot = _request('adresse_depot');
	$numero_depot = _request('numero_depot');
	$code_postal_depot = _request('code_postal_depot');
	$ville_depot = _request('ville_depot');
	$pays_depot = _request('pays_depot');
	$telephone_depot = _request('telephone_depot');
	$fax_depot = _request('fax_depot');
	$email_depot = _request('email_depot');
	$gsm_depot = _request('gsm_depot');
	 
	switch ($new){
		case 'oui':
			$arg_depot = array(
			'titre' => $titre, 
			'description' => $description,
			'adresse_depot' => $adresse_depot, 
			'numero_depot' => $numero_depot, 
			'code_postal_depot' => $code_postal_depot, 
			'ville_depot' => $ville_depot, 
			'pays_depot' => $pays_depot, 
			'telephone_depot' => $telephone_depot, 
			'fax_depot' => $fax_depot, 
			'email_depot' => $email_depot, 
			'maj' => 'NOW()'
			);
			$id_depot = sql_insertq('spip_echoppe_depots',$arg_depot);
		break;
	
		default :
			$arg_depot = array(
			'titre' => $titre, 
			'description' => $description,
			'adresse_depot' => $adresse_depot, 
			'numero_depot' => $numero_depot, 
			'code_postal_depot' => $code_postal_depot, 
			'ville_depot' => $ville_depot, 
			'pays_depot' => $pays_depot, 
			'telephone_depot' => $telephone_depot, 
			'fax_depot' => $fax_depot, 
			'gsm_depot' => $gsm_depot, 
			'email_depot' => $email_depot, 
			'maj' => 'NOW()'
			);
			sql_updateq("spip_echoppe_depots",$arg_depot,"id_depot='$id_depot'");
			
		break;
		
	}
	//echo "SQL1 - ".$sql_sauver_depot;
	$redirect = generer_url_ecrire('echoppe_gerer_depots');
	redirige_par_entete($redirect);
	
}

?>
