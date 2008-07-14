<?php

function formulaires_exemple_table_charger_dist($cfg_id){
	$valeurs = array(
		'cfg_exemple_id' => '',
	);
	
	// return $valeurs; // retourner simplement les valeurs
	return array(true,$valeurs); // forcer l'etat editable du formulaire et retourner les valeurs
}

function formulaires_exemple_table_traiter_dist($cfg_id=""){
	return array(true,''); // forcer l'etat editable du formulaire et retourner le message
}

?>
