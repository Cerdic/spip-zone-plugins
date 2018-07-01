<?php
/**
 * Définit les autorisations du plugin motscreer
 *
 * @plugin     creer rubriques
 * @copyright  2014
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\motscreer\Autorisations
 */

if( !defined('_ECRIRE_INC_VERSION') ){
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function motscreer_autoriser() { }

/**
 * Autorisation pour affichage dans le menu (admins complets uniquement)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_motscreer_menu_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}
