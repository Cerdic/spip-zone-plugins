<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function gmaps_v3_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'article',					// Table (au sens Champs Extra 2)
		'champ' => 'latitude',						// Nom du champs dans la base de donnée
		'label' => 'gmaps_v3:label_latitude',		// Nom dans le formulaire dans l'espace privé
		// 'precisions'=>'',						// 
		// 'obligatoire'=>'on',					// Champs obligatoire ?
		// 'verifier'=>'',							//
		// 'verifier_options'=>'',					//
		'rechercher'=>'',						// Ne pas inclure dans la fonction de recherche
		'type' => 'ligne',					// Type dans Champs Extra
		'sql' => "text NULL DEFAULT ''", 	// Declaration sql
		'traitements'=>''						// Table des traitements de SPIP
	));
	$champs[] = new ChampExtra(array(
		'table' => 'article',					// Table (au sens Champs Extra 2)
		'champ' => 'longitude',						// Nom du champs dans la base de donnée
		'label' => 'gmaps_v3:label_longitude',		// Nom dans le formulaire dans l'espace privé
		// 'precisions'=>'',						// 
		// 'obligatoire'=>'on',					// Champs obligatoire ?
		// 'verifier'=>'',							//
		// 'verifier_options'=>'',					//
		'rechercher'=>'',						// Ne pas inclure dans la fonction de recherche
		'type' => 'ligne',					// Type dans Champs Extra
		'sql' => "text NULL DEFAULT ''", 	// Declaration sql
		'traitements'=>''						// Table des traitements de SPIP
	));
	$champs[] = new ChampExtra(array(
		'table' => 'article',					// Table (au sens Champs Extra 2)
		'champ' => 'adresse_complete',						// Nom du champs dans la base de donnée
		'label' => 'gmaps_v3:label_adresse',		// Nom dans le formulaire dans l'espace privé
		// 'precisions'=>'',						// 
		// 'obligatoire'=>'on',					// Champs obligatoire ?
		// 'verifier'=>'',							//
		// 'verifier_options'=>'',					//
		'rechercher'=>'',						// Ne pas inclure dans la fonction de recherche
		'type' => 'ligne',					// Type dans Champs Extra
		'sql' => "text NULL DEFAULT ''", 	// Declaration sql
		'traitements'=>''						// Table des traitements de SPIP
	));
	return $champs;
}
