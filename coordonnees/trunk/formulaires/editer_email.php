<?php
/**
 * Gestion du formulaire de d'édition d'un email
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
 * @param int|string $id_email
 *     Identifiant du email. 'new' pour un nouveau email.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le email crée à cet objet,
 *     tel que `article|3`
 * @return array
 *     Tableau des saisies
 */
function formulaires_editer_email_saisies_dist($id_email='new', $retour='', $associer_objet=''){
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
			'saisie' => 'input',
			'options' => array (
				'nom' => 'email',
				'label' => _T('coordonnees:label_email'),
				/*'placeholder' => _T('coordonnees:placeholder_email'),*/
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

	// si on associe l'email à un objet, rajouter la saisie 'type'
	if($associer_objet) {
		$saisie_type = array(
			array (
			'saisie' => 'type_mel',
				'options' => array (
					'nom' => 'type',
					'label' => _T('coordonnees:label_type_email'),
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
 * @param int|string $id_email
 *     Identifiant de l'email. 'new' pour un nouveau email.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le email créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une email source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du email, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_email_identifier_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_email), $associer_objet));
}

/**
 * Chargement du formulaire d'édition d'une email
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_email
 *     Identifiant de l'email. 'new' pour un nouveau email.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le email créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une email source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du email, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
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
 * Vérifications du formulaire d'édition d'un email
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_email
 *     Identifiant de l'email. 'new' pour un nouveau email.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le email créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une email source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du email, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_email_verifier_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('email',$id_email);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'un email
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_email
 *     Identifiant de l'email. 'new' pour un nouveau email.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le email créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une email source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du email, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_email_traiter_dist($id_email='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('email',$id_email,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_email = $res['id_email']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet == intval($id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('email' => $id_email), array($objet => $id_objet), array('type'=>_request('type')));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_email', '', '&');
			}
		}
	}
	return $res;

}


?>
