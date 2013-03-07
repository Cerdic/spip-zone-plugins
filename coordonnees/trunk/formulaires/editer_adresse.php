<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_adresse_identifier_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_adresse), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_adresse_charger_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('adresse',$id_adresse,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// importer les saisies yaml
	include_spip('inc/yaml');
	$valeurs['_saisies_adresse'] = _T_ou_typo(yaml_decode_file(find_in_path('yaml/saisies_adresse.yaml')));
	// valeur de la saisie "type" dans la table de liens
	if ( $associer_objet ) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		$valeurs['type'] = sql_getfetsel('type', 'spip_adresses_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_adresse='.intval($id_adresse) );
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_adresse_verifier_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('adresse',$id_adresse);

	// verification des saisies yaml
	include_spip('inc/yaml');
	include_spip('inc/saisies');
	$erreurs = saisies_verifier(yaml_decode_file(find_in_path('yaml/saisies_adresse.yaml')));

	// verifier qu'il y a au moins le code ou la boite postale
	if ( strlen(_request('code_postal')==0) AND strlen(_request('boite_postale'))==0 ) {
		$erreurs['boite_postale'] = _T('coordonnees:erreur_code_boite_postal');
		$erreurs['code_postal'] = '&nbsp;';
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_adresse_traiter_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('adresse',$id_adresse,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_adresse = $res['id_adresse']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('adresse' => $id_adresse), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_adresse', '', '&');
			}
		}
		// remplir le champ "type" dans la table de liens
		if ( $type = _request('type') ) {
			sql_updateq('spip_adresses_liens',
				array('type' => $type),
				'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_adresse='.intval($id_adresse)
			);
		}
	}
	return $res;

}


?>
