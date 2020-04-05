<?php
/**
 * Gestion du formulaire de d'édition de factures_ligne
 *
 * @plugin     Factures &amp; devis
 * @copyright  2013
 * @author     Cyril Marion - Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Factures\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_factures_ligne
 *     Identifiant du factures_ligne. 'new' pour un nouveau factures_ligne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le factures_ligne créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un factures_ligne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du factures_ligne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_factures_ligne_identifier_dist($id_factures_ligne='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_factures_ligne), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de factures_ligne
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_factures_ligne
 *     Identifiant du factures_ligne. 'new' pour un nouveau factures_ligne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le factures_ligne créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un factures_ligne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du factures_ligne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_factures_ligne_charger_dist($id_factures_ligne='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('factures_ligne',$id_factures_ligne,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	if ($id_facture = _request('id_parent')){
		$valeurs['id_facture'] = $id_facture;
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de factures_ligne
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_factures_ligne
 *     Identifiant du factures_ligne. 'new' pour un nouveau factures_ligne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le factures_ligne créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un factures_ligne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du factures_ligne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_factures_ligne_verifier_dist($id_factures_ligne='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('factures_ligne',$id_factures_ligne, array('id_facture', 'quantite', 'unite', 'designation'));
}

/**
 * Traitement du formulaire d'édition de factures_ligne
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_factures_ligne
 *     Identifiant du factures_ligne. 'new' pour un nouveau factures_ligne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le factures_ligne créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un factures_ligne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du factures_ligne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_factures_ligne_traiter_dist($id_factures_ligne='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('factures_ligne',$id_factures_ligne,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_factures_ligne = $res['id_factures_ligne']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('factures_ligne' => $id_factures_ligne), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_factures_ligne, '&');
			}
		}
	}
	return $res;

}


?>