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
function formulaires_editer_email_saisies_dist(){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('coordonnees:label_titre'),
				'placeholder' => _T('coordonnees:placeholder_titre_email')
			)
		),
		array (
			'saisie' => 'type_mel',
			'options' => array (
				'nom' => 'type',
				'label' => _T('coordonnees:label_type_email'),
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'email',
				'label' => _T('coordonnees:label_email'),
				'placeholder' => _T('coordonnees:placeholder_email'),
				'obligatoire' => 'oui'
			),
			'verifier' => array (
				'type' => 'email',
				'options' => array (
					'mode' => 'normal'
				)
			)
		),
	);
	return $saisies;
}


/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_email_identifier_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_email), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_email_charger_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('email',$id_email,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// valeur de la saisie "type" dans la table de liens
	if ( $associer_objet ) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		$valeurs['type'] = sql_getfetsel('type', 'spip_emails_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_email='.intval($id_email) );
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_email_verifier_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('email',$id_email);

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_email_traiter_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('email',$id_email,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_email = $res['id_email']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('email' => $id_email), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_email', '', '&');
			}
		}
		// remplir le champ "type" dans la table de liens
		if ( $type = _request('type') ) {
			sql_updateq('spip_emails_liens',
				array('type' => $type),
				'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_email='.intval($id_email)
			);
		}
	}
	return $res;

}


?>
