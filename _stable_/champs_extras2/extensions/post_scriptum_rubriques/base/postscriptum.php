<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function postscriptum_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'rubrique', // sur quelle table ?
		'champ' => 'ps', // nom sql
		'label' => 'info_post_scriptum', // chaine de langue 'prefix:cle'
		'type' => 'bloc', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
	));
	return $champs;
}
?>
