<?php
/**
 * Définit les autorisations du plugin Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function info_spip_autoriser(){}


/**
 * Autorisation de voir (info_spip)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_info_spip_voir_dist($faire, $type, $id, $qui, $opt) {
        return $qui['statut'] == '0minirezo' AND $qui['webmestre'] == 'oui';
}

?>