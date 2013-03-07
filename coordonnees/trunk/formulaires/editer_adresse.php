<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Definition des saisies du formulaire
 */
function formulaires_editer_adresse_saisies_dist(){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('adresse:label_titre'),
				'placeholder' => _T('adresse:placeholder_titre'),
				'obligatoire' => 'oui'
			)
		),
		array (
			'saisie' => 'selection',
			'options' => array (
				'nom' => 'type',
				'label' => _T('adresse:label_type'),
				'obligatoire' => 'oui',
				'datas' => array (
					'home' => _T('adresse:type_adr_home'),
					'work' => _T('adresse:type_adr_work'),
					'dom'=> _T('adresse:type_adr_dom'),
					'pref' => _T('adresse:type_adr_pref'),
					'postal' => _T('adresse:type_adr_postal'),
					'intl' => _T('adresse:type_adr_intl'),
					'parcel' => _T('adresse:type_adr_parcel')
				)
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'voie',
				'label' => _T('adresse:label_voie'),
				'obligatoire' => 'oui'
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'complement',
				'label' => _T('adresse:label_complement'),
				'placeholder' => _T('adresse:placeholder_complement')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'boite_postale',
				'label' => _T('adresse:label_boite_postale'),
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'code_postal',
				'label' => _T('adresse:label_code_postal'),
				'obligatoire' => 'oui',
				'verifier' => array (
					'type' => 'code_postal'
				)
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'region',
				'label' => _T('adresse:label_region')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'ville',
				'label' => _T('adresse:label_ville'),
				'obligatoire' => 'oui',
			)
		),
		array (
			'saisie' => 'pays',
			'options' => array (
				'nom' => 'pays',
				'label' => _T('adresse:label_pays'),
				'obligatoire' => 'oui',
				'class' => 'chosen',
				'defaut' => 'FR',
				'code_pays' => 'oui'
			)
		),
	);
	return $saisies;
}

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
