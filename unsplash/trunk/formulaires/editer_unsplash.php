<?php
/**
 * Gestion du formulaire de d'édition de unsplash
 *
 * @plugin     Unsplash
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Unsplash\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_unsplash
 *     Identifiant du unsplash. 'new' pour un nouveau unsplash.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un unsplash source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du unsplash, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_unsplash_identifier_dist($id_unsplash='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_unsplash)));
}

/**
 * Chargement du formulaire d'édition de unsplash
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_unsplash
 *     Identifiant du unsplash. 'new' pour un nouveau unsplash.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un unsplash source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du unsplash, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_unsplash_charger_dist($id_unsplash='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('unsplash',$id_unsplash,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de unsplash
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_unsplash
 *     Identifiant du unsplash. 'new' pour un nouveau unsplash.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un unsplash source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du unsplash, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_unsplash_verifier_dist($id_unsplash='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	return formulaires_editer_objet_verifier('unsplash',$id_unsplash, array('filename'));

}

/**
 * Traitement du formulaire d'édition de unsplash
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_unsplash
 *     Identifiant du unsplash. 'new' pour un nouveau unsplash.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un unsplash source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du unsplash, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_unsplash_traiter_dist($id_unsplash='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('unsplash',$id_unsplash,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>