<?php
/**
 * Gestion du formulaire de d'édition de evaluations_synthese
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
 * @param int|string $id_evaluations_synthese
 *     Identifiant du evaluations_synthese. 'new' pour un nouveau evaluations_synthese.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un evaluations_synthese source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du evaluations_synthese, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_evaluations_synthese_identifier_dist($id_evaluations_synthese='new', $retour='', $options=array()){
	return serialize(array(intval($id_evaluations_synthese)));
}

/**
 * Chargement du formulaire d'édition de evaluations_synthese
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_evaluations_synthese
 *     Identifiant du evaluations_synthese. 'new' pour un nouveau evaluations_synthese.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array $options
 *     Options du formulaire.
 *     - simplifier : Cache les champs id_evaluation, objet, id_objet s'ils sont connus.
 *     - id_evaluation : evaluation définie par défaut si nouvelle synthèse
 *     - objet : objet défini par défaut si nouvelle synthèse
 *     - id_objet : objet défini par défaut si nouvelle synthèse
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_evaluations_synthese_charger_dist($id_evaluations_synthese='new', $retour='', $options=array()){
	$valeurs = formulaires_editer_objet_charger('evaluations_synthese',$id_evaluations_synthese,'', 0, $retour, '');
	if (!is_array($options)) $options = array();
	$valeurs = $options + $valeurs;
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de evaluations_synthese
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_evaluations_synthese
 *     Identifiant du evaluations_synthese. 'new' pour un nouveau evaluations_synthese.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array $options
 *     Options du formulaire.
 *     - simplifier : Cache les champs id_evaluation, objet, id_objet s'ils sont connus.
 *     - id_evaluation : evaluation définie par défaut si nouvelle synthèse
 *     - objet : objet défini par défaut si nouvelle synthèse
 *     - id_objet : objet défini par défaut si nouvelle synthèse
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_evaluations_synthese_verifier_dist($id_evaluations_synthese='new', $retour='', $options=array()){
	return formulaires_editer_objet_verifier('evaluations_synthese',$id_evaluations_synthese, array('id_evaluation', 'objet', 'id_objet'));
}

/**
 * Traitement du formulaire d'édition de evaluations_synthese
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_evaluations_synthese
 *     Identifiant du evaluations_synthese. 'new' pour un nouveau evaluations_synthese.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array $options
 *     Options du formulaire.
 *     - simplifier : Cache les champs id_evaluation, objet, id_objet s'ils sont connus.
 *     - id_evaluation : evaluation définie par défaut si nouvelle synthèse
 *     - objet : objet défini par défaut si nouvelle synthèse
 *     - id_objet : objet défini par défaut si nouvelle synthèse
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_evaluations_synthese_traiter_dist($id_evaluations_synthese='new', $retour='', $options=array()){
	$res = formulaires_editer_objet_traiter('evaluations_synthese',$id_evaluations_synthese, '', 0, $retour, '');
	if ($retour) $res['redirect'] = $retour; // éviter l'ajout systématique de id_evaluations_synthese dans l'url.
	return $res;
}


?>
