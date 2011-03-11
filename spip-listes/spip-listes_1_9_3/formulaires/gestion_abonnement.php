<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_globales');

function formulaires_gestion_abonnement_charger_dist($id_liste=''){
	$d = _request('d');
	$valeurs = array();
	$valeurs['id_liste'] = $id_liste;
	$valeurs['d'] = $d;
	$valeurs['editable'] = false;
	
	if($auteur = auteur_cookie_ou_session($d)) {
		$id_auteur = $auteur['id_auteur'];
		$valeurs['id_auteur'] = intval($id_auteur);
		$valeurs['format'] = spiplistes_format_abo_demande($id_auteur);
		$valeurs['editable'] = true;
	}
	
	return $valeurs;
}

function formulaires_gestion_abonnement_verifier($id_liste=''){
	spiplistes_debug_log('formulaires_gestion_abonnement_verifier()');
	$erreurs = array();
	return $erreurs;
}
function formulaires_gestion_abonnement_traiter_dist($id_liste=''){
	spiplistes_debug_log('formulaires_gestion_abonnement_traiter_dist()');
	$d = _request('d');
	$listes = _request('listes');
	$format = _request('suppl_abo');
	
	if($auteur = auteur_cookie_ou_session($d)) {
		$id_auteur = $auteur['id_auteur'];
		$email = $auteur['email'];
	}
	
	$prev_format = spiplistes_format_abo_demande($id_auteur);

	// pour chaque liste sélectionnée
	if(is_array($listes) && count($listes)) {
		foreach($listes as $id_liste) {
			// ajouter le nouvel abonnement
			if(spiplistes_abonnements_ajouter($id_auteur, $id_liste) !== false) {
				$message_ok = _T('spiplistes:abonnement_modifie');
			}
			// si changement dans l'abonnement
			if($format != $prev_format) {
				// désabonner l'auteur ou modifier son format d'abonnement
				if($format == 'non') {
					spiplistes_abonnements_auteur_desabonner($id_auteur, $id_liste);
					$message_ok = _T('spiplistes:desabonnement_valid')." :&nbsp;".$email;  
				}else{
					spiplistes_format_abo_modifier($id_auteur, $format);
					$message_ok = _T('spiplistes:abonnement_modifie');
					$message_ok .= "<br />"._T('spiplistes:abonnement_nouveau_format').$format;
				}
			}
		}
	}

	spiplistes_auteurs_cookie_oubli_updateq('', $d, $true);

	$contexte = array(
		'editable' => true,
		'message_ok' => $message_ok,
		'format' => $format
	);
	
	return $contexte;
}

// recuperer id_auteur, statut, nom et email pour :
// -* l'auteur associé au cookie de l'environnement
// -* ou l'auteur de la session en cours
function auteur_cookie_ou_session($d){
	$return = array();
	// si pas de cookie on chope l'auteur de la session
	if(empty($d)) {
		if($id_auteur=$GLOBALS['visiteur_session']['id_auteur']) {
			$return['id_auteur'] = intval($id_auteur);
			$row = sql_fetsel(
				"id_auteur,statut,nom,email",
				"spip_auteurs",
				"id_auteur=$id_auteur"
			);
			if($row) {
				$return['statut'] = $row['statut'];
				$return['nom'] = $row['nom'];
				$return['email'] = $row['email'];
			}
		}
	}
	// recuperer les donnes de l'auteur associe au cookie
	if(!empty($d)) {
		$row = sql_fetsel(
			"id_auteur,statut,nom,email",
			"spip_auteurs",
			"cookie_oubli=".sql_quote($d)." AND statut<>'5poubelle'"
		);
		if($row) {
			$return['id_auteur'] = $row['id_auteur'];
			$return['statut'] = $row['statut'];
			$return['nom'] = $row['nom'];
			$return['email'] = $row['email'];
		}
	}
	return $return;
}

?>