<?php
/**
 * Gestion du formulaire de d'édition de livraison_zone
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_livraison_zone
 *     Identifiant du livraison_zone. 'new' pour un nouveau livraison_zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_zone source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_livraison_zone_identifier_dist($id_livraison_zone='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_livraison_zone)));
}

/**
 * Chargement du formulaire d'édition de livraison_zone
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_livraison_zone
 *     Identifiant du livraison_zone. 'new' pour un nouveau livraison_zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_zone source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_livraison_zone_charger_dist($id_livraison_zone='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    include_spip('inc/config');
    $config=lire_config('shop_livraison',array());

	$valeurs = formulaires_editer_objet_charger('livraison_zone',$id_livraison_zone,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
    if(!intval($id_livraison_zone) AND empty($valeurs['unite'])) $valeurs['unite']=$config['unite_defaut']?$config['unite_defaut']:'';  
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de livraison_zone
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_livraison_zone
 *     Identifiant du livraison_zone. 'new' pour un nouveau livraison_zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_zone source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_livraison_zone_verifier_dist($id_livraison_zone='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('livraison_zone',$id_livraison_zone, array('nom'));
}

/**
 * Traitement du formulaire d'édition de livraison_zone
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_livraison_zone
 *     Identifiant du livraison_zone. 'new' pour un nouveau livraison_zone.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_zone source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_zone, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_livraison_zone_traiter_dist($id_livraison_zone='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('livraison_zone',$id_livraison_zone,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>