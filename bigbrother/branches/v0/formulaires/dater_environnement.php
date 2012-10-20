<?php

/**
 * Formulaire redirigeant vers une page en ajoutant dans l'environnement
 * -* Un paramètre date_debut
 * -* Un paramètre date_fin
 *
 * Il peut prendre 3 arguments différents :
 * -* Une date de début (Le cas échéant on le récupère de l'environnement
 * s'il est présent)
 * -* Une date de fin (Le cas échéant on le récupère de l'environnement
 * s'il est présent)
 * -* Une url de redirection (Le cas échéant on redirige sur la même page
 * )
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/date_gestion');

function formulaires_dater_environnement_charger_dist($date_debut='',$date_fin='', $retour=''){
	$valeurs = array();
	$valeurs["date_debut"] = $date_debut ? $date_debut : _request('date_debut');
	$valeurs["date_fin"] = $date_fin ? $date_fin : _request('date_fin');
	if($valeurs["date_debut"])
		list($valeurs["date_debut"],$valeurs["heure_debut"]) = explode(' ',date('d/m/Y H:i',strtotime($valeurs["date_debut"])));
	if($valeurs["date_fin"])
		list($valeurs["date_fin"],$valeurs["heure_fin"]) = explode(' ',date('d/m/Y H:i',strtotime($valeurs["date_fin"])));
	return $valeurs;
}

function formulaires_dater_environnement_verifier_dist($date_debut='',$date_fin='', $retour=''){
	$horaire = true;
	if(_request('date_debut')){
		$date_debut = verifier_corriger_date_saisie('debut',$horaire,$erreurs);
	}
	if(_request('date_fin')){
		$date_fin = verifier_corriger_date_saisie('fin',$horaire,$erreurs);
	}
	if ($date_debut AND $date_fin AND $date_fin<$date_debut)
		$erreurs['date_fin'] = _T('agenda:erreur_date_avant_apres');
	$erreurs = array();
	return $erreurs;
}

function formulaires_dater_environnement_traiter_dist($date_debut='',$date_fin='', $retour=''){
	$horaire = true;
	if(_request('date_debut')){
		$date_debut = date('Y-m-d H:i:s',verifier_corriger_date_saisie('debut',$horaire,$erreurs));
	}
	if(_request('date_fin')){
		$date_fin = date('Y-m-d H:i:s',verifier_corriger_date_saisie('fin',$horaire,$erreurs));
	}
	if(!$retour){
		$retour = self();
	}
	include_spip('inc/headers');
	$retour = parametre_url(parametre_url($retour,'date_debut',$date_debut),'date_fin',$date_fin);
	$message .= redirige_formulaire($retour);
	return $message;
}
?>