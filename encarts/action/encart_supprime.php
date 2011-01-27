<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_encart_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_encart = intval($arg);

	// suppression
	sql_delete('spip_encarts', 'id_encart='.$id_encart);
	sql_delete('spip_encarts_liens', 'id_encart='.$id_encart);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(_request('redirect'));

}


?>
