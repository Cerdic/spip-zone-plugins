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

function formulaires_editer_evenement_charger_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	// Recupere automatiquement les champs de la table (entre autres...)
	$valeurs = formulaires_editer_objet_charger('evenement',$id_evenement,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);

	// fixer la date par defaut en cas de creation d'evenement
	if (!intval($id_evenement)) {
		$t=time();
		$valeurs['date_debut'] = date('Y-m-d H:i:00', $t);
		$valeurs['date_fin'] = date('Y-m-d H:i:00', $t+86400);
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
	if (_request("new")=='oui'){
		$type = trim(_request("type"));
		$id_objet = trim(_request("id_objet"));
		if ($type and $id_objet){
			// assemblage du champ type et id_objet
			$valeurs["ref"] = $type.$id_objet;
		}
	}
	
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_evenement_identifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_evenement),$lier_trad));
}


// Choix par defaut des options de presentation
function evenements_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_evenement_verifier_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	// Verifications automatiques
	// ---------------------------
	$champs_obligatoires = array('titre', 'date_debut');
	$erreurs = formulaires_editer_objet_verifier('evenement', $id_evenement, $champs_obligatoires);
	
	// Autres verifications
	// ---------------------
	
	include_spip('inc/date_gestion');

	$horaire = lire_config('simplecal_horaire') == 'non' ? false : true;
	if (empty($erreurs['date_debut'])) {
		$date_debut = verifier_corriger_date_saisie('debut', $horaire, $erreurs);
	}
	if (empty($erreurs['date_fin'])) {
		$date_fin = verifier_corriger_date_saisie('fin', $horaire, $erreurs);
	}

	// Chronologie : Date de fin >= Date de debut (si pas d'autres erreurs sur les dates)
	if (!$erreurs['date_debut'] && !$erreurs['date_fin']){
		if ($date_debut && $date_fin){
			$date_debut_sql = date_saisie2sql($date_debut);
			$date_fin_sql = date_saisie2sql($date_fin);
			
			if ($date_debut_sql != '0000-00-00 00:00:00' && $date_fin_sql != '0000-00-00 00:00:00'){
				if ($date_fin_sql < $date_debut_sql){
					$erreurs['date_fin'] = _T('simplecal:validation_date_chronologie');
				}
			}
		}
	}
	
	$config = $config_fonc($row);
	if ($config['simplecal_refobj'] == 'oui'){
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
	
	return $erreurs;
}

function formulaires_editer_evenement_traiter_dist($id_evenement='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='evenements_edit_config', $row=array(), $hidden=''){
	
	// On remet les dates au format SQL ('14/08/2011' => '14/08/2011 00:00:00')
	// set_request("date_debut", date_saisie2sql(_request("date_debut")));
	// set_request("date_fin", date_saisie2sql(_request("date_fin")));

	include_spip('inc/date_gestion');
	$erreurs = array();
	$date_debut = verifier_corriger_date_saisie('debut', true, $erreurs);
	$date_fin = verifier_corriger_date_saisie('fin', true, $erreurs);
	set_request('date_debut', date('Y-m-d H:i:s', $date_debut));
	set_request('date_fin', date('Y-m-d H:i:s', $date_fin));
	
	
	// On reconstitue les champs 'type' et 'id_objet' a partir du champ 'ref'
	$config = $config_fonc($row);
	if ($config['simplecal_refobj'] == 'oui'){
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