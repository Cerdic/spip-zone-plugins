<?php
/**
 * Définit les autorisations du plugin Documentation technique
 *
 * @plugin     Documentation technique
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Doc_tech\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser
 */
function doc_tech_autoriser(){}

/**
 * Le lien vers la page de documentation technique ne s'affiche
 * que si on est webmestre
 *
 * @param string $faire
 *              L'action à faire
 * @param string $type
 *              Le type d'objet sur lequel porte l'action
 * @param int $id
 *              L'identifiant numérique de l'objet
 * @param array $qui
 *              Les éléments de session de l'utilisateur en cours
 * @param array $opt
 *              Les options
 * @return boolean true/false
 *              true si autorisé, false sinon
 */
function autoriser_doc_tech_menu_dist($faire, $type, $id, $qui, $opt) {
        if(($qui['webmestre'] == 'oui') && $qui['statut'] == '0minirezo')
                return true;
}


?>