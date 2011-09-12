<?php

function formulaires_sitra_recherche_charger_dist(){
	$valeurs = array();
	$valeurs['titre'] = _request('titre');
	$valeurs['id_sitra'] = _request('id_sitra');
	return $valeurs; 
}

?>