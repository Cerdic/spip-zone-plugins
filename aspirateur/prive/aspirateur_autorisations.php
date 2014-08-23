<?php
/**
 * Plugin aspirateur
 * (c) 2014 Anne-lise Martenot
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function aspirateur_autoriser(){}


// configurer
function autoriser_aspirateur_configurer_dist($faire, $type, $id, $qui, $opt) {
	return $qui['statut'] == '0minirezo' AND !$qui['restreint'];
}



?>