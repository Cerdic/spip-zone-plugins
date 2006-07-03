<?php

global $tables_auxiliaires;
include_spip('base/auxiliaires');
$tables_auxiliaires['spip_mots_articles']['field']['rang']='INT NOT NULL';

function balise_RANG($p) {
	$_rang = champ_sql('rang', $p);
	$p->code = "$_rang";
	$p->interdire_scripts = false;
	return $p;
}

?>