<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape3_charger_dist(){
	$valeurs = array(
		'infos_personnelles'=>unserialize($env),
		'niveau_salsa' => '',
		'style_salsa' => '',
		'activites' => '',
		'activites_liste' => '',
		'partage' => '',
		'partage_nom' => '',
		'contre_indication' => '',
		'contre_indication_nature' => '',		
	);
	return $valeurs;
}


function formulaires_etape3_verifier_dist(){
	$erreurs = array();

	// lister les champs obligatoires
	$champs_obligatoires = array(
		// nouveaux champs
		'niveau_salsa'=>'',
		'style_salsa' => ''
	);

	// verifier la presence de tous les champs obligatoires
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = '*Ce champ est obligatoire';
	}
	return $erreurs;
}

function formulaires_etape3_traiter_dist(){
	$message_ok = "<p>Merci pour ces informations; vous allez maintenant choisir votre mode de règlement.</p>";
	return array('message_ok'=>$message_ok);
}


?>