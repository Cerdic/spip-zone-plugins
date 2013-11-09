<?php
/**
 * Plugin À vos souhaits
 * (c) 2012 RastaPopoulos
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_souhait_identifier_dist($id_souhait='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_souhait)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_souhait_charger_dist($id_souhait='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('souhait',$id_souhait,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_souhait_verifier_dist($id_souhait='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('souhait',$id_souhait, array('titre'));
	
	if ($prix = _request('prix') and (!is_numeric($prix) or $prix != floatval($prix) or $prix < 0)){
		$erreurs['prix'] = _T('souhait:erreur_prix_float');
	}
	
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_souhait_traiter_dist($id_souhait='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('souhait',$id_souhait,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
