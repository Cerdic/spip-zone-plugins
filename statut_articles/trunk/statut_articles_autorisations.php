<?php
/**
 * Définit les autorisations du plugin Statut articles
 *
 * @plugin     Statut articles
 * @copyright  2015
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Statut_articles\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function statut_articles_autoriser(){}


/**
 * Autorisation de voir un élément de menu (changer_statut_articles)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_changerstatutarticles_menu_dist($faire, $type, $id, $qui, $opt) {
	return( $qui['statut'] == '0minirezo' );

}
