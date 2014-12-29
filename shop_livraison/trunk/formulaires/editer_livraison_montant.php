<?php
/**
 * Gestion du formulaire de d'édition de livraison_montant
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
 * @param int|string $id_livraison_montant
 *     Identifiant du livraison_montant. 'new' pour un nouveau livraison_montant.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_montant source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_montant, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_livraison_montant_identifier_dist($id_livraison_montant='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_livraison_montant)));
}

/**
 * Chargement du formulaire d'édition de livraison_montant
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_livraison_montant
 *     Identifiant du livraison_montant. 'new' pour un nouveau livraison_montant.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_montant source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_montant, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_livraison_montant_charger_dist($id_livraison_montant='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    include_spip('inc/config');
    $config=lire_config('shop_livraison',array());
    
	$valeurs = formulaires_editer_objet_charger('livraison_montant',$id_livraison_montant,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

if($id_livraison_zone=_request('id_livraison_zone') OR $id_livraison_zone=$valeurs['id_livraison_zone']){
        $valeurs['id_livraison_zone']=$id_livraison_zone;
        $valeurs['unite']=sql_getfetsel('unite','spip_livraison_zones','id_livraison_zone='.$id_livraison_zone);
        $valeurs['mesure_defaut']=mesure_unite($valeurs['unite']);
        }

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de livraison_montant
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_livraison_montant
 *     Identifiant du livraison_montant. 'new' pour un nouveau livraison_montant.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_montant source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_montant, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_livraison_montant_verifier_dist($id_livraison_montant='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    $obligatoires=array('montant','id_livraison_zone');
     if($id_livraison_zone=_request('id_livraison_zone') AND $unite=sql_getfetsel('unite','spip_livraison_zones','id_livraison_zone='.$id_livraison_zone)){
        $obligatoires=array_merge($obligatoires,array('mesure_max'));
        }
    $erreurs=formulaires_editer_objet_verifier('livraison_montant',$id_livraison_montant,$obligatoires);

    
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de livraison_montant
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_livraison_montant
 *     Identifiant du livraison_montant. 'new' pour un nouveau livraison_montant.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un livraison_montant source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du livraison_montant, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_livraison_montant_traiter_dist($id_livraison_montant='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('livraison_montant',$id_livraison_montant,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>