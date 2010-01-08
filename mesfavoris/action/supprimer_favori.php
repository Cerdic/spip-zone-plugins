<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2010 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

function action_supprimer_favori_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_favori = $securiser_action();

	include_spip('inc/mesfavoris');
	mesfavoris_supprimer(array('id_favori'=>$id_favori));
}

?>