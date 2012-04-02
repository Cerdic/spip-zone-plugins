<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

if (!defined('VAR_DATE')) define('VAR_DATE', 'archives');

function balise_DATE_ARCHIVES($p) {
	$p->code = "_request('".VAR_DATE."')";

	#$p->interdire_scripts = true;
	return $p;
}

function critere_archives($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$objet = objet_type($boucle->id_table);
	$date = objet_info($objet,'date');
	$champ_date = "'" . $boucle->id_table ."." .
	$date . "'";
	$boucle->where[] = array(
		'REGEXP',
		$champ_date, 
		"sql_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".VAR_DATE."']))))"
	);
}

?>