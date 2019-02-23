<?php
/**
 * Gestion du formulaire de d'édition de objets_information_lies
 *
 * @plugin     Infos extras pour objets
 * @copyright  2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Objets_infos_extras\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');



/**
 * Chargement du formulaire d'édition de objets_information_lies
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_objets_information_lies
 *     Identifiant du objets_information_lies. 'new' pour un nouveau objets_information_lies.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_information_lies créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_information_lies source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_information_lies, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_objets_information_lies_charger_dist($id_objets_information, $objet, $id_objet) {
	$valeurs = sql_fetsel(
		'*', 'spip_objets_informations_liens',
		'id_objets_information=' . $id_objets_information . ' AND objet LIKE' .sql_quote($objet) . ' AND id_objet= ' .$id_objet);;

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de objets_information_lies
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_objets_information_lies
 *     Identifiant du objets_information_lies. 'new' pour un nouveau objets_information_lies.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_information_lies créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_information_lies source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_information_lies, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_objets_information_lies_verifier_dist($id_objets_information, $objet, $id_objet) {
	$erreurs = array();

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de objets_information_lies
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_objets_information_lies
 *     Identifiant du objets_information_lies. 'new' pour un nouveau objets_information_lies.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le objets_information_lies créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un objets_information_lies source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du objets_information_lies, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_objets_information_lies_traiter_dist($id_objets_information, $objet, $id_objet) {
	include_spip('inc/headers');
	sql_updateq(
		'spip_objets_informations_liens',
		array('quantite' => _request('quantite')),
			'id_objets_information=' . $id_objets_information .
			' AND objet LIKE ' . sql_quote($objet) .
			' AND id_objet=' . $id_objet
		);

	return array('redirect' => _request('url'));

}
