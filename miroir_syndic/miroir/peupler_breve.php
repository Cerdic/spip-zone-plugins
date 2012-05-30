<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2012 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

function miroir_peupler_breve_dist($id_breve,$row) {

	include_spip("action/editer_breve");
	breve_modifier($id_breve,array(
		'titre'=>$row['titre'],
		'date_heure'=>$row['date'],
		'statut'=>'publie',
		'texte'=>$row['descriptif'],
		));

	
}
?>