<?php

/***
 * Plugin DayFill
 * Ajout d'une action
 * sans que le projet ne soit spécifié
 * il suffit de saisir :
 * - qui
 * - quoi
 * - quand
 * L'action est saisie, mais sans projet qui sera mis à la fin
 */

function formulaires_ajouter_action_charger_dist(){
	$valeurs = array(
					'date_projet'=>'',
					'id_projet'=>'',
					'id_type_action'=>'',
					'action_projet'=>'',
					'type_action'=>'',
					'id_user'=>'',
					'numero_forfait'=>'',
					'numero_facture'=>'',
					'nb_heures_passees'=>'',
					'heure_debut'=>'',
					'heure_fin'=>'',
					'nb_heures_passees_calculees'=>'',
					'nb_heures_facturees'=>'',
					'id_facture'=>''
					);
	
	return $valeurs;
}

function formulaires_ajouter_action_verifier_dist(){
	$erreurs = array();
	
	// verifier que les champs obligatoires sont bien la :
	foreach(array('action_projet','nb_heures_facturees','nb_heures_passees','date_projet') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
		
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		
	return $erreurs;
}

function formulaires_ajouter_action_traiter_dist(){
	
	
	//Faire le traitement d'insertion dans la base!!!!!
	$id_projet			=	_request('id_projet');
	
	
	//Partie qui inverse la date pour l'inserer dans la base mysql dans le bonne ordre
	$date_projet		=	_request('date_projet');	
	$date_decoupe		=	explode("/",$date_projet);
	$date_correct		=	$date_decoupe[2].'-'.$date_decoupe[1].'-'.$date_decoupe[0];
	
	//$date_projet		=	"[(#DATE|affdate{'d/m/Y'})]";
	//$date_projet		=	date("m/d/Y");	
	
	//Calcul du temps
	if( _request('nb_heures_passees') ){
		$resultat_final = _request('nb_heures_passees');
	}
	else {
		$debut		=	_request('heure_debut');
		$fin		=	_request('heure_fin');
		
		$heuredebut	=	explode(":",$debut);
		$heurefin	=	explode(":",$fin);
		
		$toalenminute_debut	=	($heuredebut[0] * 60) + $heuredebut[1];
		$toalenminute_fin	=	($heurefin[0] * 60) + $heurefin[1];
		
		$difference			=	$toalenminute_fin - $toalenminute_debut;
		
		$resultat_final		=	$difference / 60;
	}
	
	$action_projet = addslashes(_request('action_projet'));
	
	// Requete maj projet
	$sql_maj_projet = "UPDATE spip_projets SET date_maj = '".date('Y-m-d H:i:s')."' WHERE id_projet = "._request('id_projet');
	$maj_projet =	spip_query($sql_maj_projet);
	
	//Requete d'insertion
	$requete_insertion	=	"
							INSERT INTO `spip_actions` (
								`id_projet`, 
								`id_type_action`, 
								`date_action`, 
								`action`, 
								`type_action`, 
								`id_user`, 
								`heure_debut`, 
								`heure_fin`, 
								`nb_heures_passees`, 
								`nb_heures_decomptees`,
								`id_facture`
							) VALUES (
								'"._request('id_projet')."',
								'"._request('id_type_action')."',
								'".$date_correct."',
								'".$action_projet."',
								'"._request('type_action')."',
								'"._request('id_user')."',
								'"._request('heure_debut')."',
								'"._request('heure_fin')."',
								'".$resultat_final."',
								'"._request('nb_heures_facturees')."',
								'"._request('id_facture')."'
								)
							";

	$query				=	spip_query($requete_insertion);
	
	if($query = true) { 
		return array('message_ok'=>'Action ajoutée correctement !'); 
	} 
	else { 
		return array('message_ok'=>'Erreur lors de l\'ajout de l\'action !'); 
	}
}

?>