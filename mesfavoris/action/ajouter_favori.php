<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2010 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

function action_ajouter_favori_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	$arg = explode("-",$arg);
	$objet = $arg[0];
	$id_objet = $arg[1];
	if (count($arg)>2)
		$id_auteur = $arg[2];
	else
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];

	include_spip('inc/mesfavoris');
	mesfavoris_ajouter($id_favori);
}

?>