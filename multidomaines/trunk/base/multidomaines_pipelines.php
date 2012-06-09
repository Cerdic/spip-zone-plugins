<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function multidomaines_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'rubrique', // sur quelle table ?
		'champ' => 'host', // nom sql
		'label' => 'multidomaines:label_url', // chaine de langue 'prefix:cle'
		'precisions' => "multidomaines:precisions_url", // precisions sur le champ
		'obligatoire' => false, // 'oui' ou '' (ou false)
		'rechercher' => false, // false, ou true ou directement la valeur de ponderation (de 1 à 8 generalement)
		'type' => 'ligne', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}

?>