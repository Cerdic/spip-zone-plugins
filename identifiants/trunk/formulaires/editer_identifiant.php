<?php
/**
 * Editer l'identifiant d'un objet
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Charger les valeurs du formulaire
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $retour
 * @return Array Valeurs du formulaire
 */
function formulaires_editer_identifiant_charger($objet, $id_objet, $retour=''){
	$valeurs['identifiant'] = sql_getfetsel('identifiant', 'spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet));
	$valeurs['_saisie_en_cours'] = (_request('identifiant') !== null);
	$valeurs['has_identifiant'] = ($valeurs['identifiant']) !== null;

	return $valeurs;
}

/**
 * Verification avant traitement
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $retour
 * @return Array Tableau des erreurs
 */
function formulaires_editer_identifiant_verifier_dist($objet, $id_objet, $retour=''){
	$erreurs = array();

	if ($identifiant = _request('identifiant')) {
		// nombre de charactères : 50 max
		if (($nb = strlen($identifiant)) > 50) {
			$erreurs['identifiant'] = _T('identifiant:erreur_champ_identifiant_taille', array('nb'=>$nb));
		}
		// format : charactères alphanumériques en minuscules ou "_"
		elseif (!preg_match('/^[a-z0-9_]+$/', $identifiant)) {
			$erreurs['identifiant'] = _T('identifiant:erreur_champ_identifiant_format');
		}
		// doublon : on n'autorise qu'un seul identifiant par type d'objet
		elseif (sql_countsel('spip_identifiants', 'identifiant='.sql_quote($identifiant).' AND objet='.sql_quote($objet).' AND id_objet!='.intval($id_objet))) {
			$erreurs['identifiant'] = _T('identifiant:erreur_champ_identifiant_doublon');
		}
	}

	return $erreurs;
}

/**
 * Traitement
 *
 * @param string $objet
 * @param integer $id_objet
 * @param string $retour
 * @return Array
 */
function formulaires_editer_identifiant_traiter_dist($objet, $id_objet, $retour=''){

	if (
		_request('enregistrer')
		and $identifiant = _request('identifiant')
	) {
		// création
		if (!sql_countsel('spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet))) {
			sql_insertq('spip_identifiants', array('identifiant'=>$identifiant, 'objet'=>$objet, 'id_objet'=>$id_objet));
		}
		// mise à jour
		else {
			sql_updateq('spip_identifiants', array('identifiant'=>$identifiant, 'objet'=>$objet, 'id_objet'=>$id_objet));
		}

	}

	// on reset le champ
	set_request('identifiant',null);
	// le formulaire reste éditable
	$res['editable'] = true;
	// redirection éventuelle
	if ($retour) {
		$res['redirect'] = $retour;
	}

	return $res;
}

?>
