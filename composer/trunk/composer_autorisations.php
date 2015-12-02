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
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function composer_autoriser(){}



/**
 * Autorisation de voir le menu Composer
 *
 * @uses autoriser_composer_executer_dist()
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composer_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser("executer", "composer", null, $qui, $opt);
}

/**
 * Autorisation d'accéder à la page Composer
 *
 * @uses autoriser_composer_executer_dist()
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composer_acceder_dist($faire, $type, $id, $qui, $opt){
	return autoriser("executer", "composer", null, $qui, $opt);
}

/**
 * Autorisation de générer le fichier composer.json
 *
 * @uses autoriser_composer_executer_dist()
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composerjson_modifier_dist($faire, $type, $id, $qui, $opt){
	return autoriser("executer", "composer", null, $qui, $opt);
}

/**
 * Autorisation d'utiliser composer
 *
 * @uses autoriser_composer_acceder_dist()
 * 
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_composer_executer_dist($faire, $type, $id, $qui, $opt){
	return $qui['webmestre'] == 'oui';
}
