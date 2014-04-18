<?php
/**
 * Gestion du formulaire de d'édition d'une adresse
 *
 * @plugin     Coordonnees
 * @copyright  2014
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Formulaires
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Definition des saisies du formulaire
 *
 * @param int|string $id_adresse
 *     Identifiant de l'adresse. 'new' pour une nouvelle adresse.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'adresse créée à cet objet,
 *     tel que `article|3`
 * @return array
 *     Tableau des saisies
 */
function formulaires_editer_adresse_saisies_dist($id_adresse='new', $retour='', $associer_objet=''){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('coordonnees:label_titre'),
				'placeholder' => _T('coordonnees:placeholder_titre_adresse')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'voie',
				'label' => _T('coordonnees:label_voie')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'complement',
				'label' => _T('coordonnees:label_complement'),
				'placeholder' => _T('coordonnees:placeholder_complement_adresse')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'boite_postale',
				'label' => _T('coordonnees:label_boite_postale'),
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'code_postal',
				'label' => _T('coordonnees:label_code_postal')
			),
			// decommenter ces lignes quand les codes postaux
			// internationaux seront pris en compte par 'verifier'
			/*'verifier' => array (
				'type' => 'code_postal'
			)*/
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'region',
				'label' => _T('coordonnees:label_region')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'ville',
				'label' => _T('coordonnees:label_ville')
			)
		),
		array (
			'saisie' => 'pays',
			'options' => array (
				'nom' => 'pays',
				'label' => _T('coordonnees:label_pays'),
				'obligatoire' => 'oui',
				'class' => 'chosen',
				'defaut' => 'FR',
				'code_pays' => 'oui'
			)
		),
	);

	// si on associe l'adresse à un objet, rajouter la saisie 'type'
	if($associer_objet) {
		$saisie_type = array(
			array (
			'saisie' => 'type_adr',
			'options' => array (
				'nom' => 'type',
				'label' => _T('coordonnees:label_type_adresse'),
				)
			)
		);
		$saisies = array_merge($saisie_type,$saisies);
	}

	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_adresse
 *     Identifiant de l'adresse. 'new' pour une nouvelle adresse.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'adresse créée à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une adresse source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'adresse, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_adresse_identifier_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_adresse), $associer_objet));
}

/**
 * Chargement du formulaire d'édition d'une adresse
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_adresse
 *     Identifiant de l'adresse. 'new' pour une nouvelle adresse.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'adresse créée à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une adresse source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'adresse, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
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
 * Vérifications du formulaire d'édition d'une adresse
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_adresse
 *     Identifiant de l'adresse. 'new' pour une nouvelle adresse.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'adresse créée à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une adresse source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'adresse, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_adresse_verifier_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('adresse',$id_adresse);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'une adresse
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_adresse
 *     Identifiant de l'adresse. 'new' pour une nouvelle adresse.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier l'adresse créée à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une adresse source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL de l'adresse, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_adresse_traiter_dist($id_adresse='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('adresse',$id_adresse,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_adresse = $res['id_adresse']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet == intval($id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('adresse' => $id_adresse), array($objet => $id_objet), array('type'=>_request('type')));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_adresse', '', '&');
			}
		}
	}
	return $res;

}


?>
