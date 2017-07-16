<?php
/**
 * Définit les autorisations du plugin SEOptimiz'R
 *
 * @plugin     seoptimizr
 * @copyright  2014 - 2017
 * @author     Loiseau2nuit
 * @licence    GNU/GPL
 * @package    SPIP\seoptimizr\Autorisations
 */

if( !defined('_ECRIRE_INC_VERSION') ){
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function seoptimizr_autoriser() { }

/**
 * Autorisation pour affichage dans le menu
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_seoptimizr_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo';
}
