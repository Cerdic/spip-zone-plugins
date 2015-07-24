<?php
/**
 * Gestion du formulaire de d'édition d'un réseau social
 *
 * @plugin     Coordonnees
 * @copyright  2015
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
 * @param int|string $id_rezo
 *     Identifiant du réseau social. 'new' pour un nouveau réseau.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le réseau social créé à cet objet,
 *     tel que `article|3`
 * @return array
 *     Tableau des saisies
 */
function formulaires_editer_rezo_saisies_dist($id_rezo='new', $retour='', $associer_objet=''){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('coordonnees:label_titre'),
				'placeholder' => _T('coordonnees:placeholder_titre_rezo')
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'rezo',
				'label' => _T('coordonnees:rezo')
			)
		)
	);

	// si on associe le réseau social à un objet, rajouter la saisie 'type'
	if($associer_objet) {
		$saisie_type = array(
			array (
			'saisie' => 'type_rezo',
			'options' => array (
				'nom' => 'type',
				'label' => _T('coordonnees:label_type_rezo'),
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
 * @param int|string $id_rezo
 *     Identifiant du réseau social. 'new' pour un nouveau réseau social.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le réseau social créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'une adresse source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du réseau social, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_rezo_identifier_dist($id_rezo='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_rezo), $associer_objet));
}

/**
 * Chargement du formulaire d'édition d'un réseau social
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_rezo
 *     Identifiant du réseau social. 'new' pour un nouveau réseau social.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le réseau social créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un réseau social source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du réseau social, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_rezo_charger_dist($id_rezo='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('rezo',$id_rezo,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// valeur de la saisie "type" dans la table de liens
	if ( $associer_objet ) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		$valeurs['type'] = sql_getfetsel('type', 'spip_rezos_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_rezo='.intval($id_rezo) );
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition d'un réseau social
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_rezo
 *     Identifiant du réseau social. 'new' pour un nouveau réseau.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le réseau social créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un réseau social source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du réseau social, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_rezo_verifier_dist($id_rezo='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('rezo',$id_rezo);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'une adresse
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_rezo
 *     Identifiant du réseau social. 'new' pour un nouveau réseau social.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le réseau social créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un réseau social source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du réseau social, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_rezo_traiter_dist($id_rezo='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('rezo',$id_rezo,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_rezo = $res['id_rezo']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet == intval($id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('rezo' => $id_rezo), array($objet => $id_objet), array('type'=>_request('type')));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_rezo', '', '&');
			}
		}
	}
	return $res;

}


?>
