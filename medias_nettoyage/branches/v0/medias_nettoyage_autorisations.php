<?php
/**
 * Définit les autorisations du plugin Nettoyer la médiathèque
 *
 * @plugin     Nettoyer la médiathèque
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Medias_nettoyage\Autorisations
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser 
 */
function medias_nettoyage_autoriser(){}

/**
 * Autorisation pour l'onglet medias_tabbord
 * 
 * @return true
 */
function autoriser_medias_tabbord_onglet_dist($faire, $type, $id, $qui, $opt) {
    return true; // ou false
}

/**
 * Autorisation pour l'onglet medias_rep_orphelins
 * 
 * @return true
 */
function autoriser_medias_rep_orphelins_onglet_dist($faire, $type, $id, $qui, $opt) {
    return true; // ou false
}

/**
 * Autorisation pour l'onglet medias_rep_img
 * 
 * @return true
 */
function autoriser_medias_rep_img_onglet_dist($faire, $type, $id, $qui, $opt) {
    return true; // ou false
}

?>