<?php

global $tables_auxiliaires;
include_spip('base/auxiliaires');
$tables_auxiliaires['spip_mots_articles']['field']['rang']='INT NOT NULL';

function balise_RANG($p) {
	//get the calling boucle
	$boucle = &$p->boucles[$p->id_boucle];
	//consider any automatic join as an explicit join to permit selecting joint table fields
	$boucle->jointures_explicites = $boucle->jointures;
	//generate field code
	$_rang = champ_sql('rang', $p);
	$p->code = "$_rang";
	$p->interdire_scripts = false;
	return $p;
}

?>
