<?php

/* Balise #CALENDRIER_MINI
   Auteur James (c) 2006
   Plugin pour spip 1.9
   Licence GNU/GPL
*/

//projet une balise #URL_ARCHIVES
/*function balise_URL_ARCHIVES($p) {
	$_date = champ_sql('date', $p);
	$p->code = "generer_url_archives($_date)";
	
	#$p->interdire_scripts = true;
	return $p;
}

function generer_url_archives($date) {
 return parametre_url(self(), VAR_DATE, affdate($date, 'Y-m'));
}*/

function balise_DATE_ARCHIVES($p) {
	$p->code = "_request('".VAR_DATE."')";

	#$p->interdire_scripts = true;
	return $p;
}

function critere_archives($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
 $champ_date = "'" . $boucle->id_table ."." .
  $GLOBALS['table_date'][$boucle->type_requete] . "'";
 $boucle->where[] = array(
  'REGEXP',
  $champ_date, 
  "spip_abstract_quote(('^' . interdire_scripts(entites_html(\$Pile[0]['".VAR_DATE."']))))"
 );
 
}

?>