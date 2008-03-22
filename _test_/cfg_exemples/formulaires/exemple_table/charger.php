<?php
function formulaires_exemple_table_charger_dist(){

	$valeurs = array(
		'cfg_exemple_id' => '',
	);
	
	// return $valeurs; // retourner simplement les valeurs
	return array(true,$valeurs); // forcer l'etat editable du formulaire et retourner les valeurs

}
?>
