<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function facteur_autoriser() {}


function autoriser_facteur_dist($faire, $type, $id, $qui, $opt) {
	switch ($faire) {
		case 'onglet':
		case 'configurer':
		case 'editer':
			return ($qui['statut'] == '0minirezo');
			break;
		default:
			return false;
			break;
	}
}

?>