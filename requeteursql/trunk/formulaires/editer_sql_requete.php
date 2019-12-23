<?php
/**
 * Gestion du formulaire de d'édition de sql_requete
 *
 * @plugin     Requêteur SQL
 * @copyright  2014
 * @author     David Dorchies
 * @licence    GNU/GPL
 * @package    SPIP\Requeteursql\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_sql_requete
 *     Identifiant du sql_requete. 'new' pour un nouveau sql_requete.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un sql_requete source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du sql_requete, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_sql_requete_identifier_dist($id_sql_requete = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_sql_requete)));
}

/**
 * Chargement du formulaire d'édition de sql_requete
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_sql_requete
 *     Identifiant du sql_requete. 'new' pour un nouveau sql_requete.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un sql_requete source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du sql_requete, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_sql_requete_charger_dist($id_sql_requete = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('sql_requete', $id_sql_requete, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de sql_requete
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_sql_requete
 *     Identifiant du sql_requete. 'new' pour un nouveau sql_requete.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un sql_requete source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du sql_requete, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_sql_requete_verifier_dist($id_sql_requete = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {

	$erreurs = formulaires_editer_objet_verifier('sql_requete', $id_sql_requete, array('titre', 'requetesql'));

	$requete = _request('requetesql');

	$regex_mots_dangereux = '/(ALTER|CREATE|DROP|RENAME|TRUNCATE|DELETE|CALL|INSERT|REPLACE|UPDATE)/i';

	$matches = array();
	if ((! _request('chuis_un_ouf')) and
			preg_match($regex_mots_dangereux, $requete, $matches)) {
		$erreurs['requetesql'] = _T(
			'sql_requete:message_erreur_requete_dangereuse',
			array('mot' => $matches[0])
		);

		$erreurs['requetesql'] .= '<br><label for="chuis_un_ouf">' . _T('sql_requete:label_confirmation_danger') . '</label><input type="checkbox" name="chuis_un_ouf" />';
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de sql_requete
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_sql_requete
 *     Identifiant du sql_requete. 'new' pour un nouveau sql_requete.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un sql_requete source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du sql_requete, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_sql_requete_traiter_dist($id_sql_requete = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return formulaires_editer_objet_traiter('sql_requete', $id_sql_requete, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
}
