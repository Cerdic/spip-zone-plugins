<?php
/**
 * Plugin kaye
 * (c) 2012 Cédric Couvrat
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_devoir_identifier_dist($id_devoir='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_devoir)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_devoir_charger_dist($id_devoir='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('devoir',$id_devoir,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */

function formulaires_editer_devoir_verifier_dist($id_devoir='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	//return formulaires_editer_objet_verifier('devoir',$id_devoir, array('matiere', 'date_echeance', 'id_classe'));
		$erreurs = formulaires_editer_objet_verifier('devoir',$id_devoir, array('matiere', 'date_echeance', 'id_classe'));
		// verifier et changer en datetime sql la date envoyee
		$verifier = charger_fonction('verifier', 'inc');
		$champ = 'date_echeance';
		$normaliser = null;
		if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
			$erreurs[$champ] = $erreur;
		// si une valeur de normalisation a ete transmis, la prendre.
		} elseif (!is_null($normaliser)) {
			set_request($champ, $normaliser);
		}
		return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_devoir_traiter_dist($id_devoir='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('devoir',$id_devoir,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>