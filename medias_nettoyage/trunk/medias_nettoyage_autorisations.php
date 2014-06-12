<?php
/**
 * Définit les autorisations du plugin Nettoyer la médiathèque
 *
 * @plugin     Nettoyer la médiathèque
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Medias_nettoyage\Autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function medias_nettoyage_autoriser()
{
}

/**
 * Autorisation pour l'onglet medias_tabbord
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_medias_tabbord_onglet_dist($faire, $type, $id, $qui, $opt)
{
    return true; // ou false
}

/**
 * Autorisation pour l'onglet medias_rep_orphelins
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_medias_rep_orphelins_onglet_dist($faire, $type, $id, $qui, $opt)
{
    return true; // ou false
}

/**
 * Autorisation pour l'onglet medias_rep_img
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_medias_rep_img_onglet_dist($faire, $type, $id, $qui, $opt)
{
    return true; // ou false
}

/**
 * Autorisation de suppression d'un fichier orphelin
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_medias_orphelins_supprimer_dist($faire, $type, $id, $qui, $opt)
{
    return $qui['statut'] == '0minirezo' and $qui['webmestre'] == 'oui';
}

?>