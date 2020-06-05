<?php
/**
 * Plugin Agenda 4 pour Spip 3.0
 * Licence GPL 3
 *
 * 2006-2011
 * Auteurs : cf paquet.xml
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function action_separer_evenement_repetition_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_evenement = intval($securiser_action());

	// A-t-on vraiment le droit de modifier la rubrique en question ?
	if ($id_evenement
	  and autoriser('modifier', 'evenement', $id_evenement)) {

		include_spip('action/editer_evenement');
		evenement_modifier($id_evenement, array('modif_synchro_source' => 0));
	}
}
