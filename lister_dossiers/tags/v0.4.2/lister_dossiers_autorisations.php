<?php
/**
 * Définit les autorisations du plugin Lister les dossiers
 *
 * @plugin     Lister les dossiers
 * @copyright  2014-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Lister_dossiers\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 *
 * @pipeline autoriser
 */
function lister_dossiers_autoriser() { }

/**
 * Autorisation d'affichage du lien vers la page lister_dossiers
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerdossiers_menu_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo')) and $qui['webmestre'] == 'oui';
}

/**
 * Autorisation de consultation de la page "lister_dossiers"
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int $id       Identifiant de l'objet
 * @param  array $qui    Description de l'auteur demandant l'autorisation
 * @param  array $opt    Options de cette autorisation
 *
 * @return bool          true s'il a le droit, false sinon
 **/
function autoriser_listerdossiers_voir_dist($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo')) and $qui['webmestre'] == 'oui';
}
