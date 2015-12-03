<?php
/**
 * Gestion du formulaire de d'édition de pays
 *
 * @plugin     Pays
 * @copyright  2015
 * @author     2. Cyril MARION
 * @licence    GNU/GPL
 * @package    SPIP\Pays\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_pays
 *     Identifiant du pays. 'new' pour un nouveau pays.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le pays créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un pays source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du pays, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_pays_identifier_dist($id_pays='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_pays), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de pays
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_pays
 *     Identifiant du pays. 'new' pour un nouveau pays.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le pays créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un pays source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du pays, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_pays_charger_dist($id_pays='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('pays',$id_pays,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de pays
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_pays
 *     Identifiant du pays. 'new' pour un nouveau pays.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le pays créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un pays source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du pays, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_pays_verifier_dist($id_pays='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	return formulaires_editer_objet_verifier('pays',$id_pays);

}

/**
 * Traitement du formulaire d'édition de pays
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_pays
 *     Identifiant du pays. 'new' pour un nouveau pays.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le pays créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un pays source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du pays, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_pays_traiter_dist($id_pays='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('pays',$id_pays,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_pays = $res['id_pays']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('pays' => $id_pays), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_pays, '&');
			}
		}
	}
	return $res;

}


?>