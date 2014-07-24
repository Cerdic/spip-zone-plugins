<?php
/**
 * Définit les autorisations du plugin Sites pour projets - Client
 *
 * @plugin     Sites pour projets - Client
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites_client\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function projets_sites_client_autoriser(){}


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
function autoriser_projets_sites_client_voir_dist($faire, $type, $id, $qui, $opt) {
        return $qui['statut'] == '0minirezo' AND $qui['webmestre'] == 'oui';
}

?>