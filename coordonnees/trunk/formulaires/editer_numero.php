<?php
/**
 * Gestion du formulaire de d'édition d'un numero
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
 * @param int|string $id_numero
 *     Identifiant du numero. 'new' pour un nouveau numero.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le numero crée à cet objet,
 *     tel que `article|3`
 * @return array
 *     Tableau des saisies
 */
function formulaires_editer_numero_saisies_dist($id_numero='new', $retour='', $associer_objet=''){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('coordonnees:label_titre'),
				'placeholder' => _T('coordonnees:placeholder_titre_numero')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'numero',
				'label' => _T('coordonnees:label_numero'),
				'obligatoire' => 'oui'
			),
			// decommenter ces lignes quand les numeros
			// internationaux seront pris en compte par 'verifier'
			/*'verifier' => array (
				'type' => 'telephone'
			)*/
		),
	);

	// si on associe le numéro à un objet, rajouter la saisie 'type'
	if($associer_objet) {
		$saisie_type = array(
			array (
			'saisie' => 'type_tel',
				'options' => array (
					'nom' => 'type',
					'label' => _T('coordonnees:label_type_numero'),
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
 * @param int|string $id_numero
 *     Identifiant de l'numero. 'new' pour un nouveau numero.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le numero créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une numero source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du numero, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_numero_identifier_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_numero), $associer_objet));
}

/**
 * Chargement du formulaire d'édition d'une numero
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_numero
 *     Identifiant de l'numero. 'new' pour un nouveau numero.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le numero créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une numero source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du numero, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_numero_charger_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('numero',$id_numero,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// valeur de la saisie "type" dans la table de liens
	if ( $associer_objet ) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		$valeurs['type'] = sql_getfetsel('type', 'spip_numeros_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_numero='.intval($id_numero) );
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition d'un numero
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_numero
 *     Identifiant de l'numero. 'new' pour un nouveau numero.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le numero créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une numero source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du numero, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_numero_verifier_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('numero',$id_numero);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'un numero
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_numero
 *     Identifiant de l'numero. 'new' pour un nouveau numero.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le numero créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une numero source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du numero, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_numero_traiter_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('numero',$id_numero,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_numero = $res['id_numero']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet == intval($id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('numero' => $id_numero), array($objet => $id_objet), array('type'=>_request('type')));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_numero', '', '&');
			}
		}
	}
	return $res;

}


?>
