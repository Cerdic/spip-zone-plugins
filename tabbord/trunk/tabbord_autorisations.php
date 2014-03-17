<?php
/**
 * Définit les autorisations du plugin Tableau de bord
 *
 * @plugin     Tableau de bord
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Tabbord\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function tabbord_autoriser(){}


/**
 * Autorisation de voir (projets_sites_client)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_tabbord_voir_dist($faire, $type, $id, $qui, $opt) {
        return $qui['statut'] == '0minirezo' AND $qui['webmestre'] == 'oui';
}



?>