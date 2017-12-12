<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

function sympatic_autoriser() {}

function autoriser_sympatic_dist($faire, $type, $id, $qui, $opt) {
	switch ($faire) {
		case 'bouton':
		case 'onglet':
		case 'configurer':
		case 'voir':
		case 'modifier':
		case 'supprimer':
			return ($qui['statut'] == '0minirezo');
			break;
		default:
			return false;
			break;
	}
}

?>