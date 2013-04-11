<?php
/**
 * Plugin Paravent
 * (c) 2013 Scribe
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// declaration vide pour ce pipeline.
function paravent_autoriser(){}

/**
 * Autoriser a voir le site en construction : par defaut tous les auteurs authentifies
 * @return bool
 */
function autoriser_travaux($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'], array('0minirezo', '1comite'));
	}
?>