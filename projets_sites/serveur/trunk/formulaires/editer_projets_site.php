<?php
/**
 * Gestion du formulaire de d'édition de projets_site
 *
 * @plugin     Sites pour projets
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_site
 *     Identifiant du projets_site. 'new' pour un nouveau projets_site.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le projets_site créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_site source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_site, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_projets_site_identifier_dist($id_site = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return serialize(array(intval($id_site), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de projets_site
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_site
 *     Identifiant du projets_site. 'new' pour un nouveau projets_site.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le projets_site créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_site source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_site, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_projets_site_charger_dist($id_site = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    $valeurs = formulaires_editer_objet_charger('projets_site', $id_site, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
    return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de projets_site
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_site
 *     Identifiant du projets_site. 'new' pour un nouveau projets_site.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le projets_site créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_site source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_site, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_projets_site_verifier_dist($id_site = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    return formulaires_editer_objet_verifier('projets_site', $id_site);
}

/**
 * Traitement du formulaire d'édition de projets_site
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_site
 *     Identifiant du projets_site. 'new' pour un nouveau projets_site.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le projets_site créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un projets_site source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du projets_site, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_projets_site_traiter_dist($id_site = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '')
{
    $res = formulaires_editer_objet_traiter('projets_site', $id_site, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

    // Un lien a prendre en compte ?
    if ($associer_objet and $id_site = $res['id_site']) {
        list($objet, $id_objet) = explode('|', $associer_objet);

        if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
            include_spip('action/editer_liens');
            objet_associer(array('projets_site' => $id_site), array($objet => $id_objet));
            if (isset($res['redirect'])) {
                $res['redirect'] = parametre_url($res['redirect'], "id_lien_ajoute", $id_site, '&');
            }
        }
    }
    return $res;

}


?>