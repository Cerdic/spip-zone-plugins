<?php
/**
 * Définit les autorisations du plugin Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdc\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
#function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
#	return autoriser('webmestre');
#}
function sdc_autoriser() {
	return autoriser('webmestre');
}
