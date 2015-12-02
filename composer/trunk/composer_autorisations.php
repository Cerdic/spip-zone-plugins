<?php
/**
 * Autorisations du plugin Composer
 *
 * @plugin     Composer
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Composer\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Autorisation de voir le menu Composer
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composer_menu_dist($faire, $type, $id, $qui, $opt){
    return autoriser("acceder", "composer", null, $qui, $opt);
}

/**
 * Autorisation d'accéder à la page Composer
 *
 * Uniquement les webmestres
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composer_acceder_dist($faire, $type, $id, $qui, $opt){
    return $qui['webmestre'] == 'oui';
}
