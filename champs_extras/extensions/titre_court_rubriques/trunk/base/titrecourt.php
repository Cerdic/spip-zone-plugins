<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function titrecourt_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'rubrique', // sur quelle table ?
		'champ' => 'titre_court', // nom sql
		'label' => 'titrecourt:titre_court', // chaine de langue 'prefix:cle'
		'type' => 'ligne', // type de saisie
		'sql' => "varchar(30) NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>
