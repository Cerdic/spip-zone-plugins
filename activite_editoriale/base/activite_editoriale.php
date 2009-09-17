<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'rubriques', // sur quelle table ?
		'champ' => 'extras_delai', // nom sql
		'label' => 'activite_editoriale:delai', // chaine de langue 'mon_plug:mon_label'
		'precisions' => 'activite_editoriale:delai_precisions', // precisions sur le champ
		'obligatoire' => false, // 'true', 'false' ou ''
		'rechercher' => false, // 'false', 'true' ou directement la valeur de ponderation (de 1 a 8 generalement)
		'type' => 'ligne', // type de saisie
		'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'rubriques', // sur quelle table ?
		'champ' => 'extras_identifiants', // nom sql
		'label' => 'activite_editoriale:identifiants', // chaine de langue 'mon_plug:mon_label'
		'precisions' => 'activite_editoriale:identifiants_precisions', // precisions sur le champ
		'obligatoire' => false, // 'true', 'false' ou ''
		'rechercher' => false, // 'false', 'true' ou directement la valeur de ponderation (de 1 a 8 generalement)
		'type' => 'ligne', // type de saisie
		'sql' => "tinytext NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}

?>
