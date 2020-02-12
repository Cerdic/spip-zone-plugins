<?php
/**
 * Gestion du formulaire de d'édition de reservation_credit
 *
 * @plugin     Réseŕvations Crédits
 * @copyright  2015-20
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_credits\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservation_credit
 *     Identifiant du reservation_credit. 'new' pour un nouveau reservation_credit.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_reservation_credit_identifier_dist($id_reservation_credit='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_reservation_credit)));
}

/**
 * Chargement du formulaire d'édition de reservation_credit
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservation_credit
 *     Identifiant du reservation_credit. 'new' pour un nouveau reservation_credit.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_reservation_credit_charger_dist($id_reservation_credit='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('reservation_credit',$id_reservation_credit,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservation_credit
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservation_credit
 *     Identifiant du reservation_credit. 'new' pour un nouveau reservation_credit.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_reservation_credit_verifier_dist($id_reservation_credit='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	return formulaires_editer_objet_verifier('reservation_credit',$id_reservation_credit, array('email'));

}

/**
 * Traitement du formulaire d'édition de reservation_credit
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservation_credit
 *     Identifiant du reservation_credit. 'new' pour un nouveau reservation_credit.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un reservation_credit source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du reservation_credit, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_reservation_credit_traiter_dist($id_reservation_credit='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('reservation_credit',$id_reservation_credit,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>