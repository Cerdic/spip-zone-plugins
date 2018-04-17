<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/simplecal_utils');
include_spip('inc/config');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_evenement_identifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_evenement),$lier_trad));
}

function formulaires_editer_evenement_charger_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// Recupere automatiquement les champs de la table (entre autres...)
	$valeurs = formulaires_editer_objet_charger('evenement',$id_evenement,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

	// fixer la date par defaut en cas de creation d'evenement
	if (!is_numeric($id_evenement)) {
		$t=time();
		$valeurs['date_debut'] = date('Y-m-d 00:00:00', $t);
		$valeurs['date_fin'] = date('Y-m-d 00:00:00', $t);
		$valeurs['horaire'] = 'oui';
	}
	
	// dispatcher date et heure
	list($valeurs['date_debut'], $valeurs['heure_debut']) = explode(' ', date('d/m/Y H:i', strtotime($valeurs['date_debut'])));
	list($valeurs['date_fin'], $valeurs['heure_fin']) = explode(' ', date('d/m/Y H:i', strtotime($valeurs['date_fin'])));
	
	// Champ ref ("0" en base qd pas renseigne)
	if (!empty($valeurs["id_objet"])) {
		// assemblage du champ type et id_objet
		$valeurs["ref"] = $valeurs["type"].$valeurs["id_objet"];
	} 
	// Cas ou le parametre vient de l'url (appel depuis portlet)
	// Necessaire pour le rechargement si erreur a la validation...
	else if (_request("ref")){
		$valeurs["ref"] = trim(_request("ref"));
	}
	
	// Appel en creation via portlet
	if (_request('new')=='oui'){
		$type = trim(_request("type"));
		$id_objet = trim(_request("id_objet"));
		if ($type and $id_objet){
			// assemblage du champ type et id_objet
			$valeurs["ref"] = $type.$id_objet;
		}
	}
	return $valeurs;
}




// Choix par defaut des options de presentation
// function evenements_edit_config($row)
// {
// 	global $spip_ecran, $spip_lang, $spip_display;

// 	$config = $GLOBALS['meta'];
// 	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
// 	$config['langue'] = $spip_lang;

// 	$config['restreint'] = ($row['statut'] == 'publie');
// 	return $config;
// }

function formulaires_editer_evenement_verifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// Verifications automatiques
	// ---------------------------
	$champs_obligatoires = array('titre', 'date_debut');
	$erreurs = formulaires_editer_objet_verifier('evenement', $id_evenement, $champs_obligatoires);
	
	// Autres verifications
	// ---------------------
	
	// La date de fin est obligatoire et à minima égale à celle de début (Jamais de 0000-00-00 en base).
	// Si l'utilisateur la laisse vide, on la renseigne pour lui.
	// => Permet d'éviter les crash à la validation et l'affichage de 01/01/1970...
	if (!_request('date_fin')) {
		set_request('date_fin', _request('date_debut'));
	}

	//Recuperer les champs date_xx et heure_xx, verifier leur coherence et les reformater afin de tester plus bas la chronologie
	include_spip('inc/date_gestion');
	$config_horaire = lire_config('simplecal_horaire');
	if ($config_horaire == 'non') {
		$get_horaire = false;
	}
	else {
		$get_horaire = _request('horaire') == 'non' ? false : true;
	}

	if (empty($erreurs['date_debut'])) {
		$date_debut = verifier_corriger_date_saisie('debut', $get_horaire, $erreurs);
	}
	if (empty($erreurs['date_fin'])) {
		$date_fin = verifier_corriger_date_saisie('fin', $get_horaire, $erreurs);
	}

	// Chronologie : Date de fin >= Date de debut (si pas d'autres erreurs sur les dates)
	if ($date_debut and $date_fin and $date_fin < $date_debut) {
		$erreurs['date_fin'] = _T('simplecal:validation_date_chronologie');
	}
	
	$refobj = lire_meta('simplecal_refobj');
	if ($refobj == 'oui'){
		// Ref saisie correctement ?
		$ref = trim(_request('ref'));
		if ($ref){
			if (!simplecal_is_ref_ok($ref)){
				$erreurs['ref'] = _T('simplecal:validation_refobj_format');
			}
			else {
				// L'objet en question existe t-il ?
				$tab = simplecal_get_tuple_from_ref($ref);
				$type = $tab['type'];
				$id_objet = $tab['id_objet'];
				$existe = sql_fetsel("id_$type" ,"spip_".$type."s", "id_$type=".$id_objet);
				if (!$existe){
					$erreurs['ref'] = _T('simplecal:validation_type_nexiste_pas', array('type'=>$type, 'id_objet'=>$id_objet));
				} 
			}
		}
	}
	
	set_request('horaire', _request('horaire') == 'non' ? 'non' : 'oui');
	return $erreurs;
}

function formulaires_editer_evenement_traiter_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	set_request('horaire', _request('horaire') == 'non' ? 'non' : 'oui');

	$config_horaire = lire_config('simplecal_horaire');
	if ($config_horaire == 'non') {
		$get_horaire = false;
	}
	else {
		$get_horaire = _request('horaire') == 'non' ? false : true;
	}
	include_spip('inc/date_gestion');
	$date_debut = verifier_corriger_date_saisie('debut', $get_horaire, $erreurs);
	$date_fin = verifier_corriger_date_saisie('fin', $get_horaire, $erreurs);
	set_request('date_debut', date('Y-m-d H:i:s', $date_debut));
	set_request('date_fin', date('Y-m-d H:i:s', $date_fin));
	
	
	// On reconstitue les champs 'type' et 'id_objet' a partir du champ 'ref'
	$refobj = lire_meta('simplecal_refobj');
	if ($refobj == 'oui'){
		$ref = trim(_request('ref'));
		if ($ref){
			
			$tab = simplecal_get_tuple_from_ref($ref);
			set_request("type", $tab['type']);
			set_request("id_objet", $tab['id_objet']);
		} else {
			set_request("type", "");
			set_request("id_objet", 0);
		}
	} else {
		// Option desactive => on ne fait rien !
		// => Option desactivable sans risque de perdre les infos enregistrees lorsqu'elle etait active.
	}
	
	return formulaires_editer_objet_traiter('evenement',$id_evenement,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}
?>