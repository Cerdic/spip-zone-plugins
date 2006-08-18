<?php

function balise_DATE_ARCHIVES($p, $var_date = 'archives') {
	$p->code = "_request('".$var_date."')";

	#$p->interdire_scripts = true;
	return $p;
}

function critere_archives($idb, &$boucles, $crit, $var_date = 'archives') {
	$boucle = &$boucles[$idb];
 $champ_date = "'" . $boucle->id_table ."." .
  $GLOBALS['table_date'][$boucle->type_requete] . "'";
 $boucle->where[] = array(
  'REGEXP',
  $champ_date, 
  "spip_abstract_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".$var_date."']))))"
 );
 
}

?>