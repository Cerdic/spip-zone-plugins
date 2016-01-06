<?php

/**
 * Définit les autorisations du plugin Unsplash.
 *
 * @plugin     Unsplash
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Fonction d'appel pour le pipeline.
 * @pipeline autoriser */
function unsplash_autoriser()
{
}

// -----------------
// Objet unsplash


/**
 * Autorisation de voir un élément de menu (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_menu_dist($faire, $type, $id, $qui, $opt)
{
    return true;
}

/**
 * Autorisation de créer (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_creer_dist($faire, $type, $id, $qui, $opt)
{
    return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de ajouter (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_ajouter_dist($faire, $type, $id, $qui, $opt)
{
    return in_array($qui['statut'], array('0minirezo', '1comite'));
}

/**
 * Autorisation de voir (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_voir_dist($faire, $type, $id, $qui, $opt)
{
    return true;
}

/**
 * Autorisation de modifier (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_modifier_dist($faire, $type, $id, $qui, $opt)
{
    // return in_array($qui['statut'], array('0minirezo', '1comite'));
    return false;
}

/**
 * Autorisation de supprimer (unsplash).
 *
 * @param string $faire Action demandée
 * @param string $type  Type d'objet sur lequel appliquer l'action
 * @param int    $id    Identifiant de l'objet
 * @param array  $qui   Description de l'auteur demandant l'autorisation
 * @param array  $opt   Options de cette autorisation
 *
 * @return bool true s'il a le droit, false sinon
 **/
function autoriser_unsplash_supprimer_dist($faire, $type, $id, $qui, $opt)
{
    return $qui['statut'] == '0minirezo' and !$qui['restreint'];
}
