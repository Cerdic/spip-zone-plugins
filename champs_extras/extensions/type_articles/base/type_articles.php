<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function type_articles_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'article',					// Table (au sens Champs Extra 2)
		'champ' => 'type',						// Nom du champs dans la base de donnée
		'label' => 'type_articles:titre',		// Nom dans le formulaire dans l'espace privé
		// 'precisions'=>'',						// 
		// 'obligatoire'=>'on',					// Champs obligatoire ?
		// 'verifier'=>'',							//
		// 'verifier_options'=>'',					//
		'rechercher'=>'',						// Ne pas inclure dans la fonction de recherche
		'type' => 'menu-enum',					// Type dans Champs Extra
		'enum'=>",type_articles:type_defaut
		1,type_articles:type_1
		2,type_articles:type_2",				// Liste de valeur de l'énumération | Surchargeable depuis le fichier de langue
		'sql' => "text NOT NULL DEFAULT ''", 	// Declaration sql
		'traitements'=>''						// Table des traitements de SPIP
	));
	return $champs;
}
