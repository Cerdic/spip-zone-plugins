<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Suppression d'une Alerte. Fonction reprise du plugin Mes favoris de Olivier Sallou, Cedric Morin.
 */


function action_supprimer_alerte_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_alerte = $securiser_action();

	include_spip('inc/alerte');
	alertes_supprimer(array('id_alerte'=>$id_alerte));
}

?>