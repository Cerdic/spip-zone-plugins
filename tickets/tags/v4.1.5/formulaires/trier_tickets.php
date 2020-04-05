<?php

/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 * 
 * Formulaire de tri de tickets
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/date_gestion');

/**
 * Chargement des valeurs par defaut des champs du formulaire
 */
function formulaires_trier_tickets_charger_dist($lien_filtre = NULL,$lien_arg = NULL){
	$lien = $lien_filtre ? $lien_filtre : $lien_arg;

	$valeurs = array(
			'recherche' => self(),
			'editable' => 'oui'
		);
		
	$recherches = liste_champs_recherche_trier_tickets();
	foreach($recherches as $recherche){
		$valeurs[$recherche] = _request($recherche);
		if(in_array($recherche,array('date_debut','date_fin')) && $valeurs[$recherche]){
			if($valeurs[$recherche] == 0){
				$valeurs[$recherche] = '';
			}else{
				$valeurs[$recherche] = date('d/m/Y',strtotime($valeurs[$recherche]));
				set_request($recherche,$valeurs[$recherche]);
			}
		}
	}

	return $valeurs;
}

function formulaires_trier_tickets_verifier_dist($lien_filtre = NULL,$lien_arg = NULL){
	$erreurs = array();
	/**
	 * On vérifie les dates ...
	 */
	foreach(array('date_debut','date_fin') as $recherche){
		include_spip('inc/filtres');
		if(_request($recherche)){
			$date = _request($recherche);
			$date = recup_date($date);
			if(!is_numeric($date[0]) OR !is_numeric($date[1]) OR !is_numeric($date[2])){
				$erreurs[$recherche] = _T('tickets:erreur_date_saisie');
			}else{
				$date_fin[$recherche] = $date[0].''.$date[1].''.(($date[2] > 10) ? $date[2] :'0'.$date[2]);
			} 
		}
	}
	if(_request('date_debut') && _request('date_fin')){
		if($date_fin['date_debut'] > $date_fin['date_fin']){
			$erreurs['date_fin'] = _T('tickets:erreur_date_saisie_superieure');
		}
	}
	if(count($erreurs) > 0){
		$erreurs['message_erreur'] = _T('tickets:erreur_verifier_formulaire');
	}
	return $erreurs;
}

function formulaires_trier_tickets_traiter_dist($lien_filtre = NULL,$lien_arg = NULL){
	$action = ($lien ? $lien : generer_url_public('tickets'));
	$horaire = false;
	
	$recherches = liste_champs_recherche_trier_tickets();
	foreach($recherches as $recherche){
		if(($recherche == 'date_debut') && _request('date_debut')){
			$date_debut = date('Y-m-d H:i:s',verifier_corriger_date_saisie('debut',$horaire,$erreurs));
			$action = parametre_url($action,$recherche,$date_debut);
		}
		else if(($recherche == 'date_fin') && _request('date_fin')){
			$date_fin = date('Y-m-d H:i:s',verifier_corriger_date_saisie('fin',$horaire,$erreurs));
			$action = parametre_url($action,$recherche,$date_fin);
		}
		else{
			$action = parametre_url($action,$recherche,_request($recherche,''));
		}	
	}
	include_spip('inc/headers');
	redirige_formulaire($action);
}

function liste_champs_recherche_trier_tickets() {
	include_spip('base/abstract_sql');
	$groupesmots = array_map('array_shift', sql_allfetsel("id_groupe", "spip_groupes_mots", "FIND_IN_SET('tickets', tables_liees)"));
	$recherches = array();
	foreach ($groupesmots as $id_groupe) {
		$recherches[] = 'groupemots_'.$id_groupe;
	}
	$recherches = array_merge($recherches, array('recherche','auteurs','date_debut','date_fin','assignes','statuts'));
	
	return $recherches;
}
?>
