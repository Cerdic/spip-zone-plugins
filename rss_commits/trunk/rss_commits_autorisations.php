<?php
/**
 * Définit les autorisations du plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Commits\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function rss_commits_autoriser()
{
}


// -----------------
// Objet commits


/**
 * Autorisation de voir un élément de menu (commits)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rss_commits_menu_dist($faire, $type, $id, $qui, $opt)
{
    return true;
}


/**
 * Autorisation de voir le bouton d'accès rapide de création (commit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_commitcreer_menu_dist($faire, $type, $id, $qui, $opt)
{
    return autoriser('creer', 'commit', '', $qui, $opt);
}

/**
 * Autorisation de créer (commit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_commit_creer_dist($faire, $type, $id, $qui, $opt)
{
    return false;
}

/**
 * Autorisation de voir (commit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_commit_voir_dist($faire, $type, $id, $qui, $opt)
{
    return true;
}

/**
 * Autorisation de modifier (commit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_commit_modifier_dist($faire, $type, $id, $qui, $opt)
{
    return false;
}

/**
 * Autorisation de supprimer (commit)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_commit_supprimer_dist($faire, $type, $id, $qui, $opt)
{
    return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}




?>