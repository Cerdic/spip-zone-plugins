<?php
/**
 * Gestion du formulaire de d'édition de evaluations_critere
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_evaluations_critere
 *     Identifiant du evaluations_critere. 'new' pour un nouveau evaluations_critere.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un evaluations_critere source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du evaluations_critere, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_evaluations_critere_identifier_dist($id_evaluations_critere='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_evaluations_critere)));
}

/**
 * Chargement du formulaire d'édition de evaluations_critere
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_evaluations_critere
 *     Identifiant du evaluations_critere. 'new' pour un nouveau evaluations_critere.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un evaluations_critere source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du evaluations_critere, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_evaluations_critere_charger_dist($id_evaluations_critere='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('evaluations_critere',$id_evaluations_critere,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// obtenir le papa dans l'url si présent lors d'une création
	if (!intval($id_evaluations_critere) and $id_evaluation = _request('id_evaluation')) {
		$valeurs['id_evaluation'] = intval($id_evaluation);
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de evaluations_critere
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_evaluations_critere
 *     Identifiant du evaluations_critere. 'new' pour un nouveau evaluations_critere.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un evaluations_critere source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du evaluations_critere, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_evaluations_critere_verifier_dist($id_evaluations_critere='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('evaluations_critere',$id_evaluations_critere, array('id_evaluation'));
}

/**
 * Traitement du formulaire d'édition de evaluations_critere
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_evaluations_critere
 *     Identifiant du evaluations_critere. 'new' pour un nouveau evaluations_critere.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un evaluations_critere source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du evaluations_critere, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_evaluations_critere_traiter_dist($id_evaluations_critere='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('evaluations_critere',$id_evaluations_critere,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
